<?php
/**
 * Shared header for logged-in app pages.
 * Expects $conn, session already started, and $_SESSION['user'] set
 * (call require_login() before including this).
 * Optional: set $ACTIVE_NAV to one of: vehicles, bookings
 */
$ACTIVE_NAV = $ACTIVE_NAV ?? '';
?>
<div class="header-content header-flex app-header">
    <div class="header-left" onclick="window.location.href='home.php'" style="cursor:pointer;">
        <img src="assets/images/logo.png" class="logo" alt="AutoCare logo">
        <span class="divider">|</span>
        <h1>Auto<span>Care</span></h1>
    </div>

    <nav class="app-nav">
        <a href="vehicles.php" class="<?= $ACTIVE_NAV === 'vehicles' ? 'active' : '' ?>">My Vehicles</a>
        <a href="bookings.php" class="<?= $ACTIVE_NAV === 'bookings' ? 'active' : '' ?>">My Bookings</a>
    </nav>

    <div class="header-right">
        <img src="assets/images/profile.png" class="profile-icon-new" id="profileBtn" alt="Profile">

        <div class="dropdown-new" id="dropdown">
            <div class="dropdown-username"><?= htmlspecialchars(current_display_name()) ?></div>
            <a href="vehicles.php">My Vehicles</a>
            <a href="bookings.php">My Bookings</a>
            <a href="logout.php">
                <img src="assets/images/logout.png" alt=""> Logout
            </a>
        </div>
    </div>
</div>

<script>
(function(){
    var profileBtn = document.getElementById("profileBtn");
    var dropdown = document.getElementById("dropdown");
    if (!profileBtn || !dropdown) return;

    profileBtn.addEventListener("click", function (e) {
        e.stopPropagation();
        dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
    });

    document.addEventListener("click", function () {
        dropdown.style.display = "none";
    });
})();
</script>
