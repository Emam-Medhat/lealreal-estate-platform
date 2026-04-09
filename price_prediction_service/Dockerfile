# ──────────────────────────────────────────────────────────────────────────────
# Price Prediction Microservice – Docker Image
# ──────────────────────────────────────────────────────────────────────────────
# Build:  docker build -t real-estate-price-predictor .
# Run:    docker run -p 8001:8001 --env-file .env real-estate-price-predictor
# ──────────────────────────────────────────────────────────────────────────────

FROM python:3.11-slim

WORKDIR /app

# Install dependencies
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy application code
COPY . .

# Create models directory (populated at runtime via train.py or volume mount)
RUN mkdir -p models data

EXPOSE 8001

# Health-check so orchestrators know when the service is ready
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://localhost:8001/health || exit 1

CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8001", "--workers", "2"]
