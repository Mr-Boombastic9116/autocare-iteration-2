<?php
require_once("../includes/db.php");
require_once("../includes/auth.php");
require_once("../includes/services_data.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "msg" => "Not logged in"]);
    exit();
}

$user = current_username();

$vehicle_id = isset($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : 0;
$date       = $_POST['date'] ?? '';
$time       = trim($_POST['time'] ?? '');
$services   = trim($_POST['services'] ?? '');
$notes      = trim($_POST['notes'] ?? '');

// 1) The vehicle must actually belong to this user - never trust a client-sent id blindly.
$vehicle = get_owned_vehicle($conn, $vehicle_id, $user);
if (!$vehicle) {
    echo json_encode(["status" => "error", "msg" => "Invalid vehicle."]);
    exit();
}

// 2) Validate the date (format + not in the past).
$d = DateTime::createFromFormat('Y-m-d', $date);
if (!$d || $d->format('Y-m-d') !== $date) {
    echo json_encode(["status" => "error", "msg" => "Invalid date."]);
    exit();
}
$today = new DateTime('today');
if ($d < $today) {
    echo json_encode(["status" => "error", "msg" => "You cannot book a service for a past date."]);
    exit();
}

// 3) Validate the time slot against the known slot list (never trust arbitrary input).
if (!in_array($time, $AUTOCARE_TIME_SLOTS, true)) {
    echo json_encode(["status" => "error", "msg" => "Invalid time slot."]);
    exit();
}

// 4) Recompute the total server-side from the known price list — never trust a client total.
$selected_names = array_filter(array_map('trim', explode(",", $services)));
$total = $SERVICE_BASE_CHARGE;
$valid_names = [];
foreach ($selected_names as $name) {
    if (isset($AUTOCARE_SERVICES[$name])) {
        $total += $AUTOCARE_SERVICES[$name];
        $valid_names[] = $name;
    }
}
$services_clean = implode(", ", $valid_names);
$advance = $ADVANCE_AMOUNT;
$status  = "Confirmed";

// 5) Final server-side availability check right before inserting, inside a transaction,
//    so two people can't grab the same slot at the same instant.
$conn->begin_transaction();

try {
    $check = $conn->prepare("SELECT id FROM bookings WHERE service_date = ? AND time_slot = ? AND status <> 'Cancelled' FOR UPDATE");
    $check->bind_param("ss", $date, $time);
    $check->execute();
    $existing = $check->get_result();

    if ($existing->num_rows > 0) {
        $conn->rollback();
        echo json_encode(["status" => "error", "msg" => "That time slot was just booked. Please choose another."]);
        exit();
    }
    $check->close();

    $stmt = $conn->prepare("INSERT INTO bookings
        (user, vehicle_id, service_date, time_slot, services, special_request, total, advance, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sissssiis",
        $user, $vehicle_id, $date, $time, $services_clean, $notes, $total, $advance, $status
    );
    $stmt->execute();
    $booking_id = $conn->insert_id;
    $stmt->close();

    $conn->commit();

    echo json_encode([
        "status" => "success",
        "booking_id" => $booking_id
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Booking failed: " . $e->getMessage());
    echo json_encode(["status" => "error", "msg" => "Something went wrong. Please try again."]);
}
