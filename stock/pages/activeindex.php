<?php

include '../includes/connection.php';
include '../includes/sidebar.php';

// Optional: verify user is type 2 (Active) - adjust the TYPE_ID accordingly
$query = 'SELECT t.TYPE_ID, t.TYPE
          FROM users u
          JOIN type t ON t.TYPE_ID = u.TYPE_ID
          WHERE u.ID = ' . intval($_SESSION['MEMBER_ID']);
$result = mysqli_query($db, $query) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($result);



?>

<div class="row show-grid">

    <!-- Gestion membre active -->
    <div class="col-md-4 mb-4">
      <a href="Famille.php" style="text-decoration:none;">
        <div class="card border-left-success shadow h-100 py-2">
          <div class="card-body d-flex align-items-center">
            <i class="fas fa-user fa-2x text-success mr-3"></i>
            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Gestion membre active</div>
          </div>
        </div>
      </a>
    </div>

    <!-- enfants en charge -->
    <div class="col-md-4 mb-4">
      <a href="enfants_en_charge.php" style="text-decoration:none;">
        <div class="card border-left-info shadow h-100 py-2">
          <div class="card-body d-flex align-items-center">
            <i class="fas fa-child fa-2x text-info mr-3"></i>
            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">enfants en charge</div>
          </div>
        </div>
      </a>
    </div>

    <!-- Tous les membres en charges -->
    <div class="col-md-4 mb-4">
      <a href="allmembersencharge.php" style="text-decoration:none;">
        <div class="card border-left-primary shadow h-100 py-2">
          <div class="card-body d-flex align-items-center">
            <i class="fas fa-archive fa-2x text-primary mr-3"></i>
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tous les membres en charges</div>
          </div>
        </div>
      </a>
    </div>

    <!-- Gestion paiement adhesition -->
    <div class="col-md-4 mb-4">
      <a href="liste_des_adherents.php" style="text-decoration:none;">
        <div class="card border-left-warning shadow h-100 py-2">
          <div class="card-body d-flex align-items-center">
            <i class="fas fa-credit-card fa-2x text-warning mr-3"></i>
            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Gestion paiement adhésion</div>
          </div>
        </div>
      </a>
    </div>

</div>

<?php include '../includes/footer.php'; ?>
