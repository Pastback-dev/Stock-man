<?php
include '../includes/connection.php';
include '../includes/sidebar.php';

// Restrict access if user type is 'User'
$query = 'SELECT ID, t.TYPE
          FROM users u
          JOIN type t ON t.TYPE_ID=u.TYPE_ID WHERE ID = '.$_SESSION['MEMBER_ID'];
$result = mysqli_query($db, $query) or die(mysqli_error($db));

while ($row = mysqli_fetch_assoc($result)) {
    $Aa = $row['TYPE'];
    if ($Aa == 'User') {
        echo '<script type="text/javascript">
                alert("Restricted Page! You will be redirected to POS");
                window.location = "pos.php";
              </script>';
    }
}
?>

<!-- Page Content -->
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h4 class="m-2 font-weight-bold text-primary">Familles Hors Cadre&nbsp;
      <a href="famille_hors_cadre_add.php" class="btn btn-primary bg-gradient-primary" style="border-radius: 0px;">
        <i class="fas fa-fw fa-plus"></i> Add
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
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $query = 'SELECT id_mat, nom FROM famille_hors_cadre';
          $result = mysqli_query($db, $query) or die(mysqli_error($db));
          
          while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['id_mat']) . '</td>';
            echo '<td>' . htmlspecialchars($row['nom']) . '</td>';
            echo '<td align="center">
                    <div class="btn-group">
                      <a class="btn btn-info bg-gradient-info" style="border-radius: 0px;" href="bd_active.php?matricule=' . $row['id_mat'] . '&type=hc">
                        <i class="fas fa-fw fa-users"></i> Voir Membres
                      </a>
                      <a type="button" class="btn btn-warning bg-gradient-warning" style="border-radius: 0px;" href="famille_hors_cadre_edit.php?id=' . $row['id_mat'] . '">
                        <i class="fas fa-fw fa-edit"></i> Edit
                      </a>
                      <a type="button" class="btn btn-danger bg-gradient-danger" style="border-radius: 0px;" href="famille_hors_cadre_delete.php?id=' . $row['id_mat'] . '" onclick="return confirm(\'Are you sure you want to delete this family?\')">
                        <i class="fas fa-fw fa-trash"></i> Remove
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