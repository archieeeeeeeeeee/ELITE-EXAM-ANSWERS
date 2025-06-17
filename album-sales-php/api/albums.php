<?php
header("Content-Type: application/json");
require_once '../config/database.php';

/**
 * Sends a JSON response with a specific HTTP status code.
 *
 * @param mixed $data The data to encode as JSON.
 * @param int $code The HTTP status code to send.
 */
function json_response($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit();
}

/**
 * Authenticates the user via a Bearer token in the Authorization header.
 * Exits with a 401 Unauthorized error if authentication fails.
 */
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
    $result = $stmt->get_result();
    $stmt->close();
    
    if ($result->num_rows === 0) {
        $conn->close();
        json_response(['error' => 'Invalid or expired token.'], 401);
    }
    // Don't close the connection here, let the main script use it.
    return $conn;
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// The connection is established either by authenticate() or here if the method is GET.
$conn = ($method !== 'GET') ? authenticate() : get_db_connection();

switch ($method) {
    case 'GET':
        if ($id) {
            // Get a single album with its artist name
            $stmt = $conn->prepare("
                SELECT al.*, ar.name as artist_name 
                FROM albums al 
                JOIN artists ar ON al.artist_id = ar.id 
                WHERE al.id = ?
            ");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $album = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            json_response($album ?: ['error' => 'Album not found'], $album ? 200 : 404);
        } else {
            // Get all albums with their artist names
            $result = $conn->query("
                SELECT al.*, ar.name as artist_name 
                FROM albums al 
                JOIN artists ar ON al.artist_id = ar.id 
                ORDER BY ar.name, al.name
            ");
            json_response($result->fetch_all(MYSQLI_ASSOC));
        }
        break;

    case 'POST':
        // Create a new album
        $input = json_decode(file_get_contents('php://input'), true);

        // Basic validation
        if (empty($input['name']) || !isset($input['artist_id'], $input['year'], $input['sales'])) {
            json_response(['error' => 'Missing required fields: name, artist_id, year, sales.'], 400);
        }

        // Add a placeholder for the album cover if not provided
        $cover_path = $input['album_cover_path'] ?? "https://picsum.photos/seed/{$input['artist_id']}/400/400";
        
        $stmt = $conn->prepare("INSERT INTO albums (name, artist_id, year, sales, album_cover_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siiss", $input['name'], $input['artist_id'], $input['year'], $input['sales'], $cover_path);
        
        if ($stmt->execute()) {
            $new_id = $conn->insert_id;
            // Fetch the newly created record to return it
            $new_album_stmt = $conn->prepare("SELECT al.*, ar.name as artist_name FROM albums al JOIN artists ar ON al.artist_id = ar.id WHERE al.id = ?");
            $new_album_stmt->bind_param("i", $new_id);
            $new_album_stmt->execute();
            $new_album = $new_album_stmt->get_result()->fetch_assoc();
            json_response($new_album, 201);
        } else {
            json_response(['error' => 'Failed to create album. Check if artist_id exists.'], 500);
        }
        $stmt->close();
        break;

    case 'PUT':
    case 'PATCH':
        // Update an existing album
        if (!$id) json_response(['error' => 'Album ID is required for update.'], 400);
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) json_response(['error' => 'No update data provided.'], 400);

        $fields = [];
        $params = [];
        $types = '';

        if (isset($input['name'])) { $fields[] = 'name = ?'; $params[] = $input['name']; $types .= 's'; }
        if (isset($input['artist_id'])) { $fields[] = 'artist_id = ?'; $params[] = $input['artist_id']; $types .= 'i'; }
        if (isset($input['year'])) { $fields[] = 'year = ?'; $params[] = $input['year']; $types .= 'i'; }
        if (isset($input['sales'])) { $fields[] = 'sales = ?'; $params[] = $input['sales']; $types .= 'i'; }
        if (isset($input['album_cover_path'])) { $fields[] = 'album_cover_path = ?'; $params[] = $input['album_cover_path']; $types .= 's'; }

        if (empty($fields)) json_response(['error' => 'No valid fields to update.'], 400);

        $sql = "UPDATE albums SET " . implode(', ', $fields) . " WHERE id = ?";
        $params[] = $id;
        $types .= 'i';

        $stmt = $conn->prepare($sql);
        // Use the splat operator (...) for dynamic binding in PHP 5.6+
        $stmt->bind_param($types, ...$params); 
        
        if ($stmt->execute()) {
            // Fetch the updated record to return it
            $updated_album_stmt = $conn->prepare("SELECT al.*, ar.name as artist_name FROM albums al JOIN artists ar ON al.artist_id = ar.id WHERE al.id = ?");
            $updated_album_stmt->bind_param("i", $id);
            $updated_album_stmt->execute();
            $updated_album = $updated_album_stmt->get_result()->fetch_assoc();
            json_response($updated_album, 200);
        } else {
             json_response(['error' => 'Failed to update album.'], 500);
        }
        $stmt->close();
        break;

    case 'DELETE':
        // Delete an album
        if (!$id) json_response(['error' => 'Album ID is required for deletion.'], 400);
        
        $stmt = $conn->prepare("DELETE FROM albums WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            json_response(null, 204); // No Content
        } else {
            json_response(['error' => 'Failed to delete album.'], 500);
        }
        $stmt->close();
        break;

    default:
        // Method not allowed
        header('Allow: GET, POST, PUT, PATCH, DELETE');
        json_response(['error' => 'Method not allowed'], 405);
        break;
}

$conn->close();