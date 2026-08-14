# MediQueue — Technical Debt & Architectural Trade-offs

**Last Updated**: 2026-08-14  

---

## Technical Debt Register

| ID | Title | Impact | Resolution Plan | Status |
|---|---|---|---|---|
| **TD-001** | Client Polling vs WebSockets | Low-to-Medium server load from 4-second HTTP polling on active queue status screens. | In future phase, integrate Laravel Reverb / Soketi with Pusher protocol for push-based updates. For current 50-user clinic scope, lightweight polling is highly reliable and has zero infrastructure overhead. | Accepted for v1 |
| **TD-002** | In-App Notifications Delivery Channel | Notifications are stored in relational database and rendered in UI, but not dispatched to external SMS/email gateways. | Architecture already supports multi-channel dispatch via `type` and `data` JSON columns. Attach Twilio or AWS SES listener to notification events. | Architectural Hook Prepared |
| **TD-003** | SQLite in Development & Demonstration | SQLite provides fast in-memory test execution and zero-config deployment on free tiers, but lacks write concurrency under heavy loads. | Production configuration supports seamless switch to PostgreSQL or MySQL by setting `DB_CONNECTION=mysql` in environment variables. | Configurable |
| **TD-004** | Time-Series Queue Analytics | Queue metrics currently aggregate daily real-time counts. Long-term trend charting requires time-series aggregation tables. | Implement background scheduled command to roll daily stats into an `analytics_daily_summary` table. | Future Enhancement |
