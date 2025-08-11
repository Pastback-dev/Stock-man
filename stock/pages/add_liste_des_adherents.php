<?php
include '../includes/connection.php';
include '../includes/sidebar.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$message = '';
$reference = 'ADH-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

// Initialize made_by with default value
$made_by = 0; // Default value if no valid employee is found

// Verify and get valid employee ID
$made_by = $_SESSION['MEMBER_ID'];


// Get families with their types for dropdown
$familiesQuery = "SELECT id_mat_fam, nom_famille, type FROM famille ORDER BY nom_famille ASC";
$familiesResult = mysqli_query($db, $familiesQuery);

// Form submission handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricule = intval($_POST['matricule']);
    $date_paiement = $_POST['date_paiement'];
    $lieu_paiement = mysqli_real_escape_string($db, $_POST['lieu_paiement']);
    $type_paiement = mysqli_real_escape_string($db, $_POST['type_paiement']);
    $tennis = isset($_POST['tennis']) ? 1 : 0;
    $family_type = $_POST['family_type'];
    $reference = $_POST['reference'];

    // Calculate dates and amounts
    $date_fin_adhetion = date('Y-m-d', strtotime($date_paiement . ' +1 year'));
    $montant = ($family_type == 'HC') ? 360 : 150;
    if ($tennis) {
        $montant += 200;
    }

    // Set default values
    $nombre_adherents = 1;
    $etat_adhesion = 'active';
    $date_old_fin_adhesion = '0000-00-00';
    $nbr_jr_delai = 0;

    // Build INSERT query - always include made_by, but use NULL if invalid
    $made_by_value = ($made_by > 0) ? $made_by : 'NULL';
    
    $insertQuery = "INSERT INTO liste_des_adherents 
                    (Matricule, date_paiement, date_fin_adhetion, date_old_fin_adhesion, 
                     nbr_jr_delai, lieu_paiement, type_paiement, tennis, montant, 
                     nombre_adherents, etat_adhesion, reference, made_by) 
                    VALUES 
                    ($matricule, '$date_paiement', '$date_fin_adhetion', '$date_old_fin_adhesion',
                     $nbr_jr_delai, '$lieu_paiement', '$type_paiement', $tennis, $montant,
                     $nombre_adherents, '$etat_adhesion', '$reference', $made_by)";
    
    if (mysqli_query($db, $insertQuery)) {
        $message = '<div class="alert alert-success">Adhérent ajouté avec succès. Référence: <strong>' . $reference . '</strong></div>';
        // Generate new reference for next entry
        $reference = 'ADH-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    } else {
        $message = '<div class="alert alert-danger">Erreur : ' . mysqli_error($db) . '</div>';
    }
}
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h4 class="m-2 font-weight-bold text-success">Ajouter un Adhérent</h4>
        <a href="liste_des_adherents.php" class="btn btn-secondary float-right" style="border-radius: 0px;">
            <i class="fas fa-fw fa-arrow-left"></i> Retour à la liste
        </a>
    </div>
    <div class="card-body">
        <?php echo $message; ?>

        <div class="form-group">
            <label for="family_search">Rechercher Famille (matricule ou nom)</label>
            <input type="text" id="family_search" class="form-control" placeholder="Tapez matricule ou nom de famille...">
        </div>

        <form method="POST" id="adhForm">
            <!-- Reference Field (added this section) -->
            <div class="form-group">
                <label for="reference">referencet</label>
                <input type="reference" id="reference" name="reference" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="matricule">Famille</label>
                <select name="matricule" id="matricule" class="form-control" required>
                    <option value="">-- Choisir une famille --</option>
                    <?php
                    mysqli_data_seek($familiesResult, 0);
                    while ($family = mysqli_fetch_assoc($familiesResult)) {
                        $prefixMatricule = ($family['type'] === 'HC') ? 'H' . $family['id_mat_fam'] : $family['id_mat_fam'];
                        echo '<option value="' . $family['id_mat_fam'] . '" data-type="' . $family['type'] . '">'
                             . $prefixMatricule . ' - '
                             . htmlspecialchars($family['nom_famille']) . ' (' 
                             . $family['type'] . ')</option>';
                    }
                    ?>
                </select>
                <input type="hidden" name="family_type" id="family_type" value="">
            </div>

            <!-- Rest of your form fields remain exactly the same -->
            <div class="form-group">
                <label for="date_paiement">Date de Paiement</label>
                <input type="date" id="date_paiement" name="date_paiement" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="date_fin_adhetion">Date Fin Adhésion</label>
                <input type="date" id="date_fin_adhetion" name="date_fin_adhetion" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label for="lieu_paiement">Lieu de Paiement</label>
                <input type="text" id="lieu_paiement" name="lieu_paiement" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="type_paiement">Type de Paiement</label>
                <select id="type_paiement" name="type_paiement" class="form-control" required>
                    <option value="">-- Choisir --</option>
                    <option value="TPE">TPE</option>
                    <option value="Virement">Virement</option>
                    <option value="Espèces">Espèces</option>
                </select>
            </div>

            <div class="form-group form-check">
                <input type="checkbox" id="tennis" name="tennis" class="form-check-input">
                <label for="tennis" class="form-check-label">Tennis (+200 DH)</label>
            </div>

            <div class="form-group">
                <label for="montant">Montant</label>
                <div class="input-group">
                    <input type="number" id="montant" name="montant" class="form-control" value="150" readonly>
                    <div class="input-group-append">
                        <span class="input-group-text">DH</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Ajouter
            </button>
        </form>
    </div>
</div>

<script>
    // [Keep all your existing JavaScript exactly the same]
    document.getElementById('family_search').addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        const select = document.getElementById('matricule');
        
        for (let i = 1; i < select.options.length; i++) {
            const option = select.options[i];
            const text = option.text.toLowerCase();
            option.style.display = text.includes(filter) ? '' : 'none';
        }
    });

    document.getElementById('matricule').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        document.getElementById('family_type').value = selected.getAttribute('data-type') || '';
        updateMontant();
    });

    function updateDateFinAdhetion() {
        const dateInput = document.getElementById('date_paiement');
        if (dateInput.value) {
            const date = new Date(dateInput.value);
            date.setFullYear(date.getFullYear() + 1);
            document.getElementById('date_fin_adhetion').value = date.toISOString().split('T')[0];
        }
    }

    function updateMontant() {
        const familyType = document.getElementById('family_type').value;
        const tennisChecked = document.getElementById('tennis').checked;
        let amount = (familyType === 'HC') ? 360 : 150;
        if (tennisChecked) amount += 200;
        document.getElementById('montant').value = amount;
    }

    document.getElementById('tennis').addEventListener('change', updateMontant);
    document.getElementById('date_paiement').addEventListener('change', updateDateFinAdhetion);

    document.addEventListener('DOMContentLoaded', () => {
        updateDateFinAdhetion();
        updateMontant();
    });
</script>

<?php include '../includes/footer.php'; ?>