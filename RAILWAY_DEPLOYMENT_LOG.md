# Railway Deployment & Troubleshooting Log
**Date:** February 8, 2026
**Topic:** Connecting Laravel (Railway) + PostgreSQL (Railway) + ESP32

## 1. Environment Variable Parsing Error
**Issue:** Build failed with `Encountered unexpected whitespace at [<render host>]`.
**Cause:** The `.env.example` file contained `<render host>` placeholders. Railway copies this file to `.env` during build, and the parser crashed on the `<` characters.
**Fix:**
- Edited `.env.example` to use safe defaults (e.g., `DB_HOST=127.0.0.1`).
- `git push` to update the repository.

## 2. Database Connection Refused (127.0.0.1)
**Issue:** `SQLSTATE[08006] ... connection to server at "127.0.0.1" ... refused`.
**Cause:** Laravel was prioritizing the default `127.0.0.1` value from `.env` over the Railway variables, or the references (`${{ Postgres... }}`) were not resolving correctly during the build phase.
**Fix:**
- In Railway Variables, explicitly set `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` manually.
- **Critical:** Used the **Public** Hostname (e.g., `ballast.proxy.rlwy.net`) instead of the Internal one (`postgres.railway.internal`) to ensure migration commands could reach the database during the build process.

## 3. White Screen / Asset Loading Issues
**Issue:** Application deployed but showed a specific error or white screen, and assets (CSS/JS) were missing (Giant Logo).
**Cause:**
- **White Screen:** Silent crash or missing config.
- **Assets:** "Mixed Content" error. The app was generating `http://` links while the site was accessed via `https://`.
**Fix:**
- Added `APP_DEBUG=true` to see errors.
- Added `ASSET_URL` starting with `https://`.
- **Permanent Code Fix:** Updated `bootstrap/app.php` to trust proxies:
  ```php
  ->withMiddleware(function (Middleware $middleware): void {
      $middleware->trustProxies(at: '*');
  })
  ```

## 4. API 404 Not Found
**Issue:** ESP32 received `404 Not Found` when sending data to `/api/readings`.
**Cause:** Laravel 11 does **not** enable API routes by default. The `routes/api.php` file was being ignored.
**Fix:**
- Updated `bootstrap/app.php` to explicitly load API routes:
  ```php
  ->withRouting(
      web: __DIR__.'/../routes/web.php',
      api: __DIR__.'/../routes/api.php', // <-- Added this line
      ...
  )
  ```

## 5. ESP32 / Arduino Connection
**Issue:** ESP32 hanging or failing to connect.
**Cause:**
- **SSL:** ESP32 cannot verify the Railway SSL certificate by default.
- **URL:** Trailing slash (`.app/`) caused double slashes (`.app//api...`).
- **Route:** Route mismatch (`/water-readings` vs `/readings`).
**Fix:**
- **Validation:** Added `client.setInsecure();` to skip SSL verification.
- **URL:** Removed trailing slash from `serverName`.
- **Logic:** Added `http.setTimeout(15000);` to prevent infinite hanging.
- **Route:** Updated sketch endpoint to `/api/readings`.

---
*Created by Antigravity Assistant to track debugging progress.*
