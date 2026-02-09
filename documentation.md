# IoT Water Quality Monitoring System 🌊

## Overview
This system monitors water quality parameters (Turbidity, TDS, pH, Temperature, Humidity) in real-time using an ESP32 microcontroller and a Laravel-based web dashboard. It features live updates, AI-driven analysis, and smart data throttling.

## 🚀 Key Features

### 1. Real-Time Dashboard (5-Second Updates)
- **Live Chart**: Updates every **5 seconds** via Ably WebSockets.
- **Visuals**: Displays the last 20 readings. Old readings scroll off automatically.
- **Persistence**: Chart data is saved in your browser (LocalStorage). If you navigate away and come back, the chart **restores itself** instantly without losing data.

### 2. Smart Database Throttling (30-Second Saves)
- **Problem**: Receiving data every 5s would fill the database with 17,000+ records/day.
- **Solution**: The backend **only saves to the database every 30 seconds**.
- **Result**:
  - Live Chart: Smooth 5s updates (via Broadcast).
  - History/Alerts: Clean 30s intervals (via Database).

### 3. AI Analysis (5-Minute Intervals)
- **Automated**: Every 5 minutes, the system analyzes the latest reading.
- **Insights**: Generates warnings (Safe, Medium, Critical) and recommendations.
- **Notification**: Shows a draggable popup and plays a sound (`ai.mp3`) on the dashboard.

### 4. Gauge/Sensor Throttling
- **Visuals**: The 5 circular gauges on the dashboard update every **30 seconds** to avoid visual noise, while the chart remains real-time (5s).

### 5. Smart Refresh
- **Refresh Button**: Does **NOT** reload the page.
- **Action**: Instantly clears the chart and gauges visually for a "fresh start" session.
- **Note**: This does not affect the History or Alerts data in the database.

---

## 🛠 System Architecture

```
[Sensors] -> [ESP32] --(HTTPS/5s)--> [Laravel API]
                                          |
                                     (Throttle Logic)
                                          |
                       +------------------+------------------+
                       | (Every 5s)                          | (Every 30s)
                       v                                     v
                  [Ably Channel]                       [PostgreSQL DB]
                       |                                     |
                       v                                     v
                 [Web Dashboard]                       [History/Alerts]
                 (Live Chart)                       (Long-term Data)
```

---

## ⚙️ Configuration

### Environment Variables (.env & Railway)
- `ABLY_API_KEY`: Your Ably Realtime API Root Key.
- `APP_URL`: `https://aquasense.blog`
- `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: PostgreSQL Connection.
- `GEMINI_API_KEY`: (Optional) for advanced AI analysis if enabled.

### Hardware (Arduino/ESP32)
- **Upload Interval**: Must be set to **5000 ms (5 seconds)**.
- **Endpoint**: `https://aquasense.blog/api/readings`
- **WifiClientSecure**: Used to bypass SSL verification if needed (`client.setInsecure()`).

---

## 🔧 Operational Logic

### Backend (WaterReadingController)
1.  **Receive Request** from ESP32.
2.  **Check Time**: Is the last DB record > 30s old?
    *   **Yes**: Create new Database Record. Run AI Analysis (if > 5 mins).
    *   **No**: Create "Transient" object (not saved).
3.  **Broadcast**: Send the object (Saved or Transient) to Ably.

### Frontend (Dashboard.blade.php)
1.  **Listen**: Subscribe to `water-readings` channel.
2.  **On Reading (Every 5s)**:
    *   Add point to Chart.
    *   Save state to `LocalStorage`.
    *   Update Chart View.
3.  **On Timer (Every 30s)**:
    *   Update Circular Gauges.
4.  **On Refresh Click**:
    *   Clear `LocalStorage`.
    *   Clear Chart arrays.
    *   Reset Gauges to 0.

---

## ❓ Troubleshooting

-   **Chart not moving?** Check if Ably Key is correct in Railway Variables and `dashboard.blade.php`.
-   **"Not Secure" Warning?** Wait for Railway to renew the SSL certificate for the custom domain.
-   **Gauges lagging?** This is intentional (30s update rate).
-   **History missing data?** This is intentional (30s save rate). Live chart has more data points than history.
