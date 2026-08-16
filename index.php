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

$error = "";

/* =========================================================
   LOGIN PROCESS
========================================================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {

        $error = "Please enter both username/email and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1"
        );

        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {

                session_regenerate_id(true);

                $_SESSION['user'] = $row['username'];
                $_SESSION['name'] = $row['name'] ?: $row['username'];

                header("Location: vehicles.php");
                exit();

            } else {

                $error = "Invalid username/email or password.";
            }

        } else {

            $error = "Invalid username/email or password.";
        }

        $stmt->close();
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

    <title>AutoCare | Sign In</title>

    <style>

        /* =========================================================
           GLOBAL
        ========================================================= */

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100%;
        }

        body {
            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #10202a;

            color: #142a37;
        }

        button,
        input {
            font-family: inherit;
        }

        /* =========================================================
           LOGIN PAGE
        ========================================================= */

        .login-page {

            min-height: 100vh;

            width: 100%;

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: center;

            position: relative;

            overflow: hidden;

            padding:
                45px 25px;
        }

        /* =========================================================
           BACKGROUND DECORATION
        ========================================================= */

        .login-page::before {

            content: "";

            position: absolute;

            width: 430px;
            height: 430px;

            border-radius: 50%;

            border:
                1px solid
                rgba(255, 191, 0, .15);

            right: -210px;
            top: -190px;
        }

        .login-page::after {

            content: "";

            position: absolute;

            width: 310px;
            height: 310px;

            border-radius: 50%;

            border:
                1px solid
                rgba(255, 191, 0, .10);

            left: -170px;
            bottom: -170px;
        }

        /* =========================================================
           DECORATIVE GLOW
        ========================================================= */

        .login-glow {

            position: absolute;

            width: 360px;
            height: 360px;

            border-radius: 50%;

            background:
                radial-gradient(
                    circle,
                    rgba(255, 191, 0, .12) 0%,
                    rgba(255, 191, 0, .05) 35%,
                    transparent 70%
                );

            right: 12%;
            top: 18%;

            pointer-events: none;
        }

        /* =========================================================
           LOGO
        ========================================================= */

        .login-logo {

            position: relative;

            z-index: 5;

            margin-bottom: 30px;

            display: flex;

            align-items: center;

            justify-content: center;
        }

        .login-logo img {

            width: auto;

            height: 58px;

            max-width: 260px;

            object-fit: contain;

            border-radius: 8px;
        }

        /* =========================================================
           LOGIN HEADING
        ========================================================= */

        .login-heading {

            position: relative;

            z-index: 5;

            text-align: center;

            margin-bottom: 30px;
        }

        .login-heading h1 {

            margin: 0 0 10px;

            color: #ffffff;

            font-size: 38px;

            line-height: 1.15;

            font-weight: 800;

            letter-spacing: -1.1px;
        }

        .login-heading p {

            margin: 0;

            color: #aebbc3;

            font-size: 17px;

            line-height: 1.6;
        }

        /* =========================================================
           LOGIN CARD
        ========================================================= */

        .login-card {

            position: relative;

            z-index: 5;

            width: min(100%, 470px);

            background: #ffffff;

            border-radius: 18px;

            padding: 38px 42px 35px;

            box-shadow:
                0 24px 60px
                rgba(0, 0, 0, .28);

            border:
                1px solid
                rgba(255, 255, 255, .15);
        }

        /* =========================================================
           FORM
        ========================================================= */

        .login-form {

            width: 100%;
        }

        .login-field {

            margin-bottom: 22px;
        }

        .login-field label {

            display: block;

            margin-bottom: 9px;

            color: #142a37;

            font-size: 16px;

            line-height: 1.4;

            font-weight: 700;
        }

        .login-field input {

            display: block;

            width: 100%;

            height: 56px;

            padding:
                0 16px;

            border:
                1px solid
                #d7dee3;

            border-radius: 9px;

            background: #fbfcfd;

            color: #172731;

            font-size: 16px;

            outline: none;

            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                background .2s ease;
        }

        .login-field input::placeholder {

            color: #9aa5ad;

            font-size: 15px;
        }

        .login-field input:focus {

            border-color: #ffbf00;

            background: #ffffff;

            box-shadow:
                0 0 0 4px
                rgba(255, 191, 0, .13);
        }

        /* =========================================================
           SIGN IN BUTTON
        ========================================================= */

        .login-submit {

            display: flex;

            align-items: center;

            justify-content: center;

            width: 100%;

            height: 56px;

            margin-top: 5px;

            padding: 0 20px;

            border: none;

            border-radius: 10px;

            background: #ffbf00;

            color: #101c23;

            font-size: 17px;

            font-weight: 800;

            cursor: pointer;

            box-shadow:
                0 8px 20px
                rgba(255, 191, 0, .20);

            transition:
                background .2s ease,
                transform .2s ease,
                box-shadow .2s ease;
        }

        .login-submit:hover {

            background: #ffca35;

            transform: translateY(-2px);

            box-shadow:
                0 12px 25px
                rgba(255, 191, 0, .25);
        }

        .login-submit:active {

            transform: translateY(0);
        }

        /* =========================================================
           ERROR MESSAGE
        ========================================================= */

        .login-error {

            margin-top: 18px;

            padding:
                12px 14px;

            border-radius: 9px;

            background: #fff2f2;

            border:
                1px solid
                #f1c8c8;

            color: #b42318;

            font-size: 15px;

            line-height: 1.5;

            text-align: center;
        }

        /* =========================================================
           SIGN UP
        ========================================================= */

        .login-signup {

            margin-top: 24px;

            text-align: center;

            color: #697781;

            font-size: 15px;

            line-height: 1.5;
        }

        .login-signup a {

            color: #a87500;

            font-size: 15px;

            font-weight: 800;

            text-decoration: none;

            margin-left: 3px;
        }

        .login-signup a:hover {

            color: #d59b00;

            text-decoration: underline;
        }

        /* =========================================================
           SMALL BRAND LINE
        ========================================================= */

        .login-bottom-text {

            position: relative;

            z-index: 5;

            margin-top: 24px;

            color: rgba(255, 255, 255, .42);

            font-size: 12px;

            letter-spacing: .3px;

            text-align: center;
        }

        /* =========================================================
           TABLET
        ========================================================= */

        @media (max-width: 700px) {

            .login-page {

                padding:
                    35px 20px;
            }

            .login-logo {

                margin-bottom: 24px;
            }

            .login-logo img {

                height: 52px;

                max-width: 230px;
            }

            .login-heading {

                margin-bottom: 25px;
            }

            .login-heading h1 {

                font-size: 34px;
            }

            .login-heading p {

                font-size: 16px;
            }

            .login-card {

                padding:
                    34px 30px 31px;
            }
        }

        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 480px) {

            .login-page {

                min-height: 100vh;

                padding:
                    30px 16px;
            }

            .login-logo {

                margin-bottom: 20px;
            }

            .login-logo img {

                height: 48px;

                max-width: 210px;
            }

            .login-heading {

                margin-bottom: 22px;
            }

            .login-heading h1 {

                font-size: 30px;

                letter-spacing: -.7px;
            }

            .login-heading p {

                font-size: 15px;

                padding: 0 10px;
            }

            .login-card {

                width: 100%;

                padding:
                    30px 22px 28px;

                border-radius: 15px;
            }

            .login-field {

                margin-bottom: 19px;
            }

            .login-field label {

                font-size: 15px;
            }

            .login-field input {

                height: 54px;

                font-size: 16px;
            }

            .login-submit {

                height: 54px;

                font-size: 16px;
            }

            .login-signup {

                font-size: 14px;
            }

            .login-signup a {

                font-size: 14px;
            }

            .login-bottom-text {

                font-size: 11px;
            }
        }

    </style>

</head>


<body>


<main class="login-page">


    <!-- BACKGROUND GLOW -->

    <div class="login-glow"></div>


    <!-- =====================================================
         LOGO
    ====================================================== -->

    <div class="login-logo">

        <img
            src="assets/images/logo.png"
            alt="AutoCare"
        >

    </div>


    <!-- =====================================================
         WELCOME
    ====================================================== -->

    <div class="login-heading">

        <h1>
            Welcome back
        </h1>

        <p>
            Sign in to continue to your AutoCare account.
        </p>

    </div>


    <!-- =====================================================
         LOGIN CARD
    ====================================================== -->

    <section class="login-card">


        <form
            method="POST"
            class="login-form"
            novalidate
        >


            <!-- USERNAME / EMAIL -->

            <div class="login-field">

                <label for="username">
                    Username or Email
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter your username or email"
                    autocomplete="username"
                    required
                    autofocus
                    value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                >

            </div>


            <!-- PASSWORD -->

            <div class="login-field">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >

            </div>


            <!-- SIGN IN -->

            <button
                type="submit"
                class="login-submit"
            >
                Sign In
            </button>


        </form>


        <!-- =================================================
             ERROR
        ================================================== -->

        <?php if ($error != "") { ?>

            <div class="login-error">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php } ?>


        <!-- =================================================
             SIGN UP
        ================================================== -->

        <div class="login-signup">

            Don't have an account?

            <a href="signup.php">
                Create one
            </a>

        </div>


    </section>


    <!-- =====================================================
         BOTTOM TEXT
    ====================================================== -->

    <?php include("includes/footer.php"); ?>


</main>


</body>

</html>