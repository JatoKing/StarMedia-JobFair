<?php
require_once __DIR__ . '/../includes/cors.php';
require_once __DIR__ . '/../config/gemini.php';

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

$userMessage = trim($input['message'] ?? '');
$history = $input['history'] ?? [];

if ($userMessage === '') {
    http_response_code(422);
    echo json_encode(['message' => 'Mesej tidak boleh kosong.']);
    exit();
}

// === System instruction: konteks Job Fair ===
$systemInstruction = <<<EOT
Anda ialah pembantu maya rasmi untuk "Job Fair 2026", sebuah pameran kerjaya yang dianjurkan
untuk menghubungkan bakal pekerja dengan syarikat-syarikat terkemuka.

Maklumat penting tentang acara ini:
- Terdapat lebih 50+ syarikat exhibitor dari pelbagai industri (Teknologi, Kewangan, Runcit, Pembuatan, Perkhidmatan)
- Pengunjung boleh melawat direktori exhibitor, melihat floor plan, dan menempah slot sesi Job Matching/Career Talk
- Terdapat borang "Jadi Exhibitor" untuk syarikat yang berminat mengambil bahagian
- Terdapat borang "Hubungi Kami" untuk sebarang pertanyaan am
- Laman web menyokong Bahasa Melayu dan English

Tugas anda: jawab soalan pengunjung dengan mesra, ringkas (2-4 ayat), dan membantu.
Jika soalan di luar konteks job fair, arahkan pengunjung dengan sopan untuk guna borang "Hubungi Kami".
Jawab dalam bahasa yang sama seperti soalan pengunjung (Bahasa Melayu atau English).
EOT;

// === Bina array 'contents' ikut format Gemini ===
// Gemini guna role 'user' dan 'model' (bukan 'assistant' macam OpenAI)
$contents = [];

$recentHistory = array_slice($history, -10);
foreach ($recentHistory as $entry) {
    if (isset($entry['role']) && isset($entry['content'])) {
        $geminiRole = $entry['role'] === 'assistant' ? 'model' : 'user';
        $contents[] = [
            'role' => $geminiRole,
            'parts' => [['text' => $entry['content']]]
        ];
    }
}

$contents[] = [
    'role' => 'user',
    'parts' => [['text' => $userMessage]]
];

// === Payload ikut format Gemini API ===
$payload = json_encode([
    'contents' => $contents,
    'systemInstruction' => [
        'parts' => [['text' => $systemInstruction]]
    ],
    'generationConfig' => [
        'maxOutputTokens' => 300,
        'temperature' => 0.7
    ]
]);

$url = 'https://generativelanguage.googleapis.com/v1beta/models/' . GEMINI_MODEL . ':generateContent?key=' . GEMINI_API_KEY;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    http_response_code(500);
    echo json_encode(['message' => 'Ralat menghubungi perkhidmatan chatbot.']);
    exit();
}

$responseData = json_decode($response, true);

if ($httpCode !== 200 || !isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
    http_response_code(500);
    echo json_encode([
        'message' => 'Chatbot tidak dapat memproses permintaan buat masa ini.',
        'debug' => $responseData['error']['message'] ?? 'Ralat tidak diketahui'
    ]);
    exit();
}

$botReply = trim($responseData['candidates'][0]['content']['parts'][0]['text']);

echo json_encode([
    'reply' => $botReply
]);