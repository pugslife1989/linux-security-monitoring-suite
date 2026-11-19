#!/bin/bash 


CPU=$(top -bn1 | awk '/^%Cpu/{print 100 - $8 "%"}')
RAM=$(free -m | awk '/^Mem:/{printf "%.2f%%\n", $3/$2*100}')
DISK=$(df -h / | awk 'NR==2 {print $5}')
NETWORK=$(awk '/enp0s3:/{printf "RX:%.2fMB | TX:%.2fMB\n", $2/1024/1024,$10/1024/1024}' /proc/net/dev)

TIMESTAMP=$(date "+%Y-%m-%d %H:%M:%S")

cat << EOF > /var/www/html/status.html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="30">
    <title>System Health Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px 20px; }
        .card { max-width: 700px; margin: auto; background: #1e293b; border-radius: 12px; padding: 28px; border: 1px solid #334155; box-shadow: 0 10px 25px rgba(0,0,0,0.4); }
        h1 { font-size: 22px; margin-bottom: 6px; color: #38bdf8; }
        .time { font-size: 13px; color: #94a3b8; margin-bottom: 24px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; }
        .metric { background: #0f172a; padding: 18px; border-radius: 8px; border-left: 4px solid #38bdf8; }
        .label { font-size: 12px; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.8px; }
        .value { font-size: 22px; font-weight: bold; margin-top: 6px; font-family: monospace; color: #f8fafc; }
    </style>
</head>
<body>
    <div class="card">
        <h1>System Health Report</h1>
        <div class="time">Last Updated: $TIMESTAMP</div>
        <div class="grid">
            <div class="metric">
                <div class="label">CPU Usage</div>
                <div class="value">$CPU</div>
            </div>
            <div class="metric">
                <div class="label">Memory Usage</div>
                <div class="value">$RAM</div>
            </div>
            <div class="metric">
                <div class="label">Disk Usage (/)</div>
                <div class="value">$DISK</div>
            </div>
            <div class="metric">
                <div class="label">Network Traffic (enp0s3)</div>
                <div class="value">$NETWORK</div>
            </div>
        </div>
    </div>
</body>
</html>
EOF
 
