<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- Create Database ---
$servername = "127.0.0.1";
$username = "root";
$password = ""; // Your database password
$dbname = "php_album_sales";

$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);
echo "Database '$dbname' is ready.<br>";

// --- Create Tables ---
$sql_users = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";

$sql_artists = "
CREATE TABLE IF NOT EXISTS artists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";

$sql_albums = "
CREATE TABLE IF NOT EXISTS albums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artist_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    year YEAR NOT NULL,
    sales BIGINT UNSIGNED NOT NULL,
    album_cover_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE
);";

// Table for API tokens
$sql_api_tokens = "
CREATE TABLE IF NOT EXISTS api_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);";

$conn->query($sql_users) or die("Error creating users table: " . $conn->error);
$conn->query($sql_artists) or die("Error creating artists table: " . $conn->error);
$conn->query($sql_albums) or die("Error creating albums table: " . $conn->error);
$conn->query($sql_api_tokens) or die("Error creating api_tokens table: " . $conn->error);

echo "All tables created successfully.<br>";

// --- Seed Admin User ---
$admin_pass_hash = password_hash('password', PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (name, username, password) VALUES ('Admin User', 'admin', ?) ON DUPLICATE KEY UPDATE name='Admin User'");
$stmt->bind_param("s", $admin_pass_hash);
$stmt->execute();
$stmt->close();
echo "Admin user created (user: admin, pass: password).<br>";


// --- Seed from CSV ---
$csvPath = __DIR__ . '/data/Data Reference (ALBUM SALES).csv';
if (!file_exists($csvPath)) {
    die("CSV file not found at: " . $csvPath);
}

// Clear existing artist/album data to prevent duplicates on re-run
$conn->query("DELETE FROM albums");
$conn->query("DELETE FROM artists");
echo "Cleared existing album/artist data.<br>";

$file = fopen($csvPath, 'r');
fgetcsv($file); // Skip header

$processedAlbums = [];

while (($row = fgetcsv($file)) !== FALSE) {
    // Handle potential BOM issue on first line
    $artistName = trim(str_replace("\xEF\xBB\xBF", '', $row[0]));
    $albumName = trim($row[1]);
    $sales = (int)$row[2];
    $releaseDate = trim($row[3]); // YYMMDD format

    $albumKey = strtolower($artistName . '-' . $albumName);

    if (isset($processedAlbums[$albumKey])) {
        $processedAlbums[$albumKey]['sales'] += $sales;
    } else {
        $year = '20' . substr($releaseDate, 0, 2);
        $processedAlbums[$albumKey] = [
            'artist_name' => $artistName,
            'album_name' => $albumName,
            'sales' => $sales,
            'year' => (int)$year,
        ];
    }
}
fclose($file);

$artist_stmt = $conn->prepare("INSERT INTO artists (name) VALUES (?) ON DUPLICATE KEY UPDATE name=name");
$album_stmt = $conn->prepare("INSERT INTO albums (artist_id, name, year, sales, album_cover_path) VALUES (?, ?, ?, ?, ?)");
$artist_id_map = [];

foreach ($processedAlbums as $data) {
    if (!isset($artist_id_map[$data['artist_name']])) {
        $artist_stmt->bind_param("s", $data['artist_name']);
        $artist_stmt->execute();
        $artist_id_map[$data['artist_name']] = $conn->insert_id > 0 ? $conn->insert_id : $conn->query("SELECT id FROM artists WHERE name = '" . $conn->real_escape_string($data['artist_name']) . "'")->fetch_assoc()['id'];
    }
    
    $artist_id = $artist_id_map[$data['artist_name']];
    $cover_path = "https://picsum.photos/seed/{$artist_id}/400/400"; // Placeholder image
    
    $album_stmt->bind_param("isiss", $artist_id, $data['album_name'], $data['year'], $data['sales'], $cover_path);
    $album_stmt->execute();
}

$artist_stmt->close();
$album_stmt->close();
$conn->close();

echo "Seeding completed successfully! You can now delete this file.";
?>