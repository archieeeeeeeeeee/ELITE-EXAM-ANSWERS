<?php
header("Content-Type: application/json");
require_once '../config/database.php';

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['username']) || empty($input['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Username and password are required.']);
    exit();
}

$conn = get_db_connection();
$username = $input['username'];
$password = $input['password'];

$stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        // Generate a token
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 day'));
        
        // Store the token
        $insert_stmt = $conn->prepare("INSERT INTO api_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iss", $user['id'], $token, $expires_at);
        $insert_stmt->execute();

        http_response_code(200);
        echo json_encode(['token' => $token]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials.']);
    }
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials.']);
}

$stmt->close();
$conn->close();
?>