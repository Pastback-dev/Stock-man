<?php
include '../includes/connection.php';

// Get the family ID from URL
$id_mat = isset($_GET['id']) ? $_GET['id'] : '';

// Fetch existing data
$query = "SELECT * FROM famille_hors_cadre WHERE id_mat = '$id_mat'";
$result = mysqli_query($db, $query);
$famille = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = mysqli_real_escape_string($db, $_POST['nom']);

    $updateQuery = "UPDATE famille_hors_cadre SET nom = '$nom' WHERE id_mat = '$id_mat'";
    mysqli_query($db, $updateQuery) or die(mysqli_error($db));

    header("Location: famille_hors_cadre.php");
    exit();
}
?>

<?php include '../includes/sidebar.php'; ?>
<div class="container mt-4">
    <h4 class="mb-3">Modifier Famille Hors Cadre</h4>
    <form method="POST" action="">
        <div class="form-group">
            <label>ID Famille</label>
            <input type="text" value="<?php echo htmlspecialchars($famille['id_mat']); ?>" class="form-control" readonly>
        </div>
        <div class="form-group">
            <label>Nom de la Famille</label>
            <input type="text" name="nom" value="<?php echo htmlspecialchars($famille['nom']); ?>" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="famille_hors_cadre.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
<?php include '../includes/footer.php'; ?>