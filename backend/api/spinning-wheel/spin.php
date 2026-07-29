<?php
require_once __DIR__ . '/../../includes/cors.php';
require_once __DIR__ . '/../../includes/prize_store.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method tidak dibenarkan.']);
    exit();
}

[$handle, $data] = openPrizesFileForUpdate();

if (!$handle || !$data) {
    http_response_code(500);
    echo json_encode(['message' => 'Gagal memuatkan data hadiah.']);
    exit();
}

$prizes = $data['prizes'];

// Hanya pertimbangkan prize yang masih ada baki (remaining > 0)
$available = array_values(array_filter($prizes, function ($p) {
    return $p['remaining'] > 0;
}));

if (empty($available)) {
    flock($handle, LOCK_UN);
    fclose($handle);
    http_response_code(409);
    echo json_encode(['message' => 'Maaf, semua hadiah telah habis.']);
    exit();
}

// === Weighted random selection ikut 'probability', dinormalisasi antara prize yang masih ada ===
$totalWeight = array_sum(array_column($available, 'probability'));
$random = mt_rand() / mt_getrandmax() * $totalWeight;

$cumulative = 0;
$winner = null;

foreach ($available as $prize) {
    $cumulative += $prize['probability'];
    if ($random <= $cumulative) {
        $winner = $prize;
        break;
    }
}

// Fallback (jarang berlaku, floating point edge case) — pilih prize terakhir yang available
if (!$winner) {
    $winner = end($available);
}

// Kurangkan 'remaining' untuk prize yang menang, dalam array asal $prizes
foreach ($prizes as &$p) {
    if ($p['id'] === $winner['id']) {
        $p['remaining'] -= 1;
        $winner['remaining'] = $p['remaining']; // update baki terkini untuk response
        break;
    }
}
unset($p);

$data['prizes'] = $prizes;
writeAndClosePrizesFile($handle, $data);

echo json_encode([
    'prize' => [
        'id' => $winner['id'],
        'name' => $winner['name'],
        'type' => $winner['type'],
        'graphic' => $winner['graphic'],
        'remaining' => $winner['remaining']
    ]
]);