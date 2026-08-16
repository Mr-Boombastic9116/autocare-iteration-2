<?php
require_once("includes/db.php");
require_once("includes/auth.php");
require_once("includes/services_data.php");
require_once("includes/vehicle_image.php");
require_login();

$user = current_username();
$ACTIVE_NAV = 'vehicles';

// The vehicle being booked MUST be selected and MUST belong to this user.
$vehicle_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$vehicle = get_owned_vehicle($conn, $vehicle_id, $user);

if (!$vehicle) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head><meta charset="UTF-8"><title>Vehicle Not Found | AutoCare</title><link rel="stylesheet" href="assets/css/style.css"></head>
    <body>
    <?php include("includes/header_app.php"); ?>
    <div class="empty-state" style="margin-top:80px;">
        <h3>Select a vehicle first.</h3>
        <p>Please choose which vehicle you'd like to book a service for.</p>
        <a href="vehicles.php" class="primary-btn">Back to My Vehicles</a>
    </div>
    </body>
    </html>
    <?php
    exit();
}

$today_str = (new DateTime())->format("Y-m-d");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Service | AutoCare</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<?php include("includes/header_app.php"); ?>

<div class="service-container">

<!-- LEFT: Service center info -->
<div class="left-panel">

    <h1 class="section-title">Nearest Service Center</h1>
    <br>
    <h4 class="center-name">Alcon Hyundai - Margao</h4>

    <img src="assets/images/service_center.png" class="service-img" alt="Service center">

    <div class="center-details">
        <p>Authorized Hyundai service center with certified technicians and modern equipment. This is the featured demo service center for this build.</p>
    </div>

    <div class="contact-info">
        <h4 class="location-title">Contact Information</h4>
        <div class="contact-row"><span>Phone:</span><span>+91 98765 43210</span></div>
        <div class="contact-row"><span>Landline:</span><span>0832 276 8890</span></div>
        <div class="contact-row"><span>Email:</span><span>service@alconhyundai.com</span></div>
    </div>
    <br>

    <div class="ratings-box">
        <div class="ratings-overall">
            Overall Rating: <span>4.5 / 5</span>
        </div>

        <div class="rating-grid">
            <div class="rating-box"><div class="rating-title">Service Quality</div><div class="rating-value">4.6 / 5</div></div>
            <div class="rating-box"><div class="rating-title">Staff Behavior</div><div class="rating-value">4.4 / 5</div></div>
            <div class="rating-box"><div class="rating-title">Timeliness</div><div class="rating-value">4.3 / 5</div></div>
            <div class="rating-box"><div class="rating-title">Value for Money</div><div class="rating-value">4.5 / 5</div></div>
        </div>
    </div>
    <br>
    <hr class="divider-line">

    <h4 class="location-title">Location</h4>
    <img src="assets/images/map.png" class="map-img" alt="Map">
    <p class="address">Alcon Hyundai Service Hub,<br>Margao, Goa - 403720</p>

</div>

<!-- MIDDLE: booking flow -->
<div class="middle-panel">

    <?php
    $vehicle_img = get_vehicle_image($vehicle['company'], $vehicle['model'], $vehicle['fuel'] ?? '');
    ?>
    <div class="booking-vehicle-banner" style="display: flex; align-items: center; gap: 14px;">
        <img src="assets/images/<?= htmlspecialchars($vehicle_img) ?>" alt="" style="width: 52px; height: 36px; object-fit: contain; border-radius: 6px; background: rgba(0,0,0,0.05); padding: 2px;">
        <div>
            Booking service for
            <strong><?= htmlspecialchars($vehicle['company'] . ' ' . $vehicle['model']) ?></strong>
            &middot; <?= htmlspecialchars($vehicle['license_no']) ?>
        </div>
    </div>

    <h3>Select Date</h3>
    <input type="date" id="service-date" min="">

    <div id="time-section" style="display:none;">
        <h3>Select Time Slot</h3>
        <div class="time-slots" id="time-slots">
            <p id="slots-loading" class="slots-loading">Checking availability…</p>
        </div>

        <div class="slot-legend">
            <span class="legend available"></span>Available
            <span class="legend booked"></span>Unavailable
            <span class="legend selected"></span>Selected
        </div>
    </div>

    <div id="service-section" style="display:none;">
        <h3>Service Options</h3>

        <div class="services-list">
            <?php foreach ($AUTOCARE_SERVICES as $name => $price) { ?>
            <label class="service-item">
                <div class="left">
                    <input type="checkbox" value="<?= (int)$price ?>" data-name="<?= htmlspecialchars($name) ?>">
                    <span class="service-name"><?= htmlspecialchars($name) ?></span>
                </div>
                <span class="service-price">+₹<?= number_format($price) ?></span>
            </label>
            <?php } ?>
        </div>

        <textarea id="specialBox" placeholder="Special Requests (optional)"></textarea>
    </div>

</div>

<!-- RIGHT: summary -->
<div class="right-panel">

    <h3>Booking Summary</h3>

    <div id="costDetails" class="cost-details">
        <div class="row"><span>Car Wash</span><span>FREE</span></div>
        <div class="row"><span>Service Charge</span><span>₹<?= number_format($SERVICE_BASE_CHARGE) ?></span></div>
    </div>

    <h2>Estimated Total: ₹<span id="total"><?= (int)$SERVICE_BASE_CHARGE ?></span></h2>

    <p>Advance: ₹<?= (int)$ADVANCE_AMOUNT ?> *</p>

    <button class="book-btn" id="bookBtn">Book Service</button>
    <br><br>
    <small>* adjusted in final bill.</small>
</div>

</div>

<!-- REDIRECT OVERLAY -->
<div id="redirect-overlay">
    <div class="redirect-box">
        <img src="assets/images/rupee_icon.png" class="redirect-icon" alt="Rupee Coin">
        <p id="redirect-text">Confirming your booking…</p>
    </div>
</div>

<?php include("includes/footer.php"); ?>

<script>
const BASE_CHARGE = <?= (int)$SERVICE_BASE_CHARGE ?>;
const VEHICLE_ID = <?= (int)$vehicle['id'] ?>;

const dateInput = document.getElementById("service-date");
const timeSection = document.getElementById("time-section");
const timeSlots = document.getElementById("time-slots");
const serviceSection = document.getElementById("service-section");

const todayStr = "<?= $today_str ?>";
dateInput.min = todayStr;

let selectedSlot = null;

function renderSlots(date, bookedSlots) {
    timeSlots.innerHTML = "";
    selectedSlot = null;

    bookedSlots = bookedSlots || [];

    const slots = [
        ["09:00-10:00", "9:00 AM - 10:00 AM"],
        ["10:00-11:00", "10:00 AM - 11:00 AM"],
        ["11:00-12:00", "11:00 AM - 12:00 PM"],
        ["12:00-13:00", "12:00 PM - 1:00 PM"],
        ["13:00-14:00", "1:00 PM - 2:00 PM"],
        ["14:00-15:00", "2:00 PM - 3:00 PM"],
    ];

    slots.forEach(([value, label]) => {
        const d = document.createElement("div");
        const isBooked = bookedSlots.includes(value);
        d.className = isBooked ? "slot booked" : "slot available";
        d.dataset.value = value;
        d.innerText = label;

        d.onclick = () => {
            if (d.classList.contains("booked")) return;
            document.querySelectorAll(".slot").forEach(s => s.classList.remove("selected"));
            d.classList.add("selected");
            selectedSlot = value;
            serviceSection.style.display = "block";
        };

        timeSlots.appendChild(d);
    });
}

dateInput.addEventListener("change", () => {
    const date = dateInput.value;
    if (!date) return;

    timeSection.style.display = "block";
    serviceSection.style.display = "none";
    timeSlots.innerHTML = '<p class="slots-loading">Checking availability…</p>';

    fetch("ajax/get_slots.php?date=" + encodeURIComponent(date))
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                renderSlots(date, data.booked);
            } else {
                timeSlots.innerHTML = '<p class="slots-loading">Could not load availability. Please try again.</p>';
            }
        })
        .catch(() => {
            timeSlots.innerHTML = '<p class="slots-loading">Could not load availability. Please try again.</p>';
        });
});

let total = BASE_CHARGE;
const totalEl = document.getElementById("total");
const costDetails = document.getElementById("costDetails");

function recalcTotal() {
    total = BASE_CHARGE;
    costDetails.innerHTML = `
        <div class="row"><span>Car Wash</span><span>FREE</span></div>
        <div class="row"><span>Service Charge</span><span>₹${BASE_CHARGE}</span></div>
    `;

    document.querySelectorAll(".services-list input:checked").forEach(c => {
        total += parseInt(c.value, 10);
        costDetails.innerHTML += `
            <div class="row">
                <span>${c.dataset.name}</span>
                <span>₹${c.value}</span>
            </div>`;
    });

    totalEl.innerText = total;
}

document.querySelectorAll(".services-list input").forEach(cb => {
    cb.addEventListener("change", recalcTotal);
});

const bookBtn = document.getElementById("bookBtn");
bookBtn.addEventListener("click", function () {

    const date = dateInput.value;
    const notes = document.getElementById("specialBox") ? document.getElementById("specialBox").value : "";

    if (!date) {
        alert("Please select a date.");
        return;
    }
    if (!selectedSlot) {
        alert("Please select a time slot.");
        return;
    }

    const selectedServices = [];
    document.querySelectorAll(".services-list input:checked").forEach(cb => {
        selectedServices.push(cb.dataset.name);
    });

    bookBtn.disabled = true;
    bookBtn.innerText = "Booking...";

    const overlay = document.getElementById("redirect-overlay");
    overlay.style.display = "block";
    const startTime = Date.now();

    const body = new URLSearchParams();
    body.set("vehicle_id", VEHICLE_ID);
    body.set("date", date);
    body.set("time", selectedSlot);
    body.set("services", selectedServices.join(", "));
    body.set("notes", notes);

    fetch("ajax/save_booking.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: body.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            document.getElementById("redirect-text").innerText = "Booking confirmed! Redirecting to Payment Gateway…";
            const elapsed = Date.now() - startTime;
            const remainingDelay = Math.max(0, 3000 - elapsed);
            setTimeout(() => {
                window.location.href = "confirmation.php?id=" + encodeURIComponent(data.booking_id);
            }, remainingDelay);
        } else {
            overlay.style.display = "none";
            bookBtn.disabled = false;
            bookBtn.innerText = "Book Service";
            alert(data.msg || "This slot was just booked by someone else. Please pick another time.");
            dateInput.dispatchEvent(new Event("change"));
        }
    })
    .catch(() => {
        overlay.style.display = "none";
        bookBtn.disabled = false;
        bookBtn.innerText = "Book Service";
        alert("Something went wrong. Please try again.");
    });
});
</script>

</body>
</html>
