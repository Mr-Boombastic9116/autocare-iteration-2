<?php

require_once("includes/db.php");
require_once("includes/auth.php");

require_login();

$user = current_username();
$error = "";


/* =========================================================
   AJAX DATA LOADING
   ========================================================= */

if (isset($_GET['ajax'])) {

    header("Content-Type: application/json");

    $action = $_GET['ajax'];


    /* -----------------------------------------------------
       LOAD MODELS
       ----------------------------------------------------- */

    if ($action === "models") {

        $company_id = (int)($_GET['company_id'] ?? 0);
        $data = [];

        if ($company_id > 0) {

            $stmt = $conn->prepare(
                "SELECT id, name
                 FROM models
                 WHERE company_id = ?
                 ORDER BY name"
            );

            $stmt->bind_param("i", $company_id);
            $stmt->execute();

            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {

                $data[] = [
                    "id" => $row["id"],
                    "name" => $row["name"]
                ];
            }

            $stmt->close();
        }

        echo json_encode($data);
        exit();
    }


    /* -----------------------------------------------------
       LOAD YEARS
       ----------------------------------------------------- */

    if ($action === "years") {

        $data = [];

        $result = $conn->query(
            "SELECT id, year
             FROM years
             ORDER BY year DESC"
        );

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    "id" => $row["id"],
                    "name" => $row["year"]
                ];
            }
        }

        echo json_encode($data);
        exit();
    }


    /* -----------------------------------------------------
       LOAD FUELS
       ----------------------------------------------------- */

    if ($action === "fuels") {

        $model_id = (int)($_GET['model_id'] ?? 0);
        $company_id = (int)($_GET['company_id'] ?? 0);
        $company_name = strtolower(trim($_GET['company_name'] ?? ''));
        $model_name = strtolower(trim($_GET['model_name'] ?? ''));

        if ($model_id > 0) {
            $stmt = $conn->prepare("SELECT m.name AS model_name, c.name AS company_name FROM models m LEFT JOIN companies c ON m.company_id = c.id WHERE m.id = ?");
            $stmt->bind_param("i", $model_id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($row) {
                if (!empty($row['company_name'])) $company_name = strtolower(trim($row['company_name']));
                if (!empty($row['model_name'])) $model_name = strtolower(trim($row['model_name']));
            }
        }

        if ($company_id > 0 && empty($company_name)) {
            $stmt = $conn->prepare("SELECT name FROM companies WHERE id = ?");
            $stmt->bind_param("i", $company_id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($row) {
                $company_name = strtolower(trim($row['name']));
            }
        }

        $combined = $company_name . ' ' . $model_name;
        $is_ev = false;

        // 1. Pure EV Manufacturers (Any vehicle under these companies is Electric EV)
        $ev_companies = [
            'ather', 'ola', 'byd', 'ampere', 'okinawa', 'hero electric', 
            'pure ev', 'simple', 'revolt', 'bounce', 'kabira', 'tork', 
            'ultraviolette', 'matter', 'lectrix', 'komaki', 'gemopai'
        ];

        foreach ($ev_companies as $ev_c) {
            if (strpos($company_name, $ev_c) !== false || strpos($combined, $ev_c) !== false) {
                $is_ev = true;
                break;
            }
        }

        // 2. EV Models (Specific EV models from multi-fuel manufacturers)
        if (!$is_ev) {
            $ev_keywords = [
                'ev', 'electric', 'iqube', 'chetak', 'vida', 'ioniq', 'comet', 
                'windsor', 'xuv400', 'taycan', 'recharge', '450', 'rizta', 's1',
                'i4', 'ix', 'eqb', 'eqs', 'eqe'
            ];
            foreach ($ev_keywords as $kw) {
                if (preg_match('/\b' . preg_quote($kw, '/') . '\b/i', $combined) || strpos($combined, $kw) !== false) {
                    $is_ev = true;
                    break;
                }
            }
        }

        $data = [];
        if ($is_ev) {
            $data[] = ["id" => 3, "name" => "Electric (EV)"];
        } else {
            $result = $conn->query("SELECT id, name FROM fuels ORDER BY id");
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = [
                        "id" => $row["id"],
                        "name" => $row["name"]
                    ];
                }
            }
        }

        echo json_encode($data);
        exit();
    }


    /* -----------------------------------------------------
       LOAD VARIANTS
       ----------------------------------------------------- */

    if ($action === "variants") {

        $model_id = (int)($_GET['model_id'] ?? 0);
        $data = [];

        if ($model_id > 0) {
            $stmt = $conn->prepare("SELECT id, name FROM variants WHERE model_id = ? ORDER BY name");
            if ($stmt) {
                $stmt->bind_param("i", $model_id);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $data[] = [
                        "id" => $row["id"],
                        "name" => $row["name"]
                    ];
                }
                $stmt->close();
            }

            if (empty($data)) {
                $m_stmt = $conn->prepare("SELECT m.name AS mname, c.name AS cname FROM models m LEFT JOIN companies c ON m.company_id = c.id WHERE m.id = ?");
                $m_stmt->bind_param("i", $model_id);
                $m_stmt->execute();
                $m_row = $m_stmt->get_result()->fetch_assoc();
                $m_stmt->close();

                $combined = strtolower(trim(($m_row['cname'] ?? '') . ' ' . ($m_row['mname'] ?? '')));

                if (strpos($combined, 'ather') !== false) {
                    $vars = ['450X 3.7 kWh (Pro Pack)', '450X 2.9 kWh', '450S Standard', 'Rizta Z 3.7 kWh', 'Rizta S 2.9 kWh', 'Apex Limited Edition'];
                } elseif (strpos($combined, 'ola') !== false) {
                    $vars = ['S1 Pro Gen 2 (4kWh)', 'S1 Air (3kWh)', 'S1 X+ (3kWh)', 'S1 X (2kWh)', 'Roadster EV 6kWh'];
                } elseif (strpos($combined, 'chetak') !== false) {
                    $vars = ['Chetak 2901', 'Chetak Urbane', 'Chetak Premium 2024'];
                } elseif (strpos($combined, 'iqube') !== false) {
                    $vars = ['iQube 2.2 kWh', 'iQube 3.4 kWh', 'iQube ST 3.4 kWh', 'iQube ST 5.1 kWh'];
                } elseif (strpos($combined, 'creta') !== false || strpos($combined, 'hyundai') !== false) {
                    $vars = ['EX 1.5 Petrol', 'S 1.5 Petrol', 'S(O) 1.5 Petrol', 'SX Tech 1.5 Petrol', 'SX(O) 1.5 Turbo DCT', 'N Line N10'];
                } elseif (strpos($combined, 'swift') !== false || strpos($combined, 'baleno') !== false || strpos($combined, 'maruti') !== false) {
                    $vars = ['LXi 1.2 Petrol', 'VXi 1.2 AMT', 'ZXi 1.2 Petrol', 'ZXi+ Dual Tone', 'VXi CNG'];
                } elseif (strpos($combined, 'thar') !== false || strpos($combined, 'mahindra') !== false) {
                    $vars = ['AX(O) Convertible 4WD', 'LX Hard Top Diesel MT', 'LX Hard Top Petrol AT', 'Earth Edition 4WD'];
                } elseif (strpos($combined, 'fortuner') !== false || strpos($combined, 'toyota') !== false) {
                    $vars = ['4x2 MT 2.7 Petrol', '4x2 AT 2.8 Diesel', '4x4 AT 2.8 Diesel', 'Legender 4x4 AT', 'GR-Sport 4x4 AT'];
                } elseif (strpos($combined, 'nexon') !== false || strpos($combined, 'tata') !== false) {
                    $vars = ['Smart+ 1.2 Revotron', 'Pure+ 1.5 Revotorq', 'Creative+ S', 'Fearless+ S Dark', 'Empowered+ Lux EV'];
                } elseif (strpos($combined, 'city') !== false || strpos($combined, 'honda') !== false) {
                    $vars = ['SV 1.5 i-VTEC', 'V 1.5 i-VTEC CVT', 'VX 1.5 i-VTEC', 'ZX 1.5 i-VTEC CVT', 'e:HEV Hybrid ZX'];
                } elseif (strpos($combined, 'bmw') !== false) {
                    $vars = ['330i M Sport', '320d Luxury Line', '530d M Sport Edition', 'X5 xDrive40i M Sport', 'i4 eDrive40 EV'];
                } elseif (strpos($combined, 'mercedes') !== false || strpos($combined, 'benz') !== false) {
                    $vars = ['C 200 Progressive', 'C 220d AMG Line', 'E 200 Exclusive', 'G 63 AMG V8'];
                } elseif (strpos($combined, 'royal') !== false || strpos($combined, 'bullet') !== false || strpos($combined, 'ktm') !== false || strpos($combined, 'pulsar') !== false) {
                    $vars = ['Halcyon Dual Channel ABS', 'Dark Stealth Black', 'Chrome Red', 'Standard Kick Start', 'Duke 390 GP Edition'];
                } else {
                    $vars = ['Base / Standard', 'Mid / Executive', 'Top / Luxury', 'Performance / Edition'];
                }

                foreach ($vars as $idx => $v) {
                    $data[] = ["id" => $idx + 1, "name" => $v];
                }
            }
        }

        echo json_encode($data);
        exit();
    }


    echo json_encode([]);
    exit();
}


/* =========================================================
   SAVE VEHICLE
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $company          = trim($_POST['company'] ?? '');
    $model            = trim($_POST['model'] ?? '');
    $year             = trim($_POST['year'] ?? '');
    $fuel             = trim($_POST['fuel'] ?? '');
    $variant          = trim($_POST['variant'] ?? '');
    $license          = trim($_POST['license'] ?? '');
    $kms              = $_POST['kms'] ?? '';
    $ownership_date   = $_POST['ownership_date'] ?? '';
    $last_service     = $_POST['last_service'] ?? '';
    $kms_last_service = $_POST['kms_last_service'] ?? '';


    /* -----------------------------------------------------
       VALIDATION
       ----------------------------------------------------- */

    if (
        $company === '' ||
        $model === '' ||
        $year === '' ||
        $fuel === '' ||
        $variant === '' ||
        $license === '' ||
        $kms === '' ||
        $ownership_date === '' ||
        $last_service === '' ||
        $kms_last_service === ''
    ) {

        $error = "Please fill in all required fields.";

    } elseif (
        !is_numeric($kms) ||
        !is_numeric($kms_last_service) ||
        (int)$kms < 0 ||
        (int)$kms_last_service < 0
    ) {

        $error = "KMs values must be positive numbers.";

    } else {


        /* -------------------------------------------------
           INSERT VEHICLE

           7 strings:
           user
           company
           model
           year
           fuel
           variant
           license

           2 integers:
           kms
           kms_last_service

           2 strings:
           ownership_date
           last_service
           ------------------------------------------------- */

        $stmt = $conn->prepare(
            "INSERT INTO vehicles
            (
                user,
                company,
                model,
                year,
                fuel,
                variant,
                license_no,
                kms,
                last_service,
                ownership_date,
                kms_last_service
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );


        if ($stmt) {

            $kms = (int)$kms;
            $kms_last_service = (int)$kms_last_service;

            $stmt->bind_param(
                "sssssssiiss",
                $user,
                $company,
                $model,
                $year,
                $fuel,
                $variant,
                $license,
                $kms,
                $kms_last_service,
                $ownership_date,
                $last_service
            );


            if ($stmt->execute()) {

                $new_id = $conn->insert_id;

                $stmt->close();

                header(
                    "Location: vehicle_details.php?id=" .
                    $new_id .
                    "&added=1"
                );

                exit();

            } else {

                $error = "Something went wrong while saving your vehicle.";

                $stmt->close();
            }

        } else {

            $error = "Unable to prepare vehicle information.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Add Vehicle | AutoCare</title>

<link
    rel="stylesheet"
    href="assets/css/style.css"
>


<style>

/* =========================================================
   PAGE
   ========================================================= */

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    background:
        linear-gradient(
            135deg,
            #f5f7f8,
            #edf1f3
        );

    color: #17232d;
}


/* =========================================================
   MAIN PAGE
   ========================================================= */

.add-vehicle-page {

    width: min(
        1160px,
        calc(100% - 40px)
    );

    margin: auto;

    padding: 36px 0 80px;
}


/* =========================================================
   HERO
   ========================================================= */

.add-vehicle-hero {

    position: relative;

    min-height: 175px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 25px;

    padding: 34px 38px;

    margin-bottom: 20px;

    overflow: hidden;

    border-radius: 20px;

    background:
        linear-gradient(
            125deg,
            #101a21,
            #1b2b35,
            #263943
        );

    box-shadow:
        0 15px 40px
        rgba(
            20,
            32,
            41,
            .13
        );
}


.add-vehicle-hero::before {

    content: "";

    position: absolute;

    width: 330px;
    height: 330px;

    right: -120px;
    top: -180px;

    border-radius: 50%;

    border:
        1px solid
        rgba(
            255,
            193,
            7,
            .22
        );
}


.add-vehicle-hero::after {

    content: "";

    position: absolute;

    width: 190px;
    height: 190px;

    right: 150px;
    bottom: -150px;

    border-radius: 50%;

    border:
        1px solid
        rgba(
            255,
            255,
            255,
            .07
        );
}


.hero-text {

    position: relative;

    z-index: 2;
}


.hero-kicker {

    display: flex;

    align-items: center;

    gap: 9px;

    margin-bottom: 10px;

    color: #ffc107;

    font-size: 11px;

    font-weight: 900;

    letter-spacing: 2px;

    text-transform: uppercase;
}


.hero-kicker::before {

    content: "";

    width: 21px;
    height: 2px;

    background: #ffc107;
}


.hero-text h1 {

    margin: 0 0 8px;

    color: white;

    font-size: 40px;

    line-height: 1.1;

    font-weight: 850;
}


.hero-text p {

    margin: 0;

    max-width: 590px;

    color: #b9c5cb;

    font-size: 15px;

    line-height: 1.55;
}


/* =========================================================
   BACK BUTTON
   ========================================================= */

.back-link {

    position: relative;

    z-index: 3;

    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 12px 17px;

    border:
        1px solid
        rgba(
            255,
            255,
            255,
            .14
        );

    border-radius: 9px;

    color: #d8e0e4;

    background:
        rgba(
            255,
            255,
            255,
            .05
        );

    text-decoration: none;

    font-size: 12px;

    font-weight: 800;

    transition: .2s;
}


.back-link:hover {

    background:
        rgba(
            255,
            255,
            255,
            .09
        );

    border-color:
        rgba(
            255,
            255,
            255,
            .22
        );
}


/* =========================================================
   ERROR
   ========================================================= */

.form-error {

    margin-bottom: 18px;

    padding: 14px 17px;

    border:
        1px solid
        #efcccc;

    border-radius: 10px;

    background: #fff3f3;

    color: #b42318;

    font-size: 13px;

    font-weight: 700;
}


/* =========================================================
   MAIN LAYOUT
   ========================================================= */

.add-layout {

    display: grid;

    grid-template-columns:
        minmax(0, 1.55fr)
        minmax(290px, .75fr);

    gap: 20px;

    align-items: start;
}


/* =========================================================
   FORM CARD
   ========================================================= */

.form-card {

    padding: 30px;

    border:
        1px solid
        #e1e6e9;

    border-radius: 17px;

    background: white;

    box-shadow:
        0 8px 28px
        rgba(
            25,
            40,
            52,
            .055
        );
}


/* =========================================================
   FORM HEADER
   ========================================================= */

.form-card-header {

    display: flex;

    align-items: center;

    gap: 13px;

    margin-bottom: 27px;

    padding-bottom: 20px;

    border-bottom:
        1px solid
        #edf0f2;
}


.form-card-number {

    width: 40px;
    height: 40px;

    display: flex;

    align-items: center;

    justify-content: center;

    flex-shrink: 0;

    border-radius: 10px;

    background: #fff4c7;

    color: #a87800;

    font-size: 14px;

    font-weight: 900;
}


.form-card-header h2 {

    margin: 0 0 4px;

    color: #1c2a34;

    font-size: 19px;

    font-weight: 850;
}


.form-card-header p {

    margin: 0;

    color: #89949c;

    font-size: 12px;
}


/* =========================================================
   FORM GRID
   ========================================================= */

.form-grid {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 20px 16px;
}


.form-group {

    min-width: 0;
}


.form-group.full {

    grid-column: 1 / -1;
}


/* =========================================================
   LABELS
   ========================================================= */

.form-group label {

    display: block;

    margin-bottom: 8px;

    color: #33414a;

    font-size: 12px;

    font-weight: 800;
}


.required {

    color: #d39c00;
}


/* =========================================================
   INPUTS
   ========================================================= */

.form-group input,
.form-group select {

    width: 100%;

    height: 50px;

    padding: 0 14px;

    border:
        1px solid
        #d9e0e4;

    border-radius: 9px;

    outline: none;

    background: #fafcfd;

    color: #26343e;

    font-family: inherit;

    font-size: 14px;

    transition: .2s;
}


.form-group input::placeholder {

    color: #9ba5ab;
}


.form-group input:focus,
.form-group select:focus {

    border-color: #e5ae08;

    background: white;

    box-shadow:
        0 0 0 4px
        rgba(
            255,
            193,
            7,
            .11
        );
}


.form-group select:disabled {

    cursor: not-allowed;

    opacity: .55;

    background: #f1f3f4;
}


/* =========================================================
   DIVIDER
   ========================================================= */

.form-divider {

    grid-column: 1 / -1;

    height: 1px;

    margin: 2px 0;

    background: #edf0f2;
}


/* =========================================================
   EXTRA FIELDS
   ========================================================= */

#extra-fields {

    grid-column: 1 / -1;

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 20px 16px;
}


/* =========================================================
   ACTIONS
   ========================================================= */

.form-actions {

    display: flex;

    justify-content: flex-end;

    gap: 10px;

    margin-top: 28px;

    padding-top: 22px;

    border-top:
        1px solid
        #edf0f2;
}


.cancel-btn,
.submit-btn {

    height: 47px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    border-radius: 8px;

    font-size: 12px;

    font-weight: 850;

    text-decoration: none;

    transition: .2s;
}


.cancel-btn {

    padding: 0 19px;

    border:
        1px solid
        #d8dfe3;

    color: #5b6871;

    background: white;
}


.cancel-btn:hover {

    background: #f7f8f9;
}


.submit-btn {

    padding: 0 24px;

    border: none;

    color: #17202a;

    background: #ffc107;

    cursor: pointer;

    box-shadow:
        0 7px 17px
        rgba(
            255,
            193,
            7,
            .20
        );
}


.submit-btn:hover {

    background: #e9ad00;

    transform: translateY(-1px);
}


/* =========================================================
   PREVIEW CARD
   ========================================================= */

.preview-card {

    position: sticky;

    top: 25px;

    overflow: hidden;

    border:
        1px solid
        #e0e6e9;

    border-radius: 17px;

    background: white;

    box-shadow:
        0 8px 28px
        rgba(
            25,
            40,
            52,
            .055
        );
}


/* =========================================================
   PREVIEW HEADER
   NO CAR IMAGE
   ========================================================= */

.preview-image {

    position: relative;

    height: 160px;

    display: flex;

    align-items: center;
    justify-content: flex-end;

    overflow: hidden;

    background: #ffffff;

    border-bottom: 1px solid #edf0f3;

    padding: 14px;
}


/* soft background glow */

.preview-image::before {

    content: "";

    position: absolute;

    width: 170px;
    height: 170px;

    right: 15px;
    top: 50%;
    transform: translateY(-50%);

    border-radius: 50%;

    background:
        radial-gradient(
            circle,
            rgba(240, 244, 248, 0.9) 0%,
            rgba(255, 255, 255, 0) 70%
        );
}


/* =========================================================
   AUTOCARE LABEL
   ========================================================= */

.preview-label {

    position: absolute;

    z-index: 4;

    top: 14px;
    left: 14px;

    padding: 5px 11px;

    border-radius: 20px;

    color: #17202a;

    background: #ffc107;

    font-size: 10px;

    font-weight: 900;

    letter-spacing: 1px;
}


/* =========================================================
   PREVIEW CONTENT
   ========================================================= */

.preview-content {

    padding: 24px;
}


.preview-content h3 {

    margin: 0 0 6px;

    color: #1b2933;

    font-size: 21px;

    font-weight: 850;
}


.preview-subtitle {

    margin: 0 0 18px;

    color: #8b969e;

    font-size: 12px;

    line-height: 1.5;
}


/* =========================================================
   PREVIEW FEATURES
   ========================================================= */

.preview-feature {

    display: flex;

    align-items: center;

    gap: 11px;

    padding: 12px 0;

    border-top:
        1px solid
        #edf0f2;
}


.preview-icon {

    width: 33px;
    height: 33px;

    display: flex;

    align-items: center;

    justify-content: center;

    flex-shrink: 0;

    border-radius: 8px;

    background: #f3f5f6;

    font-size: 14px;
}


.preview-feature strong {

    display: block;

    color: #3c4a53;

    font-size: 12px;
}


.preview-feature span {

    display: block;

    margin-top: 3px;

    color: #929ca3;

    font-size: 11px;
}


/* =========================================================
   PROGRESS BOX
   ========================================================= */

.progress-box {

    margin-top: 16px;

    padding: 20px;

    border:
        1px solid
        #e0e6e9;

    border-radius: 14px;

    background: white;
}


.progress-title {

    display: flex;

    justify-content: space-between;

    margin-bottom: 12px;
}


.progress-title strong {

    color: #36444e;

    font-size: 12px;
}


.progress-title span {

    color: #a17a00;

    font-size: 11px;

    font-weight: 800;
}


.progress-track {

    height: 5px;

    overflow: hidden;

    border-radius: 10px;

    background: #edf0f2;
}


.progress-bar {

    width: 25%;

    height: 100%;

    border-radius: 10px;

    background: #ffc107;

    transition: .3s;
}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media(max-width: 900px) {

    .add-layout {

        grid-template-columns: 1fr;
    }

    .preview-card {

        position: static;

        order: -1;
    }
}


@media(max-width: 650px) {

    .add-vehicle-page {

        width: calc(100% - 24px);

        padding-top: 22px;
    }


    .add-vehicle-hero {

        flex-direction: column;

        align-items: flex-start;

        padding: 28px 23px;
    }


    .hero-text h1 {

        font-size: 32px;
    }


    .hero-text p {

        font-size: 14px;
    }


    .back-link {

        width: 100%;

        justify-content: center;
    }


    .form-card {

        padding: 22px;
    }


    .form-grid,
    #extra-fields {

        grid-template-columns: 1fr;
    }


    .form-group.full,
    .form-divider {

        grid-column: auto;
    }


    .form-actions {

        flex-direction: column-reverse;
    }


    .cancel-btn,
    .submit-btn {

        width: 100%;
    }


    .preview-image {

        height: 125px;
    }
}

</style>

</head>


<body>


<?php

$ACTIVE_NAV = 'vehicles';

include("includes/header_app.php");

?>


<main class="add-vehicle-page">


<!-- =====================================================
     HERO
     ===================================================== -->

<section class="add-vehicle-hero">

    <div class="hero-text">

        <div class="hero-kicker">
            My Garage
        </div>

        <h1>
            Add Your Vehicle
        </h1>

        <p>
            Add your vehicle once and keep its service,
            mileage and maintenance information organized.
        </p>

    </div>


    <a
        href="vehicles.php"
        class="back-link"
    >
        ← Back to Garage
    </a>

</section>


<?php if ($error !== "") { ?>

<div class="form-error">

    <?= htmlspecialchars($error) ?>

</div>

<?php } ?>


<div class="add-layout">


<!-- =====================================================
     FORM
     ===================================================== -->

<section class="form-card">


<div class="form-card-header">

    <div class="form-card-number">
        01
    </div>

    <div>

        <h2>
            Vehicle Information
        </h2>

        <p>
            Select your vehicle details below.
        </p>

    </div>

</div>


<form
    method="POST"
    id="addVehicleForm"
>


<div class="form-grid">


<!-- =====================================================
     COMPANY
     ===================================================== -->

<div class="form-group">

<label>
    Car Company
    <span class="required">*</span>
</label>


<select
    id="company"
    name="company"
    required
>

<option value="">
    Select Company
</option>


<?php

$res = $conn->query(
    "SELECT * FROM companies ORDER BY name"
);

while ($row = $res->fetch_assoc()) {

    echo
    '<option
        value="' .
        htmlspecialchars($row['name']) .
        '"
        data-id="' .
        (int)$row['id'] .
    '">' .
        htmlspecialchars($row['name']) .
    '</option>';
}

?>

</select>

</div>


<!-- =====================================================
     MODEL
     ===================================================== -->

<div
    class="form-group"
    id="model-group"
>

<label>
    Model
    <span class="required">*</span>
</label>


<select
    id="model"
    name="model"
    required
    disabled
>

<option value="">
    Select company first
</option>

</select>

</div>


<!-- =====================================================
     YEAR
     ===================================================== -->

<div
    class="form-group"
    id="year-group"
>

<label>
    Manufacturing Year
    <span class="required">*</span>
</label>


<select
    id="year"
    name="year"
    required
    disabled
>

<option value="">
    Select model first
</option>

</select>

</div>


<!-- =====================================================
     FUEL
     ===================================================== -->

<div
    class="form-group"
    id="fuel-group"
>

<label>
    Fuel Type
    <span class="required">*</span>
</label>


<select
    id="fuel"
    name="fuel"
    required
    disabled
>

<option value="">
    Select model first
</option>

</select>

</div>


<!-- =====================================================
     VARIANT
     ===================================================== -->

<div
    class="form-group full"
    id="variant-group"
>

<label>
    Variant
    <span class="required">*</span>
</label>


<select
    id="variant"
    name="variant"
    required
    disabled
>

<option value="">
    Select model first
</option>

</select>

</div>


<div class="form-divider"></div>


<!-- =====================================================
     EXTRA DETAILS
     ===================================================== -->

<div id="extra-fields">


<!-- REGISTRATION -->

<div class="form-group">

<label>
    Registration Number
    <span class="required">*</span>
</label>


<input
    type="text"
    name="license"
    placeholder="e.g. GA03AB1234"
    required
>

</div>


<!-- CURRENT KM -->

<div class="form-group">

<label>
    Current Odometer
    <span class="required">*</span>
</label>


<input
    type="number"
    name="kms"
    min="0"
    placeholder="e.g. 13000"
    required
>

</div>


<!-- LAST SERVICE KM -->

<div class="form-group">

<label>
    KMs at Last Service
    <span class="required">*</span>
</label>


<input
    type="number"
    name="kms_last_service"
    min="0"
    placeholder="e.g. 5000"
    required
>

</div>


<!-- OWNERSHIP DATE -->

<div class="form-group">

<label>
    Ownership Date
    <span class="required">*</span>
</label>


<input
    type="date"
    name="ownership_date"
    required
>

</div>


<!-- LAST SERVICE -->

<div class="form-group">

<label>
    Last Service Date
    <span class="required">*</span>
</label>


<input
    type="date"
    name="last_service"
    required
>

</div>


</div>


</div>


<!-- =====================================================
     ACTIONS
     ===================================================== -->

<div class="form-actions">


<a
    href="vehicles.php"
    class="cancel-btn"
>
    Cancel
</a>


<button
    type="submit"
    class="submit-btn"
>
    Add Vehicle →
</button>


</div>


</form>

</section>


<!-- =====================================================
     RIGHT PREVIEW
     ===================================================== -->

<aside>


<div class="preview-card">


<div class="preview-content">


<h3 id="previewName">
    Your Vehicle
</h3>


<p class="preview-subtitle">
    Complete the details to create your vehicle profile.
</p>


<!-- FEATURE 1 -->

<div class="preview-feature">

    <div class="preview-icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="display:inline-block; vertical-align:middle;"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.04 3H5.81l1.04-3zM19 17H5v-4h14v4z"/><circle cx="7.5" cy="15" r="1.5"/><circle cx="16.5" cy="15" r="1.5"/></svg>
    </div>

    <div>

        <strong>
            Vehicle Profile
        </strong>

        <span>
            Model, year & variant
        </span>

    </div>

</div>


<!-- FEATURE 2 -->

<div class="preview-feature">

    <div class="preview-icon">
        ◷
    </div>

    <div>

        <strong>
            Service History
        </strong>

        <span>
            Mileage & service dates
        </span>

    </div>

</div>


<!-- FEATURE 3 -->

<div class="preview-feature">

    <div class="preview-icon">
        ✓
    </div>

    <div>

        <strong>
            Ready for AutoCare
        </strong>

        <span>
            Track your vehicle easily
        </span>

    </div>

</div>


</div>

</div>


<!-- =====================================================
     PROGRESS
     ===================================================== -->

<div class="progress-box">


<div class="progress-title">

    <strong>
        Vehicle setup
    </strong>

    <span id="progressText">
        25%
    </span>

</div>


<div class="progress-track">

    <div
        class="progress-bar"
        id="progressBar"
    ></div>

</div>


</div>


</aside>


</div>

</main>


<?php include("includes/footer.php"); ?>


<script src="assets/js/script.js"></script>


<script>

/* =========================================================
   ELEMENTS
   ========================================================= */

const companySelect =
    document.getElementById("company");

const modelSelect =
    document.getElementById("model");

const yearSelect =
    document.getElementById("year");

const fuelSelect =
    document.getElementById("fuel");

const variantSelect =
    document.getElementById("variant");

const previewName =
    document.getElementById("previewName");

const previewCarImg =
    document.getElementById("previewCarImg");

const progressBar =
    document.getElementById("progressBar");

const progressText =
    document.getElementById("progressText");


/* =========================================================
   UPDATE PREVIEW IMAGE
   ========================================================= */

function updatePreviewImage() {
    if (!previewCarImg) return;

    const c = (companySelect.value || "").toLowerCase().trim();
    const m = (modelSelect.value || "").toLowerCase().trim();
    const f = (fuelSelect.value || "").toLowerCase().trim();
    const combined = c + " " + m;

    let src = "car-bg.png";

    if (
        combined.includes("ather") ||
        combined.includes("ola") ||
        combined.includes("chetak") ||
        combined.includes("iqube") ||
        combined.includes("bounce") ||
        combined.includes("450") ||
        combined.includes("rizta") ||
        combined.includes("s1")
    ) {
        src = "car3.png";
    } else if (
        combined.includes("royal") ||
        combined.includes("yamaha") ||
        combined.includes("ktm") ||
        combined.includes("bajaj") ||
        combined.includes("tvs") ||
        combined.includes("hero") ||
        combined.includes("bike") ||
        combined.includes("bullet") ||
        combined.includes("pulsar")
    ) {
        src = "car3.png";
    } else if (
        m.includes("thar") ||
        m.includes("scorpio") ||
        m.includes("bolero") ||
        m.includes("jimny")
    ) {
        src = "thar.png";
    } else if (
        m.includes("fortuner") ||
        m.includes("innova") ||
        m.includes("hycross") ||
        m.includes("crysta")
    ) {
        src = "fortuner.png";
    } else if (
        c.includes("tata") ||
        m.includes("nexon") ||
        m.includes("punch") ||
        m.includes("harrier") ||
        m.includes("safari") ||
        m.includes("curvv")
    ) {
        src = "nexon.png";
    } else if (
        c.includes("honda") ||
        m.includes("city") ||
        m.includes("amaze") ||
        m.includes("elevate") ||
        m.includes("slavia") ||
        m.includes("virtus") ||
        m.includes("verna")
    ) {
        src = "city.png";
    } else if (
        c.includes("maruti") ||
        c.includes("suzuki") ||
        m.includes("swift") ||
        m.includes("baleno") ||
        m.includes("wagon") ||
        m.includes("brezza")
    ) {
        src = (m.includes("alto") || m.includes("k10") || m.includes("800")) ? "car2.png" : "swift.png";
    } else if (
        c.includes("hyundai") ||
        m.includes("creta") ||
        m.includes("venue") ||
        m.includes("alcazar") ||
        m.includes("exter") ||
        m.includes("tucson")
    ) {
        src = "creta.png";
    } else if (c.includes("bmw") || c.includes("mercedes") || c.includes("audi") || c.includes("porsche")) {
        src = "car1.png";
    } else {
        src = "car-bg.png";
    }

    previewCarImg.src = "assets/images/" + src;
}


/* =========================================================
   RESET SELECT
   ========================================================= */

function resetSelect(
    select,
    text
) {

    select.innerHTML =
        `<option value="">${text}</option>`;

    select.disabled = true;
}


/* =========================================================
   LOAD AJAX DATA
   ========================================================= */

async function loadData(
    action,
    params
) {

    const query =
        new URLSearchParams({
            ajax: action,
            ...params
        });


    try {

        const response =
            await fetch(
                "add_vehicle.php?" +
                query.toString()
            );


        if (!response.ok) {

            throw new Error(
                "Request failed"
            );
        }


        return await response.json();


    } catch (error) {

        console.error(
            "AutoCare loading error:",
            error
        );

        return [];
    }
}


/* =========================================================
   COMPANY → MODEL
   ========================================================= */

companySelect.addEventListener(
    "change",
    async function () {

        const companyId =
            this.options[
                this.selectedIndex
            ].dataset.id;


        resetSelect(
            modelSelect,
            "Loading models..."
        );


        resetSelect(
            yearSelect,
            "Select model first"
        );


        resetSelect(
            fuelSelect,
            "Select model first"
        );


        resetSelect(
            variantSelect,
            "Select model first"
        );


        if (!companyId) {

            resetSelect(
                modelSelect,
                "Select company first"
            );

            previewName.textContent =
                "Your Vehicle";

            updateProgress();

            return;
        }


        const models =
            await loadData(
                "models",
                {
                    company_id:
                        companyId
                }
            );


        modelSelect.innerHTML =
            '<option value="">Select Model</option>';


        models.forEach(
            function (item) {

                const option =
                    document.createElement(
                        "option"
                    );


                option.value =
                    item.name;


                option.dataset.id =
                    item.id;


                option.textContent =
                    item.name;


                modelSelect.appendChild(
                    option
                );
            }
        );


        if (models.length > 0) {

            modelSelect.disabled = false;

        } else {

            modelSelect.innerHTML =
                '<option value="">No models found</option>';
        }


        previewName.textContent =
            companySelect.value ||
            "Your Vehicle";


        updatePreviewImage();


        updateProgress();
    }
);


/* =========================================================
   MODEL → YEAR + FUEL + VARIANT
   ========================================================= */

modelSelect.addEventListener(
    "change",
    async function () {

        const modelId =
            this.options[
                this.selectedIndex
            ].dataset.id;


        resetSelect(
            yearSelect,
            "Loading years..."
        );


        resetSelect(
            fuelSelect,
            "Loading fuel types..."
        );


        resetSelect(
            variantSelect,
            "Loading variants..."
        );


        if (!modelId) {

            resetSelect(
                yearSelect,
                "Select model first"
            );


            resetSelect(
                fuelSelect,
                "Select model first"
            );


            resetSelect(
                variantSelect,
                "Select model first"
            );


            previewName.textContent =
                companySelect.value ||
                "Your Vehicle";


            updateProgress();

            return;
        }


        previewName.textContent =
            companySelect.value +
            " " +
            modelSelect.value;


        /* -------------------------------------------------
           YEARS
           ------------------------------------------------- */

        const years =
            await loadData(
                "years",
                {
                    model_id:
                        modelId
                }
            );


        yearSelect.innerHTML =
            '<option value="">Select Year</option>';


        years.forEach(
            function (item) {

                const option =
                    document.createElement(
                        "option"
                    );


                option.value =
                    item.name;


                option.dataset.id =
                    item.id;


                option.textContent =
                    item.name;


                yearSelect.appendChild(
                    option
                );
            }
        );


        yearSelect.disabled =
            years.length === 0;


        /* -------------------------------------------------
           FUELS
           ------------------------------------------------- */

        const companyIdVal = companySelect.options[companySelect.selectedIndex] ? companySelect.options[companySelect.selectedIndex].dataset.id : "";
        const fuels = await loadData("fuels", {
            model_id: modelId,
            company_id: companyIdVal,
            company_name: companySelect.value || ""
        });


        fuelSelect.innerHTML =
            '<option value="">Select Fuel Type</option>';


        fuels.forEach(
            function (item) {

                const option =
                    document.createElement(
                        "option"
                    );


                option.value =
                    item.name;


                option.dataset.id =
                    item.id;


                option.textContent =
                    item.name;


                fuelSelect.appendChild(
                    option
                );
            }
        );


        fuelSelect.disabled =
            fuels.length === 0;

        if (fuels.length === 1) {
            fuelSelect.value = fuels[0].name;
            fuelSelect.dispatchEvent(new Event("change"));
        }


        /* -------------------------------------------------
           VARIANTS
           ------------------------------------------------- */

        const variants =
            await loadData(
                "variants",
                {
                    model_id:
                        modelId
                }
            );


        variantSelect.innerHTML =
            '<option value="">Select Variant</option>';


        if (variants && variants.length > 0) {
            variants.forEach(
                function (item) {

                    const option =
                        document.createElement(
                            "option"
                        );


                    option.value =
                        item.name;


                    option.dataset.id =
                        item.id;


                    option.textContent =
                        item.name;


                    variantSelect.appendChild(
                        option
                    );
                }
            );
        } else {
            const fallbacks = [
                "Base / Standard",
                "Mid / Executive",
                "Top / Luxury",
                "Other / Standard"
            ];
            fallbacks.forEach(function (name) {
                const option = document.createElement("option");
                option.value = name;
                option.textContent = name;
                variantSelect.appendChild(option);
            });
        }


        variantSelect.disabled = false;


        updatePreviewImage();


        updateProgress();
    }
);


/* =========================================================
   FUEL CHANGE
   ========================================================= */

fuelSelect.addEventListener(
    "change",
    function () {
        updatePreviewImage();
        updateProgress();
    }
);


/* =========================================================
   PROGRESS
   ========================================================= */

function updateProgress() {

    let completed = 0;


    const fields = [

        companySelect,

        modelSelect,

        yearSelect,

        fuelSelect,

        variantSelect,

        document.querySelector(
            '[name="license"]'
        ),

        document.querySelector(
            '[name="kms"]'
        )

    ];


    fields.forEach(
        function(field) {

            if (
                field &&
                field.value
            ) {

                completed++;
            }
        }
    );


    const percentage =
        Math.max(
            25,
            Math.round(
                (
                    completed /
                    fields.length
                ) * 100
            )
        );


    progressBar.style.width =
        percentage + "%";


    progressText.textContent =
        percentage + "%";
}


/* =========================================================
   WATCH FORM FIELDS
   ========================================================= */

document
    .querySelectorAll(
        "#addVehicleForm input, #addVehicleForm select"
    )
    .forEach(
        function(field) {

            field.addEventListener(
                "input",
                updateProgress
            );


            field.addEventListener(
                "change",
                updateProgress
            );
        }
    );


/* =========================================================
   INITIAL PROGRESS
   ========================================================= */

updateProgress();

</script>


</body>
</html>