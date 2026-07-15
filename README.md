# WiFi Evil-Twin — Security Awareness PoC

A proof-of-concept **WiFi evil-twin / captive portal** built for **security
awareness (purple team)** exercises. It mimics a corporate "please sign in"
captive portal to demonstrate how easily users hand over their credentials on
a rogue public Wi-Fi, and tracks two simple metrics on a dashboard:
**access attempts** and **connections** (credentials submitted).

## ⚠️ Educational use only

> This project is intended **exclusively** for education, security awareness,
> and authorized purple-team engagements.
>
> - Use it **only** on networks and with people for which you have **explicit,
>   written authorization**.
> - Capturing others' credentials or running a rogue access point on networks
>   you don't own is **illegal** in most jurisdictions.
> - The authors accept **no liability** for misuse. You are solely responsible
>   for complying with the laws and regulations that apply to you.
>
> No real credentials are validated or stored by design — the goal is
> awareness, not compromise.

## How it works

```
┌─ Raspberry Pi (rogue AP) ───────┐        ┌─ Dashboard (container) ──────────┐
│  captive_portal/  (web root)    │  HTTP  │  dashboard/ (PHP + MariaDB)      │
│   index.php  → "attempt"        │ ─────► │   increment.php / stats.php      │
│   connect.php → "connection"    │ ─────► │   index.php (metrics UI)         │
│  raspberry/nginx.conf (system)  │        │                                  │
└─────────────────────────────────┘        └──────────────────────────────────┘
```

- The **captive portal** (on the Pi) redirects all traffic to a fake login
  page and notifies the dashboard over HTTP on page open and on form submit.
- The **dashboard** (deployable as a container / Kubernetes) stores the two
  counters in MariaDB and shows them live.

## Repository layout

| Path | Description |
|---|---|
| `captive_portal/` | Web content served on the Pi (scp to `/var/www/html`) |
| `raspberry/` | Pi system config (e.g. `nginx.conf` → `/etc/nginx/sites-available/`) |
| `dashboard/` | Metrics dashboard (PHP + MariaDB), Docker / k8s ready |

## Full write-up

The complete documentation and step-by-step build of this project is on my
blog: **<https://blog.corentinbringer.fr/blog/faux-portail-captif-raspberry-pi/#créer-le-point-daccès-wi-fi>**

See also [`dashboard/README.md`](dashboard/README.md) for deploying the
dashboard.
