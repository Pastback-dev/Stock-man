<?php 
include '../includes/connection.php'; 
include '../includes/sidebar.php'; 

// First, check for expired records and move them to history
$checkExpiredQuery = "SELECT * FROM liste_des_adherents 
                     WHERE DATE_ADD(date_fin_adhetion, INTERVAL nbr_jr_delai DAY) < CURDATE()";
$expiredResult = mysqli_query($db, $checkExpiredQuery);

while ($expiredRow = mysqli_fetch_assoc($expiredResult)) {
    // Insert into history table
    $insertHistoryQuery = "INSERT INTO liste_des_adherents_history 
                          (Matricule, date_paiement, date_fin_adhetion, date_old_fin_adhesion, 
                           nbr_jr_delai, lieu_paiement, type_paiement, tennis, montant, 
                           nombre_adherents, etat_adhesion, reference, made_by)
                          VALUES (
                              '".$expiredRow['Matricule']."',
                              '".$expiredRow['date_paiement']."',
                              '".$expiredRow['date_fin_adhetion']."',
                              '".$expiredRow['date_old_fin_adhesion']."',
                              ".$expiredRow['nbr_jr_delai'].",
                              '".$expiredRow['lieu_paiement']."',
                              '".$expiredRow['type_paiement']."',
                              ".$expiredRow['tennis'].",
                              ".$expiredRow['montant'].",
                              ".$expiredRow['nombre_adherents'].",
                              '".$expiredRow['etat_adhesion']."',
                              '".$expiredRow['reference']."',
                              '".$expiredRow['made_by']."'
                          )";
    mysqli_query($db, $insertHistoryQuery);
    
    // Update the current record - move date_fin_adhetion to date_old_fin_adhesion
    $updateCurrentQuery = "UPDATE liste_des_adherents 
                          SET date_old_fin_adhesion = date_fin_adhetion
                          WHERE id = ".$expiredRow['id'];
    mysqli_query($db, $updateCurrentQuery);
}

// Auto-update nombre_adherents for all records on page load
$updateQuery = "UPDATE liste_des_adherents a
                JOIN (
                    SELECT Matricule, COUNT(*) as count 
                    FROM bd_active 
                    WHERE altitude_a_adh = 1 
                    GROUP BY Matricule
                ) b ON a.Matricule = b.Matricule
                SET a.nombre_adherents = b.count";
mysqli_query($db, $updateQuery);

// Also update famille table
$updateFamilleQuery = "UPDATE famille f
                      JOIN (
                          SELECT Matricule, COUNT(*) as count 
                          FROM bd_active 
                          WHERE altitude_a_adh = 1 
                          GROUP BY Matricule
                      ) b ON f.id_mat_fam = b.Matricule
                      SET f.nombre = b.count";
mysqli_query($db, $updateFamilleQuery);

// Update etat_adhesion based on date_fin_adhetion + nbr_jr_delai
$updateStatusQuery = "UPDATE liste_des_adherents 
                     SET etat_adhesion = CASE 
                         WHEN DATE_ADD(date_fin_adhetion, INTERVAL nbr_jr_delai DAY) >= CURDATE() THEN 1 
                         ELSE 0 
                     END";
mysqli_query($db, $updateStatusQuery);
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h4 class="m-2 font-weight-bold text-success">Gestion paiement adhesition active
            <a href="famille.php" class="btn btn-secondary float-right ml-2" style="border-radius: 0px;">
                <i class="fas fa-fw fa-arrow-left"></i> Retour
            </a>
            <a href="add_liste_des_adherents.php" class="btn btn-success float-right" style="border-radius: 0px;">
                <i class="fas fa-fw fa-plus"></i> Ajouter un Adhérent
            </a>
        </h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        
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
                        <th>Nombre jours délai</th>
                        <th>Montant</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = "SELECT a.*, f.nom_famille, f.type, u.* 
                              FROM liste_des_adherents a 
                              LEFT JOIN famille f ON a.Matricule = f.id_mat_fam 
                              LEFT JOIN users u ON a.made_by = u.id
                              ORDER BY a.date_paiement DESC";
                    $result = mysqli_query($db, $query) or die(mysqli_error($db));
                    
                    while ($row = mysqli_fetch_assoc($result)) {
                        $expiration_date = date('Y-m-d', strtotime($row['date_fin_adhetion'] . " + {$row['nbr_jr_delai']} days"));
                        $is_active = strtotime($expiration_date) >= time();
                        
                        // Format matricule based on family type
                        $matricule_display = $row['Matricule'];
                        if ($row['type'] == 'NR') {
                            $matricule_display = '' . $row['Matricule'];
                        } elseif ($row['type'] == 'HC') {
                            $matricule_display = 'H' . $row['Matricule'];
                        }
                        
                        echo '<tr>';
                        
                        echo '<td>' . htmlspecialchars($row['reference']) . '</td>';
                        echo '<td>' . $matricule_display . '</td>';
                        echo '<td>' . htmlspecialchars($row['nom_famille']) . '</td>';
                        echo '<td>' . ($row['date_paiement'] ? $row['date_paiement'] : '') . '</td>';
                        echo '<td>' . ($row['date_fin_adhetion'] ? $row['date_fin_adhetion'] : '') . '</td>';
                        echo '<td>' . ($row['date_old_fin_adhesion'] ? htmlspecialchars($row['date_old_fin_adhesion']) : '') . '</td>';
                        echo '<td>' . ($row['lieu_paiement'] ? htmlspecialchars($row['lieu_paiement']) : '') . '</td>';
                        echo '<td>' . ($row['type_paiement'] ? htmlspecialchars($row['type_paiement']) : '') . '</td>';
                        echo '<td>' . ($row['USERNAME'] ? htmlspecialchars($row['USERNAME']) : 'System') . '</td>';
                        echo '<td>' . $row['nombre_adherents'] . '</td>';
                        echo '<td>' . ($row['tennis'] ? 'Oui' : 'Non') . '</td>';
                        echo '<td>' . $row['nbr_jr_delai'] . '</td>';
                        echo '<td>' . number_format($row['montant'], 2) . '</td>';
                        echo '<td><span class="badge ' . ($is_active ? 'badge-success' : 'badge-danger') . '">' . 
                             ($is_active ? 'Actif' : 'Expiré') . '</span></td>';
                        echo '<td>
                                <div class="btn-group">
                                    <a href="edit_liste_des_adherents.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm" style="border-radius: 0px;">
                                        <i class="fas fa-fw fa-edit"></i>
                                    </a>
                                    <a href="delete_liste_des_adherents.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm" style="border-radius: 0px;" onclick="return confirm(\'Voulez-vous vraiment supprimer cet adhérent ?\')">
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