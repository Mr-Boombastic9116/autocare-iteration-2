<?php
require_once("includes/db.php");
require_once("includes/auth.php");
require_once("includes/vehicle_image.php");

require_login();

$user = current_username();
$ACTIVE_NAV = 'bookings';

$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($booking_id <= 0) {
    header("Location: bookings.php");
    exit();
}

// Check ownership
$data = get_owned_booking($conn, $booking_id, $user);

if (!$data) {
    http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Not Found | AutoCare</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .not-found-wrapper {
            min-height: 70vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .not-found-card {
            width: 100%;
            max-width: 500px;
            background: #ffffff;
            padding: 45px 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
        }

        .not-found-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: #fff1f2;
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
        }

        .not-found-card h3 {
            margin: 0 0 10px;
            color: #1f2937;
            font-size: 25px;
        }

        .not-found-card p {
            margin: 0 0 25px;
            color: #6b7280;
            line-height: 1.6;
        }
    </style>
</head>

<body>

<?php include("includes/header_app.php"); ?>

<div class="not-found-wrapper">

    <div class="not-found-card">

        <div class="not-found-icon">
            !
        </div>

        <h3>Booking Not Found</h3>

        <p>
            It may not exist, or it doesn't belong to your account.
        </p>

        <a href="bookings.php" class="primary-btn">
            Back to My Bookings
        </a>

    </div>

</div>

</body>
</html>

<?php
    exit();
}


// Convert services into array
$services = [];

if (!empty($data['services'])) {
    $services = array_filter(
        array_map(
            'trim',
            explode(",", $data['services'])
        )
    );
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Booking Confirmed | AutoCare</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>

        body {
            background: #f5f7fb;
        }

        /* Main Wrapper */

        .confirmation-wrapper {
            width: 100%;
            min-height: 70vh;

            display: flex;
            justify-content: center;

            padding: 50px 20px 70px;

            box-sizing: border-box;
        }


        /* Main Card */

        .confirm-box {
            width: 100%;
            max-width: 720px;

            background: #ffffff;

            padding: 40px;

            border-radius: 22px;

            border: 1px solid #edf0f4;

            box-shadow:
                0 15px 45px rgba(15, 23, 42, 0.08);

            box-sizing: border-box;
        }


        /* Success Header */

        .confirmation-header {
            text-align: center;

            padding-bottom: 28px;

            margin-bottom: 25px;

            border-bottom: 1px solid #edf0f4;
        }


        .success-icon {
            width: 75px;
            height: 75px;

            margin: 0 auto 18px;

            border-radius: 50%;

            background: #e9f9ef;

            color: #16a34a;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 38px;

            font-weight: bold;

            box-shadow:
                0 8px 20px rgba(22, 163, 74, 0.12);
        }


        .confirmation-header h2 {
            margin: 0 0 8px;

            color: #111827;

            font-size: 30px;
        }


        .confirmation-header p {
            margin: 0;

            color: #6b7280;

            font-size: 14px;
        }


        /* Booking ID */

        .booking-id {
            display: inline-block;

            margin-top: 17px;

            padding: 8px 15px;

            border-radius: 30px;

            background: #f1f5f9;

            color: #334155;

            font-size: 14px;

            font-weight: 600;
        }


        /* Details */

        .details {
            width: 100%;
        }


        .detail-row {
            display: flex;

            justify-content: space-between;
            align-items: center;

            gap: 20px;

            padding: 17px 0;

            border-bottom: 1px solid #f0f2f5;
        }


        .detail-label {
            color: #64748b;

            font-size: 14px;

            font-weight: 600;
        }


        .detail-value {
            color: #1e293b;

            font-size: 15px;

            font-weight: 600;

            text-align: right;
        }


        /* Services */

        .services-section {
            padding: 20px 0;

            border-bottom: 1px solid #f0f2f5;
        }


        .services-title {
            margin-bottom: 12px;

            color: #64748b;

            font-size: 14px;

            font-weight: 600;
        }


        .services-list {
            display: flex;

            flex-wrap: wrap;

            gap: 8px;

            list-style: none;

            margin: 0;

            padding: 0;
        }


        .services-list li {
            padding: 8px 12px;

            border-radius: 8px;

            background: #effdf4;

            border: 1px solid #dcfce7;

            color: #15803d;

            font-size: 13px;

            font-weight: 600;
        }


        /* Special Request */

        .special-request {
            margin-top: 20px;

            padding: 15px 17px;

            background: #f8fafc;

            border-left: 4px solid #ffc107;

            border-radius: 10px;
        }


        .special-request strong {
            display: block;

            margin-bottom: 5px;

            color: #475569;

            font-size: 13px;
        }


        .special-request span {
            color: #334155;

            font-size: 14px;

            line-height: 1.5;
        }


        /* Total */

        .total {
            margin-top: 25px;

            padding: 20px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            border-radius: 14px;

            background: #f1f5f9;

            border: 1px solid #e2e8f0;

            color: #10202a;

            font-size: 16px;

            font-weight: 700;
        }


        .total-amount {
            color: #10202a;

            font-size: 24px;

            font-weight: 800;

            white-space: nowrap;
        }


        /* Payment + Status */

        .summary-row {
            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 15px;

            margin-top: 18px;
        }


        .summary-card {
            padding: 17px;

            background: #f8fafc;

            border: 1px solid #eef2f7;

            border-radius: 12px;
        }


        .summary-card .label {
            display: block;

            margin-bottom: 6px;

            color: #64748b;

            font-size: 12px;

            font-weight: 600;
        }


        .summary-card .value {
            color: #1e293b;

            font-size: 17px;

            font-weight: 700;
        }


        .status {
            display: inline-block;

            padding: 6px 11px;

            border-radius: 20px;

            background: #dcfce7;

            color: #15803d;

            font-size: 12px;

            font-weight: 700;
        }


        /* Button */

        .action-area {
            margin-top: 30px;

            padding-top: 25px;

            border-top: 1px solid #edf0f4;
        }


        .home-btn {
            width: 100%;

            padding: 14px 20px;

            border: none;

            border-radius: 12px;

            background: #10202a;

            color: #ffffff;

            font-size: 15px;

            font-weight: 700;

            cursor: pointer;

            transition: all 0.2s ease;
        }


        .home-btn:hover {
            background: #1e3240;

            color: #ffc107;

            transform: translateY(-1px);

            box-shadow:
                0 7px 18px rgba(16, 32, 42, 0.25);
        }


        /* Mobile */

        @media (max-width: 600px) {

            .confirmation-wrapper {
                padding: 30px 14px 50px;
            }


            .confirm-box {
                padding: 28px 20px;

                border-radius: 18px;
            }


            .confirmation-header h2 {
                font-size: 25px;
            }


            .success-icon {
                width: 65px;
                height: 65px;

                font-size: 32px;
            }


            .detail-row {
                display: block;
            }


            .detail-label {
                display: block;

                margin-bottom: 6px;
            }


            .detail-value {
                text-align: left;
            }


            .total {
                display: block;
            }


            .total-amount {
                display: block;

                margin-top: 8px;

                font-size: 22px;
            }


            .summary-row {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>


<body>


<?php include("includes/header_app.php"); ?>


<div class="confirmation-wrapper">

    <div class="confirm-box">


        <!-- Confirmation Header -->

        <div class="confirmation-header">

            <div class="success-icon">
                &#10004;
            </div>


            <h2>
                Booking Confirmed!
            </h2>


            <p>
                Your service appointment has been successfully booked.
            </p>


            <div class="booking-id">
                Booking ID: #<?= (int)$data['id'] ?>
            </div>

        </div>


        <!-- Booking Details -->

        <div class="details">

            <?php
            $v_conf_img = get_vehicle_image($data['company'] ?? '', $data['model'] ?? '');
            ?>

            <div class="detail-row" style="align-items: center;">

                <div class="detail-label">
                    Vehicle
                </div>

                <div class="detail-value" style="display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
                    <img src="assets/images/<?= htmlspecialchars($v_conf_img) ?>" alt="" style="width: 48px; height: 32px; object-fit: contain; border-radius: 5px; background: #f1f5f9; padding: 2px;">
                    <span>
                        <?= htmlspecialchars(
                            trim(
                                ($data['company'] ?? '') . " " . ($data['model'] ?? '')
                            )
                        ) ?>
                    </span>
                </div>

            </div>


            <div class="detail-row">

                <div class="detail-label">
                    Registration
                </div>

                <div class="detail-value">
                    <?= htmlspecialchars(
                        $data['license_no'] ?? 'N/A'
                    ) ?>
                </div>

            </div>


            <div class="detail-row">

                <div class="detail-label">
                    Service Date
                </div>

                <div class="detail-value">

                    <?php
                    if (!empty($data['service_date'])) {

                        echo (new DateTime(
                            $data['service_date']
                        ))->format("d-m-Y");

                    } else {

                        echo "N/A";

                    }
                    ?>

                </div>

            </div>


            <div class="detail-row">

                <div class="detail-label">
                    Time Slot
                </div>

                <div class="detail-value">

                    <?php

                    if (!empty($data['time_slot'])) {

                        $parts = explode(
                            "-",
                            trim($data['time_slot'])
                        );


                        if (count($parts) === 2) {

                            $start_time = trim($parts[0]);
                            $end_time = trim($parts[1]);


                            echo date(
                                "g:i A",
                                strtotime($start_time)
                            );


                            echo " - ";


                            echo date(
                                "g:i A",
                                strtotime($end_time)
                            );

                        } else {

                            echo htmlspecialchars(
                                $data['time_slot']
                            );

                        }

                    } else {

                        echo "N/A";

                    }

                    ?>

                </div>

            </div>


        </div>


        <!-- Services -->

        <div class="services-section">

            <div class="services-title">
                Services Selected
            </div>


            <ul class="services-list">

                <?php if (!empty($services)): ?>

                    <?php foreach ($services as $s): ?>

                        <li>
                            <?= htmlspecialchars($s) ?>
                        </li>

                    <?php endforeach; ?>

                <?php else: ?>

                    <li>
                        No extra services
                    </li>

                <?php endif; ?>

            </ul>

        </div>


        <!-- Special Request -->

        <?php if (!empty($data['special_request'])): ?>

            <div class="special-request">

                <strong>
                    Special Request
                </strong>

                <span>
                    <?= htmlspecialchars(
                        $data['special_request']
                    ) ?>
                </span>

            </div>

        <?php endif; ?>


        <!-- Total -->

        <div class="total">

            <span>
                Total Estimated Cost
            </span>

            <span class="total-amount">
                ₹<?= number_format(
                    (int)$data['total']
                ) ?>
            </span>

        </div>


        <!-- Payment and Status -->

        <div class="summary-row">


            <div class="summary-card">

                <span class="label">
                    Advance Paid
                </span>

                <span class="value">
                    ₹<?= number_format(
                        (int)$data['advance']
                    ) ?>
                </span>

            </div>


            <div class="summary-card">

                <span class="label">
                    Booking Status
                </span>

                <span class="status">
                    <?= htmlspecialchars(
                        $data['status']
                    ) ?>
                </span>

            </div>


        </div>


        <!-- Button -->

        <div class="action-area">

            <button
                type="button"
                class="home-btn"
                onclick="window.location.href='bookings.php'"
            >
                View My Bookings
            </button>

        </div>


    </div>

</div>


<?php include("includes/footer.php"); ?>


</body>

</html>
