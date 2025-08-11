<?php


include '../includes/connection.php';
include '../includes/sidebar.php';

// Optional: verify user is type 3 (Retraité)
$query = 'SELECT t.TYPE_ID, t.TYPE
          FROM users u
          JOIN type t ON t.TYPE_ID = u.TYPE_ID
          WHERE u.ID = ' . intval($_SESSION['MEMBER_ID']);
$result = mysqli_query($db, $query) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($result);

if (!$row || $row['TYPE_ID'] != 3) {
    // Not Retraité - redirect or deny access
    echo '<script>alert("Access denied!"); window.location="index.php";</script>';
    exit;
}

?>

<div class="row show-grid">

    <!-- Gestion membre retraite -->
    <div class="col-md-4 mb-4">
      <a href="famille_retraite.php" style="text-decoration:none;">
        <div class="card border-left-warning shadow h-100 py-2">
          <div class="card-body d-flex align-items-center">
            <i class="fas fa-user-clock fa-2x text-warning mr-3"></i>
            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Gestion membre retraite</div>
          </div>
        </div>
      </a>
    </div>

    <!-- Gestion paiement adhésion Retraités -->
    <div class="col-md-4 mb-4">
      <a href="liste_des_adherents_retraites.php" style="text-decoration:none;">
        <div class="card border-left-info shadow h-100 py-2">
          <div class="card-body d-flex align-items-center">
            <i class="fas fa-credit-card fa-2x text-info mr-3"></i>
            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Paiement adhésion retraités</div>
          </div>
        </div>
      </a>
    </div>

    <!-- Ajouter un troisième bouton si besoin -->
    <div class="col-md-4 mb-4">
      <a href="liste_des_adherents_retraites.php" style="text-decoration:none;">
        <div class="card border-left-primary shadow h-100 py-2">
          <div class="card-body d-flex align-items-center">
            <i class="fas fa-cogs fa-2x text-primary mr-3"></i>
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Gestion paiement  adhesition (Retraités)</div>
          </div>
        </div>
      </a>
    </div>

</div>

<?php include '../includes/footer.php'; ?>

