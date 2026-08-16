<?php
/**
 * Single source of truth for bookable services and their prices.
 * Used by book_service.php (to render the checklist) AND
 * ajax/save_booking.php (to recompute the total server-side so a
 * tampered client-side total can never be trusted).
 */
$SERVICE_BASE_CHARGE = 1500; // "Service Charge" line, always included
$ADVANCE_AMOUNT = 500;

$AUTOCARE_SERVICES = [
    "Engine oil change"          => 2500,
    "Oil filter replacement"     => 500,
    "Air filter replacement"     => 800,
    "Cabin filter replacement"   => 1000,
    "Fuel filter replacement"    => 1800,
    "Brake pad (front)"          => 3000,
    "Brake pad (rear)"           => 2800,
    "Brake disc"                 => 4000,
    "Wheel alignment"            => 500,
    "Wheel balancing"            => 400,
    "Tyre rotation"              => 500,
    "Tyre replacement"           => 7000,
    "Battery replacement"        => 6000,
    "Spark plug replacement"     => 1500,
    "Coolant replacement"        => 1200,
    "Brake fluid replacement"    => 1200,
    "Transmission fluid"         => 3500,
    "Clutch replacement"         => 9000,
    "Suspension repair"          => 7000,
    "AC gas refill"              => 2500,
    "AC servicing"               => 2000,
    "Engine tuning"              => 2500,
    "Throttle cleaning"          => 1500,
    "Injector cleaning"          => 2500,
    "Radiator flushing"          => 2000,
    "Timing belt replacement"    => 7000,
    "Alternator repair"          => 5000,
    "Starter motor repair"       => 3000,
    "Headlight bulb replacement" => 500,
];

/**
 * The service center only has one bay in this demo, so slots are
 * global per date rather than per-vehicle. Stored/compared in 24hr
 * "HH:MM-HH:MM" form; displayed to users in 12hr form.
 */
$AUTOCARE_TIME_SLOTS = [
    "09:00-10:00",
    "10:00-11:00",
    "11:00-12:00",
    "12:00-13:00",
    "13:00-14:00",
    "14:00-15:00",
];

function format_time_slot_label($slot) {
    // "09:00-10:00" -> "9:00 AM - 10:00 AM"
    $parts = explode("-", $slot);
    if (count($parts) !== 2) return $slot;
    $start = date("g:i A", strtotime($parts[0]));
    $end   = date("g:i A", strtotime($parts[1]));
    return "$start - $end";
}
