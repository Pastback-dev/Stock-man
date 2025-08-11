<?php
include '../includes/connection.php';

$id_mat = isset($_GET['id']) ? $_GET['id'] : '';

// Check if family exists
$checkQuery = "SELECT * FROM famille_hors_cadre WHERE id_mat = '$id_mat'";
$checkResult = mysqli_query($db, $checkQuery);

if (mysqli_num_rows($checkResult) == 0) {
    header("Location: famille_hors_cadre.php");
    exit();
}

// Delete the family
$deleteQuery = "DELETE FROM famille_hors_cadre WHERE id_mat = '$id_mat'";
mysqli_query($db, $deleteQuery) or die(mysqli_error($db));

header("Location: famille_hors_cadre.php");
exit();
?>