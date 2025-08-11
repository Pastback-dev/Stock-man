<?php
ob_start(); 
include '../includes/connection.php';

// Fetch all enfants_en_charge with their birth dates from bd_active
$query = "
    SELECT e.id, e.code, e.matricule, b.date_naissance 
    FROM enfants_en_charge e 
    JOIN bd_active b ON e.code = b.CODE
";

$result = mysqli_query($db, $query);

$today = new DateTime();

while ($row = mysqli_fetch_assoc($result)) {
    $birthDate = new DateTime($row['date_naissance']);
    $age = $birthDate->diff($today)->y;

    if ($age >= 21) {
        // Reset en_charge in both tables if age >= 21
        $id = intval($row['id']);
        $code = intval($row['code']);

        $updateEnfants = "UPDATE enfants_en_charge SET en_charge = 0 WHERE id = $id";
        $updateBdActive = "UPDATE bd_active SET en_charge = 0 WHERE CODE = $code";

        mysqli_query($db, $updateEnfants);
        mysqli_query($db, $updateBdActive);
    }
}ob_end_flush();
?>
