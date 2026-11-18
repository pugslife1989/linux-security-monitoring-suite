# Linux Server Monitoring & Network Visibility Suite

A lightweight, automated security and infrastructure monitoring toolkit built on Ubuntu Linux using **Bash, Nmap, Apache, and Cron**. This project delivers continuous file integrity alerting, real-time system health diagnostics, and web-based network port visibility at zero software licensing cost.

---

## Business Impact

* **Faster Threat Detection:** Cuts Mean Time to Detect (MTTD) unauthorized edits on critical authentication files (`/etc/passwd`, `/etc/ssh/sshd_config`) to under 15 minutes via automated SHA-256 baseline alerts.
* **Democratized Network Visibility:** Translates raw CLI `nmap` scans into a centralized Apache web dashboard so non-CLI staff and helpdesk teams can check port availability in seconds.
* **Proactive Resource Monitoring:** Tracks CPU, RAM, disk, and network traffic on an auto-refreshing HTML status page to spot bottlenecks before downtime occurs.
* **Zero Licensing Cost:** Replaces costly commercial monitoring tools with native Linux utilities and open-source software.

---

## Project Modules

### 1. [File Integrity Monitor (`1-file-integrity-monitor/`)](1-file-integrity-monitor/)
* **Challenge:** Unauthorized users or compromised accounts can silently modify authentication files (`/etc/passwd`, `/etc/ssh/sshd_config`) to create backdoor access or weaken SSH security without triggering standard system errors.
* **Solution:** Automates cryptographic **SHA-256 baseline verification** every 15 minutes via `cron` ,logging file discrepancies to `/var/log/integrity.log`

### 2. [System Health Dashboard (`2-system-health-dashboard/`)](2-system-health-dashboard/)
* **Challenge:** Runaway background processes or full disk partitions can degrade server performance or cause unexpected downtime if resource spikes go unnoticed.
* **Solution:** Collects live **CPU, RAM, root disk (`/`), and network RX/TX metrics** every 5 minutes and renders a CSS-styled, auto-refreshing Apache status page (`/status.html`) for instant visual triage.

### 3. [Web-Based Network Scanner (`3-nmap-web-scanner/`)](3-nmap-web-scanner/)
* **Challenge:** Manually auditing subnets for rogue devices or unexpected open ports is time-consuming, and non-CLI staff lack an easy way to view current network exposure without SSH access.
* **Solution:** Automates scheduled `nmap` subnet sweeps via `cron` (`network_scan.sh`) and renders the latest host and port discovery results on a lightweight PHP/Apache dashboard (`network.php`) accessible from any browser.

---

## Security & Hardening Practices

* **Principle of Least Privilege (`chmod 700`):** Monitoring scripts are owned by `root:root` so regular users cannot view alert rules or tamper with checks.
* **Custom Group Isolation (`webedit`):** Web directories use `775` permissions (`root:webedit`) instead of insecure `777` permissions, allowing authorized admins to update files without giving write access to the Apache service account (`www-data`).
* **Credential Isolation:** SMTP relay passwords are stored strictly in `/etc/msmtprc` (`chmod 600`) and excluded from version control.

---

## Quick Deployment

1. **Install dependencies:** `apache2`, `php`, `nmap`, `msmtp`, `msmtp-mta`, and `mailutils`.
2. **Deploy scripts:** Copy `fim.sh` and `health.sh` to `/usr/local/bin/` with `chmod 700` ownership under `root`.
3. **Schedule automation:** Add cron jobs under `sudo crontab -e` to run `health.sh`,`network_scan.sh` every 5 minutes and `fim.sh` every 15 minutes.

---

## Screenshots

*(Add screenshots of your live `/status.html` dashboard and email alert here)*
