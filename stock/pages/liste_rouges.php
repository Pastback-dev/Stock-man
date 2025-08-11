<?php
include '../includes/connection.php';
include '../includes/sidebar.php';

/*-----------------------------------------------------------
  1)  Build optional WHERE clause (filter by matricule)
-----------------------------------------------------------*/
$condition     = '';
$displayTitle  = 'Liste Rouge Complète';

if (isset($_GET['matricule']) && $_GET['matricule'] !== '') {
    $matricule    = mysqli_real_escape_string($db, $_GET['matricule']);
    $condition    = " WHERE lr.matricule = '$matricule' ";
    $displayTitle = "Liste Rouge";
}
?>

<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h4 class="m-2 font-weight-bold text-danger">
      <?= htmlspecialchars($displayTitle) ?>
      <a href="famille.php" class="btn btn-secondary float-right ml-2" style="border-radius:0;">
        <i class="fas fa-fw fa-arrow-left"></i> Retour
      </a>
      <a href="add_liste_rouges.php" class="btn btn-danger float-right" style="border-radius:0;">
        <i class="fas fa-fw fa-plus"></i> Ajouter à la Liste Rouge
      </a>
    </h4>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>Code</th>
            <th>Matricule</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Date Désactivation</th>
            <th>Date Activation</th>
            <th>Nombre</th>
            <th>Motif</th>
            <th>État</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
        <?php
        /*-----------------------------------------------------------
          2)  Query joins famille (for type) so we can format matricule
        -----------------------------------------------------------*/
        $query = "
          SELECT lr.*, f.type 
          FROM liste_rouges lr
          LEFT JOIN famille f ON f.id_mat_fam = lr.matricule
          $condition
          ORDER BY lr.date_activation DESC
        ";
        $result = mysqli_query($db, $query) or die(mysqli_error($db));

        while ($row = mysqli_fetch_assoc($result)) {

            /*------ Format matricule with prefix rules ------*/
            $type      = strtoupper($row['type'] ?? '');
            $matricule = htmlspecialchars($row['matricule']);

            switch ($type) {
                case 'HC':
                    $prefix = 'H';
                    break;
                case 'NR':
                    $prefix = '';
                    break;
                case 'RT':
                    $prefix = '';
                    break;
                default:
                    $prefix = $type;     // Unknown types keep full label
            }
            $formattedMatricule = $prefix ? "{$prefix}{$matricule}" : $matricule;

            /*------ Display row ------*/
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['code']) . '</td>';
            echo '<td>' . $formattedMatricule . '</td>';
            echo '<td>' . htmlspecialchars($row['nom']) . '</td>';
            echo '<td>' . htmlspecialchars($row['prenom']) . '</td>';
            echo '<td>' . htmlspecialchars($row['date_desactivation']) . '</td>';
            echo '<td>' . htmlspecialchars($row['date_activation']) . '</td>';
            echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
            echo '<td>' . htmlspecialchars($row['motif']) . '</td>';
            echo '<td>' . ($row['etat'] == 1
                    ? '<span class="text-success">Actif</span>'
                    : '<span class="text-success">Actif</span>') . '</td>';

            /* actions */
            echo '<td>
                    <div class="btn-group">
                      <a href="edit_liste_rouges.php?code=' . urlencode($row['code']) . '" 
                         class="btn btn-warning btn-sm" style="border-radius:0;">
                        <i class="fas fa-fw fa-edit"></i>
                      </a>
                      <a href="delete_liste_rouges.php?code=' . urlencode($row['code']) . '" 
                         class="btn btn-danger btn-sm" style="border-radius:0;"
                         onclick="return confirm(\'Voulez‑vous vraiment supprimer cet élément ?\')">
                        <i class="fas fa-fw fa-trash"></i>
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
