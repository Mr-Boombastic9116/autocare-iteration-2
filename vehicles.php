<?php

require_once("includes/db.php");
require_once("includes/auth.php");

require_login();

$user = current_username();
$ACTIVE_NAV = 'vehicles';


/* =========================================================
   DELETE VEHICLE
========================================================= */

if (isset($_GET['delete'])) {

    $delete_id = (int)$_GET['delete'];

    if ($delete_id > 0) {

        $delete_stmt = $conn->prepare(
            "DELETE FROM vehicles WHERE id = ? AND user = ?"
        );

        $delete_stmt->bind_param("is", $delete_id, $user);
        $delete_stmt->execute();
        $delete_stmt->close();
    }

    header("Location: vehicles.php");
    exit();
}


/* =========================================================
   LOAD VEHICLES
========================================================= */

$stmt = $conn->prepare(
    "SELECT * FROM vehicles WHERE user = ? ORDER BY id DESC"
);

$stmt->bind_param("s", $user);
$stmt->execute();

$vehicles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt->close();


/* =========================================================
   DASHBOARD DATA
========================================================= */

$total_vehicles = count($vehicles);

$upcoming_stmt = $conn->prepare(
    "SELECT COUNT(*) AS c
     FROM bookings
     WHERE user = ?
     AND service_date >= CURDATE()
     AND status <> 'Cancelled'"
);

$upcoming_stmt->bind_param("s", $user);
$upcoming_stmt->execute();
$upcoming_count = $upcoming_stmt->get_result()->fetch_assoc()['c'];
$upcoming_stmt->close();

$today_dt = new DateTime();
$expired_docs_count = 0;
$nearest_next_service_date = null;
$nearest_next_service_car = "";
$nearest_next_service_km = 0;

foreach ($vehicles as $v) {
    // 1. Calculate document expiries for this vehicle
    $own_dt = !empty($v['ownership_date']) ? new DateTime($v['ownership_date']) : new DateTime($v['created_at']);
    $rc_exp = (clone $own_dt)->modify("+15 years");
    $ins_exp = (clone $own_dt)->modify("+1 year");
    $puc_exp = (clone $own_dt)->modify("+6 months");
    $tax_exp = (clone $own_dt)->modify("+15 years");
    $dl_exp = (clone $own_dt)->modify("+20 years");
    $permit_exp = (clone $own_dt)->modify("+5 years");

    $doc_expiries = [$rc_exp, $ins_exp, $puc_exp, $tax_exp, $dl_exp, $permit_exp];
    foreach ($doc_expiries as $exp) {
        if ($exp < $today_dt) {
            $expired_docs_count++;
        }
    }

    // 2. Calculate next service date for this vehicle
    $last_serv = !empty($v['last_service']) ? new DateTime($v['last_service']) : (clone $own_dt);
    $next_serv = (clone $last_serv)->modify("+6 months");
    $v_name = trim(($v['company'] ?? '') . ' ' . ($v['model'] ?? ''));

    if ($nearest_next_service_date === null || $next_serv < $nearest_next_service_date) {
        $nearest_next_service_date = $next_serv;
        $nearest_next_service_car = $v_name;
        $nearest_next_service_km = (int)($v['kms_last_service'] ?? 0) + 5000;
    }
}

$next_service_display = $nearest_next_service_date
    ? $nearest_next_service_date->format("d M Y")
    : "—";

// Dynamic status colors for summary cards
if ($nearest_next_service_date === null) {
    $next_service_status_class = "card-green";
} else {
    $diff_days = (int)$today_dt->diff($nearest_next_service_date)->format("%r%a");
    if ($diff_days > 0) {
        $next_service_status_class = "card-green";
    } elseif ($diff_days >= -30) {
        $next_service_status_class = "card-yellow";
    } else {
        $next_service_status_class = "card-red";
    }
}

if ($expired_docs_count === 0) {
    $doc_status_class = "card-green";
} elseif ($expired_docs_count < 3) {
    $doc_status_class = "card-yellow";
} else {
    $doc_status_class = "card-red";
}

$upcoming_status_class = "card-green";

require_once("includes/vehicle_image.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>My Garage | AutoCare</title>

<link
    rel="stylesheet"
    href="assets/css/style.css"
>

<style>

/* =========================================================
   MODERN GLOBAL RESET
========================================================= */

* {
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    margin: 0;
    padding: 0;

    background:
        linear-gradient(
            135deg,
            #f7f9fa 0%,
            #eef2f4 100%
        );

    color: #17232d;

    font-family:
        Inter,
        "Segoe UI",
        Arial,
        Helvetica,
        sans-serif;

    overflow-x: hidden;

    -webkit-font-smoothing: antialiased;
    text-rendering: optimizeLegibility;
}

button,
input,
a {
    font-family: inherit;
}

a {
    text-decoration: none;
}


/* =========================================================
   HEADER — MATCH HOME PAGE
   ========================================================= */

/*
   My Vehicles is already the current page,
   so it is hidden from the header.
*/

.app-header .app-nav a[href="vehicles.php"] {
    display: none !important;
}


/*
   Keep header proportions exactly consistent.
*/

.app-header {
    width: 100%;
    height: 78px;

    background: #0d1820;

    padding: 0 44px;

    display: flex;
    align-items: center;

    position: sticky;
    top: 0;

    z-index: 1000;

    border-bottom:
        1px solid rgba(255,255,255,.07);

    box-shadow:
        0 6px 25px rgba(0,0,0,.14);
}


/* Logo */

.app-header .logo {
    width: 54px !important;
    height: 54px !important;

    object-fit: contain;

    border-radius: 10px;
}


/* Divider */

.app-header .divider {
    color: #ffbf00;

    font-size: 25px;

    margin: 0 17px;

    opacity: .55;
}


/* Brand */

.app-header .header-left h1 {
    margin: 0;

    color: #ffffff;

    font-size: 25px !important;
    line-height: 1;

    font-weight: 800;

    letter-spacing: -.8px;
}

.app-header .header-left h1 span {
    color: #ffbf00;
}


/* Navigation */

.app-header .app-nav {
    display: flex;
    align-items: center;

    gap: 8px;

    margin-left: auto;
    margin-right: 30px;

    height: 100%;
}

.app-header .app-nav a {
    height: 44px;

    display: flex;
    align-items: center;

    padding: 0 17px;

    color: #aebbc3;

    font-size: 14px !important;
    font-weight: 700;

    border-radius: 8px;

    transition:
        color .2s ease,
        background .2s ease;
}

.app-header .app-nav a:hover {
    color: #ffffff;

    background:
        rgba(255,255,255,.06);
}


/* Profile */

.app-header .header-right {
    position: relative;

    display: flex;
    align-items: center;

    flex-shrink: 0;
}

.app-header .profile-icon-new {
    width: 44px !important;
    height: 44px !important;

    object-fit: contain;

    padding: 3px;

    border-radius: 50%;

    border:
        1px solid rgba(255,255,255,.12);

    background:
        rgba(255,255,255,.04);

    cursor: pointer;

    transition: .2s ease;
}

.app-header .profile-icon-new:hover {
    transform: translateY(-1px);

    background:
        rgba(255,191,0,.10);

    border-color:
        rgba(255,191,0,.35);
}


/* =========================================================
   PAGE CONTAINER
========================================================= */

.vehicles-page {

    width: min(
        1180px,
        calc(100% - 44px)
    );

    margin: 0 auto;

    padding:
        38px 0 80px;
}


/* =========================================================
   GARAGE HERO
========================================================= */

.garage-hero {

    position: relative;

    min-height: 205px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 35px;

    padding:
        38px 42px;

    margin-bottom: 28px;

    overflow: hidden;

    border-radius: 20px;

    background:
        linear-gradient(
            125deg,
            #0f1b23 0%,
            #182832 60%,
            #233740 100%
        );

    box-shadow:
        0 15px 40px
        rgba(20,31,40,.14);
}


.garage-hero::before {

    content: "";

    position: absolute;

    width: 330px;
    height: 330px;

    right: -120px;
    top: -170px;

    border-radius: 50%;

    border:
        1px solid
        rgba(255,193,7,.18);
}


.garage-hero::after {

    content: "";

    position: absolute;

    width: 230px;
    height: 230px;

    right: 90px;
    bottom: -175px;

    border-radius: 50%;

    border:
        1px solid
        rgba(255,255,255,.07);
}


/* =========================================================
   HERO CONTENT
========================================================= */

.hero-content {

    position: relative;

    z-index: 2;
}


.hero-kicker {

    display: block;

    margin-bottom: 11px;

    color: #ffbf00;

    font-size: 13px;

    font-weight: 800;

    letter-spacing: 1.8px;

    text-transform: uppercase;
}


.hero-content h1 {

    margin:
        0 0 10px;

    color: #ffffff;

    font-size: 42px;

    line-height: 1.08;

    font-weight: 800;

    letter-spacing: -1.3px;
}


.hero-content p {

    max-width: 600px;

    margin: 0;

    color: #b8c3c9;

    font-size: 16px;

    line-height: 1.65;

    font-weight: 500;
}


/* =========================================================
   ADD VEHICLE BUTTON
========================================================= */

.hero-add-btn {

    position: relative;

    z-index: 3;

    flex-shrink: 0;

    min-width: 145px;
    min-height: 51px;

    padding: 0 24px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: #ffc107;

    color: #17202a;

    font-size: 15px;

    font-weight: 800;

    box-shadow:
        0 8px 22px
        rgba(255,193,7,.20);

    transition: .22s ease;
}

.hero-add-btn:hover {

    background: #eab000;

    transform: translateY(-2px);

    box-shadow:
        0 12px 26px
        rgba(255,193,7,.27);
}


/* =========================================================
   STATISTICS
========================================================= */

.garage-stats {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 18px;

    margin-bottom: 44px;
}


.stat-card {

    min-height: 125px;

    display: flex;

    flex-direction: column;

    justify-content: center;

    align-items: center;

    text-align: center;

    padding: 22px 24px;

    border: 1px solid #e0e6e9;

    border-radius: 15px;

    background: #ffffff;

    box-shadow: 0 7px 25px rgba(30,45,55,.045);

    transition: all .25s ease;
}


.stat-card:hover {

    transform: translateY(-3px);

    box-shadow: 0 12px 30px rgba(30,45,55,.075);
}


.stat-number {

    display: block;

    color: inherit;

    font-size: 32px;

    line-height: 1.1;

    font-weight: 800;

    letter-spacing: -.6px;

    margin-top: 4px;
}


.stat-main-date {

    display: block;

    color: inherit;

    font-size: 26px;

    line-height: 1.15;

    font-weight: 800;

    margin-top: 4px;
}


.stat-car-name {

    display: block;

    color: inherit;

    font-size: 14.5px;

    font-weight: 600;

    margin-top: 5px;

    opacity: 0.9;
}


.stat-label {

    display: block;

    color: inherit;

    font-size: 13px;

    line-height: 1.3;

    font-weight: 700;

    letter-spacing: .25px;

    text-transform: uppercase;

    opacity: 0.85;

    margin-bottom: 4px;
}


/* DYNAMIC STATUS BACKGROUNDS */

.stat-card.card-green {

    background: #dcfce7 !important;

    border-color: #86efac !important;

    color: #14532d !important;
}


.stat-card.card-yellow {

    background: #fef9c3 !important;

    border-color: #fde047 !important;

    color: #713f12 !important;
}


.stat-card.card-red {

    background: #fee2e2 !important;

    border-color: #fca5a5 !important;

    color: #7f1d1d !important;
}


/* =========================================================
   SECTION TITLE
========================================================= */

.garage-section-title {

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-bottom: 18px;
}


.garage-section-title h2 {

    margin: 0;

    color: #17232d;

    font-size: 27px;

    line-height: 1.2;

    font-weight: 800;

    letter-spacing: -.7px;
}


.garage-count {

    padding:
        7px 12px;

    border-radius: 20px;

    background: #e8edef;

    color: #5f6d76;

    font-size: 12px;

    font-weight: 750;

    letter-spacing: .3px;
}


/* =========================================================
   VEHICLE GRID
========================================================= */

.vehicle-grid {

    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    gap: 22px;

    align-items: stretch;
}


/* =========================================================
   VEHICLE CARD
========================================================= */

.vehicle-card {

    min-width: 0;

    overflow: hidden;

    border:
        1px solid #dfe5e8;

    border-radius: 17px;

    background: #ffffff;

    box-shadow:
        0 8px 28px
        rgba(25,40,52,.055);

    transition:
        transform .25s ease,
        box-shadow .25s ease;
}


.vehicle-card:hover {

    transform: translateY(-5px);

    box-shadow:
        0 16px 38px
        rgba(25,40,52,.11);
}


/* =========================================================
   VEHICLE IMAGE AREA
   EXACT SAME HEIGHT
========================================================= */

.vehicle-image {

    position: relative;

    width: 100%;

    height: 220px;

    min-height: 220px;

    display: flex;

    align-items: center;
    justify-content: center;

    overflow: hidden;

    padding: 16px;

    background: #ffffff;

    border-bottom: 1px solid #edf0f3;
}


/* Soft background glow */

.vehicle-image::before {

    content: "";

    position: absolute;

    width: 220px;
    height: 220px;

    border-radius: 50%;

    background:
        radial-gradient(
            circle,
            rgba(240, 244, 248, 0.85) 0%,
            rgba(255, 255, 255, 0) 70%
        );
}


/* Ground shadow line */

.vehicle-image::after {

    content: "";

    position: absolute;

    left: 14%;
    right: 14%;

    bottom: 16px;

    height: 6px;

    border-radius: 50%;

    background:
        radial-gradient(
            ellipse,
            rgba(0, 0, 0, 0.09) 0%,
            rgba(0, 0, 0, 0) 70%
        );
}


/* =========================================================
   IMAGE NORMALIZATION
   UNDISTORTED ASPECT RATIO + CLEAN CONTAIN
========================================================= */

.vehicle-image img {

    position: relative;

    z-index: 2;

    width: 90%;
    max-width: 260px;

    height: 150px;
    max-height: 150px;

    object-fit: contain;

    object-position: center center;

    display: block;

    filter:
        drop-shadow(
            0 10px 14px
            rgba(0, 0, 0, 0.12)
        );

    transition:
        transform .3s ease;
}


/* Keep exact same image scale on hover */

.vehicle-card:hover
.vehicle-image img {

    transform: scale(1.04);
}


/* =========================================================
   VEHICLE BADGE
========================================================= */

.vehicle-badge {

    position: absolute;

    z-index: 5;

    top: 14px;
    left: 14px;

    padding: 5px 11px;

    border-radius: 20px;

    background: #f1f5f9;

    color: #334155;

    font-size: 10.5px;

    font-weight: 800;

    letter-spacing: .6px;

    text-transform: uppercase;

    border: 1px solid #e2e8f0;
}


/* =========================================================
   VEHICLE BODY
========================================================= */

.vehicle-body {

    padding: 22px;
}


.vehicle-name {

    margin: 0;

    color: #17232d;

    font-size: 21px;

    line-height: 1.25;

    font-weight: 800;

    letter-spacing: -.35px;
}


.vehicle-plate {

    display: inline-flex;

    margin:
        9px 0 17px;

    padding:
        6px 10px;

    border-radius: 5px;

    background: #eef1f3;

    color: #53616a;

    font-size: 12px;

    font-weight: 750;

    letter-spacing: .7px;
}


/* =========================================================
   DETAILS
========================================================= */

.vehicle-details {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 14px;

    padding:
        15px 0;

    border-top:
        1px solid #e9edef;

    border-bottom:
        1px solid #e9edef;
}


.detail-label {

    display: block;

    margin-bottom: 6px;

    color: #89959d;

    font-size: 11px;

    font-weight: 750;

    letter-spacing: .5px;

    text-transform: uppercase;
}


.detail-value {

    color: #3f4e58;

    font-size: 14px;

    line-height: 1.4;

    font-weight: 700;
}


/* =========================================================
   SERVICE STATUS
========================================================= */

.service-status {

    display: flex;

    align-items: center;

    gap: 8px;

    margin-top: 15px;

    color: #65727a;

    font-size: 12px;

    line-height: 1.4;

    font-weight: 650;
}


.status-dot {

    width: 8px;
    height: 8px;

    flex-shrink: 0;

    border-radius: 50%;

    background: #46a36a;

    box-shadow:
        0 0 0 4px
        rgba(70,163,106,.10);
}


/* =========================================================
   ACTION BUTTONS
========================================================= */

.vehicle-actions {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 9px;

    margin-top: 17px;
}


.vehicle-btn {

    height: 43px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 8px;

    font-size: 13px;

    font-weight: 750;

    transition: .18s ease;

    cursor: pointer;
}


.vehicle-btn-outline {

    border:
        1px solid #d5dde1;

    background: #ffffff;

    color: #4c5a63;
}


.vehicle-btn-outline:hover {

    background: #f4f6f7;

    border-color: #b9c3c8;
}


.vehicle-btn-primary {

    border:
        1px solid #ffc107;

    background: #ffc107;

    color: #17202a;
}


.vehicle-btn-primary:hover {

    border-color: #e8ad00;

    background: #e8ad00;
}


/* =========================================================
   DELETE
========================================================= */

.vehicle-delete-btn {

    width: 100%;

    height: 40px;

    margin-top: 9px;

    display: flex;

    align-items: center;
    justify-content: center;

    border:
        1px solid #ead9d9;

    border-radius: 8px;

    background: #fffafa;

    color: #a44d4d;

    font-size: 12px;

    font-weight: 750;

    transition: .18s ease;
}


.vehicle-delete-btn:hover {

    background: #fff0f0;

    border-color: #d9b7b7;

    color: #8d3030;
}


/* =========================================================
   ADD VEHICLE CARD
========================================================= */

.add-vehicle-card {

    min-height: 470px;

    display: flex;

    align-items: center;
    justify-content: center;

    flex-direction: column;

    padding: 30px;

    border:
        1.5px dashed #c8d1d6;

    background:
        linear-gradient(
            145deg,
            #ffffff,
            #fafbfc
        );

    cursor: pointer;

    text-align: center;

    transition: .25s ease;
}


.add-vehicle-card:hover {

    border-color: #e7af00;

    background: #fffdf5;

    transform: translateY(-5px);

    box-shadow:
        0 16px 35px
        rgba(25,40,52,.09);
}


.add-icon {

    width: 58px;
    height: 58px;

    display: flex;

    align-items: center;
    justify-content: center;

    margin-bottom: 17px;

    border-radius: 50%;

    background: #fff7d8;

    color: #a87900;

    font-size: 29px;

    font-weight: 400;

    transition: .25s ease;
}


.add-vehicle-card:hover .add-icon {

    background: #ffc107;

    color: #17202a;

    transform: rotate(90deg);
}


.add-vehicle-card h3 {

    margin:
        0 0 8px;

    color: #26343e;

    font-size: 18px;

    font-weight: 800;
}


.add-vehicle-card p {

    max-width: 210px;

    margin: 0;

    color: #7e8b93;

    font-size: 13px;

    line-height: 1.6;
}


/* =========================================================
   EMPTY STATE
========================================================= */

.vehicle-empty {

    padding:
        80px 25px;

    border:
        1px solid #e1e7ea;

    border-radius: 17px;

    background: #ffffff;

    text-align: center;

    box-shadow:
        0 7px 25px
        rgba(25,40,52,.045);
}


.vehicle-empty img {

    width: 170px;
    height: 110px;

    object-fit: contain;

    opacity: .60;

    margin-bottom: 14px;
}


.vehicle-empty h3 {

    margin:
        0 0 9px;

    color: #26343e;

    font-size: 22px;

    font-weight: 800;
}


.vehicle-empty p {

    margin:
        0 0 24px;

    color: #71808a;

    font-size: 14px;
}


.empty-add-btn {

    display: inline-flex;

    align-items: center;
    justify-content: center;

    min-height: 45px;

    padding:
        0 22px;

    border-radius: 8px;

    background: #ffc107;

    color: #17202a;

    font-size: 13px;

    font-weight: 750;
}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 1000px) {

    .vehicle-grid {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 720px) {

    .app-header {
        height: 72px;
        padding: 0 18px;
    }

    .app-header .logo {
        width: 46px !important;
        height: 46px !important;
    }

    .app-header .header-left h1 {
        font-size: 22px !important;
    }

    .app-header .divider {
        margin: 0 11px;
        font-size: 22px;
    }

    .app-header .app-nav {
        margin-right: 14px;
    }

    .app-header .app-nav a {
        font-size: 13px !important;
        padding: 0 11px;
    }

    .app-header .profile-icon-new {
        width: 42px !important;
        height: 42px !important;
    }


    .vehicles-page {

        width:
            calc(100% - 30px);

        padding:
            28px 0 60px;
    }


    .garage-hero {

        align-items:
            flex-start;

        flex-direction:
            column;

        padding:
            30px 26px;
    }


    .hero-content h1 {

        font-size: 36px;
    }


    .hero-content p {

        font-size: 15px;
    }


    .hero-add-btn {

        width: 100%;
    }


    .garage-stats {

        grid-template-columns:
            1fr;

        gap: 12px;
    }


    .vehicle-grid {

        grid-template-columns:
            1fr;
    }


    .vehicle-image {

        height: 210px;
        min-height: 210px;
    }


    .vehicle-image img {

        width: 90%;
        max-width: 250px;
        height: 140px;
        max-height: 140px;
        object-fit: contain;
        object-position: center;
    }


    .add-vehicle-card {

        min-height: 270px;
    }

}


/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width: 450px) {

    .app-header {
        padding: 0 13px;
    }

    .app-header .logo {
        width: 42px !important;
        height: 42px !important;
    }

    .app-header .header-left h1 {
        font-size: 20px !important;
    }

    .app-header .divider {
        margin: 0 8px;
    }

    .app-header .app-nav a {
        font-size: 12px !important;
        padding: 0 8px;
    }

    .app-header .profile-icon-new {
        width: 39px !important;
        height: 39px !important;
    }


    .vehicles-page {

        width:
            calc(100% - 20px);
    }


    .garage-hero {

        padding:
            26px 21px;
    }


    .hero-content h1 {

        font-size: 31px;
    }


    .garage-section-title h2 {

        font-size: 23px;
    }


    .vehicle-actions {

        grid-template-columns:
            1fr;
    }


    .vehicle-name {

        font-size: 20px;
    }

}

</style>

</head>


<body>


<!-- =====================================================
     SAME APPLICATION HEADER
===================================================== -->

<?php include("includes/header_app.php"); ?>


<main class="vehicles-page">


<!-- =====================================================
     GARAGE HERO
===================================================== -->

<section class="garage-hero">

    <div class="hero-content">

        <span class="hero-kicker">
            AutoCare Garage
        </span>

        <h1>
            My Vehicles
        </h1>

        <p>
            Everything about your vehicles, organized in one place.
            Keep track of mileage, servicing and maintenance.
        </p>

    </div>


    <a
        href="add_vehicle.php"
        class="hero-add-btn"
    >
        Add Vehicle
    </a>

</section>


<!-- =====================================================
     STATISTICS
===================================================== -->

<section class="garage-stats">

    <!-- 1st: NEXT SERVICE -->
    <div class="stat-card <?= $next_service_status_class ?>">

        <span class="stat-label">
            Next Service
        </span>

        <span class="stat-main-date">
            <?= htmlspecialchars($next_service_display) ?>
        </span>

        <?php if (!empty($nearest_next_service_car)): ?>
        <span class="stat-car-name">
            <?= htmlspecialchars($nearest_next_service_car) ?>
        </span>
        <?php endif; ?>

    </div>


    <!-- 2nd: DOCUMENT EXPIRED -->
    <div class="stat-card <?= $doc_status_class ?>">

        <span class="stat-label">
            Document Expired
        </span>

        <span class="stat-number">
            <?= $expired_docs_count ?>
        </span>

    </div>


    <!-- 3rd: UPCOMING SERVICES -->
    <div class="stat-card <?= $upcoming_status_class ?>">

        <span class="stat-label">
            Upcoming Services
        </span>

        <span class="stat-number">
            <?= $upcoming_count ?>
        </span>

    </div>

</section>


<!-- =====================================================
     GARAGE TITLE
===================================================== -->

<div class="garage-section-title">

    <h2>
        Your Garage
    </h2>


    <?php if (!empty($vehicles)) { ?>

        <span class="garage-count">

            <?= count($vehicles) ?>

            <?= count($vehicles) == 1
                ? "Vehicle"
                : "Vehicles"
            ?>

        </span>

    <?php } ?>

</div>


<?php if (empty($vehicles)) { ?>


<!-- =====================================================
     EMPTY GARAGE
===================================================== -->

<div class="vehicle-empty">

    <img
        src="assets/images/car-bg.png"
        alt=""
    >

    <h3>
        Your garage is empty
    </h3>

    <p>
        Add your first vehicle and start managing
        its maintenance with AutoCare.
    </p>

    <a
        href="add_vehicle.php"
        class="empty-add-btn"
    >
        Add Your First Vehicle
    </a>

</div>


<?php } else { ?>


<!-- =====================================================
     VEHICLE GRID
===================================================== -->

<div class="vehicle-grid">


<?php foreach ($vehicles as $v) {


    $kms = (int)$v['kms'];


    $last_service_display =
        !empty($v['last_service'])
        ? (new DateTime($v['last_service']))
            ->format("d M Y")
        : "Not recorded";


    $vehicle_image = get_vehicle_image(
        $v['company'],
        $v['model'],
        $v['fuel'] ?? ''
    );


    $vehicle_name =
        $v['company'] . " " . $v['model'];


    /* Vehicle type badge */
    $c_lower = strtolower($v['company'] . ' ' . $v['model']);
    $f_lower = strtolower($v['fuel'] ?? '');

    if (
        strpos($f_lower, 'electric') !== false ||
        strpos($f_lower, 'ev') !== false ||
        strpos($c_lower, 'ather') !== false ||
        strpos($c_lower, 'ola') !== false
    ) {
        $vehicle_type = "ELECTRIC";
    } elseif (
        strpos($c_lower, 'royal') !== false ||
        strpos($c_lower, 'yamaha') !== false ||
        strpos($c_lower, 'ktm') !== false ||
        strpos($c_lower, 'bajaj') !== false ||
        strpos($c_lower, 'tvs') !== false ||
        strpos($c_lower, 'hero') !== false
    ) {
        $vehicle_type = "TWO-WHEELER";
    } elseif (
        strpos($c_lower, 'thar') !== false ||
        strpos($c_lower, 'fortuner') !== false ||
        strpos($c_lower, 'scorpio') !== false ||
        strpos($c_lower, 'creta') !== false ||
        strpos($c_lower, 'nexon') !== false ||
        strpos($c_lower, 'suv') !== false
    ) {
        $vehicle_type = "SUV";
    } else {
        $vehicle_type = "CAR";
    }

?>


<!-- =====================================================
     VEHICLE CARD
===================================================== -->

<article class="vehicle-card">


    <!-- VEHICLE IMAGE -->

    <div class="vehicle-image">

        <span class="vehicle-badge">

            <?= htmlspecialchars(
                $vehicle_type
            ) ?>

        </span>


        <img
            src="assets/images/<?= htmlspecialchars($vehicle_image) ?>"
            alt="<?= htmlspecialchars($vehicle_name) ?>"
        >

    </div>


    <!-- VEHICLE CONTENT -->

    <div class="vehicle-body">


        <h3 class="vehicle-name">

            <?= htmlspecialchars(
                $vehicle_name
            ) ?>

        </h3>


        <span class="vehicle-plate">

            <?= htmlspecialchars(
                $v['license_no']
            ) ?>

        </span>


        <!-- DETAILS -->

        <div class="vehicle-details">


            <div>

                <span class="detail-label">
                    Odometer
                </span>

                <span class="detail-value">

                    <?= number_format($kms) ?> km

                </span>

            </div>


            <div>

                <span class="detail-label">
                    Last Service
                </span>

                <span class="detail-value">

                    <?= htmlspecialchars(
                        $last_service_display
                    ) ?>

                </span>

            </div>


            <div>

                <span class="detail-label">
                    Next Service
                </span>

                <span class="detail-value">

                    <?= (new DateTime($v['last_service'] ?? 'now'))->modify('+6 months')->format('d M Y') ?> • <?= number_format(((int)($v['kms_last_service'] ?? 0)) + 5000) ?> km

                </span>

            </div>


        </div>


        <!-- STATUS -->

        <div class="service-status">

            <span class="status-dot"></span>

            Maintenance tracking active

        </div>


        <!-- ACTIONS -->

        <div class="vehicle-actions">


            <a
                href="vehicle_details.php?id=<?= (int)$v['id'] ?>"
                class="vehicle-btn vehicle-btn-outline"
            >
                View Details
            </a>


            <a
                href="book_service.php?id=<?= (int)$v['id'] ?>"
                class="vehicle-btn vehicle-btn-primary"
            >
                Book Service
            </a>


        </div>


        <!-- DELETE -->

        <a
            href="vehicles.php?delete=<?= (int)$v['id'] ?>"
            class="vehicle-delete-btn"
            onclick="return confirm('Are you sure you want to delete this vehicle? This action cannot be undone.');"
        >
            Delete Vehicle
        </a>


    </div>

</article>


<?php } ?>


<!-- =====================================================
     ADD VEHICLE CARD
===================================================== -->

<div
    class="vehicle-card add-vehicle-card"
    onclick="window.location.href='add_vehicle.php'"
>


    <div class="add-icon">
        +
    </div>


    <h3>
        Add Another Vehicle
    </h3>


    <p>
        Add another car or two-wheeler
        to your AutoCare garage.
    </p>


</div>


</div>


<?php } ?>


</main>


<?php include("includes/footer.php"); ?>


</body>

</html>