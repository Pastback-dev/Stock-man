<?php
include '../includes/connection.php';
include '../includes/sidebar.php';

// Restrict access if user type is 'User'
$query = 'SELECT ID, t.TYPE FROM users u JOIN type t ON t.TYPE_ID=u.TYPE_ID WHERE ID = '.$_SESSION['MEMBER_ID'];
$result = mysqli_query($db, $query) or die(mysqli_error($db));
while ($row = mysqli_fetch_assoc($result)) {
    if ($row['TYPE'] == 'User') {
        echo '<script>alert("Restricted Page! You will be redirected to POS"); window.location = "pos.php";</script>';
    }
}
?>

<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h4 class="m-2 font-weight-bold text-primary">Familles Retraitées&nbsp;
      <a href="famille_retraite_add.php" class="btn btn-primary bg-gradient-primary" style="border-radius: 0px;">
        <i class="fas fa-fw fa-plus"></i> Ajouter
      </a>
    </h4>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom de la Famille</th>
            <th>Nombre</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $query = "SELECT id_mat_fam, nom_famille, nombre FROM famille WHERE type = 'RT'";
          $result = mysqli_query($db, $query) or die(mysqli_error($db));

          while ($row = mysqli_fetch_assoc($result)) {
              $displayId =  $row['id_mat_fam'];

              echo '<tr>';
              echo '<td>' . $displayId . '</td>';
              echo '<td>' . htmlspecialchars($row['nom_famille']) . '</td>';
              echo '<td>' . intval($row['nombre']) . '</td>';
              echo '<td align="center">
                      <div class="btn-group">
                        <a class="btn btn-info bg-gradient-info" style="border-radius: 0px;" href="bd_active.php?matricule=' . $row['id_mat_fam'] . '">
                          <i class="fas fa-fw fa-users"></i> Voir Membres
                        </a>
                        <a class="btn btn-warning bg-gradient-warning" style="border-radius: 0px;" href="famille_edit.php?id=' . $row['id_mat_fam'] . '">
                          <i class="fas fa-fw fa-edit"></i> Modifier
                        </a>
                        <a class="btn btn-danger bg-gradient-danger" style="border-radius: 0px;" href="famille_delete.php?id=' . $row['id_mat_fam'] . '" onclick="return confirm(\'Voulez-vous vraiment supprimer cette famille ?\')">
                          <i class="fas fa-fw fa-trash"></i> Supprimer
                        </a>
                        <a class="btn btn-danger bg-gradient-danger" style="border-radius:0px;"
   href="liste_rouges.php?matricule=' . urlencode($row['id_mat_fam']) . '">
    <i class="fas fa-fw fa-ban"></i> Liste Rouge
</a>
                      </div>
                    </td>';
              echo '</tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
