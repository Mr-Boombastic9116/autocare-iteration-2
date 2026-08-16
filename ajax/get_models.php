<?php
require_once("../includes/db.php");
require_once("../includes/auth.php");
require_login();

$company_id = isset($_GET['company_id']) ? (int)$_GET['company_id'] : 0;

echo '<option value="">Select Model</option>';

if ($company_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM models WHERE company_id = ? ORDER BY name");
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($row['name']) . '" data-id="' . (int)$row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
        }
    } else {
        echo '<option value="" disabled>No models found</option>';
    }
    $stmt->close();
}
