<?php
/**
 * Database connection.
 * Keeps the original XAMPP-friendly credentials (localhost / root / no password / autocare)
 * as required — do not change unless the environment truly changes.
 */
$conn = new mysqli("localhost", "root", "", "autocare");

if ($conn->connect_error) {
    // Never leak raw DB errors to the browser.
    error_log("DB connection failed: " . $conn->connect_error);
    die("Something went wrong. Please try again later.");
}

$conn->set_charset("utf8mb4");
?>
