<?php 
include '../includes/connection.php'; 
include '../includes/sidebar.php'; 

/* -------------------------------------------------------------------
   ❶  Mises à jour automatiques (nombre adhérents, état adhésion)
   ----------------------------------------------------------------- */
mysqli_query($db, "
  UPDATE liste_des_adherents a
  JOIN ( SELECT Matricule, COUNT(*) AS count
         FROM bd_active
         WHERE altitude_a_adh = 1
         GROUP BY Matricule ) b ON a.Matricule = b.Matricule
  SET a.nombre_adherents = b.count
");

mysqli_query($db, "
  UPDATE famille f
  JOIN ( SELECT Matricule, COUNT(*) AS count
         FROM bd_active
         WHERE altitude_a_adh = 1
         GROUP BY Matricule ) b
       ON f.id_mat_fam = b.Matricule
  SET f.nombre = b.count
");

mysqli_query($db, "
  UPDATE liste_des_adherents
  SET etat_adhesion = CASE
      WHEN DATE_ADD(date_fin_adhetion, INTERVAL nbr_jr_delai DAY) >= CURDATE() THEN 1
      ELSE 0
  END
");
?>

<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h4 class="m-2 font-weight-bold text-success">Gestion paiement adhesition Retraitées
      
      <a href="famille.php" class="btn btn-secondary float-right ml-2" style="border-radius:0px;">
        <i class="fas fa-fw fa-arrow-left"></i> Retour
      </a>
      <a href="add_liste_des_adherents.php" class="btn btn-success float-right" style="border-radius:0px;">
        <i class="fas fa-fw fa-plus"></i> Ajouter un Adhérent
      </a>
    </h4>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Référence</th>
            <th>Matricule</th>
            <th>Nom Famille</th>
            <th>Date Paiement</th>
            <th>Date Fin Adhésion</th>
            <th>Date Fin Adhésion precedent</th>
            <th>Lieu Paiement</th>
            <th>Type Paiement</th>
            <th>Créé par</th>
            <th>Nombre Adhérents</th>
            <th>Tennis</th>
            <th>Jours délai</th>
            <th>Montant</th>
            <th>État</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
<?php
/* -------------------------------------------------------------------
   ❷  Sélection uniquement des familles de type RT
   ----------------------------------------------------------------- */
$sql = "
  SELECT a.*, f.nom_famille, f.type, u.username
  FROM   liste_des_adherents a
  JOIN   famille f ON a.Matricule = f.id_mat_fam
  LEFT JOIN users u ON a.made_by = u.id
  WHERE  f.type = 'RT'                           -- *** filtre Retraite ***
  ORDER  BY a.date_paiement DESC
";
$res = mysqli_query($db, $sql) or die(mysqli_error($db));

while ($row = mysqli_fetch_assoc($res)) {
    /* Calcul expiration (date_fin + délai) */
    $expiration = date('Y-m-d', strtotime($row['date_fin_adhetion']." +{$row['nbr_jr_delai']} days"));
    $isActive   = (strtotime($expiration) >= time());

    /* Matricule affiché (préfixe RT-) */
    $matriculeAff = $row['Matricule'];     // toujours RT ici

    echo '<tr>';
    echo '<td>'.$row['id'].'</td>';
    echo '<td>'.htmlspecialchars($row['reference']).'</td>';
    echo '<td>'.$matriculeAff.'</td>';
    echo '<td>'.htmlspecialchars($row['nom_famille']).'</td>';
    echo '<td>'.$row['date_paiement'].'</td>';
    echo '<td>'.$row['date_fin_adhetion'].'</td>';
    echo '<td>' . ($row['date_old_fin_adhesion'] ? htmlspecialchars($row['date_old_fin_adhesion']) : '') . '</td>';
    echo '<td>'.htmlspecialchars($row['lieu_paiement']).'</td>';
    echo '<td>'.htmlspecialchars($row['type_paiement']).'</td>';
    echo '<td>'.($row['username'] ? htmlspecialchars($row['username']) : 'System').'</td>';
    echo '<td>'.$row['nombre_adherents'].'</td>';
    echo '<td>'.($row['tennis'] ? 'Oui' : 'Non').'</td>';
    echo '<td>'.$row['nbr_jr_delai'].'</td>';
    echo '<td>'.number_format($row['montant'],2).'</td>';
    echo '<td><span class="badge '.($isActive?'badge-success':'badge-danger').'">'.
            ($isActive?'Actif':'Expiré').'</span></td>';
    echo '<td>
            <div class="btn-group">
              <a href="edit_liste_des_adherents.php?id='.$row['id'].'" class="btn btn-warning btn-sm" style="border-radius:0px;">
                <i class="fas fa-fw fa-edit"></i>
              </a>
              <a href="delete_liste_des_adherents.php?id='.$row['id'].'" class="btn btn-danger btn-sm" style="border-radius:0px;"
                 onclick="return confirm(\'Voulez-vous vraiment supprimer cet adhérent ?\')">
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