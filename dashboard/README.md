# Evil-Twin Dashboard Fideciel

Small PHP dashboard showing two counters:

- **Access attempts** — incremented on every captive portal opening
- **Connections** — incremented on every form submission

Storage: MariaDB (key/value `counters` table), atomic increments via
`INSERT ... ON DUPLICATE KEY UPDATE`.

## Contents

| File | Role |
|---|---|
| `index.php` | The dashboard (5 s auto-refresh) |
| `stats.php` | JSON read endpoint `{ "attempt": N, "connection": M }` |
| `increment.php` | Increment endpoint: `?type=attempt` or `?type=connection` |
| `lib.php` | PDO connection + `stats_increment()` / `stats_read()` functions |
| `config.php` | DB config (via environment variables) |
| `auth.php` | HTTP Basic authentication for the UI |
| `health.php` | Health endpoint for probes (no auth, no DB) |
| `schema.sql` | Database + table creation |
| `Dockerfile` / `docker-compose.yml` | Containerized deployment |

## Deployment (recommended: docker-compose)

Start the app **and** the database at once:

```bash
cd dashboard
docker compose up -d --build
```

By default the dashboard is served at the root: `http://<host>:8080/`.
Remember to change the passwords in `docker-compose.yml`
(`MARIADB_PASSWORD` / `MARIADB_ROOT_PASSWORD` / `DB_PASS`).

### Optional context path

Serve the dashboard under a sub-path by setting the `CONTEXT_PATH` build arg
(empty = root). For example, to serve it at `http://<host>:8080/context/`:

```bash
CONTEXT_PATH=context docker compose up -d --build
# or put CONTEXT_PATH=context in a .env file next to docker-compose.yml
```

The app uses relative paths, so it works at the root or under any sub-path
with no code change. When you use a context path, make sure the portal's
`DASHBOARD_URL` (in `captive_portal/notify.php`) and any k8s probe paths
include it too.

### Or: image only (external MySQL database)

```bash
docker build -t evil-twin-dashboard ./dashboard
docker run -d -p 8080:80 \
  -e DB_HOST=my-sql-server -e DB_NAME=evil_twin \
  -e DB_USER=evil_twin -e DB_PASS=secret \
  evil-twin-dashboard
```

The database must then be initialized manually once:
`mysql -u root -p < schema.sql`.

## Connecting the captive portal (on the Raspberry Pi)

The portal calls the dashboard API over HTTP. Set the dashboard URL
in `captive_portal/notify.php` (`DASHBOARD_URL` constant), e.g.:

```php
define('DASHBOARD_URL', 'http://192.168.1.50:8080/context');
```

The URL must include the context path (`/context`).
`curl` must be installed on the Pi (present by default on Raspberry Pi OS).

## UI authentication

The interface (`index.php` + `stats.php`) is protected by HTTP Basic auth.
`increment.php` stays **intentionally open** (called by the captive portal).

Generate a password and set it in `docker-compose.yml`:

```bash
openssl rand -hex 32
# -> paste the value into DASHBOARD_PASSWORD
```

Default user: `admin` (configurable via `DASHBOARD_USER`).
If `DASHBOARD_PASSWORD` is empty, the UI denies all access (fail-closed).

> Basic Auth sends credentials in clear text (base64-encoded): serve it
> behind HTTPS for real-world use.

## Security (PoC)

`increment.php` is open: anyone who can reach it can inflate the counters.
To harden: restrict by source IP, add a shared token, or expose the port
only on the Pi's network.
