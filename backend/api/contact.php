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

$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');
$message = trim($input['message'] ?? '');

// === Server-side validation ===
$errors = [];

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

if ($message === '') {
    $errors['message'] = 'Mesej diperlukan.';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'message' => 'Sila semak semula borang anda.',
        'errors' => $errors
    ]);
    exit();
}

// === Simpan ke database ===
try {
    $stmt = $pdo->prepare(
        'INSERT INTO contact_submissions (name, email, phone, message, created_at)
         VALUES (:name, :email, :phone, :message, NOW())'
    );

    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'message' => $message
    ]);

    http_response_code(201);
    echo json_encode([
        'message' => 'Mesej berjaya dihantar.',
        'id' => $pdo->lastInsertId()
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Ralat pelayan. Sila cuba lagi.']);
}