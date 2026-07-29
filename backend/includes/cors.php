<?php
// Benarkan request dari Vite dev server (localhost:5173)
// TODO: tukar ke domain production sebenar bila deploy
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Preflight request (browser hantar OPTIONS dulu sebelum POST sebenar)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}