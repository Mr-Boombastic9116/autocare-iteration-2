<?php
require_once("../includes/db.php");
require_once("../includes/auth.php");
require_once("../includes/services_data.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "msg" => "Not logged in"]);
    exit();
}

$date = $_GET['date'] ?? '';

// Validate date format and disallow past dates
$d = DateTime::createFromFormat('Y-m-d', $date);
$valid = $d && $d->format('Y-m-d') === $date;

if (!$valid) {
    echo json_encode(["status" => "error", "msg" => "Invalid date"]);
    exit();
}

$today = new DateTime('today');
if ($d < $today) {
    echo json_encode(["status" => "error", "msg" => "Cannot check availability for a past date"]);
    exit();
}

$stmt = $conn->prepare("SELECT time_slot FROM bookings WHERE service_date = ? AND status <> 'Cancelled'");
$stmt->bind_param("s", $date);
$stmt->execute();
$res = $stmt->get_result();

$booked = [];
while ($row = $res->fetch_assoc()) {
    $booked[] = trim($row['time_slot']);
}
$stmt->close();

// Always include predefined unavailable slots for demonstration so red slot styling is visible
$demo_booked = ["10:00-11:00", "14:00-15:00", "10:00 AM", "02:00 PM"];
$booked = array_values(array_unique(array_merge($booked, $demo_booked)));

echo json_encode(["status" => "success", "booked" => $booked]);
