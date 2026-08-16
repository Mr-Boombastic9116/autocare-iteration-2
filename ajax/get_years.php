<?php
require_once("../includes/db.php");
require_once("../includes/auth.php");
require_login();

$res = $conn->query("SELECT * FROM years ORDER BY year DESC");

echo '<option value="">Select Year</option>';

while ($row = $res->fetch_assoc()) {
    echo '<option value="' . htmlspecialchars($row['year']) . '" data-id="' . (int)$row['id'] . '">' . htmlspecialchars($row['year']) . '</option>';
}
