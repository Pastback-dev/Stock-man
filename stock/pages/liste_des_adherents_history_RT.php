<?php 
include '../includes/connection.php'; 
include '../includes/sidebar.php'; 
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h4 class="m-2 font-weight-bold text-primary">Historique des Adhésions Retraités
            <a href="famille.php" class="btn btn-secondary float-right ml-2" style="border-radius: 0px;">
                <i class="fas fa-fw fa-arrow-left"></i> Retour
            </a>
            <a href="liste_des_adherents.php" class="btn btn-success float-right" style="border-radius: 0px;">
                <i class="fas fa-fw fa-exchange-alt"></i> Voir les Adhésions Actives
            </a>
        </h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Matricule</th>
                        <th>Nom Famille</th>
                        <th>Date Paiement</th>
                        <th>Date Fin Adhésion</th>
                        <th>Date Ancienne Fin</th>
                        <th>Lieu Paiement</th>
                        <th>Type Paiement</th>
                        <th>Nombre Adhérents</th>
                        <th>Tennis</th>
                        <th>Nombre jours délai</th>
                        <th>Montant</th>
                        <th>État</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = "SELECT h.*, f.nom_famille, f.type 
                              FROM liste_des_adherents_history h
                              LEFT JOIN famille f ON h.Matricule = f.id_mat_fam 
                              WHERE f.type = 'RT'
                              ORDER BY h.date_fin_adhetion DESC";
                    $result = mysqli_query($db, $query) or die(mysqli_error($db));
                    
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Format matricule based on family type
                        $matricule_display = 'RT-' . $row['Matricule'];
                        
                        echo '<tr>';
                        echo '<td>' . $row['id'] . '</td>';
                        echo '<td>' . $matricule_display . '</td>';
                        echo '<td>' . htmlspecialchars($row['nom_famille']) . '</td>';
                        echo '<td>' . $row['date_paiement'] . '</td>';
                        echo '<td>' . $row['date_fin_adhetion'] . '</td>';
                        
                        echo '<td>' . htmlspecialchars($row['lieu_paiement']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['type_paiement']) . '</td>';
                        echo '<td>' . $row['nombre_adherents'] . '</td>';
                        echo '<td>' . ($row['tennis'] ? 'Oui' : 'Non') . '</td>';
                        echo '<td>' . $row['nbr_jr_delai'] . '</td>';
                        echo '<td>' . number_format($row['montant'], 2) . '</td>';
                        echo '<td><span class="badge badge-info">RT Historique</span></td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>