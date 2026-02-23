# 🤖 Real Estate AI Platform – API Documentation

Version: 1.0.0  
Base URL: `/api/ai`

---

## 🔍 1. Smart Search (Natural Language)
Parses Arabic/English natural language queries into structured database filters.

- **Endpoint:** `POST /api/ai/smart-search`
- **Rate Limit:** 20 requests / min
- **Request Body:**
  ```json
  {
    "text": "شقة 3 غرف في التجمع الخامس تحت 5 مليون جنيه"
  }
  ```
- **Response:**
  ```json
  {
    "success": true,
    "raw": { "property_type": "apartment", "city": "Cairo", "district": "New Cairo", "max_price": 5000000, ... },
    "data": { "current_page": 1, "data": [...] }
  }
  ```

---

## 🤖 2. AI Real Estate Advisor (Chatbot)
Conversational interface with context about available properties.

- **Endpoint:** `POST /api/ai/chat`
- **Rate Limit:** 10 requests / min
- **Request Body:**
  ```json
  {
    "message": "ما هي أفضل المناطق للاستثمار في القاهرة؟",
    "session_id": "optional-uuid-string"
  }
  ```
- **Response:**
  ```json
  {
    "success": true,
    "session_id": "uuid-string",
    "reply": "تعتبر العاصمة الإدارية والتجمع الخامس حالياً...",
    "suggested_ids": [102, 405]
  }
  ```

- **Clear Session:** `DELETE /api/ai/chat/{sessionId}`

---

## 📈 3. Price Prediction
Predicts the estimated value of a property based on features.

- **Endpoint:** `POST /api/ai/predict-price`
- **Rate Limit:** 200 requests / hour
- **Request Body:**
  ```json
  {
    "area": 150,
    "city": "Sheikh Zayed",
    "district": "District 1",
    "bedrooms": 3,
    "finishing": "super-lux",
    "building_age": 2,
    "listing_type": "sale"
  }
  ```

---

## 🖼️ 4. Image Analysis
Analyzes property photos for quality, room type, and issues.

- **Upload & Analyze:** `POST /api/ai/images/analyze` (multipart/form-data)
- **Get Result:** `GET /api/ai/images/{id}/result`

---

## 🎯 5. Recommendations
Personalised property suggestions for authenticated users.

- **User Recommendations:** `GET /api/ai/recommendations?limit=5`
- **Similar Properties:** `GET /api/ai/recommendations/{propertyId}/similar`

---

## 🔒 Security & Data
1. **Prompt Injection:** All inputs are sanitized using `SanitizesPrompt` trait.
2. **Rate Limiting:** Applied via `AiRateLimitMiddleware` per user/IP.
3. **Budget Guard:** Services automatically fallback or block if the monthly USD budget is reached.
4. **Caching:** Search results and price percentiles are cached in Redis for high performance.
