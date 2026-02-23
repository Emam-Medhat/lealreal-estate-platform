"""
Model Training Script
Trains a GradientBoostingRegressor on real data exported from MySQL.

Usage:
    python train.py  [--csv path/to/properties.csv]

The CSV must have these columns:
    area, city, bedrooms, floor, finishing, building_age, listing_type, price

Output:
    models/price_model.joblib
    models/encoder.joblib
    models/feature_importances.json
"""

import argparse
import json
import logging
from pathlib import Path

import numpy as np
import pandas as pd
import joblib
from sklearn.ensemble import GradientBoostingRegressor
from sklearn.model_selection import train_test_split, cross_val_score
from sklearn.preprocessing import OrdinalEncoder
from sklearn.metrics import mean_absolute_percentage_error, r2_score
from sklearn.pipeline import Pipeline

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# ─────────────────────────────────────────────────
FINISHING_RANK = {
    "unfinished": 1,
    "semi-finished": 2,
    "standard": 3,
    "super-lux": 4,
    "luxury": 5,
}

def load_data(csv_path: str) -> pd.DataFrame:
    df = pd.read_csv(csv_path)
    required = {"area", "city", "bedrooms", "floor", "finishing", "building_age", "listing_type", "price"}
    missing = required - set(df.columns)
    if missing:
        raise ValueError(f"Missing columns: {missing}")
    # Drop nulls and outliers
    df = df.dropna(subset=list(required))
    q01, q99 = df["price"].quantile([0.01, 0.99])
    df = df[(df["price"] >= q01) & (df["price"] <= q99)]
    return df

def engineer_features(df: pd.DataFrame, encoder: OrdinalEncoder = None, fit: bool = True):
    if fit:
        encoder = OrdinalEncoder(handle_unknown="use_encoded_value", unknown_value=-1)
        city_enc = encoder.fit_transform(df[["city"]])
    else:
        city_enc = encoder.transform(df[["city"]])

    df = df.copy()
    df["finishing_rank"]  = df["finishing"].map(FINISHING_RANK).fillna(3)
    df["listing_sale"]    = (df["listing_type"] == "sale").astype(int)
    df["area_per_room"]   = df["area"] / (df["bedrooms"].clip(lower=1))
    df["newness_score"]   = (20 - df["building_age"]).clip(lower=0)
    df["city_enc"]        = city_enc.flatten()

    feature_cols = [
        "area", "bedrooms", "floor", "building_age",
        "finishing_rank", "listing_sale", "area_per_room", "newness_score", "city_enc"
    ]
    return df[feature_cols].values, encoder

def train(csv_path: str):
    Path("models").mkdir(exist_ok=True)

    logger.info(f"Loading data from {csv_path}")
    df = load_data(csv_path)
    logger.info(f"Dataset: {len(df)} rows after cleaning")

    X, encoder = engineer_features(df, fit=True)
    y = df["price"].values

    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.15, random_state=42
    )

    model = GradientBoostingRegressor(
        n_estimators    = 500,
        max_depth       = 5,
        learning_rate   = 0.05,
        subsample       = 0.8,
        min_samples_leaf= 10,
        random_state    = 42,
    )

    logger.info("Training GradientBoostingRegressor ...")
    model.fit(X_train, y_train)

    y_pred = model.predict(X_test)
    mape   = mean_absolute_percentage_error(y_test, y_pred) * 100
    r2     = r2_score(y_test, y_pred)

    logger.info(f"MAPE: {mape:.2f}%  |  R²: {r2:.4f}")

    joblib.dump(model,   "models/price_model.joblib")
    joblib.dump(encoder, "models/encoder.joblib")

    # Feature importances
    feature_names = [
        "area", "bedrooms", "floor", "building_age",
        "finishing_rank", "listing_sale", "area_per_room", "newness_score", "city_enc"
    ]
    importances = dict(zip(feature_names, model.feature_importances_.tolist()))
    with open("models/feature_importances.json", "w") as f:
        json.dump(importances, f, indent=2)

    logger.info("Models saved to models/")
    return {"mape": mape, "r2": r2}

if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("--csv", default="data/properties.csv")
    args = parser.parse_args()
    result = train(args.csv)
    logger.info(f"Training complete: {result}")
