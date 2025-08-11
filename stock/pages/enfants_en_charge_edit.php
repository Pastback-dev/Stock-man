<?php
ob_start(); 
include '../includes/connection.php';
include '../includes/sidebar.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "ID invalide.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $en_charge = isset($_POST['en_charge']) ? 1 : 0;

    // Update enfants_en_charge en_charge
    $updateEnfants = "UPDATE enfants_en_charge SET en_charge = $en_charge WHERE id = $id";

    // Get the 'code' to update bd_active table
    $queryCode = "SELECT code FROM enfants_en_charge WHERE id = $id";
    $resultCode = mysqli_query($db, $queryCode);
    $code = ($resultCode && mysqli_num_rows($resultCode) > 0) ? mysqli_fetch_assoc($resultCode)['code'] : 0;

    if ($code) {
        // Update bd_active en_charge to match enfants_en_charge
        $updateBdActive = "UPDATE bd_active SET en_charge = $en_charge WHERE CODE = $code";

        if (mysqli_query($db, $updateEnfants) && mysqli_query($db, $updateBdActive)) {
            header("Location: enfants_en_charge.php");
            exit;
        } else {
            $error = "Erreur lors de la mise à jour : " . mysqli_error($db);
        }
    } else {
        $error = "Code introuvable.";
    }
}

// Fetch current enfants_en_charge record
$query = "SELECT * FROM enfants_en_charge WHERE id = $id";
$result = mysqli_query($db, $query);

if (mysqli_num_rows($result) == 0) {
    echo "Enfant non trouvé.";
    exit;
}

$row = mysqli_fetch_assoc($result);
?>

<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h4 class="m-2 font-weight-bold text-primary">Modifier En Charge - <?php echo htmlspecialchars($row['nom'] . ' ' . $row['prenom']); ?></h4>
  </div>

  <div class="card-body">
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post" action="">
      <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="en_charge" name="en_charge" <?php echo $row['en_charge'] ? 'checked' : ''; ?>>
        <label class="form-check-label" for="en_charge">En Charge</label>
      </div>
      <button type="submit" class="btn btn-primary">Sauvegarder</button>
      <a href="enfants_en_charge.php" class="btn btn-secondary">Annuler</a>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; 
ob_end_flush();?>
