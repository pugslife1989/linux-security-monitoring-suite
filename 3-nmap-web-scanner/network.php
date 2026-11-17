<?php
$xmlFile = '/var/www/html/output.xml';

if (!file_exists($xmlFile)) {
    die("Scan report not found. Run: sudo nmap -sV -oX /var/www/html/output.xml 192.168.0.1/24");
}

$xml = simplexml_load_file($xmlFile);

// Commonly risky ports and service names
$riskyPorts = array(21, 23, 25, 80, 139, 445, 3389, 5900);
$riskyServices = array('ftp', 'telnet', 'http', 'netbios-ssn', 'microsoft-ds', 'ms-wbt-server', 'vnc');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Network Scan Report</title>
<style>
    @import url('https://googleapis.com');

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        background: radial-gradient(circle at top, #1e293b 0%, #090d16 100%);
        color: #e2e8f0;
        min-height: 100vh;
        padding: 40px 20px;
    }
    .container {
        max-width: 1000px;
        margin: 0 auto;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(56, 189, 248, 0.2);
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
        overflow: hidden;
    }

    /* Top Header Bar */
    .top-bar {
        padding: 24px 32px;
        background: linear-gradient(90deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.9) 100%);
        border-bottom: 1px solid rgba(148, 163, 184, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    .title-group h1 {
        font-size: 22px;
        font-weight: 700;
        color: #f8fafc;
        letter-spacing: 0.4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .subtitle { font-size: 13px; color: #94a3b8; margin-top: 4px; }

    /* Live Pulsing Status Pill */
    .live-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.35);
        color: #34d399;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .pulse-dot {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        box-shadow: 0 0 8px #10b981;
        animation: pulse 1.8s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(0.95); opacity: 0.9; }
        50% { transform: scale(1.25); opacity: 0.4; }
        100% { transform: scale(0.95); opacity: 0.9; }
    }

    /* KPI Summary Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        padding: 24px 32px;
        background: rgba(9, 13, 22, 0.4);
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
    }
    .kpi-card {
        background: rgba(30, 41, 59, 0.55);
        border: 1px solid rgba(148, 163, 184, 0.15);
        border-radius: 12px;
        padding: 16px 20px;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }
    .kpi-card:hover { transform: translateY(-2px); border-color: rgba(56, 189, 248, 0.4); }
    .kpi-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; font-weight: 600; }
    .kpi-number { font-size: 26px; font-weight: 700; color: #38bdf8; margin-top: 6px; font-family: 'JetBrains Mono', monospace; }
    .kpi-card.alert .kpi-number { color: #f87171; text-shadow: 0 0 15px rgba(248, 113, 113, 0.3); }

    /* Search & Filter Toolbar */
    .toolbar {
        padding: 16px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        background: rgba(15, 23, 42, 0.6);
    }
    .search-input {
        width: 100%;
        max-width: 340px;
        background: #090d16;
        border: 1px solid rgba(148, 163, 184, 0.25);
        color: #f8fafc;
        padding: 9px 14px;
        border-radius: 8px;
        font-size: 13px;
        outline: none;
        transition: border-color 0.2s;
    }
    .search-input:focus { border-color: #38bdf8; }

    /* Data Table */
    table { width: 100%; border-collapse: collapse; }
    th {
        background: rgba(9, 13, 22, 0.75);
        color: #94a3b8;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        text-align: left;
        padding: 14px 32px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.15);
    }
    td {
        padding: 14px 32px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.08);
        font-size: 14px;
        color: #cbd5e1;
    }
    tr { transition: background 0.15s ease; }
    tr:hover { background: rgba(56, 189, 248, 0.06); }

    /* Left Neon Border Accent for High Risk Rows */
    tr.row-high {
        background: rgba(239, 68, 68, 0.07);
        border-left: 3px solid #ef4444;
    }
    tr.row-high:hover { background: rgba(239, 68, 68, 0.13); }

    .mono { font-family: 'JetBrains Mono', monospace; font-weight: 500; color: #f8fafc; }

    /* Glowing Risk Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-high {
        background: rgba(239, 68, 68, 0.15);
        color: #fca5a5;
        border: 1px solid rgba(239, 68, 68, 0.4);
        box-shadow: 0 0 12px rgba(239, 68, 68, 0.15);
    }
    .badge-normal {
        background: rgba(56, 189, 248, 0.12);
        color: #7dd3fc;
        border: 1px solid rgba(56, 189, 248, 0.3);
    }
    .badge-low {
        background: rgba(16, 185, 129, 0.12);
        color: #6ee7b7;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
</style>

<script>
// Live table filter for video demos
function filterTable() {
    const query = document.getElementById("tableSearch").value.toLowerCase();
    const rows = document.querySelectorAll("tbody tr");
    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(query) ? "" : "none";
    });
}
</script>

</head>
<body>

<h2>Network Scan Report</h2>
<p><strong>Last Updated:</strong> <?= htmlspecialchars((string)$xml['startstr']) ?></p>

<table>
    <thead>
        <tr>
            <th>Host</th>
            <th>Open Ports</th>
            <th>Detected Service</th>
            <th>Risk Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($xml->xpath('//host[status/@state="up"]') as $host): ?>
            <?php 
                $ip = (string)$host->address['addr'];
                $openPorts = $host->xpath('ports/port[state/@state="open"]');
            ?>
            <?php if (empty($openPorts)): ?>
                <tr class="normal">
                    <td><?= htmlspecialchars($ip) ?></td>
                    <td>None</td>
                    <td>Host is up (no open ports)</td>
                    <td>Low</td>
                </tr>
            <?php else: ?>
                <?php foreach ($openPorts as $p): ?>
                    <?php 
                        $portNum = (int)$p['portid'];
                        $portProto = $portNum . '/' . $p['protocol'];
                        $svcName = strtolower((string)($p->service['name'] ?? ''));
                        $fullSvc = trim("{$p->service['name']} {$p->service['product']} {$p->service['version']}");
                        $isRisky = in_array($portNum, $riskyPorts) || in_array($svcName, $riskyServices);
                    ?>
                    <tr class="<?= $isRisky ? 'high-risk' : 'normal' ?>">
                        <td><?= htmlspecialchars($ip) ?></td>
                        <td><?= htmlspecialchars($portProto) ?></td>
                        <td><?= htmlspecialchars($fullSvc) ?></td>
                        <td><?= $isRisky ? '⚠️ High Risk' : 'Normal' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
