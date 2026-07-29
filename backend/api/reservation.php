<?php
require_once __DIR__ . '/../includes/cors.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method tidak dibenarkan.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['message' => 'Data tidak sah.']);
    exit();
}

$sessionId = isset($input['sessionId']) ? (int) $input['sessionId'] : 0;
$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');

// === Server-side validation ===
$errors = [];

if ($sessionId <= 0) {
    $errors['sessionId'] = 'Sesi tidak sah.';
}

if ($name === '') {
    $errors['name'] = 'Nama diperlukan.';
}

if ($email === '') {
    $errors['email'] = 'Email diperlukan.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Format email tidak sah.';
}

if ($phone === '') {
    $errors['phone'] = 'Nombor telefon diperlukan.';
} elseif (!preg_match('/^(\+?6?01)[0-46-9]-*[0-9]{7,8}$/', str_replace(' ', '', $phone))) {
    $errors['phone'] = 'Format nombor telefon tidak sah.';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'message' => 'Sila semak semula borang anda.',
        'errors' => $errors
    ]);
    exit();
}

// === Transaction: check capacity + insert reservation + update seats_taken ===
// Guna transaction supaya tiada race condition (dua orang reserve slot terakhir serentak)
try {
    $pdo->beginTransaction();

    // Lock row sesi ni untuk elak double-booking (FOR UPDATE)
    $stmt = $pdo->prepare(
        'SELECT capacity, seats_taken FROM career_talk_sessions WHERE id = :id FOR UPDATE'
    );
    $stmt->execute(['id' => $sessionId]);
    $session = $stmt->fetch();

    if (!$session) {
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode(['message' => 'Sesi tidak dijumpai.']);
        exit();
    }

    if ($session['seats_taken'] >= $session['capacity']) {
        $pdo->rollBack();
        http_response_code(409);
        echo json_encode(['message' => 'Maaf, sesi ini telah penuh.']);
        exit();
    }

    // Insert reservation
    $stmt = $pdo->prepare(
        'INSERT INTO reservations (session_id, name, email, phone, created_at)
         VALUES (:session_id, :name, :email, :phone, NOW())'
    );
    $stmt->execute([
        'session_id' => $sessionId,
        'name' => $name,
        'email' => $email,
        'phone' => $phone
    ]);

    // Increment seats_taken
    $stmt = $pdo->prepare(
        'UPDATE career_talk_sessions SET seats_taken = seats_taken + 1 WHERE id = :id'
    );
    $stmt->execute(['id' => $sessionId]);

    $pdo->commit();

    http_response_code(201);
    echo json_encode(['message' => 'Tempahan berjaya!']);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['message' => 'Ralat pelayan. Sila cuba lagi.']);
}