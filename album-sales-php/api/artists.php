<?php
header("Content-Type: application/json");
require_once '../config/database.php';

// Helper function for sending JSON response
function json_response($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit();
}

// Helper function for API auth
function authenticate() {
    if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
        json_response(['error' => 'Authorization header not found.'], 401);
    }
    $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
    if (sscanf($auth_header, 'Bearer %s', $token) !== 1) {
        json_response(['error' => 'Malformed token.'], 401);
    }
    
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT user_id FROM api_tokens WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        $conn->close();
        json_response(['error' => 'Invalid or expired token.'], 401);
    }
    $conn->close();
}

$method = $_SERVER['REQUEST_METHOD'];
$conn = get_db_connection();
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $conn->prepare("SELECT * FROM artists WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $artist = $stmt->get_result()->fetch_assoc();
            json_response($artist ?: ['error' => 'Artist not found'], $artist ? 200 : 404);
        } else {
            $result = $conn->query("SELECT * FROM artists ORDER BY name");
            json_response($result->fetch_all(MYSQLI_ASSOC));
        }
        break;

    case 'POST':
        authenticate();
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['name'])) {
            json_response(['error' => 'Name is required.'], 400);
        }
        $stmt = $conn->prepare("INSERT INTO artists (name) VALUES (?)");
        $stmt->bind_param("s", $input['name']);
        $stmt->execute();
        $new_id = $conn->insert_id;
        $new_artist = $conn->query("SELECT * FROM artists WHERE id = $new_id")->fetch_assoc();
        json_response($new_artist, 201);
        break;

    case 'PUT':
    case 'PATCH':
        authenticate();
        if (!$id) json_response(['error' => 'ID is required.'], 400);
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['name'])) {
            json_response(['error' => 'Name is required.'], 400);
        }
        $stmt = $conn->prepare("UPDATE artists SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $input['name'], $id);
        $stmt->execute();
        $updated_artist = $conn->query("SELECT * FROM artists WHERE id = $id")->fetch_assoc();
        json_response($updated_artist);
        break;

    case 'DELETE':
        authenticate();
        if (!$id) json_response(['error' => 'ID is required.'], 400);
        $stmt = $conn->prepare("DELETE FROM artists WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        json_response(null, 204);
        break;

    default:
        json_response(['error' => 'Method not allowed'], 405);
        break;
}

$conn->close();
?>