<?php

/**
 * AutoCare shared application header.
 *
 * Clean version:
 * AutoCare                         My Bookings       Profile
 *
 * Expects the session to already be started.
 */

$ACTIVE_NAV = $ACTIVE_NAV ?? '';

$is_logged_in = isset($_SESSION['user']);

?>

<header class="ac-app-header">


    <!-- =====================================================
         BRAND
         ===================================================== -->

    <a
        href="home.php"
        class="ac-header-brand"
        aria-label="AutoCare Home"
    >

        <img
            src="assets/images/logo.png"
            alt="AutoCare"
            class="ac-header-logo"
        >

        <span class="ac-header-divider"></span>

        <span class="ac-header-name">
            Auto<span>Care</span>
        </span>

    </a>


    <!-- =====================================================
         RIGHT SIDE
         ===================================================== -->

    <?php if ($is_logged_in): ?>

        <div class="ac-header-right">


            <!-- MY BOOKINGS -->

            <a
                href="bookings.php"
                class="ac-header-bookings
                <?= $ACTIVE_NAV === 'bookings'
                    ? 'active'
                    : ''
                ?>"
            >
                My Bookings
            </a>


            <!-- PROFILE -->

            <div class="ac-profile-wrapper">

                <button
                    type="button"
                    class="ac-profile-button"
                    id="acProfileButton"
                    aria-label="Open profile menu"
                    aria-expanded="false"
                >

                    <img
                        src="assets/images/profile.png"
                        alt="Profile"
                    >

                </button>


                <!-- PROFILE DROPDOWN -->

                <div
                    class="ac-profile-dropdown"
                    id="acProfileDropdown"
                >

                    <div class="ac-profile-name">

                        <?= htmlspecialchars(
                            current_display_name()
                        ) ?>

                    </div>


                    <a href="vehicles.php">
                        My Vehicles
                    </a>


                    <a href="bookings.php">
                        My Bookings
                    </a>


                    <a href="logout.php">
                        Logout
                    </a>

                </div>

            </div>


        </div>


    <?php else: ?>


        <!-- GUEST HEADER -->

        <div class="ac-header-right">

            <a
                href="index.php"
                class="ac-header-login"
            >
                Login
            </a>


            <a
                href="signup.php"
                class="ac-header-signup"
            >
                Sign Up
            </a>

        </div>


    <?php endif; ?>


</header>


<script>

(function () {

    const button =
        document.getElementById("acProfileButton");

    const dropdown =
        document.getElementById("acProfileDropdown");


    if (!button || !dropdown) {
        return;
    }


    button.addEventListener("click", function (event) {

        event.stopPropagation();

        const isOpen =
            dropdown.classList.toggle("open");

        button.setAttribute(
            "aria-expanded",
            isOpen ? "true" : "false"
        );

    });


    dropdown.addEventListener(
        "click",
        function (event) {

            event.stopPropagation();

        }
    );


    document.addEventListener(
        "click",
        function () {

            dropdown.classList.remove("open");

            button.setAttribute(
                "aria-expanded",
                "false"
            );

        }
    );

})();

</script>