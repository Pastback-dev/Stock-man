<?php
include '../includes/connection.php';
include '../includes/sidebar.php';
?>

<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h4 class="m-2 font-weight-bold text-primary">Enfants en Charge
      <a href="famille.php" class="btn btn-secondary float-right ml-2" style="border-radius: 0px;">
        <i class="fas fa-fw fa-arrow-left"></i> Retour
      </a>
    </h4>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            
            <th>Matricule</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>En Charge</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $query = "SELECT e.*, f.type 
                    FROM enfants_en_charge e
                    LEFT JOIN famille f ON e.matricule = f.id_mat_fam";
          $result = mysqli_query($db, $query) or die(mysqli_error($db));

          while ($row = mysqli_fetch_assoc($result)) {
              // Format matricule based on family type
              $matricule_display = $row['matricule'];
              if ($row['type'] == 'NR') {
                  $matricule_display = '' . $row['matricule'];
              } elseif ($row['type'] == 'HC') {
                  $matricule_display = 'H' . $row['matricule'];
              }
              
              echo '<tr>';
              
              echo '<td>' . $matricule_display . '</td>';
              echo '<td>' . htmlspecialchars($row['nom']) . '</td>';
              echo '<td>' . htmlspecialchars($row['prenom']) . '</td>';
              echo '<td>' . ($row['en_charge'] ? 'Oui' : 'Non') . '</td>';
              echo '<td>
                      <a href="enfants_en_charge_edit.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm" style="border-radius: 0px;">
                        <i class="fas fa-fw fa-edit"></i> Éditer
                      </a>
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