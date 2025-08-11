<?php
include '../includes/connection.php';
include '../includes/sidebar.php';

$code = isset($_GET['code']) ? intval($_GET['code']) : 0;

// Fetch the current record
$query = "SELECT * FROM liste_rouges WHERE code = $code";
$result = mysqli_query($db, $query);
$data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = mysqli_real_escape_string($db, $_POST['nom']);
    $prenom = mysqli_real_escape_string($db, $_POST['prenom']);
    $date_desactivation = $_POST['date_desactivation'];
    $date_activation = $_POST['date_activation'];
    $nombre = intval($_POST['nombre']);
    $motif = mysqli_real_escape_string($db, $_POST['motif']);
    $etat = isset($_POST['etat']) ? 1 : 0;

    $update = "
        UPDATE liste_rouges SET
            
            date_desactivation = '$date_desactivation',
            date_activation = '$date_activation',
            nombre = $nombre,
            motif = '$motif',
            etat = $etat
        WHERE code = $code
    ";

    if (mysqli_query($db, $update)) {
        header("Location: liste_rouges.php");
        exit;
    } else {
        echo "Erreur : " . mysqli_error($db);
    }
}
?>

<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h4 class="m-2 font-weight-bold text-primary">Modifier l'entrée Liste Rouge
      <a href="liste_rouges.php" class="btn btn-secondary float-right" style="border-radius: 0px;">
        <i class="fas fa-fw fa-arrow-left"></i> Retour
      </a>
    </h4>
  </div>
  <div class="card-body">
    <form method="POST">
      

      <div class="form-group">
        <label>Date Désactivation:</label>
        <input type="date" class="form-control" name="date_desactivation" value="<?= $data['date_desactivation'] ?>">
      </div>

      <div class="form-group">
        <label>Date Activation:</label>
        <input type="date" class="form-control" name="date_activation" value="<?= $data['date_activation'] ?>">
      </div>

      <div class="form-group">
        <label>Nombre:</label>
        <input type="number" class="form-control" name="nombre" value="<?= $data['nombre'] ?>">
      </div>

      <div class="form-group">
        <label>Motif:</label>
        <input type="text" class="form-control" name="motif" value="<?= htmlspecialchars($data['motif']) ?>">
      </div>

      <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" name="etat" id="etat" <?= $data['etat'] ? 'checked' : '' ?>>
        <label class="form-check-label" for="etat">Actif</label>
      </div>

      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Mettre à jour</button>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
