<?php
// famille_delete.php — Suppression d’une famille
// =============================================================
// Appel : famille_delete.php?id=123
// -------------------------------------------------------------
// • Vérifie le rôle de l’utilisateur (interdit aux simples « User »)
// • Valide l’ID (numérique)
// • Supprime la famille via un prepared‑statement
// • Affiche un message de succès / erreur puis redirige vers famille.php
// =============================================================

session_start();
include '../includes/connection.php';
include '../includes/sidebar.php';

// -------------------------------------------------------------
// 0. Debug : afficher toutes les erreurs pendant le développement
// -------------------------------------------------------------
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', '1');

// -------------------------------------------------------------
// 1. Contrôle d’accès — seules les personnes ≠ « User » peuvent supprimer
// -------------------------------------------------------------
$idSession = $_SESSION['MEMBER_ID'] ?? 0;
$stmt = $db->prepare('SELECT t.TYPE
                      FROM users u
                      JOIN type t ON t.TYPE_ID = u.TYPE_ID
                      WHERE u.ID = ?');
$stmt->bind_param('i', $idSession);
$stmt->execute();
$stmt->bind_result($connectedType);
$stmt->fetch();
$stmt->close();

if ($connectedType === 'User') {
    echo '<script>alert("Restricted Page! You will be redirected to POS");window.location="pos.php";</script>';
    exit;
}

// -------------------------------------------------------------
// 2. Validation du paramètre GET « id »
// -------------------------------------------------------------
$id = $_GET['id'] ?? '';
if (!ctype_digit($id)) {
    header('Location: famille.php');
    exit;
}
$id = (int)$id;

// -------------------------------------------------------------
// 3. Suppression de la famille
// -------------------------------------------------------------
try {
    // Pour éviter l’erreur FK, assurez‑vous que vos tables enfants
    // (ex. membres) ont ON DELETE CASCADE OU supprimez‑les ici :
    // $db->query('DELETE FROM membres WHERE id_mat_fam = ' . $id);

    $stmt = $db->prepare('DELETE FROM famille WHERE id_mat_fam = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    if ($affected === 0) {
        // L’ID n’existe plus (ou déjà supprimé)
        echo '<script>alert("Famille introuvable ou déjà supprimée.");window.location="famille.php";</script>';
        exit;
    }

    echo '<script>alert("La famille a été supprimée avec succès.");window.location="famille.php";</script>';
    exit;
} catch (mysqli_sql_exception $e) {
    // Erreur typique : contrainte d’intégrité
    $msg = addslashes($e->getMessage());
    echo "<script>alert('Erreur lors de la suppression : $msg');window.location='famille.php';</script>";
    exit;
}
?>
