<?php
include '../includes/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_mat = 'H' . uniqid(); // Generates ID starting with H followed by unique string
    $nom = mysqli_real_escape_string($db, $_POST['nom']);

    // Insert into famille_hors_cadre
    $query = "INSERT INTO famille_hors_cadre (id_mat, nom) VALUES ('$id_mat', '$nom')";
    mysqli_query($db, $query) or die(mysqli_error($db));

    header("Location: famille_hors_cadre.php");
    exit();
}
?>

<?php include '../includes/sidebar.php'; ?>
<div class="container mt-4">
    <h4 class="mb-3">Ajouter une Famille Hors Cadre</h4>
    <form method="POST" action="">
        <div class="form-group">
            <label>Nom de la Famille</label>
            <input type="text" name="nom" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
        <a href="famille_hors_cadre.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
<?php include '../includes/footer.php'; ?>