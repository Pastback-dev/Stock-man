<?php
include '../includes/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_mat_fam = $_POST['id_mat_fam'];
    $nom_famille = $_POST['nom_famille'];

    // Validation serveur : matricule = 10 chiffres
    if (!preg_match('/^\d{10}$/', $id_mat_fam)) {
        echo "<script>alert('Le matricule doit contenir exactement 10 chiffres.'); window.history.back();</script>";
        exit();
    }

    $query = "INSERT INTO famille (id_mat_fam, nom_famille, type) VALUES ('$id_mat_fam', '$nom_famille', 'RT')";
    mysqli_query($db, $query) or die(mysqli_error($db));

    header("Location: famille_retraite.php");
    exit();
}
?>

<?php include '../includes/sidebar.php'; ?>
<div class="container mt-4">
    <h4 class="mb-3">Ajouter une Famille Retraitée</h4>
    <form method="POST" action="">
        <div class="form-group">
            <label>ID Matricule Famille</label>
            <input type="text" name="id_mat_fam" class="form-control" required pattern="\d{10}" title="Le matricule doit contenir exactement 10 chiffres">
        </div>
        <div class="form-group">
            <label>Nom de la Famille</label>
            <input type="text" name="nom_famille" class="form-control" required>
        </div>
        <input type="hidden" name="type" value="RT">
        <button type="submit" class="btn btn-primary">Ajouter</button>
        <a href="famille_retraite.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
<?php include '../includes/footer.php'; ?>
