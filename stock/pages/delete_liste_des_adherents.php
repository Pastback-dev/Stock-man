<?php
include '../includes/connection.php';

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Delete the adherent
    $deleteQuery = "DELETE FROM liste_des_adherents WHERE id = $id";
    $result = mysqli_query($db, $deleteQuery);
    
    if ($result) {
        $_SESSION['success_message'] = "Adhérent supprimé avec succès.";
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression: " . mysqli_error($db);
    }
}

// Redirect back to the list
header("Location: liste_des_adherents.php");
exit();
?>