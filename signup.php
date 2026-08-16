<?php

require_once("includes/db.php");
require_once("includes/auth.php");


/* =========================================================
   ALREADY LOGGED IN
   ========================================================= */

if (isset($_SESSION['user'])) {
    header("Location: vehicles.php");
    exit();
}


$success = false;
$error = "";
$old = [];


/* =========================================================
   SIGNUP PROCESS
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name         = trim($_POST['name'] ?? '');
    $username     = trim($_POST['username'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $password     = $_POST['password'] ?? '';
    $mobile       = trim($_POST['mobile'] ?? '');
    $country_code = trim($_POST['country_code'] ?? '');
    $state        = trim($_POST['state'] ?? '');
    $city         = trim($_POST['city'] ?? '');
    $pincode      = trim($_POST['pincode'] ?? '');
    $address      = trim($_POST['address'] ?? '');
    $notify_email = isset($_POST['notify_email']) ? 1 : 0;


    $old = compact(
        'name',
        'username',
        'email',
        'mobile',
        'country_code',
        'state',
        'city',
        'pincode',
        'address'
    );


    /* =====================================================
       VALIDATION
       ===================================================== */

    if (
        $name === '' ||
        $username === '' ||
        $email === '' ||
        $password === '' ||
        $mobile === ''
    ) {

        $error = "Please fill in all required fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (!preg_match('/^[A-Za-z0-9_.]{3,50}$/', $username)) {

        $error = "Username must be 3-50 characters (letters, numbers, underscore, dot only).";

    } elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters long.";

    } elseif (!preg_match('/^[0-9]{6,15}$/', $mobile)) {

        $error = "Please enter a valid mobile number.";

    } else {

        /* =================================================
           CHECK EXISTING USER
           ================================================= */

        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ? OR username = ?"
        );

        $check->bind_param("ss", $email, $username);
        $check->execute();

        $result = $check->get_result();


        if ($result->num_rows > 0) {

            $error = "Username or Email already exists!";

        } else {

            /* =================================================
               HASH PASSWORD
               ================================================= */

            $hashed = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            /* =================================================
               INSERT USER
               ================================================= */

            $stmt = $conn->prepare(
                "INSERT INTO users
                (
                    name,
                    username,
                    email,
                    password,
                    mobile,
                    country_code,
                    state,
                    city,
                    pincode,
                    address,
                    notify_email
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );


            $stmt->bind_param(
                "ssssssssssi",
                $name,
                $username,
                $email,
                $hashed,
                $mobile,
                $country_code,
                $state,
                $city,
                $pincode,
                $address,
                $notify_email
            );


            if ($stmt->execute()) {

                $success = true;

            } else {

                error_log(
                    "Signup insert failed: " . $stmt->error
                );

                $error = "Something went wrong. Please try again.";
            }


            $stmt->close();
        }


        $check->close();
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

    <title>AutoCare | Sign Up</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <style>

        /* =========================================================
           SIGNUP PAGE
           ========================================================= */

        * {
            box-sizing: border-box;
        }


        html,
        body {
            margin: 0;
            padding: 0;
        }


        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #10202a;
            color: #142a37;
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }


        /* =========================================================
           PAGE WRAPPER
           ========================================================= */

        .signup-page {

            min-height: calc(100vh - 67px);

            width: 100%;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 45px 24px;

            position: relative;

            overflow: hidden;

            background: #10202a;
        }

        .signup-page::before {

            content: "";

            position: absolute;

            width: 430px;
            height: 430px;

            border-radius: 50%;

            border: 1px solid rgba(255, 191, 0, .15);

            right: -210px;
            top: -190px;
        }

        .signup-page::after {

            content: "";

            position: absolute;

            width: 310px;
            height: 310px;

            border-radius: 50%;

            border: 1px solid rgba(255, 191, 0, .10);

            left: -170px;
            bottom: -170px;
        }


        /* =========================================================
           SIGNUP CARD
           ========================================================= */

        .signup-card {

            width: min(100%, 920px);

            background: #ffffff;

            border-radius: 24px;

            border: 1px solid rgba(16, 24, 32, 0.05);

            box-shadow:
                0 20px 55px rgba(16, 24, 32, 0.12);

            padding: 48px 58px 45px;

            position: relative;

            overflow: hidden;
        }


        /* =========================================================
           TOP YELLOW ACCENT
           ========================================================= */

        .signup-card::before {

            content: "";

            position: absolute;

            top: 0;
            left: 0;

            width: 100%;

            height: 5px;

            background: #ffc107;
        }


        /* =========================================================
           LOGO
           ========================================================= */

        .signup-brand {

            display: flex;

            justify-content: center;

            align-items: center;

            margin-bottom: 24px;
        }


        .signup-brand img {

            height: 48px;

            width: auto;

            max-width: 200px;

            object-fit: contain;
        }


        /* =========================================================
           HEADING
           ========================================================= */

        .signup-heading {

            text-align: center;

            margin-bottom: 35px;
        }


        .signup-heading h1 {

            margin: 0 0 10px;

            color: #101820;

            font-size: 38px;

            line-height: 1.2;

            font-weight: 800;

            letter-spacing: -0.5px;
        }


        .signup-heading p {

            margin: 0;

            color: #697681;

            font-size: 18px;

            line-height: 1.55;

            font-weight: 500;
        }


        /* =========================================================
           FORM
           ========================================================= */

        .signup-form {

            width: 100%;
        }


        /* =========================================================
           FIELD ROW
           ========================================================= */

        .signup-row {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 22px;

            width: 100%;
        }


        /* =========================================================
           MOBILE NUMBER ROW
           ========================================================= */

        .signup-row.mobile-row {

            grid-template-columns: 120px 1fr;
        }


        /* =========================================================
           FIELD
           ========================================================= */

        .signup-field {

            width: 100%;

            margin-bottom: 22px;

            min-width: 0;
        }


        /* =========================================================
           LABEL
           ========================================================= */

        .signup-field label {

            display: block;

            margin-bottom: 9px;

            color: #27343e;

            font-size: 16px;

            line-height: 1.4;

            font-weight: 750;
        }


        /* =========================================================
           INPUT
           ========================================================= */

        .signup-field input,
        .signup-field textarea {

            width: 100%;

            height: 56px;

            margin: 0;

            padding: 0 17px;

            border: 1px solid #d5dce1;

            border-radius: 11px;

            background: #fbfcfd;

            color: #18232c;

            font-family: inherit;

            font-size: 17px;

            font-weight: 500;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                background 0.2s ease;
        }


        /* =========================================================
           TEXTAREA
           ========================================================= */

        .signup-field textarea {

            height: 105px;

            padding: 14px 17px;

            resize: vertical;

            line-height: 1.55;
        }


        /* =========================================================
           PLACEHOLDER
           ========================================================= */

        .signup-field input::placeholder,
        .signup-field textarea::placeholder {

            color: #9aa5ad;

            opacity: 1;
        }


        /* =========================================================
           INPUT FOCUS
           ========================================================= */

        .signup-field input:focus,
        .signup-field textarea:focus {

            outline: none;

            border-color: #e5ae12;

            background: #ffffff;

            box-shadow:
                0 0 0 4px rgba(255, 193, 7, 0.13);
        }


        /* =========================================================
           CHECKBOX
           ========================================================= */

        .signup-checkbox {

            display: flex;

            align-items: center;

            gap: 11px;

            margin: 2px 0 25px;

            color: #444f57;

            font-size: 16px;

            line-height: 1.4;

            font-weight: 600;

            cursor: pointer;
        }


        .signup-checkbox input {

            width: 19px;

            height: 19px;

            margin: 0;

            accent-color: #ffc107;

            cursor: pointer;

            flex-shrink: 0;
        }


        /* =========================================================
           SIGN UP BUTTON
           ========================================================= */

        .signup-submit {

            width: 100%;

            height: 58px;

            margin-top: 2px;

            border: 0;

            border-radius: 11px;

            background: #ffc107;

            color: #151a1e;

            font-family: inherit;

            font-size: 18px;

            font-weight: 800;

            letter-spacing: 0.1px;

            cursor: pointer;

            transition:
                background 0.2s ease,
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }


        .signup-submit:hover {

            background: #eab000;

            transform: translateY(-1px);

            box-shadow:
                0 8px 20px rgba(234, 176, 0, 0.20);
        }


        .signup-submit:active {

            transform: translateY(0);
        }


        /* =========================================================
           ERROR MESSAGE
           ========================================================= */

        .signup-error {

            margin-top: 20px;

            padding: 14px 16px;

            border-radius: 10px;

            background: #fff1f1;

            border: 1px solid #f3caca;

            color: #b42318;

            font-size: 15px;

            line-height: 1.5;

            font-weight: 600;

            text-align: left;
        }


        /* =========================================================
           LOGIN LINK
           ========================================================= */

        .signup-login {

            margin-top: 25px;

            color: #68757f;

            text-align: center;

            font-size: 16px;

            line-height: 1.5;

            font-weight: 500;
        }


        .signup-login a {

            color: #a97900;

            font-weight: 800;

            text-decoration: none;

            margin-left: 3px;
        }


        .signup-login a:hover {

            text-decoration: underline;
        }


        /* =========================================================
           FOOTER
           ========================================================= */

        .signup-footer {

            width: 100%;

            min-height: 67px;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 20px;

            box-sizing: border-box;

            background: #101820;

            color: rgba(255, 255, 255, 0.72);

            text-align: center;

            font-size: 15px;

            font-weight: 500;

            border-top: 1px solid rgba(255, 193, 7, 0.12);
        }


        /* =========================================================
           TABLET
           ========================================================= */

        @media (max-width: 760px) {

            .signup-page {

                padding: 35px 18px;
            }


            .signup-card {

                padding: 40px 38px 38px;

                border-radius: 20px;
            }


            .signup-heading h1 {

                font-size: 34px;
            }


            .signup-heading p {

                font-size: 17px;
            }


            .signup-row {

                grid-template-columns: 1fr;

                gap: 0;
            }


            .signup-row.mobile-row {

                grid-template-columns: 105px 1fr;

                gap: 18px;
            }
        }


        /* =========================================================
           MOBILE
           ========================================================= */

        @media (max-width: 520px) {

            .signup-page {

                padding: 25px 13px;
            }


            .signup-card {

                padding: 35px 22px 32px;

                border-radius: 18px;
            }


            .signup-brand {

                margin-bottom: 20px;
            }


            .signup-brand img {

                height: 42px;
            }


            .signup-heading {

                margin-bottom: 28px;
            }


            .signup-heading h1 {

                font-size: 30px;

                letter-spacing: -0.3px;
            }


            .signup-heading p {

                font-size: 16px;

                line-height: 1.5;
            }


            .signup-field {

                margin-bottom: 19px;
            }


            .signup-field label {

                font-size: 15px;

                margin-bottom: 8px;
            }


            .signup-field input,
            .signup-field textarea {

                font-size: 16px;
            }


            .signup-row.mobile-row {

                grid-template-columns: 95px 1fr;

                gap: 12px;
            }


            .signup-checkbox {

                font-size: 15px;
            }


            .signup-submit {

                height: 56px;

                font-size: 17px;
            }


            .signup-login {

                font-size: 15px;
            }


            .signup-footer {

                min-height: 60px;

                padding: 18px 15px;

                font-size: 14px;
            }
        }


        /* =========================================================
           VERY SMALL SCREENS
           ========================================================= */

        @media (max-width: 360px) {

            .signup-card {

                padding-left: 18px;

                padding-right: 18px;
            }


            .signup-heading h1 {

                font-size: 27px;
            }


            .signup-row.mobile-row {

                grid-template-columns: 1fr;

                gap: 0;
            }
        }

    </style>


    <script>

        function showSuccessAndRedirect() {

            window.location.href = "index.php?registered=1";

        }

    </script>

</head>


<body>


<!-- =========================================================
     SIGNUP PAGE
     NO HEADER
     ========================================================= -->

<main class="signup-page">


    <section class="signup-card">


        <!-- =====================================================
             LOGO
             ===================================================== -->

        <div class="signup-brand">

            <img
                src="assets/images/logo.png"
                alt="AutoCare"
            >

        </div>


        <!-- =====================================================
             PAGE HEADING
             ===================================================== -->

        <div class="signup-heading">

            <h1>
                Create your account
            </h1>

            <p>
                Sign up to start managing your vehicles with AutoCare.
            </p>

        </div>


        <!-- =====================================================
             SIGNUP FORM
             ===================================================== -->

        <form
            method="POST"
            class="signup-form"
            novalidate
        >


            <!-- FULL NAME -->

            <div class="signup-field">

                <label for="name">
                    Full Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your full name"
                    required
                    value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                >

            </div>


            <!-- USERNAME + EMAIL -->

            <div class="signup-row">


                <div class="signup-field">

                    <label for="username">
                        Username
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Choose a username"
                        required
                        value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                    >

                </div>


                <div class="signup-field">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="you@example.com"
                        required
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    >

                </div>


            </div>


            <!-- PASSWORD -->

            <div class="signup-field">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimum 6 characters"
                    required
                    minlength="6"
                >

            </div>


            <!-- COUNTRY CODE + MOBILE -->

            <div class="signup-row mobile-row">


                <div class="signup-field">

                    <label for="country_code">
                        Code
                    </label>

                    <input
                        type="text"
                        id="country_code"
                        name="country_code"
                        placeholder="+91"
                        required
                        value="<?= htmlspecialchars($old['country_code'] ?? '+91') ?>"
                    >

                </div>


                <div class="signup-field">

                    <label for="mobile">
                        Mobile Number
                    </label>

                    <input
                        type="text"
                        id="mobile"
                        name="mobile"
                        placeholder="Enter your mobile number"
                        required
                        value="<?= htmlspecialchars($old['mobile'] ?? '') ?>"
                    >

                </div>


            </div>


            <!-- STATE + CITY -->

            <div class="signup-row">


                <div class="signup-field">

                    <label for="state">
                        State
                    </label>

                    <input
                        type="text"
                        id="state"
                        name="state"
                        placeholder="State"
                        required
                        value="<?= htmlspecialchars($old['state'] ?? '') ?>"
                    >

                </div>


                <div class="signup-field">

                    <label for="city">
                        City
                    </label>

                    <input
                        type="text"
                        id="city"
                        name="city"
                        placeholder="City"
                        required
                        value="<?= htmlspecialchars($old['city'] ?? '') ?>"
                    >

                </div>


            </div>


            <!-- PINCODE -->

            <div class="signup-field">

                <label for="pincode">
                    Pincode
                </label>

                <input
                    type="text"
                    id="pincode"
                    name="pincode"
                    placeholder="Pincode"
                    required
                    value="<?= htmlspecialchars($old['pincode'] ?? '') ?>"
                >

            </div>


            <!-- FULL ADDRESS -->

            <div class="signup-field">

                <label for="address">
                    Full Address
                </label>

                <textarea
                    id="address"
                    name="address"
                    rows="3"
                    placeholder="Street, area, landmark"
                ><?= htmlspecialchars($old['address'] ?? '') ?></textarea>

            </div>


            <!-- EMAIL NOTIFICATIONS -->

            <label class="signup-checkbox">

                <input
                    type="checkbox"
                    name="notify_email"
                    checked
                >

                <span>
                    Receive email notifications
                </span>

            </label>


            <!-- SIGN UP BUTTON -->

            <button
                type="submit"
                class="signup-submit"
            >
                Sign Up
            </button>


        </form>


        <!-- =====================================================
             ERROR MESSAGE
             ===================================================== -->

        <?php if ($error != "") { ?>

            <div class="signup-error">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php } ?>


        <!-- =====================================================
             LOGIN LINK
             ===================================================== -->

        <div class="signup-login">

            Already have an account?

            <a href="index.php">
                Sign in
            </a>

        </div>


    </section>


</main>


<!-- =========================================================
     DARK FOOTER
     ========================================================= -->

<?php include("includes/footer.php"); ?>


<?php if ($success) { ?>

<script>

    showSuccessAndRedirect();

</script>

<?php } ?>


</body>

</html>