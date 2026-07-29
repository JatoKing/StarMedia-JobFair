<?php
require_once __DIR__ . '/includes/cors.php';
echo json_encode(['message' => 'StarMedia Job Fair Backend PHP jalan!', 'status' => 'ok']);
EOF'
cat > index.php << 'EOF'
<?php
require_once __DIR__ . '/includes/cors.php';
echo json_encode(['message' => 'StarMedia Job Fair Backend PHP jalan!', 'status' => 'ok']);
