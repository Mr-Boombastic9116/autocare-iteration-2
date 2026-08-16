<?php
/**
 * Session + auth helpers shared by every logged-in page.
 * Include this AFTER includes/db.php (it calls session_start()).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login() {
    if (!isset($_SESSION['user'])) {
        header("Location: index.php");
        exit();
    }
}

function current_username() {
    return $_SESSION['user'] ?? null;
}

function current_display_name() {
    return $_SESSION['name'] ?? ($_SESSION['user'] ?? 'User');
}

/**
 * Fetch a vehicle by id but ONLY if it belongs to the given username.
 * Returns null if it doesn't exist or isn't owned by this user.
 * Prevents the "?id=someone_else_vehicle" ownership bypass.
 */
function get_owned_vehicle($conn, $vehicle_id, $username) {
    $stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ? AND user = ?");
    $stmt->bind_param("is", $vehicle_id, $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $vehicle = $res->fetch_assoc();
    $stmt->close();
    return $vehicle ?: null;
}

/**
 * Fetch a booking by id but ONLY if it belongs to the given username.
 */
function get_owned_booking($conn, $booking_id, $username) {
    $stmt = $conn->prepare("
        SELECT b.*, v.company, v.model, v.license_no
        FROM bookings b
        LEFT JOIN vehicles v ON b.vehicle_id = v.id
        WHERE b.id = ? AND b.user = ?
    ");
    $stmt->bind_param("is", $booking_id, $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $booking = $res->fetch_assoc();
    $stmt->close();
    return $booking ?: null;
}
