<?php
require_once __DIR__ . '/../includes/cors.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['message' => 'Method tidak dibenarkan.']);
    exit();
}

try {
    $stmt = $pdo->query(
        'SELECT id, title, speaker, session_time, capacity, seats_taken,
                (capacity - seats_taken) AS seats_remaining
         FROM career_talk_sessions
         ORDER BY session_time ASC'
    );

    $sessions = $stmt->fetchAll();

    echo json_encode(['sessions' => $sessions]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Ralat pelayan. Sila cuba lagi.']);
}