<?php
require_once __DIR__ . '/../../includes/cors.php';
require_once __DIR__ . '/../../includes/prize_store.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['message' => 'Method tidak dibenarkan.']);
    exit();
}

$data = readPrizesFile();

if (!$data) {
    http_response_code(500);
    echo json_encode(['message' => 'Gagal memuatkan data hadiah.']);
    exit();
}

echo json_encode(['prizes' => $data['prizes']]);