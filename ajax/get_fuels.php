<?php
require_once("../includes/db.php");
require_once("../includes/auth.php");
require_login();

$model_id = (int)($_GET['model_id'] ?? 0);
$company_id = (int)($_GET['company_id'] ?? 0);
$company_name = strtolower(trim($_GET['company_name'] ?? ''));
$model_name = strtolower(trim($_GET['model_name'] ?? ''));

if ($model_id > 0) {
    $stmt = $conn->prepare("SELECT m.name AS model_name, c.name AS company_name FROM models m LEFT JOIN companies c ON m.company_id = c.id WHERE m.id = ?");
    $stmt->bind_param("i", $model_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($row) {
        if (!empty($row['company_name'])) $company_name = strtolower(trim($row['company_name']));
        if (!empty($row['model_name'])) $model_name = strtolower(trim($row['model_name']));
    }
}

if ($company_id > 0 && empty($company_name)) {
    $stmt = $conn->prepare("SELECT name FROM companies WHERE id = ?");
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($row) {
        $company_name = strtolower(trim($row['name']));
    }
}

$combined = $company_name . ' ' . $model_name;
$is_ev = false;

// 1. Pure EV Manufacturers (Any vehicle under these companies is Electric EV)
$ev_companies = [
    'ather', 'ola', 'byd', 'ampere', 'okinawa', 'hero electric', 
    'pure ev', 'simple', 'revolt', 'bounce', 'kabira', 'tork', 
    'ultraviolette', 'matter', 'lectrix', 'komaki', 'gemopai'
];

foreach ($ev_companies as $ev_c) {
    if (strpos($company_name, $ev_c) !== false || strpos($combined, $ev_c) !== false) {
        $is_ev = true;
        break;
    }
}

// 2. EV Models (Specific EV models from multi-fuel manufacturers)
if (!$is_ev) {
    $ev_keywords = [
        'ev', 'electric', 'iqube', 'chetak', 'vida', 'ioniq', 'comet', 
        'windsor', 'xuv400', 'taycan', 'recharge', '450', 'rizta', 's1',
        'i4', 'ix', 'eqb', 'eqs', 'eqe'
    ];
    foreach ($ev_keywords as $kw) {
        if (preg_match('/\b' . preg_quote($kw, '/') . '\b/i', $combined) || strpos($combined, $kw) !== false) {
            $is_ev = true;
            break;
        }
    }
}

echo '<option value="">Select Fuel</option>';

if ($is_ev) {
    echo '<option value="Electric (EV)" data-id="3">Electric (EV)</option>';
} else {
    $res = $conn->query("SELECT * FROM fuels ORDER BY id");
    while ($row = $res->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['name']) . '" data-id="' . (int)$row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
    }
}
