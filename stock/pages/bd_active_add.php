<?php
ob_start();
include '../includes/connection.php';
include '../includes/sidebar.php';

$matricule = isset($_GET['matricule']) ? intval($_GET['matricule']) : 0;
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = mysqli_real_escape_string($db, $_POST['nom']);
    $prenom = mysqli_real_escape_string($db, $_POST['prenom']);
    $lien = mysqli_real_escape_string($db, $_POST['lien']);
    $date_naissance = $_POST['date_naissance'];
    $cin = mysqli_real_escape_string($db, $_POST['cin']);
    $nombre = intval($_POST['nombre']);
    $altitude_a_adh = isset($_POST['altitude_a_adh']) ? 1 : 0;

    // Calcul automatique de l'âge et de en_charge
    $birthDate = new DateTime($date_naissance);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
    $en_charge = ($age <= 21) ? 1 : 0;

    $query = "INSERT INTO bd_active (MATRICULE, NOM, PRENOM, LIEN, date_naissance, CIN, en_charge, nombre, altitude_a_adh) 
              VALUES ($matricule, '$nom', '$prenom', '$lien', '$date_naissance', '$cin', $en_charge, $nombre, $altitude_a_adh)";

    if (mysqli_query($db, $query)) {
        // Mise à jour du nombre de membres dans la famille
        $updateNombreQuery = "UPDATE famille 
                              SET nombre = (SELECT COUNT(*) FROM bd_active WHERE MATRICULE = $matricule) 
                              WHERE id_mat_fam = $matricule";
        mysqli_query($db, $updateNombreQuery);

        header("Location: bd_active.php?matricule=$matricule");
        exit();
    } else {
        $error = "Erreur lors de l'ajout du membre : " . mysqli_error($db);
    }
}
?>

<!-- HTML -->
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h4 class="m-2 font-weight-bold text-primary">Ajouter un membre à la famille</h4>
    <a href="bd_active.php?matricule=<?php echo $matricule; ?>" class="btn btn-secondary float-right" style="border-radius: 0px;">
      <i class="fas fa-fw fa-arrow-left"></i> Retour
    </a>
  </div>

  <div class="card-body">
    <?php if ($error) echo '<div class="alert alert-danger">' . $error . '</div>'; ?>
    <form method="POST">
      <div class="form-group">
        <label>Nom</label>
        <input type="text" name="nom" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Prénom</label>
        <input type="text" name="prenom" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Lien</label>
        <select name="lien" class="form-control" required>
          <option value="">Sélectionner un lien</option>
          <option value="AGENT">AGENT</option>
          <option value="CONJOINT">CONJOINT</option>
          <option value="CONJOINT_2">CONJOINT_2</option>
          <option value="ENFANT">ENFANT</option>
          <option value="ENFANT_HANDICAP">ENFANT_HANDICAP</option>
          <option value="REVERSION">REVERSION</option>
          <option value="ACCOMPAGNATRICE">ACCOMPAGNATRICE</option>
          <option value="MORT">ACCOMPAGNATRICE</option>
        </select>
      </div>
      <div class="form-group">
        <label>Date de Naissance</label>
        <input type="date" name="date_naissance" class="form-control">
      </div>
      <div class="form-group">
        <label>CIN</label>
        <input type="text" name="cin" class="form-control">
      </div>
      
      <div class="form-check">
        <input type="checkbox" name="altitude_a_adh" class="form-check-input" id="altitude_a_adh">
        <label class="form-check-label" for="altitude_a_adh">Altitude à Adhésion</label>
      </div>
      <br>
      <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; 
ob_end_flush(); ?>
