<?php
include '../includes/connection.php';

if (isset($_GET['matricule'])) {
    $matricule = intval($_GET['matricule']);
    $query = "SELECT COUNT(*) as count FROM bd_active WHERE MATRICULE = $matricule AND en_charge = 1";
    $result = mysqli_query($db, $query);
    $row = mysqli_fetch_assoc($result);
    echo json_encode(['count' => $row['count']]);
} else {
    echo json_encode(['count' => 0]);
}
?>