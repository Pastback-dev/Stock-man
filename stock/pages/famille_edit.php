<?php
// famille_edit.php – Modification des informations d’une famille

// ----------------------------------------------------------------------------
// 0. Initialisation
// ----------------------------------------------------------------------------

include '../includes/connection.php';
include '../includes/sidebar.php';

// Affiche toutes les erreurs pendant le debug
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', '1');

// ----------------------------------------------------------------------------
// 1. Contrôle d’accès : seuls les administrateurs / managers peuvent rester
// ----------------------------------------------------------------------------
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

// ----------------------------------------------------------------------------
// 2. Récupération de l’ID de la famille (GET)
// ----------------------------------------------------------------------------
$id = $_GET['id'] ?? '';
if (!ctype_digit($id)) {
    header('Location: famille.php');
    exit;
}
$id = (int)$id;

// ----------------------------------------------------------------------------
// 3. Récupération des données existantes
// ----------------------------------------------------------------------------
$stmt = $db->prepare('SELECT id_mat_fam, nom_famille, nombre, type
                      FROM famille
                      WHERE id_mat_fam = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$family = $result->fetch_assoc();
$stmt->close();

if (!$family) {
    echo '<script>alert("Famille introuvable.");window.location="famille.php";</script>';
    exit;
}

// ----------------------------------------------------------------------------
// 4. Traitement du formulaire (POST)
// ----------------------------------------------------------------------------
$error = '';
if (isset($_POST['update'])) {
    $nom_famille = trim($_POST['nom_famille']);
    $nombre      = (int)$_POST['nombre'];
    $type        = trim($_POST['type']);   // NR ou HC

    if ($nom_famille === '' || !in_array($type, ['NR', 'HC'], true) || $nombre < 0) {
        $error = 'Veuillez remplir correctement tous les champs.';
    } else {
        $stmt = $db->prepare('UPDATE famille
                              SET nom_famille = ?, nombre = ?, type = ?
                              WHERE id_mat_fam = ?');
        $stmt->bind_param('sisi', $nom_famille, $nombre, $type, $id);
        $stmt->execute();
        $stmt->close();

        echo '<script>alert("La famille a été mise à jour avec succès.");window.location="famille.php";</script>';
        exit;
    }
}
?>

<!-- Page Content -->
<div class="card shadow mb-4">
  <div class="card-header py-3 d-flex justify-content-between align-items-center">
    <h4 class="m-0 font-weight-bold text-primary">Modifier la Famille</h4>
    <a href="famille.php" class="btn btn-secondary bg-gradient-secondary" style="border-radius:0px;">
      <i class="fas fa-fw fa-arrow-left"></i> Retour
    </a>
  </div>
  <div class="card-body">
    <?php if ($error !== '') : ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="" autocomplete="off">
      <div class="row mb-3">
        

        <div class="col-md-4">
          <label for="type" class="font-weight-bold">Type</label>
          <select id="type" name="type" class="form-control" required>
            <option value="NR" <?php echo $family['type'] === 'NR' ? 'selected' : ''; ?>>OE_TAMCA</option>
            <option value="HC" <?php echo $family['type'] === 'HC' ? 'selected' : ''; ?>>Hors Catégorie</option>
          </select>
        </div>

        
      </div>

      <div class="mb-3">
        <label for="nom_famille" class="font-weight-bold">Nom de la Famille</label>
        <input type="text" id="nom_famille" name="nom_famille" class="form-control"
               value="<?php echo htmlspecialchars($family['nom_famille']); ?>" required>
      </div>

      <button type="submit" name="update" class="btn btn-success bg-gradient-success" style="border-radius:0px;">
        <i class="fas fa-fw fa-save"></i> Enregistrer
      </button>
      <a href="famille.php" class="btn btn-danger bg-gradient-danger" style="border-radius:0px;">
        <i class="fas fa-fw fa-times"></i> Annuler
      </a>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>