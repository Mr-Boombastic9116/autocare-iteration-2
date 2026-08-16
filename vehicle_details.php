<?php
session_start();
include("includes/db.php");
require_once("includes/auth.php");
require_login();
$ACTIVE_NAV = 'vehicles';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$result = $conn->query("SELECT * FROM vehicles WHERE id = $id");

if (!$result || $result->num_rows == 0) {
    echo "Vehicle not found";
    exit();
}

$vehicle = $result->fetch_assoc();

$kms = (int)$vehicle['kms'];

$last_service = !empty($vehicle['last_service'])
    ? new DateTime($vehicle['last_service'])
    : new DateTime();

$ownership = !empty($vehicle['ownership_date'])
    ? new DateTime($vehicle['ownership_date'])
    : new DateTime();

$today = new DateTime();

$kms_last_service = (int)$vehicle['kms_last_service'];


/* =========================================================
   SERVICE CALCULATIONS
========================================================= */

$days_since_service = $today->diff($last_service)->days;

$months_since_service =
    ($today->diff($last_service)->y * 12)
    + $today->diff($last_service)->m;

$service_interval_km = 10000;

$next_service_km = $kms_last_service + 5000;

$next_service_date = clone $last_service;
$next_service_date->modify("+6 months");

$kms_since_service = $kms - $kms_last_service;

$monthly_km = $months_since_service > 0
    ? $kms / $months_since_service
    : 0;

$usage_percent = ($kms_since_service / $service_interval_km) * 100;


/* ENGINE OIL */

if ($usage_percent < 50) {
    $oil_status = "Good";
} elseif ($usage_percent < 80) {
    $oil_status = "Degrading";
} else {
    $oil_status = "Poor";
}


/* BRAKES */

$brake_usage = ($kms / 35000) * 100;

if ($brake_usage < 50) {
    $brake_status = "Good";
} elseif ($brake_usage < 80) {
    $brake_status = "Moderate";
} else {
    $brake_status = "Worn";
}


/* TYRES */

$tyre_years = $today->diff($ownership)->y;

$tyre_usage = ($kms / 40000) * 100;

if ($tyre_years >= 3 || $tyre_usage > 70) {
    $tyre_status = "Replace Soon";
} else {
    $tyre_status = "Good";
}


/* BATTERY */

if ($tyre_years < 2) {
    $battery = "Healthy";
} elseif ($tyre_years < 3) {
    $battery = "Weak";
} else {
    $battery = "Risky";
}


/* SERVICE URGENCY */

if ($usage_percent > 100) {
    $urgency = "Immediate";
} elseif ($usage_percent > 80) {
    $urgency = "Plan Soon";
} else {
    $urgency = "Low";
}


/* ALERTS */

$alerts = [];

if ($usage_percent > 100) {
    $alerts[] = "Service overdue";
}

if ($battery == "Risky") {
    $alerts[] = "Battery may fail soon";
}

if ($tyre_status == "Replace Soon") {
    $alerts[] = "Tyres need attention";
}


/* =========================================================
   STATUS FUNCTIONS
========================================================= */

function getStatusClass($status)
{
    $status = strtolower($status);

    if (in_array($status, ["good", "healthy", "low"])) {
        return "good";
    }

    if (in_array($status, ["moderate", "degrading", "weak", "plan soon"])) {
        return "warning";
    }

    return "danger";
}


/* =========================================================
   DOCUMENT DATES
========================================================= */

$ownershipDate = new DateTime($vehicle['ownership_date']);

$rc_expiry = (clone $ownershipDate)->modify("+15 years");

$insurance_expiry = (clone $ownershipDate)->modify("+1 year");

$puc_expiry = (clone $ownershipDate)->modify("+6 months");

$tax_expiry = (clone $ownershipDate)->modify("+15 years");

$dl_expiry = (clone $ownershipDate)->modify("+20 years");

$permit_expiry = (clone $ownershipDate)->modify("+5 years");

$fastag_balance = 1500;


function getDocStatus($expiry, $today)
{
    if ($expiry < $today) {
        return "danger";
    }

    $diff = $today->diff($expiry)->days;

    if ($diff < 60) {
        return "warning";
    }

    return "good";
}


/* =========================================================
   VEHICLE IMAGE
========================================================= */

require_once("includes/vehicle_image.php");

$vehicleImage = get_vehicle_image(
    $vehicle['company'],
    $vehicle['model'],
    $vehicle['fuel'] ?? ''
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>
<?= htmlspecialchars($vehicle['company'] . " " . $vehicle['model']) ?>
| AutoCare
</title>

<link rel="stylesheet" href="assets/css/style.css">

<style>

/* =========================================================
   GLOBAL
========================================================= */

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #f5f7f9;
    color: #102333;
    font-size: 15.5px;
    line-height: 1.5;
}

button {
    font-family: inherit;
}

a {
    text-decoration: none;
}


/* =========================================================
   PAGE
========================================================= */

.page {
    max-width: 1380px;
    margin: 35px auto 70px;
    padding: 0 28px;
}


/* =========================================================
   BREADCRUMB
========================================================= */

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #71808b;
    font-size: 14px;
    margin-bottom: 22px;
}

.breadcrumb a {
    color: #71808b;
}

.breadcrumb a:hover {
    color: #e5a900;
}

.breadcrumb strong {
    color: #142631;
}


/* =========================================================
   VEHICLE HERO
========================================================= */

.vehicle-hero {
    background: #10202a;
    border-radius: 24px;
    min-height: 265px;
    display: grid;
    grid-template-columns: 330px 1fr 210px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 18px 45px rgba(10, 25, 35, .12);
}


/* subtle decorative circle */

.vehicle-hero::after {
    content: "";
    position: absolute;
    width: 330px;
    height: 330px;
    border: 1px solid rgba(255,191,0,.18);
    border-radius: 50%;
    right: -110px;
    bottom: -200px;
}


/* IMAGE */

.hero-image {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    position: relative;
    z-index: 2;
}

.hero-image-box {
    width: 100%;
    max-width: 285px;
    height: 185px;
    background: #ffffff;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px;
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.22);
}

.hero-image-box img {
    width: 100%;
    max-width: 255px;
    height: 155px;
    max-height: 155px;
    object-fit: contain;
    object-position: center;
    display: block;
    filter: drop-shadow(0 8px 12px rgba(0, 0, 0, 0.12));
}


/* HERO TEXT */

.hero-info {
    display: flex;
    justify-content: center;
    flex-direction: column;
    padding: 30px 20px;
    position: relative;
    z-index: 3;
}

.vehicle-label {
    color: #ffbf00;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.hero-info h1 {
    color: white;
    font-size: 40px;
    line-height: 1.05;
    margin: 0 0 12px;
    letter-spacing: -1.3px;
}

.hero-info p {
    color: #aebbc3;
    margin: 0;
    font-size: 15.5px;
    line-height: 1.5;
}

.plate {
    display: inline-flex;
    margin-top: 18px;
    background: white;
    color: #12232d;
    border-radius: 8px;
    padding: 9px 15px;
    font-size: 15px;
    font-weight: 800;
    letter-spacing: 1px;
    width: fit-content;
}


/* HERO STATS */

.hero-stats {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 12px;
    padding: 25px;
    position: relative;
    z-index: 3;
}

.hero-stat {
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 13px;
    padding: 14px 16px;
}

.hero-stat span {
    display: block;
    color: #82929d;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    margin-bottom: 6px;
}

.hero-stat strong {
    color: white;
    font-size: 17px;
}


/* =========================================================
   MAIN CARD
========================================================= */

.main-card {
    background: white;
    border: 1px solid #e2e7ea;
    border-radius: 22px;
    margin-top: 24px;
    box-shadow: 0 12px 35px rgba(20,40,50,.06);
    overflow: hidden;
}


/* =========================================================
   TABS
========================================================= */

.tabs {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 7px;
    background: #eef2f4;
    padding: 7px;
    margin: 20px 20px 0;
    border-radius: 15px;
}

.tab-btn {
    border: 0;
    background: transparent;
    padding: 15px 12px;
    border-radius: 11px;
    cursor: pointer;
    color: #697984;
    font-size: 14.5px;
    font-weight: 700;
    transition: .2s;
}

.tab-btn:hover {
    color: #162936;
}

.tab-btn.active {
    background: #ffbd08;
    color: #101d25;
    box-shadow: 0 5px 14px rgba(255,189,8,.2);
}


/* =========================================================
   TAB CONTENT
========================================================= */

.tab-content {
    display: none;
    padding: 32px;
}

.tab-content.active {
    display: block;
}

.section-title {
    margin-bottom: 25px;
}

.section-title h2 {
    font-size: 27px;
    margin: 0 0 6px;
    color: #112532;
}

.section-title p {
    margin: 0;
    color: #80909a;
    font-size: 14.5px;
    line-height: 1.5;
}


/* =========================================================
   VEHICLE DETAILS
========================================================= */

.details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.details-card {
    border: 1px solid #e3e8eb;
    border-radius: 17px;
    overflow: hidden;
    background: #fff;
}

.details-card-title {
    background: #f5f7f8;
    padding: 16px 20px;
    font-size: 14.5px;
    font-weight: 800;
    color: #152a36;
    border-bottom: 1px solid #e3e8eb;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    padding: 16px 20px;
    border-bottom: 1px solid #edf0f2;
    min-height: 52px;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    color: #74848f;
    font-size: 15px;
}

.detail-value {
    color: #162b38;
    font-size: 15px;
    font-weight: 700;
    text-align: right;
}


/* =========================================================
   QUICK HIGHLIGHTS
========================================================= */

.highlights {
    margin-top: 22px;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 13px;
}

.highlight {
    background: #f6f8f9;
    border: 1px solid #e6eaec;
    border-radius: 14px;
    padding: 17px;
}

.highlight-label {
    color: #84929a;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}

.highlight-value {
    color: #112733;
    font-size: 20px;
    font-weight: 800;
}


/* =========================================================
   SERVICE
========================================================= */

.service-top {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 25px;
}

.service-summary {
    border: 1px solid #e3e8eb;
    border-radius: 15px;
    padding: 19px;
}

.service-summary small {
    display: block;
    color: #82919a;
    font-size: 12.5px;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}

.service-summary strong {
    font-size: 22px;
    color: #142a37;
}

.service-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.service-card {
    border: 1px solid #e3e8eb;
    border-radius: 16px;
    padding: 20px;
}

.service-card h3 {
    margin: 0 0 15px;
    font-size: 17px;
}

.service-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #edf0f2;
    font-size: 14.5px;
}

.service-row:last-child {
    border-bottom: none;
}

.service-row span:first-child {
    color: #768691;
}

.service-row span:last-child {
    font-weight: 700;
}


/* STATUS */

.status {
    padding: 6px 12px;
    border-radius: 30px;
    font-size: 12px !important;
    font-weight: 800 !important;
}

.good {
    background: #e7f7ef;
    color: #16804b !important;
}

.warning {
    background: #fff4d7;
    color: #ad7900 !important;
}

.danger {
    background: #fde9e9;
    color: #c83939 !important;
}


/* =========================================================
   RENEWAL
========================================================= */

.renewal-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.renewal-card {
    border: 1px solid #e2e7ea;
    border-radius: 16px;
    padding: 20px;
    transition: .2s;
}

.renewal-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(20,40,50,.06);
}

.renewal-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}

.renewal-head h3 {
    margin: 0;
    font-size: 17px;
}

.doc-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #fff5d8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
}

.renewal-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.renewal-details small {
    display: block;
    color: #82909a;
    margin-bottom: 5px;
    font-size: 12.5px;
}

.renewal-details strong {
    font-size: 15px;
}

.renew-btn {
    display: block;
    text-align: center;
    text-decoration: none;
    margin-top: 18px;
    width: 100%;
    border: 1px solid #10202a;
    background: #10202a;
    color: #ffffff;
    border-radius: 9px;
    padding: 10px;
    font-weight: 700;
    font-size: 13.5px;
    cursor: pointer;
    transition: all 0.25s ease;
}

.renew-btn:hover {
    background: #1e3240;
    border-color: #1e3240;
    color: #ffc107;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 32, 42, 0.25);
}


/* =========================================================
   DOCUMENT HOLDER
========================================================= */

.doc-list {
    display: flex;
    flex-direction: column;
    gap: 11px;
}

.doc-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    border: 1px solid #e2e7ea;
    border-radius: 14px;
    padding: 15px 18px;
}

.doc-name {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14.5px;
    font-weight: 700;
}

.doc-icon-small {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    background: #f0f3f5;
    display: flex;
    justify-content: center;
    align-items: center;
}

.doc-actions {
    display: flex;
    gap: 8px;
}

.view-btn,
.download-btn {
    border: 1px solid #10202a;
    background: #10202a;
    color: #ffffff;
    border-radius: 8px;
    padding: 9px 14px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.25s ease;
}

.view-btn:hover,
.download-btn:hover {
    background: #1e3240;
    border-color: #1e3240;
    color: #ffc107;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 32, 42, 0.20);
}

.add-document {
    border: 2px dashed #d8dee1;
    background: #fafbfb;
    color: #7a8992;
    justify-content: center;
    cursor: pointer;
}

.add-document:hover {
    border-color: #ffbd08;
    color: #a67400;
}


/* =========================================================
   BACK BUTTON
========================================================= */

.bottom-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 25px;
}

.back-btn {
    border: 1px solid #dce2e5;
    background: white;
    padding: 11px 18px;
    border-radius: 10px;
    font-weight: 700;
    color: #41515c;
    cursor: pointer;
}

.back-btn:hover {
    background: #eef2f4;
}

.book-btn {
    border: none;
    background: #ffbd08;
    color: #101c23;
    padding: 12px 22px;
    border-radius: 10px;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 6px 15px rgba(255,189,8,.2);
}

.book-btn:hover {
    background: #ffca35;
}


/* =========================================================
   FOOTER
========================================================= */

footer {
    text-align: center;
    padding: 25px;
    color: #87949b;
    font-size: 12px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media(max-width: 1050px) {

    .vehicle-hero {
        grid-template-columns: 260px 1fr;
    }

    .hero-stats {
        display: none;
    }

    .highlights {
        grid-template-columns: repeat(2,1fr);
    }
}


@media(max-width: 800px) {

    .top-header {
        padding: 0 20px;
    }

    .page {
        padding: 0 15px;
    }

    .vehicle-hero {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .hero-image {
        padding-bottom: 0;
    }

    .hero-info {
        align-items: center;
    }

    .details-grid,
    .service-grid,
    .renewal-grid {
        grid-template-columns: 1fr;
    }

    .tabs {
        grid-template-columns: 1fr 1fr;
    }

    .highlights {
        grid-template-columns: 1fr 1fr;
    }
}


@media(max-width: 520px) {

    .tabs {
        grid-template-columns: 1fr;
    }

    .tab-content {
        padding: 20px 15px;
    }

    .details-grid {
        gap: 12px;
    }

    .hero-info h1 {
        font-size: 30px;
    }

    .highlights {
        grid-template-columns: 1fr;
    }

    .doc-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .doc-actions {
        width: 100%;
    }

    .doc-actions button {
        flex: 1;
    }
}

</style>

</head>


<body>


<!-- =====================================================
     HEADER
===================================================== -->

<?php include("includes/header_app.php"); ?>



<!-- =====================================================
     PAGE
===================================================== -->

<main class="page">


    <!-- BREADCRUMB -->

    <div class="breadcrumb">

        <a href="home.php">Home</a>

        <span>›</span>

        <a href="vehicles.php">My Vehicles</a>

        <span>›</span>

        <strong>
            <?= htmlspecialchars($vehicle['model']) ?>
        </strong>

    </div>



    <!-- =================================================
         VEHICLE HERO
    ================================================= -->

    <section class="vehicle-hero">


        <div class="hero-image">

            <div class="hero-image-box">
                <img
                    src="assets/images/<?= $vehicleImage ?>"
                    alt="<?= htmlspecialchars($vehicle['company'].' '.$vehicle['model']) ?>"
                >
            </div>

        </div>



        <div class="hero-info">

            <div class="vehicle-label">
                Vehicle Profile
            </div>

            <h1>
                <?= htmlspecialchars($vehicle['company']) ?>
                <?= htmlspecialchars($vehicle['model']) ?>
            </h1>

            <p>
                <?= htmlspecialchars($vehicle['variant']) ?>
                &nbsp; • &nbsp;
                <?= htmlspecialchars($vehicle['fuel']) ?>
            </p>

            <div class="plate">
                <?= htmlspecialchars($vehicle['license_no']) ?>
            </div>

        </div>



        <div class="hero-stats">

            <div class="hero-stat">

                <span>Manufacturing Year</span>

                <strong>
                    <?= htmlspecialchars($vehicle['year']) ?>
                </strong>

            </div>


            <div class="hero-stat">

                <span>Current Mileage</span>

                <strong>
                    <?= number_format($kms) ?> km
                </strong>

            </div>


            <div class="hero-stat">

                <span>Last Service</span>

                <strong>
                    <?= $last_service->format("d M Y") ?>
                </strong>

            </div>

        </div>

    </section>



    <!-- =================================================
         MAIN CARD
    ================================================= -->

    <section class="main-card">


        <!-- TABS -->

        <div class="tabs">

            <button
                class="tab-btn active"
                onclick="openTab('details', this)"
            >
                Vehicle Details
            </button>

            <button
                class="tab-btn"
                onclick="openTab('service', this)"
            >
                Service & Health
            </button>

            <button
                class="tab-btn"
                onclick="openTab('renewal', this)"
            >
                Document Renewal
            </button>

            <button
                class="tab-btn"
                onclick="openTab('docs', this)"
            >
                Document Holder
            </button>

        </div>



        <!-- =================================================
             VEHICLE DETAILS
        ================================================= -->

        <div
            class="tab-content active"
            id="details"
        >

            <div class="section-title">

                <h2>Vehicle Information</h2>

                <p>
                    Complete specifications and registered vehicle details
                </p>

            </div>


            <div class="details-grid">


                <!-- BASIC INFORMATION -->

                <div class="details-card">

                    <div class="details-card-title">
                        Basic Information
                    </div>


                    <div class="detail-row">
                        <span class="detail-label">Company</span>
                        <span class="detail-value">
                            <?= htmlspecialchars($vehicle['company']) ?>
                        </span>
                    </div>


                    <div class="detail-row">
                        <span class="detail-label">Model</span>
                        <span class="detail-value">
                            <?= htmlspecialchars($vehicle['model']) ?>
                        </span>
                    </div>


                    <div class="detail-row">
                        <span class="detail-label">Variant</span>
                        <span class="detail-value">
                            <?= htmlspecialchars($vehicle['variant']) ?>
                        </span>
                    </div>


                    <div class="detail-row">
                        <span class="detail-label">Manufacturing Year</span>
                        <span class="detail-value">
                            <?= htmlspecialchars($vehicle['year']) ?>
                        </span>
                    </div>


                    <div class="detail-row">
                        <span class="detail-label">Colour</span>
                        <span class="detail-value">
                            White
                        </span>
                    </div>


                    <div class="detail-row">
                        <span class="detail-label">Registration Number</span>
                        <span class="detail-value">
                            <?= htmlspecialchars($vehicle['license_no']) ?>
                        </span>
                    </div>

                </div>



                <!-- ENGINE & PERFORMANCE -->

                <div class="details-card">

                    <div class="details-card-title">
                        Engine & Performance
                    </div>


                    <div class="detail-row">
                        <span class="detail-label">
                            Engine Capacity
                        </span>

                        <span class="detail-value">
                            1497 cc
                        </span>
                    </div>


                    <div class="detail-row">
                        <span class="detail-label">
                            No. of Cylinders
                        </span>

                        <span class="detail-value">
                            4
                        </span>
                    </div>


                    <div class="detail-row">
                        <span class="detail-label">
                            Turbo
                        </span>

                        <span class="detail-value">
                            Yes
                        </span>
                    </div>


                    <div class="detail-row">
                        <span class="detail-label">
                            Power
                        </span>

                        <span class="detail-value">
                            113 BHP
                        </span>
                    </div>


                    <div class="detail-row">
                        <span class="detail-label">
                            Torque
                        </span>

                        <span class="detail-value">
                            144 Nm
                        </span>
                    </div>


                    <div class="detail-row">
                        <span class="detail-label">
                            Mileage
                        </span>

                        <span class="detail-value">
                            17 km/l
                        </span>
                    </div>

                </div>



                <!-- FUEL & TRANSMISSION -->

                <div class="details-card">

                    <div class="details-card-title">
                        Fuel & Transmission
                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Fuel Type
                        </span>

                        <span class="detail-value">
                            <?= htmlspecialchars($vehicle['fuel']) ?>
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Fuel Tank Capacity
                        </span>

                        <span class="detail-value">
                            50 Litres
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Transmission
                        </span>

                        <span class="detail-value">
                            Automatic
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Gearbox
                        </span>

                        <span class="detail-value">
                            6-Speed iVT
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Drive Type
                        </span>

                        <span class="detail-value">
                            Front Wheel Drive
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Seating Capacity
                        </span>

                        <span class="detail-value">
                            5
                        </span>

                    </div>

                </div>



                <!-- VEHICLE IDENTIFICATION -->

                <div class="details-card">

                    <div class="details-card-title">
                        Vehicle Identification
                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Chassis Number
                        </span>

                        <span class="detail-value">
                            ABC123XYZ789
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Engine Number
                        </span>

                        <span class="detail-value">
                            ENG456789
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Ownership Date
                        </span>

                        <span class="detail-value">
                            <?= $ownership->format("d-m-Y") ?>
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Current Mileage
                        </span>

                        <span class="detail-value">
                            <?= number_format($kms) ?> km
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Last Service
                        </span>

                        <span class="detail-value">
                            <?= $last_service->format("d-m-Y") ?>
                        </span>

                    </div>

                </div>



                <!-- BRAKES & SUSPENSION -->

                <div class="details-card">

                    <div class="details-card-title">
                        Brakes, Suspension & Steering
                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Front Brake
                        </span>

                        <span class="detail-value">
                            Disc
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Rear Brake
                        </span>

                        <span class="detail-value">
                            Disc
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Front Suspension
                        </span>

                        <span class="detail-value">
                            MacPherson Strut
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Rear Suspension
                        </span>

                        <span class="detail-value">
                            Rear Twist Beam
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Steering Type
                        </span>

                        <span class="detail-value">
                            Electric
                        </span>

                    </div>

                </div>



                <!-- DIMENSIONS -->

                <div class="details-card">

                    <div class="details-card-title">
                        Dimensions & Capacity
                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Length
                        </span>

                        <span class="detail-value">
                            4330 mm
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Width
                        </span>

                        <span class="detail-value">
                            1790 mm
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Height
                        </span>

                        <span class="detail-value">
                            1635 mm
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Wheelbase
                        </span>

                        <span class="detail-value">
                            2610 mm
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Front Alloy Wheel
                        </span>

                        <span class="detail-value">
                            18 Inch
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Rear Alloy Wheel
                        </span>

                        <span class="detail-value">
                            18 Inch
                        </span>

                    </div>


                    <div class="detail-row">

                        <span class="detail-label">
                            Boot Space
                        </span>

                        <span class="detail-value">
                            433 Litres
                        </span>

                    </div>

                </div>

            </div>



            <!-- HIGHLIGHTS -->

            <div class="highlights">

                <div class="highlight">

                    <div class="highlight-label">
                        Vehicle Age
                    </div>

                    <div class="highlight-value">
                        <?= $today->diff($ownership)->y ?> Years
                    </div>

                </div>


                <div class="highlight">

                    <div class="highlight-label">
                        Current Mileage
                    </div>

                    <div class="highlight-value">
                        <?= number_format($kms) ?> km
                    </div>

                </div>


                <div class="highlight">

                    <div class="highlight-label">
                        Fuel Type
                    </div>

                    <div class="highlight-value">
                        <?= htmlspecialchars($vehicle['fuel']) ?>
                    </div>

                </div>


                <div class="highlight">

                    <div class="highlight-label">
                        Registration
                    </div>

                    <div class="highlight-value">
                        <?= htmlspecialchars($vehicle['license_no']) ?>
                    </div>

                </div>

            </div>

        </div>



        <!-- =================================================
             SERVICE & HEALTH
        ================================================= -->

        <div
            class="tab-content"
            id="service"
        >

            <div class="section-title">

                <h2>Service & Health</h2>

                <p>
                    Maintenance history and vehicle health insights
                </p>

            </div>


            <div class="service-top">

                <div class="service-summary">

                    <small>Last Service</small>

                    <strong>
                        <?= $last_service->format("d M Y") ?>
                    </strong>

                </div>


                <div class="service-summary">

                    <small>Next Service</small>

                    <strong>
                        <?= $next_service_date->format("d M Y") ?> • <?= number_format($next_service_km) ?> km
                    </strong>

                </div>


                <div class="service-summary">

                    <small>Service Urgency</small>

                    <strong
                        class="status <?= getStatusClass($urgency) ?>"
                    >
                        <?= $urgency ?>
                    </strong>

                </div>

            </div>



            <div class="service-grid">


                <div class="service-card">

                    <h3>Service Overview</h3>

                    <div class="service-row">
                        <span>Last Service</span>
                        <span><?= $last_service->format("d-m-Y") ?></span>
                    </div>

                    <div class="service-row">
                        <span>KMs at Last Service</span>
                        <span><?= number_format($kms_last_service) ?> km</span>
                    </div>

                    <div class="service-row">
                        <span>Total KMs</span>
                        <span><?= number_format($kms) ?> km</span>
                    </div>

                    <div class="service-row">
                        <span>Time Since Service</span>
                        <span><?= $months_since_service ?> months</span>
                    </div>

                    <div class="service-row">
                        <span>Days Since Service</span>
                        <span><?= $days_since_service ?> days</span>
                    </div>

                    <div class="service-row">
                        <span>Next Service</span>
                        <span><?= $next_service_date->format("d-m-Y") ?> • <?= number_format($next_service_km) ?> km</span>
                    </div>

                </div>



                <div class="service-card">

                    <h3>Smart Health Insights</h3>

                    <div class="service-row">
                        <span>Engine Oil</span>
                        <span class="status <?= getStatusClass($oil_status) ?>">
                            <?= $oil_status ?>
                        </span>
                    </div>

                    <div class="service-row">
                        <span>Brake Condition</span>
                        <span class="status <?= getStatusClass($brake_status) ?>">
                            <?= $brake_status ?>
                        </span>
                    </div>

                    <div class="service-row">
                        <span>Tyre Health</span>
                        <span class="status <?= getStatusClass($tyre_status) ?>">
                            <?= $tyre_status ?>
                        </span>
                    </div>

                    <div class="service-row">
                        <span>Battery</span>
                        <span class="status <?= getStatusClass($battery) ?>">
                            <?= $battery ?>
                        </span>
                    </div>

                    <div class="service-row">
                        <span>Driving Load</span>
                        <span><?= round($monthly_km) ?> km/month</span>
                    </div>

                </div>

            </div>


            <div class="bottom-actions">



                <button
                    class="book-btn"
                    onclick="window.location.href='book_service.php?id=<?= $id ?>'"
                >
                    Book Service
                </button>

            </div>

        </div>



        <!-- =================================================
             DOCUMENT RENEWAL
        ================================================= -->

        <div
            class="tab-content"
            id="renewal"
        >

            <div class="section-title">

                <h2>Document Renewal</h2>

                <p>
                    Keep your vehicle documents active and up to date
                </p>

            </div>


            <div class="renewal-grid">


                <?php

                $documents = [

                    [
                        "Registration Certificate",
                        "Issued",
                        $ownershipDate,
                        $rc_expiry,
                        "https://parivahan.gov.in/"
                    ],

                    [
                        "Insurance",
                        "Issued",
                        $ownershipDate,
                        $insurance_expiry,
                        "https://www.policybazaar.com/"
                    ],

                    [
                        "Pollution Certificate (PUC)",
                        "Issued",
                        $ownershipDate,
                        $puc_expiry,
                        "https://vahan.parivahan.gov.in/puc/"
                    ],

                    [
                        "Road Tax",
                        "Issued",
                        $ownershipDate,
                        $tax_expiry,
                        "https://echallan.parivahan.gov.in/"
                    ],

                    [
                        "Driving License",
                        "Issued",
                        $ownershipDate,
                        $dl_expiry,
                        "https://parivahan.gov.in/"
                    ],

                    [
                        "Vehicle Permit",
                        "Issued",
                        $ownershipDate,
                        $permit_expiry,
                        "https://parivahan.gov.in/"
                    ]

                ];


                foreach ($documents as $doc):

                    $name = $doc[0];
                    $issued = $doc[2];
                    $expiry = $doc[3];
                    $renew_url = $doc[4];

                ?>

                <div class="renewal-card">

                    <div class="renewal-head">

                        <h3>
                            <?= $name ?>
                        </h3>

                        <div class="doc-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        </div>

                    </div>


                    <div class="renewal-details">

                        <div>

                            <small>Issued</small>

                            <strong>
                                <?= $issued->format("d-m-Y") ?>
                            </strong>

                        </div>


                        <div>

                            <small>Expires</small>

                            <strong
                                class="<?= getDocStatus($expiry, $today) ?>"
                            >
                                <?= $expiry->format("d-m-Y") ?>
                            </strong>

                        </div>

                    </div>


                    <a
                        href="<?= htmlspecialchars($renew_url) ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="renew-btn"
                    >
                        Renew Document ↗
                    </a>

                </div>

                <?php endforeach; ?>


                <!-- FASTAG -->

                <div class="renewal-card">

                    <div class="renewal-head">

                        <h3>FASTag</h3>

                        <div class="doc-icon">
                            ₹
                        </div>

                    </div>


                    <div class="renewal-details">

                        <div>

                            <small>Balance</small>

                            <strong class="good">
                                ₹<?= $fastag_balance ?>
                            </strong>

                        </div>


                        <div>

                            <small>Status</small>

                            <strong class="good">
                                Active
                            </strong>

                        </div>

                    </div>


                    <a
                        href="https://www.netc.org.in/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="renew-btn"
                    >
                        Recharge FASTag ↗
                    </a>

                </div>


                <!-- ADD -->

                <div class="renewal-card add-document">

                    <h3>
                        + Add Document
                    </h3>

                </div>

            </div>

        </div>



        <!-- =================================================
             DOCUMENT HOLDER
        ================================================= -->

        <div
            class="tab-content"
            id="docs"
        >

            <div class="section-title">

                <h2>Document Holder</h2>

                <p>
                    Keep all your important vehicle documents in one place
                </p>

            </div>


            <div class="doc-list">


                <?php

                $docNames = [

                    "Driving Licence",
                    "Registration Certificate (RC)",
                    "Car Insurance Certificate",
                    "Pollution Under Control (PUC) Certificate",
                    "Vehicle Invoice Copy",
                    "Loan / Hypothecation Papers",
                    "Service Records Copy"

                ];

                foreach ($docNames as $doc):

                ?>

                <div class="doc-row">

                    <div class="doc-name">

                        <div class="doc-icon-small">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        </div>

                        <?= $doc ?>

                    </div>


                    <div class="doc-actions">

                        <button class="view-btn">
                            View
                        </button>

                        <button class="download-btn">
                             Download PDF
                        </button>

                    </div>

                </div>

                <?php endforeach; ?>


                <div class="doc-row add-document">

                    + Add Another Document

                </div>

            </div>

        </div>

    </section>

</main>



<?php include("includes/footer.php"); ?>



<script>

/* =========================================================
   TABS
========================================================= */

function openTab(tabId, button) {

    document
        .querySelectorAll(".tab-content")
        .forEach(function(tab) {

            tab.classList.remove("active");

        });


    document
        .querySelectorAll(".tab-btn")
        .forEach(function(btn) {

            btn.classList.remove("active");

        });


    document
        .getElementById(tabId)
        .classList.add("active");


    button.classList.add("active");

}


</script>


</body>
</html>