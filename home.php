<?php

require_once("includes/auth.php");

$logged_in = isset($_SESSION['user']);
$dashboard_link = $logged_in ? "vehicles.php" : "index.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>AutoCare | Smart Vehicle Care</title>

<style>

/* =========================================================
   RESET
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
    font-family: Arial, Helvetica, sans-serif;
    background: #f5f7f9;
    color: #102333;
    overflow-x: hidden;
}

button,
input {
    font-family: inherit;
}

a {
    text-decoration: none;
}


/* =========================================================
   DARK APPLICATION HEADER
========================================================= */

.app-header {
    width: 100%;
    height: 78px;

    background: #0d1820;

    border-bottom: 1px solid rgba(255,255,255,.07);

    display: flex;
    align-items: center;

    padding: 0 42px;

    position: sticky;
    top: 0;
    z-index: 1000;

    box-shadow:
        0 5px 25px rgba(0,0,0,.14);
}


/* Header inner alignment */

.app-header.header-content {
    max-width: none;
}


/* =========================================================
   HEADER LEFT
========================================================= */

.app-header .header-left {
    display: flex;
    align-items: center;

    height: 100%;

    cursor: pointer;

    flex-shrink: 0;
}


/* Logo */

.app-header .logo {
    width: 54px;
    height: 54px;

    object-fit: contain;

    display: block;

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

    font-size: 25px;

    font-weight: 800;

    letter-spacing: -.7px;
}

.app-header .header-left h1 span {
    color: #ffbf00;
}


/* =========================================================
   HEADER NAVIGATION
========================================================= */

.app-header .app-nav {
    display: flex;
    align-items: center;

    gap: 8px;

    margin-left: auto;
    margin-right: 30px;

    height: 100%;
}

.app-header .app-nav a {
    position: relative;

    display: flex;
    align-items: center;

    height: 44px;

    padding: 0 17px;

    color: #aebbc3;

    font-size: 14px;
    font-weight: 700;

    border-radius: 8px;

    transition:
        color .2s ease,
        background .2s ease;
}

.app-header .app-nav a:hover {
    color: #ffffff;

    background: rgba(255,255,255,.06);
}

.app-header .app-nav a.active {
    color: #ffffff;

    background: rgba(255,191,0,.10);
}

.app-header .app-nav a.active::after {
    content: "";

    position: absolute;

    left: 17px;
    right: 17px;

    bottom: 3px;

    height: 2px;

    background: #ffbf00;

    border-radius: 10px;
}


/* =========================================================
   PROFILE
========================================================= */

.app-header .header-right {
    position: relative;

    display: flex;
    align-items: center;

    flex-shrink: 0;
}

.app-header .profile-icon-new {
    width: 44px;
    height: 44px;

    object-fit: contain;

    border-radius: 50%;

    cursor: pointer;

    padding: 3px;

    border: 1px solid rgba(255,255,255,.12);

    background: rgba(255,255,255,.04);

    transition:
        transform .2s ease,
        background .2s ease,
        border-color .2s ease;
}

.app-header .profile-icon-new:hover {
    transform: translateY(-1px);

    background: rgba(255,191,0,.10);

    border-color: rgba(255,191,0,.35);
}


/* =========================================================
   PROFILE DROPDOWN
========================================================= */

.app-header .dropdown-new {
    display: none;

    position: absolute;

    top: 58px;
    right: 0;

    width: 210px;

    background: #ffffff;

    border: 1px solid #e1e6e9;

    border-radius: 13px;

    padding: 8px;

    box-shadow:
        0 18px 40px rgba(10,25,35,.18);

    z-index: 2000;
}

.app-header .dropdown-username {
    padding: 13px 13px 11px;

    color: #10202a;

    font-size: 14px;

    font-weight: 800;

    border-bottom: 1px solid #edf0f2;

    margin-bottom: 5px;
}

.app-header .dropdown-new a {
    display: flex;
    align-items: center;

    gap: 9px;

    width: 100%;

    padding: 11px 12px;

    color: #34444e;

    font-size: 13px;

    font-weight: 600;

    border-radius: 8px;

    transition: .2s;
}

.app-header .dropdown-new a:hover {
    background: #f5f7f9;

    color: #10202a;
}

.app-header .dropdown-new a img {
    width: 18px;
    height: 18px;

    object-fit: contain;
}


/* =========================================================
   HERO
========================================================= */

.hero {
    max-width: 1380px;

    margin: 34px auto 0;

    padding: 0 28px;
}

.hero-box {
    min-height: 450px;

    background: #10202a;

    border-radius: 26px;

    position: relative;

    overflow: hidden;

    display: grid;

    grid-template-columns: 1.05fr .95fr;

    box-shadow:
        0 18px 45px rgba(10,25,35,.14);
}


/* Decorative circle */

.hero-box::before {
    content: "";

    position: absolute;

    width: 440px;
    height: 440px;

    border: 1px solid rgba(255,191,0,.14);

    border-radius: 50%;

    right: -150px;
    top: -190px;
}

.hero-box::after {
    content: "";

    position: absolute;

    width: 300px;
    height: 300px;

    border: 1px solid rgba(255,191,0,.08);

    border-radius: 50%;

    left: -160px;
    bottom: -180px;
}


/* =========================================================
   HERO CONTENT
========================================================= */

.hero-content {
    position: relative;

    z-index: 5;

    display: flex;

    justify-content: center;

    flex-direction: column;

    padding: 55px 30px 55px 65px;
}

.hero-kicker {
    color: #ffbf00;

    font-size: 13px;

    font-weight: 800;

    letter-spacing: 2px;

    text-transform: uppercase;

    margin-bottom: 15px;
}

.hero-content h1 {
    margin: 0 0 18px;

    color: white;

    font-size: clamp(40px, 4vw, 58px);

    line-height: 1.04;

    letter-spacing: -2px;

    max-width: 650px;
}

.hero-content h1 span {
    color: #ffbf00;
}

.hero-content p {
    margin: 0 0 28px;

    color: #aebbc3;

    font-size: 16px;

    line-height: 1.7;

    max-width: 560px;
}


/* =========================================================
   PRIMARY BUTTON
========================================================= */

.primary-btn {
    border: none;

    background: #ffbd08;

    color: #101c23;

    padding: 14px 22px;

    border-radius: 10px;

    font-size: 14px;

    font-weight: 800;

    cursor: pointer;

    width: fit-content;

    box-shadow:
        0 7px 18px rgba(255,189,8,.18);

    transition:
        transform .2s ease,
        background .2s ease,
        box-shadow .2s ease;
}

.primary-btn span {
    margin-left: 8px;

    font-size: 17px;
}

.primary-btn:hover {
    background: #ffca35;

    transform: translateY(-2px);

    box-shadow:
        0 10px 24px rgba(255,189,8,.24);
}


/* =========================================================
   HERO VEHICLE VISUAL
========================================================= */

.hero-visual {
    position: relative;

    z-index: 4;

    display: flex;

    align-items: center;

    justify-content: center;

    min-height: 450px;

    padding: 25px;
}

.vehicle-display {
    width: 390px;
    height: 390px;

    border-radius: 50%;

    position: relative;

    display: flex;

    align-items: center;

    justify-content: center;

    background:
        radial-gradient(
            circle,
            rgba(255,191,0,.15) 0%,
            rgba(255,191,0,.06) 43%,
            transparent 70%
        );
}

.vehicle-display::before {
    content: "";

    position: absolute;

    width: 310px;
    height: 310px;

    border: 1px solid rgba(255,191,0,.20);

    border-radius: 50%;
}

.vehicle-display::after {
    content: "";

    position: absolute;

    width: 250px;
    height: 250px;

    border: 1px dashed rgba(255,255,255,.10);

    border-radius: 50%;
}

/* =========================================================
   SIMPLE SERVICE DASHBOARD VISUAL
========================================================= */

.service-illustration {
    position: relative;

    width: 300px;
    height: 300px;

    z-index: 5;

    display: flex;

    align-items: center;
    justify-content: center;

    animation: serviceFloat 4s ease-in-out infinite;
}

@keyframes serviceFloat {
    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-7px);
    }
}


/* MAIN CARD */

.service-card {
    position: relative;

    width: 210px;
    height: 160px;

    padding: 24px;

    background: #172831;

    border: 1px solid rgba(255,255,255,.16);

    border-radius: 20px;

    box-shadow:
        0 25px 45px rgba(255, 248, 248, 0.3);

    box-sizing: border-box;
}


/* TOP ICON */

.service-icon {
    width: 52px;
    height: 52px;

    margin-bottom: 18px;

    background: #ffbf00;

    border-radius: 50%;

    display: flex;

    align-items: center;
    justify-content: center;

    box-shadow:
        0 0 25px rgba(255,191,0,.25);
}


/* CHECKMARK */

.service-icon::after {
    content: "✓";

    color: #10202a;

    font-size: 30px;

    font-weight: 900;

    line-height: 1;
}


/* TEXT LINES */

.service-line {
    width: 125px;
    height: 8px;

    margin-bottom: 10px;

    background: #71818a;

    border-radius: 20px;

    opacity: .8;
}

.service-line.short {
    width: 85px;

    background: #ffbf00;
}


/* SERVICE ITEMS */

.service-items {
    position: absolute;

    left: 24px;
    right: 24px;

    bottom: 22px;

    display: flex;

    gap: 8px;
}

.service-dot {
    width: 8px;
    height: 8px;

    background: #ffbf00;

    border-radius: 50%;
}

.service-dot.gray {
    background: #71818a;
}


/* SMALL FLOATING CARDS */

.service-badge {
    position: absolute;

    padding: 10px 13px;

    background: rgba(255,255,255,.08);

    border: 1px solid rgba(255,255,255,.14);

    border-radius: 10px;

    color: white;

    font-size: 11px;

    font-weight: 700;

    box-shadow:
        0 10px 25px rgba(0,0,0,.18);

    backdrop-filter: blur(10px);
}

.service-badge.top {
    right: -45px;
    top: 30px;
}

.service-badge.bottom {
    left: -45px;
    bottom: 28px;
}

.service-badge span {
    color: #ffbf00;

    margin-left: 4px;
}


/* =========================================================
   VEHICLE STATUS
========================================================= */

.vehicle-status,
.service-status {
    position: absolute;

    z-index: 15;

    background: rgba(255,255,255,.08);

    border: 1px solid rgba(255,255,255,.14);

    backdrop-filter: blur(10px);

    padding: 13px 16px;

    border-radius: 12px;

    color: white;

    box-shadow:
        0 12px 25px rgba(0,0,0,.18);
}

.vehicle-status {
    right: 5px;
    top: 60px;
}

.service-status {
    left: 0;
    bottom: 58px;
}

.vehicle-status strong,
.service-status strong {
    display: block;

    font-size: 13px;

    margin-bottom: 4px;
}

.vehicle-status span {
    color: #ffbf00;

    font-size: 11px;

    font-weight: 700;
}

.service-status span {
    color: #aebbc3;

    font-size: 11px;
}


/* =========================================================
   GENERAL SECTIONS
========================================================= */

.home-section {
    max-width: 1380px;

    margin: 75px auto;

    padding: 0 28px;
}

.section-heading {
    margin-bottom: 35px;
}

.section-heading span {
    display: block;

    color: #b17d00;

    font-size: 12px;

    font-weight: 800;

    letter-spacing: 2px;

    margin-bottom: 9px;
}

.section-heading h2 {
    margin: 0 0 9px;

    color: #112532;

    font-size: 31px;

    letter-spacing: -1px;
}

.section-heading p {
    margin: 0;

    color: #80909a;

    font-size: 15px;
}


/* =========================================================
   FEATURES
========================================================= */

.features-grid {
    display: grid;

    grid-template-columns: repeat(4, 1fr);

    gap: 16px;
}

.feature-card {
    background: white;

    border: 1px solid #e2e7ea;

    border-radius: 17px;

    padding: 25px;

    min-height: 190px;

    transition: .25s;

    box-shadow:
        0 8px 25px rgba(20,40,50,.035);
}

.feature-card:hover {
    transform: translateY(-4px);

    box-shadow:
        0 14px 30px rgba(20,40,50,.08);
}

.feature-number {
    width: 40px;
    height: 40px;

    border-radius: 10px;

    background: #fff4d4;

    color: #a67400;

    display: flex;

    align-items: center;
    justify-content: center;

    font-size: 12px;

    font-weight: 800;

    margin-bottom: 22px;
}

.feature-card h3 {
    margin: 0 0 9px;

    font-size: 17px;

    color: #142a37;
}

.feature-card p {
    margin: 0;

    color: #7d8b94;

    font-size: 14px;

    line-height: 1.65;
}


/* =========================================================
   INFO STRIP
========================================================= */

.info-strip {
    max-width: 1380px;

    margin: 0 auto;

    padding: 0 28px;
}

.info-strip-inner {
    background: #10202a;

    border-radius: 19px;

    display: grid;

    grid-template-columns: repeat(3, 1fr);

    overflow: hidden;
}

.info-item {
    padding: 24px 28px;

    display: flex;

    align-items: center;

    gap: 15px;

    border-right:
        1px solid rgba(255,255,255,.08);
}

.info-item:last-child {
    border-right: none;
}

.info-check {
    width: 38px;
    height: 38px;

    flex-shrink: 0;

    border-radius: 50%;

    background:
        rgba(255,191,0,.12);

    color: #ffbf00;

    display: flex;

    align-items: center;
    justify-content: center;

    font-weight: 900;

    font-size: 15px;
}

.info-item h4 {
    color: white;

    margin: 0 0 4px;

    font-size: 14px;
}

.info-item p {
    color: #81919b;

    margin: 0;

    font-size: 12px;
}


/* =========================================================
   HOW IT WORKS
========================================================= */

.steps {
    margin-top: 80px;
}

.steps-grid {
    display: grid;

    grid-template-columns: repeat(4, 1fr);

    gap: 15px;
}

.step-card {
    background: white;

    border: 1px solid #e2e7ea;

    border-radius: 17px;

    padding: 26px;

    position: relative;

    min-height: 150px;
}

.step-number {
    color: #ffb800;

    font-size: 12px;

    font-weight: 900;

    margin-bottom: 23px;
}

.step-card h3 {
    margin: 0 0 7px;

    font-size: 16px;

    color: #142a37;
}

.step-card p {
    margin: 0;

    color: #81909a;

    font-size: 13px;

    line-height: 1.55;
}


/* =========================================================
   CTA
========================================================= */

.cta-wrapper {
    max-width: 1380px;

    margin: 80px auto 60px;

    padding: 0 28px;
}

.cta {
    background: #10202a;

    border-radius: 22px;

    padding: 42px 48px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 30px;

    position: relative;

    overflow: hidden;
}

.cta::after {
    content: "";

    position: absolute;

    width: 260px;
    height: 260px;

    border: 1px solid rgba(255,191,0,.12);

    border-radius: 50%;

    right: -90px;
    bottom: -170px;
}

.cta-content {
    position: relative;

    z-index: 2;
}

.cta-label {
    color: #ffbf00;

    font-size: 11px;

    font-weight: 800;

    letter-spacing: 2px;
}

.cta h2 {
    color: white;

    margin: 8px 0;

    font-size: 28px;

    letter-spacing: -.7px;
}

.cta p {
    color: #9caab2;

    margin: 0;

    font-size: 14px;
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
   RESPONSIVE — TABLET
========================================================= */

@media(max-width: 1050px) {

    .app-header {
        padding: 0 25px;
    }

    .app-header .app-nav {
        margin-right: 20px;
    }

    .app-header .app-nav a {
        padding: 0 12px;
    }

    .hero-box {
        grid-template-columns: 1fr .85fr;
    }

    .hero-content {
        padding-left: 45px;
    }

    .features-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .steps-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}


/* =========================================================
   RESPONSIVE — MOBILE
========================================================= */

@media(max-width: 800px) {

    .app-header {
        height: auto;

        min-height: 72px;

        padding: 12px 18px;

        flex-wrap: wrap;
    }

    .app-header .logo {
        width: 46px;
        height: 46px;
    }

    .app-header .divider {
        margin: 0 10px;

        font-size: 21px;
    }

    .app-header .header-left h1 {
        font-size: 21px;
    }

    .app-header .app-nav {
        order: 3;

        width: 100%;

        height: 42px;

        margin: 8px 0 0;

        justify-content: center;
    }

    .app-header .app-nav a {
        height: 38px;

        font-size: 12px;

        padding: 0 13px;
    }

    .app-header .profile-icon-new {
        width: 40px;
        height: 40px;
    }

    .hero,
    .home-section,
    .info-strip,
    .cta-wrapper {
        padding-left: 15px;
        padding-right: 15px;
    }

    .hero-box {
        grid-template-columns: 1fr;

        min-height: auto;
    }

    .hero-content {
        padding: 45px 30px 20px;

        text-align: center;

        align-items: center;
    }

    .hero-content h1 {
        font-size: 39px;
    }

    .hero-content p {
        font-size: 14px;
    }

    .hero-visual {
        min-height: 360px;

        padding-top: 5px;
    }

    .vehicle-display {
        transform: scale(.86);
    }

    .info-strip-inner {
        grid-template-columns: 1fr;
    }

    .info-item {
        border-right: none;

        border-bottom:
            1px solid rgba(255,255,255,.08);
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .cta {
        flex-direction: column;

        align-items: flex-start;

        padding: 35px 28px;
    }
}


/* =========================================================
   RESPONSIVE — SMALL MOBILE
========================================================= */

@media(max-width: 520px) {

    .app-header {
        padding: 10px 14px;
    }

    .app-header .header-left h1 {
        font-size: 19px;
    }

    .app-header .logo {
        width: 42px;
        height: 42px;
    }

    .app-header .profile-icon-new {
        width: 38px;
        height: 38px;
    }

    .app-header .app-nav a {
        font-size: 11px;

        padding: 0 10px;
    }

    .hero-content {
        padding: 38px 22px 15px;
    }

    .hero-content h1 {
        font-size: 33px;

        letter-spacing: -1.2px;
    }

    .hero-visual {
        min-height: 300px;
    }

    .vehicle-display {
        transform: scale(.68);
    }

    .vehicle-status {
        right: 0;
    }

    .service-status {
        left: 0;
    }

    .features-grid,
    .steps-grid {
        grid-template-columns: 1fr;
    }

    .section-heading h2 {
        font-size: 26px;
    }

    .cta h2 {
        font-size: 23px;
    }
}

</style>

</head>


<body>


<!-- =====================================================
     DARK HEADER
===================================================== -->

<?php include("includes/header_app.php"); ?>


<!-- =====================================================
     HERO
===================================================== -->

<section class="hero">

    <div class="hero-box">

        <div class="hero-content">

            <div class="hero-kicker">
                SMARTER VEHICLE CARE
            </div>

            <h1>
                Everything your
                <span>vehicle</span>
                needs, in one place.
            </h1>

            <p>
                Manage your vehicles, schedule services,
                track bookings and keep important records
                organized through one simple dashboard.
            </p>

            <button
                class="primary-btn"
                onclick="window.location.href='<?= $dashboard_link ?>'"
            >
                <?= $logged_in
                    ? 'Open My Vehicles'
                    : 'Get Started'
                ?>

                <span>→</span>
            </button>

        </div>


        <!-- VEHICLE VISUAL -->

        <div class="hero-visual">

            <div class="vehicle-display">

  <div class="service-illustration">

    <div class="service-card">

        <div class="service-icon"></div>

        <div class="service-line"></div>

        <div class="service-line short"></div>

        <div class="service-items">

            <div class="service-dot"></div>
            <div class="service-dot"></div>
            <div class="service-dot gray"></div>

        </div>

    </div>


    <div class="service-badge top">
        Health
        <span>✓</span>
    </div>


    <div class="service-badge bottom">
        Service
        <span>✓</span>
    </div>

</div>


                <div class="vehicle-status">

                    <strong>
                        Vehicle Health
                    </strong>

                    <span>
                        ● Good Condition
                    </span>

                </div>


                <div class="service-status">

                    <strong>
                        Service Tracking
                    </strong>

                    <span>
                        Next service · 24 Aug
                    </span>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     WHY AUTOCARE
===================================================== -->

<section class="home-section">

    <div class="section-heading">

        <span>
            WHY AUTOCARE
        </span>

        <h2>
            Built to make vehicle care simpler.
        </h2>

        <p>
            Everything important is easy to find,
            manage and update.
        </p>

    </div>


    <div class="features-grid">


        <div class="feature-card">

            <div class="feature-number">
                01
            </div>

            <h3>
                Manage Vehicles
            </h3>

            <p>
                Keep your vehicle details,
                specifications and ownership
                information organized in one place.
            </p>

        </div>


        <div class="feature-card">

            <div class="feature-number">
                02
            </div>

            <h3>
                Book Services
            </h3>

            <p>
                Choose the service your vehicle needs
                and schedule an appointment without
                unnecessary hassle.
            </p>

        </div>


        <div class="feature-card">

            <div class="feature-number">
                03
            </div>

            <h3>
                Track Bookings
            </h3>

            <p>
                View upcoming and previous service
                bookings clearly from your account.
            </p>

        </div>


        <div class="feature-card">

            <div class="feature-number">
                04
            </div>

            <h3>
                Keep Records
            </h3>

            <p>
                Keep important vehicle information
                and documents available whenever
                you need them.
            </p>

        </div>

    </div>

</section>


<!-- =====================================================
     INFO STRIP
===================================================== -->

<section class="info-strip">

    <div class="info-strip-inner">

        <div class="info-item">

            <div class="info-check">
                ✓
            </div>

            <div>

                <h4>
                    Simple
                </h4>

                <p>
                    Intuitive vehicle management
                </p>

            </div>

        </div>


        <div class="info-item">

            <div class="info-check">
                ✓
            </div>

            <div>

                <h4>
                    Organized
                </h4>

                <p>
                    Important records together
                </p>

            </div>

        </div>


        <div class="info-item">

            <div class="info-check">
                ✓
            </div>

            <div>

                <h4>
                    Convenient
                </h4>

                <p>
                    Book and track services
                </p>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     HOW IT WORKS
===================================================== -->

<section class="home-section steps">

    <div class="section-heading">

        <span>
            HOW IT WORKS
        </span>

        <h2>
            Four simple steps.
        </h2>

        <p>
            Get your vehicle organized without
            unnecessary complexity.
        </p>

    </div>


    <div class="steps-grid">


        <div class="step-card">

            <div class="step-number">
                STEP 01
            </div>

            <h3>
                Add your vehicle
            </h3>

            <p>
                Enter your vehicle details
                and create its profile.
            </p>

        </div>


        <div class="step-card">

            <div class="step-number">
                STEP 02
            </div>

            <h3>
                Choose a service
            </h3>

            <p>
                Select the maintenance or
                service your vehicle requires.
            </p>

        </div>


        <div class="step-card">

            <div class="step-number">
                STEP 03
            </div>

            <h3>
                Book your slot
            </h3>

            <p>
                Select a convenient date
                and book your service.
            </p>

        </div>


        <div class="step-card">

            <div class="step-number">
                STEP 04
            </div>

            <h3>
                Track your booking
            </h3>

            <p>
                Keep track of your service
                appointment from your dashboard.
            </p>

        </div>

    </div>

</section>


<!-- =====================================================
     CTA
===================================================== -->

<section class="cta-wrapper">

    <div class="cta">

        <div class="cta-content">

            <div class="cta-label">
                AUTOCARE
            </div>

            <h2>
                Take better care of every drive.
            </h2>

            <p>
                Manage your vehicle from one
                professional, easy-to-use dashboard.
            </p>

        </div>


        <button
            class="primary-btn"
            onclick="window.location.href='<?= $dashboard_link ?>'"
        >

            <?= $logged_in
                ? 'Go to My Vehicles'
                : 'Get Started'
            ?>

            <span>→</span>

        </button>

    </div>

</section>


<!-- =====================================================
     FOOTER
===================================================== -->

<?php include("includes/footer.php"); ?>


</body>

</html>