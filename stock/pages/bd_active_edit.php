<?php
ob_start();
include '../includes/connection.php';
include '../includes/sidebar.php';

$code = isset($_GET['code']) ? intval($_GET['code']) : 0;
$error = '';

// Fetch member details
$query = "SELECT * FROM bd_active WHERE CODE = $code";
$result = mysqli_query($db, $query);
if (mysqli_num_rows($result) == 0) {
    echo "Membre introuvable.";
    exit;
}
$member = mysqli_fetch_assoc($result);
$matricule = $member['MATRICULE'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = mysqli_real_escape_string($db, $_POST['nom']);
    $prenom = mysqli_real_escape_string($db, $_POST['prenom']);
    $lien = mysqli_real_escape_string($db, $_POST['lien']);
    $date_naissance = $_POST['date_naissance'];
    $cin = mysqli_real_escape_string($db, $_POST['cin']);
    $en_charge = isset($_POST['en_charge']) ? 1 : 0;
    $nombre = intval($_POST['nombre']);
    $altitude_a_adh = isset($_POST['altitude_a_adh']) ? 1 : 0;

    $update_query = "UPDATE bd_active SET 
                     NOM='$nom', PRENOM='$prenom', LIEN='$lien', date_naissance='$date_naissance', 
                     CIN='$cin', en_charge=$en_charge, nombre=$nombre, altitude_a_adh=$altitude_a_adh 
                     WHERE CODE = $code";

    if (mysqli_query($db, $update_query)) {
        header("Location: bd_active.php?matricule=$matricule");
        exit();
    } else {
        $error = "Erreur lors de la mise à jour : " . mysqli_error($db);
    }
}
?>

<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h4 class="m-2 font-weight-bold text-primary">Modifier le membre</h4>
    <a href="bd_active.php?matricule=<?php echo $matricule; ?>" class="btn btn-secondary float-right" style="border-radius: 0px;">
      <i class="fas fa-fw fa-arrow-left"></i> Retour
    </a>
  </div>

  <div class="card-body">
    <?php if ($error) echo '<div class="alert alert-danger">' . $error . '</div>'; ?>
    <form method="POST">
      <div class="form-group">
        <label>Nom</label>
        <input type="text" name="nom" class="form-control" required value="<?php echo htmlspecialchars($member['NOM']); ?>">
      </div>
      <div class="form-group">
        <label>Prénom</label>
        <input type="text" name="prenom" class="form-control" required value="<?php echo htmlspecialchars($member['PRENOM']); ?>">
      </div>
      <div class="form-group">
        <label>Lien</label>
        <input type="text" name="lien" class="form-control" value="<?php echo htmlspecialchars($member['LIEN']); ?>">
      </div>
      <div class="form-group">
        <label>Date de Naissance</label>
        <input type="date" name="date_naissance" class="form-control" required value="<?php echo $member['date_naissance']; ?>">
      </div>
      <div class="form-group">
        <label>CIN</label>
        <input type="text" name="cin" class="form-control" value="<?php echo htmlspecialchars($member['CIN']); ?>">
      </div>
      <div class="form-check">
        <input type="checkbox" name="en_charge" class="form-check-input" id="en_charge" <?php if ($member['en_charge']) echo 'checked'; ?>>
        <label class="form-check-label" for="en_charge">En Charge</label>
      </div>
      <div class="form-group">
        <label>Nombre</label>
        <input type="number" name="nombre" class="form-control" min="0" value="<?php echo $member['nombre']; ?>">
      </div>
      <div class="form-check">
        <input type="checkbox" name="altitude_a_adh" class="form-check-input" id="altitude_a_adh" <?php if ($member['altitude_a_adh']) echo 'checked'; ?>>
        <label class="form-check-label" for="altitude_a_adh">Altitude à Adhésion</label>
      </div>
      <br>
      <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; 
ob_end_flush();?>
