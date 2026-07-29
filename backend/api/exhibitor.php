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

$companyName = trim($input['companyName'] ?? '');
$contactPerson = trim($input['contactPerson'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');
$category = trim($input['category'] ?? '');
$message = trim($input['message'] ?? '');

$allowedCategories = ['Teknologi', 'Kewangan', 'Runcit', 'Pembuatan', 'Perkhidmatan'];

// === Server-side validation ===
$errors = [];

if ($companyName === '') {
    $errors['companyName'] = 'Nama syarikat diperlukan.';
}

if ($contactPerson === '') {
    $errors['contactPerson'] = 'Nama pegawai dihubungi diperlukan.';
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

if ($category === '' || !in_array($category, $allowedCategories, true)) {
    $errors['category'] = 'Sila pilih kategori industri yang sah.';
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
        'INSERT INTO exhibitor_registrations
            (company_name, contact_person, email, phone, category, message, status, created_at)
         VALUES
            (:company_name, :contact_person, :email, :phone, :category, :message, "pending", NOW())'
    );

    $stmt->execute([
        'company_name' => $companyName,
        'contact_person' => $contactPerson,
        'email' => $email,
        'phone' => $phone,
        'category' => $category,
        'message' => $message
    ]);

    http_response_code(201);
    echo json_encode([
        'message' => 'Pendaftaran berjaya dihantar.',
        'id' => $pdo->lastInsertId()
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Ralat pelayan. Sila cuba lagi.']);
}