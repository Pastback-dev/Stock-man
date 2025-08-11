<?php
include '../includes/connection.php';
include '../includes/sidebar.php';

// Check user type and redirect if necessary
$query = 'SELECT t.TYPE
          FROM users u
          JOIN type t ON t.TYPE_ID = u.TYPE_ID
          WHERE u.ID = ' . $_SESSION['MEMBER_ID'];
$result = mysqli_query($db, $query) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($result);
$user_type = $row['TYPE'];

if ($user_type == 'User') {
    echo '<script type="text/javascript">
            alert("Restricted Page! You will be redirected to POS");
            window.location = "pos.php";
          </script>';
    exit;
}
?>

<?php if ($user_type == 'Admin'): ?>
    <!-- Admin Dashboard Cards -->
    <div class="row show-grid">
        <!-- Gestion membre active Card -->
        <div class="col-md-3 mb-4">
            <a href="Famille.php" style="text-decoration:none;">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-user fa-2x text-primary mr-3"></i>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Gestion membre active</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Gestion membre retraite Card -->
        <div class="col-md-3 mb-4">
            <a href="famille_retraite.php" style="text-decoration:none;">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-user-clock fa-2x text-warning mr-3"></i>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Gestion membre retraite</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Enfants en charge Card -->
        <div class="col-md-3 mb-4">
            <a href="enfants_en_charge.php" style="text-decoration:none;">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-child fa-2x text-success mr-3"></i>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Enfants en charge</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Listes rouges Card -->
        <div class="col-md-3 mb-4">
            <a href="liste_rouges.php" style="text-decoration:none;">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger mr-3"></i>
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Listes rouges</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row show-grid">
        <!-- Tous les membres en charges Card -->
        <div class="col-md-3 mb-4">
            <a href="allmembersencharge.php" style="text-decoration:none;">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-archive fa-2x text-info mr-3"></i>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tous les membres en charges</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Gestion paiement adhesion Card -->
        <div class="col-md-3 mb-4">
            <a href="liste_des_adherents.php" style="text-decoration:none;">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-credit-card fa-2x text-secondary mr-3"></i>
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Gestion paiement adhesion</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Gestion paiement adhesion (Retraités) Card -->
        <div class="col-md-3 mb-4">
            <a href="liste_des_adherents_retraites.php" style="text-decoration:none;">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-credit-card fa-2x text-info mr-3"></i>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Gestion paiement adhesion (Retraités)</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

<?php elseif ($user_type == 'Retraité'): ?>
    <!-- Retraité Dashboard Cards -->
    <div class="row show-grid">
        <!-- Gestion membre retraite Card -->
        <div class="col-md-6 mb-4">
            <a href="famille_retraite.php" style="text-decoration:none;">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-user-clock fa-2x text-warning mr-3"></i>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Gestion membre retraite</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Gestion paiement adhesion (Retraités) Card -->
        <div class="col-md-6 mb-4">
            <a href="liste_des_adherents_retraites.php" style="text-decoration:none;">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-credit-card fa-2x text-info mr-3"></i>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Gestion paiement adhesion (Retraités)</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

<?php elseif ($user_type == 'active'): ?>
    <!-- Active Member Dashboard Cards -->
    <div class="row show-grid">
        <!-- Gestion membre active Card -->
        <div class="col-md-3 mb-4">
            <a href="Famille.php" style="text-decoration:none;">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-user fa-2x text-primary mr-3"></i>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Gestion membre active</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Enfants en charge Card -->
        <div class="col-md-3 mb-4">
            <a href="enfants_en_charge.php" style="text-decoration:none;">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-child fa-2x text-success mr-3"></i>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Enfants en charge</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Tous les membres en charges Card -->
        <div class="col-md-3 mb-4">
            <a href="allmembersencharge.php" style="text-decoration:none;">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-archive fa-2x text-info mr-3"></i>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tous les membres en charges</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Gestion paiement adhesion Card -->
        <div class="col-md-3 mb-4">
            <a href="liste_des_adherents.php" style="text-decoration:none;">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-credit-card fa-2x text-secondary mr-3"></i>
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Gestion paiement adhesion</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

<?php elseif ($user_type == 'global'): ?>
    <!-- Global User Dashboard Cards -->
    <div class="row show-grid">
        <!-- Tous les membres en charges Card -->
        <div class="col-md-12 mb-4">
            <a href="allmembersencharge.php" style="text-decoration:none;">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <i class="fas fa-archive fa-2x text-info mr-3"></i>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tous les membres en charges</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

<?php else: ?>
    <!-- Default/Unknown User Type -->
    <div class="row show-grid">
        <div class="col-md-12 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body d-flex align-items-center justify-content-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-secondary mr-3"></i>
                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Aucune permission accordée</div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php include '../includes/footer.php'; ?>