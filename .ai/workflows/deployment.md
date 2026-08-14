# Deployment Workflow

Follow this procedure when releasing or deploying MediQueue builds.

---

## Workflow Steps

1. **Pre-Flight Audit**: Check that all automated tests pass and `.env` production config is set.
2. **Build Production Assets**: Run `npm ci` and `npm run build`.
3. **Database Migration Check**: Run `php artisan migrate --force`.
4. **Cache Rebuild**: Run `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`.
5. **Sanity Check Health Endpoint**: Verify HTTP 200 response from `/health`.
6. **Update Deployment State**: Update [.ai/state/current-status.md](file:///c:/Users/user/Desktop/ug-swe-exams/.ai/state/current-status.md).
