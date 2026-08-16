
<?php

require_once("includes/db.php");
require_once("includes/auth.php");
require_once("includes/vehicle_image.php");
require_login();

$user = current_username();
$ACTIVE_NAV = 'bookings';


$stmt = $conn->prepare("
    SELECT b.*, v.company, v.model, v.license_no
    FROM bookings b
    LEFT JOIN vehicles v ON b.vehicle_id = v.id
    WHERE b.user = ?
    ORDER BY b.service_date DESC, b.id DESC
");

$stmt->bind_param("s", $user);
$stmt->execute();

$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Bookings | AutoCare</title>

<link rel="stylesheet" href="assets/css/style.css">


<style>

/* =========================================================
   AUTOCARE BOOKINGS PAGE
========================================================= */

body {
    background: #f5f7f9;
}


/* =========================================================
   PAGE WRAPPER
========================================================= */

.bookings-page {
    max-width: 1250px;

    margin: 0 auto;

    padding: 45px 30px 70px;
}


/* =========================================================
   PAGE HEADER
========================================================= */

.bookings-header {
    display: flex;

    align-items: flex-end;

    justify-content: space-between;

    gap: 20px;

    margin-bottom: 30px;
}

.bookings-heading span {
    display: block;

    color: #b17d00;

    font-size: 11px;

    font-weight: 800;

    letter-spacing: 2px;

    margin-bottom: 8px;
}

.bookings-heading h1 {
    margin: 0;

    color: #112532;

    font-size: 32px;

    letter-spacing: -1px;
}

.bookings-heading p {
    margin: 8px 0 0;

    color: #81909a;

    font-size: 13px;
}


/* =========================================================
   BOOKING COUNT
========================================================= */

.booking-count {
    background: #10202a;

    color: white;

    border-radius: 12px;

    padding: 12px 20px;

    font-size: 14px;

    font-weight: 700;

    white-space: nowrap;

    display: inline-flex;

    align-items: center;

    gap: 6px;
}

.booking-count strong {
    color: #ffc107;

    font-size: 18px;

    font-weight: 800;
}


/* =========================================================
   EMPTY STATE
========================================================= */

.empty-state {
    background: white;

    border: 1px solid #e2e7ea;

    border-radius: 20px;

    padding: 65px 30px;

    text-align: center;

    box-shadow:
        0 10px 30px rgba(20,40,50,.045);
}

.empty-icon {
    width: 65px;
    height: 65px;

    margin: 0 auto 20px;

    border-radius: 18px;

    background: #fff4d4;

    color: #a67400;

    display: flex;

    align-items: center;
    justify-content: center;

    font-size: 28px;

    font-weight: 800;
}

.empty-state h3 {
    margin: 0 0 8px;

    color: #142a37;

    font-size: 20px;
}

.empty-state p {
    margin: 0 auto 25px;

    color: #81909a;

    font-size: 13px;

    max-width: 420px;

    line-height: 1.6;
}


/* =========================================================
   BOOKINGS LIST
========================================================= */

.bookings-list {
    display: flex;

    flex-direction: column;

    gap: 15px;
}


/* =========================================================
   BOOKING CARD
========================================================= */

.booking-card {
    background: white;

    border: 1px solid #e1e7ea;

    border-radius: 18px;

    padding: 22px 24px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 25px;

    position: relative;

    overflow: hidden;

    box-shadow:
        0 8px 25px rgba(20,40,50,.035);

    transition:
        transform .2s ease,
        box-shadow .2s ease,
        border-color .2s ease;
}

.booking-card:hover {
    transform: translateY(-3px);

    border-color: #d8dee1;

    box-shadow:
        0 15px 32px rgba(20,40,50,.08);
}


/* Yellow accent */

.booking-card::before {
    content: "";

    position: absolute;

    left: 0;
    top: 0;
    bottom: 0;

    width: 4px;

    background: #ffbf00;
}


/* =========================================================
   MAIN CONTENT
========================================================= */

.booking-card-main {
    min-width: 0;

    flex: 1;
}


/* =========================================================
   TOP ROW
========================================================= */

.booking-card-top {
    display: flex;

    align-items: center;

    gap: 9px;

    margin-bottom: 10px;
}


/* Booking ID */

.booking-id-tag {
    display: inline-flex;

    align-items: center;

    justify-content: center;

    background: #f1f4f5;

    color: #63747d;

    border-radius: 7px;

    padding: 5px 9px;

    font-size: 10px;

    font-weight: 800;

    letter-spacing: .5px;
}


/* =========================================================
   STATUS
========================================================= */

.booking-status-pill {
    display: inline-flex;

    align-items: center;

    gap: 5px;

    border-radius: 20px;

    padding: 5px 10px;

    font-size: 10px;

    font-weight: 800;

    text-transform: capitalize;
}

.booking-status-pill::before {
    content: "";

    width: 6px;
    height: 6px;

    border-radius: 50%;
}


/* Confirmed */

.status-good {
    background: #eaf8ef;

    color: #247544;
}

.status-good::before {
    background: #36a45c;
}


/* Cancelled */

.status-bad {
    background: #fcecec;

    color: #a73535;
}

.status-bad::before {
    background: #c94b4b;
}


/* Other */

.status-moderate {
    background: #fff6dd;

    color: #9a6b00;
}

.status-moderate::before {
    background: #e3aa00;
}


/* =========================================================
   VEHICLE NAME
========================================================= */

.booking-card h3 {
    margin: 0 0 5px;

    color: #142a37;

    font-size: 18px;

    letter-spacing: -.3px;
}


/* =========================================================
   NUMBER PLATE
========================================================= */

.vehicle-plate {
    display: inline-block;

    margin: 0 0 11px;

    padding: 4px 8px;

    background: #f5f6f7;

    border: 1px solid #e0e5e7;

    border-radius: 5px;

    color: #52646d;

    font-size: 10px;

    font-weight: 800;

    letter-spacing: 1px;
}


/* =========================================================
   BOOKING META
========================================================= */

.booking-meta {
    margin: 0;

    color: #667984;

    font-size: 12px;

    font-weight: 600;
}


/* =========================================================
   SERVICES
========================================================= */

.booking-services {
    margin: 10px 0 0;

    color: #89969d;

    font-size: 11px;

    line-height: 1.5;

    max-width: 700px;
}


/* =========================================================
   RIGHT SIDE
========================================================= */

.booking-card-side {
    min-width: 155px;

    display: flex;

    flex-direction: column;

    align-items: flex-end;

    justify-content: center;

    gap: 12px;

    padding-left: 20px;

    border-left:
        1px solid #edf0f1;
}


/* =========================================================
   AMOUNT
========================================================= */

.booking-amount {
    color: #000000;

    font-size: 21px;

    font-weight: 800;

    letter-spacing: -.5px;
}


/* =========================================================
   VIEW DETAILS BUTTON
========================================================= */

.ghost-btn.small {
    display: inline-flex;

    align-items: center;

    justify-content: center;

    padding: 9px 14px;

    border-radius: 9px;

    background: white;

    border: 1px solid #dce3e6;

    color: #263d49;

    font-size: 11px;

    font-weight: 800;

    transition: .2s;
}

.ghost-btn.small:hover {
    background: #10202a;

    border-color: #10202a;

    color: white;
}


/* =========================================================
   PRIMARY BUTTON
========================================================= */

.empty-state .primary-btn {
    display: inline-flex;

    align-items: center;

    justify-content: center;

    background: #ffbd08;

    color: #101c23;

    border: none;

    padding: 12px 20px;

    border-radius: 9px;

    font-size: 12px;

    font-weight: 800;

    transition: .2s;
}

.empty-state .primary-btn:hover {
    background: #ffca35;

    transform: translateY(-2px);
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media(max-width: 800px) {

    .bookings-page {
        padding:
            30px 18px 50px;
    }

    .bookings-header {
        align-items: flex-start;

        flex-direction: column;
    }

    .bookings-heading h1 {
        font-size: 28px;
    }

    .booking-card {
        align-items: stretch;

        flex-direction: column;

        gap: 18px;

        padding: 20px;
    }

    .booking-card-side {
        min-width: 0;

        width: 100%;

        flex-direction: row;

        align-items: center;

        justify-content: space-between;

        padding-left: 0;
        padding-top: 16px;

        border-left: none;

        border-top:
            1px solid #edf0f1;
    }

    .booking-amount {
        font-size: 18px;
    }

}


@media(max-width: 500px) {

    .bookings-page {
        padding:
            25px 14px 45px;
    }

    .bookings-heading h1 {
        font-size: 25px;
    }

    .booking-card {
        padding: 18px;

        border-radius: 15px;
    }

    .booking-card h3 {
        font-size: 16px;
    }

    .booking-card-top {
        flex-wrap: wrap;
    }

    .booking-meta {
        line-height: 1.6;
    }

    .booking-card-side {
        flex-wrap: wrap;
    }

}

</style>

</head>


<body>


<?php include("includes/header_app.php"); ?>


<!-- =====================================================
     BOOKINGS PAGE
===================================================== -->

<main class="bookings-page">


    <!-- PAGE HEADER -->

    <div class="bookings-header">

        <div class="bookings-heading">

            <span>
                AUTOCARE SERVICES
            </span>

            <h1>
                My Bookings
            </h1>

            <p>
                View and manage your vehicle service appointments.
            </p>

        </div>


        <div class="booking-count">

            <strong>
                <?= count($bookings) ?>
            </strong>

            <?= count($bookings) === 1 ? 'Booking' : 'Bookings' ?>

        </div>

    </div>



    <?php if (empty($bookings)) { ?>


        <!-- EMPTY STATE -->

        <div class="empty-state">

            <div class="empty-icon">
                +
            </div>

            <h3>
                No service bookings yet.
            </h3>

            <p>
                Once you book a service for one of your vehicles,
                your appointment details will appear here.
            </p>

            <a
                href="vehicles.php"
                class="primary-btn"
            >
                Go to My Vehicles →
            </a>

        </div>


    <?php } else { ?>


        <!-- BOOKINGS -->

        <div class="bookings-list">


            <?php foreach ($bookings as $b) {


                $date_display =
                    !empty($b['service_date'])
                    ? (new DateTime($b['service_date']))->format("d-m-Y")
                    : "N/A";


                $slot_display = "N/A";


                if (!empty($b['time_slot'])) {

                    $parts =
                        explode("-", trim($b['time_slot']));

                    if (count($parts) === 2) {

                        $slot_display =
                            date("g:i A", strtotime($parts[0]))
                            . " - "
                            . date("g:i A", strtotime($parts[1]));

                    }

                }


                $status =
                    $b['status']
                    ?: 'Confirmed';


                $status_lower =
                    strtolower($status);


                $status_class =
                    $status_lower === 'confirmed'
                    ? 'status-good'
                    : (
                        $status_lower === 'cancelled'
                        ? 'status-bad'
                        : 'status-moderate'
                    );

                $b_img = get_vehicle_image($b['company'] ?? '', $b['model'] ?? '');
                $vehicle_name = trim(($b['company'] ?? '') . ' ' . ($b['model'] ?? '')) ?: 'Vehicle unavailable';

            ?>


                <article class="booking-card">


                    <!-- VEHICLE THUMB -->
                    <div style="width: 85px; height: 60px; flex-shrink: 0; background: #f8fafc; border-radius: 10px; display: flex; align-items: center; justify-content: center; padding: 4px; border: 1px solid #eef2f6;">
                        <img src="assets/images/<?= htmlspecialchars($b_img) ?>" alt="<?= htmlspecialchars($vehicle_name) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>


                    <!-- MAIN -->

                    <div class="booking-card-main">


                        <div class="booking-card-top">

                            <span class="booking-id-tag">

                                #<?= (int)$b['id'] ?>

                            </span>


                            <span
                                class="<?= $status_class ?> booking-status-pill"
                            >

                                <?= htmlspecialchars($status) ?>

                            </span>

                        </div>



                        <h3>

                            <?= htmlspecialchars($vehicle_name) ?>

                        </h3>



                        <?php if (!empty($b['license_no'])) { ?>

                            <p class="vehicle-plate">

                                <?= htmlspecialchars(
                                    $b['license_no']
                                ) ?>

                            </p>

                        <?php } ?>



                        <p class="booking-meta">

                            📅 <?= $date_display ?>

                            &nbsp; · &nbsp;

                            🕐 <?= $slot_display ?>

                        </p>



                        <?php if (!empty($b['services'])) { ?>

                            <p class="booking-services">

                                <?= htmlspecialchars(
                                    $b['services']
                                ) ?>

                            </p>

                        <?php } ?>


                    </div>



                    <!-- RIGHT SIDE -->

                    <div class="booking-card-side">


                        <div class="booking-amount">

                            ₹<?= number_format(
                                (int)$b['total']
                            ) ?>

                        </div>


                        <a
                            href="confirmation.php?id=<?= (int)$b['id'] ?>"
                            class="ghost-btn small"
                        >
                            View Details →
                        </a>


                    </div>


                </article>


            <?php } ?>


        </div>


    <?php } ?>


</main>


<?php include("includes/footer.php"); ?>


</body>

</html>
