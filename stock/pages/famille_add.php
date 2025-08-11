<?php
include '../includes/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_mat_fam = $_POST['id_mat_fam'];
    $nom_famille = $_POST['nom_famille'];
    $type = $_POST['type'];

    $query = "INSERT INTO famille (id_mat_fam, nom_famille, type) VALUES ('$id_mat_fam', '$nom_famille', '$type')";
    mysqli_query($db, $query) or die(mysqli_error($db));

    header("Location: famille.php");
    exit();
}
?>

<?php include '../includes/sidebar.php'; ?>
<div class="container mt-4">
    <h4 class="mb-3">Ajouter une Famille</h4>
    <form method="POST" action="">
        <div class="form-group">
            <label>ID Matricule Famille</label>
            <input type="text" name="id_mat_fam" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Nom de la Famille</label>
            <input type="text" name="nom_famille" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Type de Famille</label>
            <select name="type" class="form-control" required>
                <option value="">Sélectionner un type</option>
                <option value="NR">OE_TAMCA</option>
                <option value="HC">Hors Cadre</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
        <a href="famille.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
<?php include '../includes/footer.php'; ?>
