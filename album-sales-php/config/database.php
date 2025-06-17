<?php
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); 
define('DB_NAME', 'php_album_sales');

/**
 * Creates and returns a MySQLi database connection object.
 * Terminates script on connection failure.
 * @return mysqli
 */
function get_db_connection() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to handle special characters
    $conn->set_charset('utf8mb4');
    
    return $conn;
}
?>