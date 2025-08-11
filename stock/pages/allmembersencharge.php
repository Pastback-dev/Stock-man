<?php 
include '../includes/connection.php'; 
include '../includes/sidebar.php'; 
?> 

<div class="card shadow mb-4"> 
  <div class="card-header py-3"> 
    <h4 class="m-2 font-weight-bold text-success"> 
      Liste Global des adherents
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
            <th>Lien</th> 
            <th>Date de Naissance</th>  
            <th>CIN</th>   
            <th>Date fin Adhésion</th> 
            <th>Tennis</th> 
          </tr> 
        </thead> 

        <tbody> 
          <?php 
          // Requête mise à jour pour inclure le type de famille
          $query = "
            SELECT b.*, f.nom_famille, f.type, l.tennis,l.date_fin_adhetion,l.etat_adhesion
            FROM bd_active b 
            LEFT JOIN famille f ON b.MATRICULE = f.id_mat_fam 
            LEFT JOIN liste_des_adherents l ON b.MATRICULE = l.Matricule 
            WHERE l.etat_adhesion = 1 and b.altitude_a_adh = 1
            ORDER BY b.MATRICULE ASC, b.CODE ASC
          ";

          $result = mysqli_query($db, $query) or die(mysqli_error($db));

          while ($row = mysqli_fetch_assoc($result)) {
              // Formattage du matricule selon le type
              $type = strtoupper($row['type'] ?? '');
              $matricule = htmlspecialchars($row['MATRICULE']);

              switch ($type) {
                  case 'HC':
                      $prefix = 'H';
                      break;
                  case 'NR':
                      $prefix = 'NR';
                      break;
                  case 'RT':
                      $prefix = '';
                      break;
                  default:
                      $prefix = $type;
              }

              $formattedMatricule = $prefix ? $prefix .  $matricule : $matricule;

              // Calcul de l'âge
              $birthDate = new DateTime($row['date_naissance']);
              $age = $birthDate->diff(new DateTime())->y;

              echo '<tr>';
              echo '<td>' . $formattedMatricule . '</td>';
              echo '<td>' . htmlspecialchars($row['NOM']) . '</td>';
              echo '<td>' . htmlspecialchars($row['PRENOM']) . '</td>';
              echo '<td>' . htmlspecialchars($row['LIEN']) . '</td>';
              echo '<td>' . $row['date_naissance'] . '</td>';
              echo '<td>' . htmlspecialchars($row['CIN']) . '</td>';
              echo '<td>' . ($row['date_fin_adhetion'] ) . '</td>';
              echo '<td>' . ($row['tennis'] == 1 ? 'Oui' : 'Non') . '</td>';
              echo '</tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
  </div> 
</div> 

<?php include '../includes/footer.php'; ?>
