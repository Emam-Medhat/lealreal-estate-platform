"""
Real Estate Price Prediction Microservice
Python 3.11 + FastAPI + Scikit-learn

Installation:
    pip install fastapi uvicorn scikit-learn pandas numpy joblib python-dotenv

Run:
    uvicorn main:app --host 0.0.0.0 --port 8001 --reload

Train the model first:
    python train.py
"""

import os
import json
import logging
from typing import Optional
from pathlib import Path

import numpy as np
import pandas as pd
import joblib
from fastapi import FastAPI, HTTPException, Depends, Header
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field, validator

# ─────────────────────────────────────────────────
# Logging
# ─────────────────────────────────────────────────
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# ─────────────────────────────────────────────────
# FastAPI app
# ─────────────────────────────────────────────────
app = FastAPI(
    title="Real Estate Price Prediction API",
    version="1.0.0",
    description="Predicts property price range using ML regression model",
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],   # restrict in production
    allow_methods=["POST", "GET"],
    allow_headers=["*"],
)

# ─────────────────────────────────────────────────
# Model loader (lazy singleton)
# ─────────────────────────────────────────────────
_model = None
_encoder = None
MODEL_PATH  = Path("models/price_model.joblib")
ENCODER_PATH = Path("models/encoder.joblib")

def get_model():
    global _model, _encoder
    if _model is None:
        if not MODEL_PATH.exists():
            raise RuntimeError("Model not found. Run train.py first.")
        _model   = joblib.load(MODEL_PATH)
        _encoder = joblib.load(ENCODER_PATH)
    return _model, _encoder

# ─────────────────────────────────────────────────
# Auth
# ─────────────────────────────────────────────────
API_KEY = os.getenv("PRICE_PREDICTION_API_KEY", "changeme")

def verify_api_key(authorization: Optional[str] = Header(None)):
    if authorization != f"Bearer {API_KEY}":
        raise HTTPException(status_code=401, detail="Unauthorized")

# ─────────────────────────────────────────────────
# Schemas
# ─────────────────────────────────────────────────
VALID_FINISHING = {"unfinished", "semi-finished", "standard", "super-lux", "luxury"}

class PredictRequest(BaseModel):
    area:          float  = Field(..., gt=0,   description="Area in m²")
    city:          str    = Field(..., min_length=1)
    district:      Optional[str] = None
    bedrooms:      int    = Field(..., ge=0, le=20)
    floor:         int    = Field(0,  ge=0)
    finishing:     str    = Field("standard")
    building_age:  int    = Field(0,  ge=0)
    listing_type:  str    = Field("sale")

    @validator("finishing")
    def validate_finishing(cls, v):
        if v not in VALID_FINISHING:
            return "standard"
        return v

class PredictResponse(BaseModel):
    predicted_price_min:  float
    predicted_price_max:  float
    predicted_price_mid:  float
    confidence_score:     float   # 0.0 – 1.0
    investment_score:     int     # 1 – 10
    risk_level:           str     # low | medium | high
    valuation_rationale:  str

# ─────────────────────────────────────────────────
# Feature Engineering
# ─────────────────────────────────────────────────
FINISHING_RANK = {
    "unfinished": 1,
    "semi-finished": 2,
    "standard": 3,
    "super-lux": 4,
    "luxury": 5,
}

def engineer_features(req: PredictRequest, encoder) -> np.ndarray:
    """Build the feature vector expected by the trained model."""

    city_encoded = encoder.transform([[req.city]])[0]  # OrdinalEncoder

    features = [
        req.area,
        req.bedrooms,
        req.floor,
        req.building_age,
        FINISHING_RANK.get(req.finishing, 3),
        1 if req.listing_type == "sale" else 0,
        req.area / max(req.bedrooms, 1),               # area_per_room
        max(0, 20 - req.building_age),                 # newness_score
        *city_encoded,                                  # city embedding dims
    ]

    return np.array(features).reshape(1, -1)

# ─────────────────────────────────────────────────
# Endpoints
# ─────────────────────────────────────────────────
@app.get("/health")
def health():
    return {"status": "ok"}

@app.post("/predict", response_model=PredictResponse)
def predict(
    req: PredictRequest,
    _: None = Depends(verify_api_key),
):
    try:
        model, encoder = get_model()
        X = engineer_features(req, encoder)

        predicted_mid  = float(model.predict(X)[0])
        # Estimate variance from model type
        margin         = predicted_mid * 0.12  # ±12%
        predicted_min  = max(0, predicted_mid - margin)
        predicted_max  = predicted_mid + margin

        # Confidence: higher when area/city signals are strong
        confidence = min(0.95, 0.60 + (0.05 * min(req.bedrooms, 5)) + (0.05 if req.area > 80 else 0))

        # Investment score: heuristic based on location & finishing
        finishing_boost = FINISHING_RANK.get(req.finishing, 3)
        age_penalty = min(3, req.building_age // 10)
        investment_score = max(1, min(10, finishing_boost + 3 - age_penalty))

        # Risk
        if req.building_age > 20 or confidence < 0.65:
            risk = "high"
        elif req.building_age > 10 or confidence < 0.80:
            risk = "medium"
        else:
            risk = "low"

        rationale = (
            f"{req.area}m² {req.finishing} {req.listing_type} property in {req.city}"
            f" with {req.bedrooms} bedrooms, floor {req.floor}, age {req.building_age}yr. "
            f"Estimated at {predicted_mid:,.0f} EGP ±12%."
        )

        logger.info(f"Prediction: city={req.city}, area={req.area}, mid={predicted_mid:.0f}")

        return PredictResponse(
            predicted_price_min  = round(predicted_min,  2),
            predicted_price_max  = round(predicted_max,  2),
            predicted_price_mid  = round(predicted_mid,  2),
            confidence_score     = round(confidence,     4),
            investment_score     = investment_score,
            risk_level           = risk,
            valuation_rationale  = rationale,
        )

    except RuntimeError as e:
        raise HTTPException(status_code=503, detail=str(e))
    except Exception as e:
        logger.error(f"Prediction failed: {e}")
        raise HTTPException(status_code=500, detail="Prediction error")
