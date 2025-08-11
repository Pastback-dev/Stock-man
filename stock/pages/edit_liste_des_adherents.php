<?php
include '../includes/connection.php';
include '../includes/sidebar.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';
$adherent = [];
$showEditForm = true;
$showNewPaymentForm = false;

// Get ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get adherent data
if ($id > 0) {
    $query = "SELECT * FROM liste_des_adherents WHERE id = $id";
    $result = mysqli_query($db, $query);
    $adherent = mysqli_fetch_assoc($result);

    if (!$adherent) {
        header("Location: liste_des_adherents.php");
        exit();
    }
}

// Check which form to show
if (isset($_GET['action'])) {
    $showEditForm = ($_GET['action'] == 'edit');
    $showNewPaymentForm = ($_GET['action'] == 'new_payment');
}

// Get families list
$familiesQuery = "SELECT id_mat_fam, nom_famille FROM famille ORDER BY nom_famille ASC";
$familiesResult = mysqli_query($db, $familiesQuery);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['new_payment'])) {
        // Handle new payment submission
        $new_date_paiement = $_POST['new_date_paiement'];
        $new_reference = mysqli_real_escape_string($db, $_POST['new_reference']);
        $new_date_fin_adhesion = $_POST['new_date_fin_adhesion'];
        $matricule = $adherent['Matricule'];
        
        // Verify and get valid employee ID
        $made_by = 0;
        if (isset($_SESSION['user_id'])) {
            $user_id = intval($_SESSION['user_id']);
            $check_employee = "SELECT EMPLOYEE_ID FROM employee WHERE EMPLOYEE_ID = $user_id";
            $result = mysqli_query($db, $check_employee);
            if ($result && mysqli_num_rows($result) > 0) {
                $made_by = $user_id;
            }
        }

        // Build the INSERT query with proper NULL handling for made_by
        $made_by_value = ($made_by > 0) ? $made_by : 'NULL';
        
        $insertQuery = "INSERT INTO liste_des_adherents 
                        (Matricule, date_paiement, date_fin_adhetion, date_old_fin_adhesion, 
                         nbr_jr_delai, lieu_paiement, type_paiement, tennis, montant, 
                         nombre_adherents, etat_adhesion, reference, made_by) 
                        VALUES 
                        ($matricule, '$new_date_paiement', '$new_date_fin_adhesion', '{$adherent['date_fin_adhetion']}',
                         {$adherent['nbr_jr_delai']}, '{$adherent['lieu_paiement']}', '{$adherent['type_paiement']}', 
                         {$adherent['tennis']}, {$adherent['montant']},
                         {$adherent['nombre_adherents']}, 'active', '$new_reference', $made_by_value)";
        
        if (mysqli_query($db, $insertQuery)) {
            // Update ALL records with same Matricule to have the same end date
            $updateQuery = "UPDATE liste_des_adherents SET 
                           date_fin_adhetion = '$new_date_fin_adhesion'
                           WHERE Matricule = $matricule";
            mysqli_query($db, $updateQuery);
            
            // Mark old record as inactive
            $updateQuery = "UPDATE liste_des_adherents SET 
                           etat_adhesion = 0 
                           WHERE id = $id";
            mysqli_query($db, $updateQuery);
            
            $message = '<div class="alert alert-success">Nouveau paiement ajouté avec succès.</div>';
            // Refresh data
            $result = mysqli_query($db, $query);
            $adherent = mysqli_fetch_assoc($result);
            $showEditForm = true;
            $showNewPaymentForm = false;
        } else {
            $message = '<div class="alert alert-danger">Erreur: ' . mysqli_error($db) . '</div>';
        }
    } else {
        // Handle edit information submission
        $matricule = intval($_POST['matricule']);
        $date_paiement = $_POST['date_paiement'];
        $date_fin_adhetion = $_POST['date_fin_adhetion'];
        $lieu_paiement = mysqli_real_escape_string($db, $_POST['lieu_paiement']);
        $type_paiement = mysqli_real_escape_string($db, $_POST['type_paiement']);
        $tennis = isset($_POST['tennis']) ? 1 : 0;
        $montant = floatval($_POST['montant']);
        $nbr_jr_delai = intval($_POST['nbr_jr_delai']);

        // First update all records with same Matricule to have the same end date
        $updateAllQuery = "UPDATE liste_des_adherents SET
                date_fin_adhetion = '$date_fin_adhetion'
                WHERE Matricule = $matricule";
        mysqli_query($db, $updateAllQuery);
        
        // Then update the specific record
        $updateQuery = "UPDATE liste_des_adherents SET
                Matricule = $matricule,
                date_paiement = '$date_paiement',
                date_fin_adhetion = '$date_fin_adhetion',
                lieu_paiement = '$lieu_paiement',
                type_paiement = '$type_paiement',
                tennis = $tennis,
                montant = $montant,
                nbr_jr_delai = $nbr_jr_delai
                WHERE id = $id";

        if (mysqli_query($db, $updateQuery)) {
            $message = '<div class="alert alert-success">Informations modifiées avec succès.</div>';
            $result = mysqli_query($db, $query);
            $adherent = mysqli_fetch_assoc($result);
        } else {
            $message = '<div class="alert alert-danger">Erreur: ' . mysqli_error($db) . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Adhérent</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div class="container-fluid">
        
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h4 class="m-2 font-weight-bold text-primary">Gestion Adhérent</h4>
                <a href="liste_des_adherents.php" class="btn btn-secondary float-right" style="border-radius:0px;">
                    <i class="fas fa-fw fa-arrow-left"></i> Retour
                </a>
            </div>

            <div class="card-body">
                <?= $message; ?>

                <!-- Action Buttons -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <a href="?id=<?= $id ?>&action=edit" class="btn btn-primary btn-block">
                            <i class="fas fa-edit"></i> Éditer les informations
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="?id=<?= $id ?>&action=new_payment" class="btn btn-success btn-block">
                            <i class="fas fa-plus-circle"></i> Ajouter nouveau paiement
                        </a>
                    </div>
                </div>

                <!-- Edit Information Form -->
                <?php if ($showEditForm): ?>
                <form method="POST" id="editAdhForm">
                    <div class="form-group">
                        <label for="matricule">Famille</label>
                        <select name="matricule" id="matricule" class="form-control" required>
                            <option value="">-- Choisir une famille --</option>
                            <?php
                            mysqli_data_seek($familiesResult, 0);
                            while ($family = mysqli_fetch_assoc($familiesResult)) {
                                $selected = ($family['id_mat_fam'] == $adherent['Matricule']) ? 'selected' : '';
                                echo '<option value="' . $family['id_mat_fam'] . '" ' . $selected . '>'
                                   . htmlspecialchars($family['nom_famille']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date_paiement">Date de Paiement</label>
                        <input type="date" id="date_paiement" name="date_paiement"
                               value="<?= htmlspecialchars($adherent['date_paiement'] ?? ''); ?>"
                               class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="date_fin_adhetion">Date Fin Adhésion</label>
                        <input type="date" id="date_fin_adhetion" name="date_fin_adhetion"
                               value="<?= htmlspecialchars($adherent['date_fin_adhetion'] ?? ''); ?>"
                               class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="lieu_paiement">Lieu de Paiement</label>
                        <input type="text" id="lieu_paiement" name="lieu_paiement"
                               value="<?= htmlspecialchars($adherent['lieu_paiement'] ?? ''); ?>"
                               class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="nbr_jr_delai">Nombre jours délai</label>
                        <input type="number" id="nbr_jr_delai" name="nbr_jr_delai" min="0"
                               value="<?= htmlspecialchars($adherent['nbr_jr_delai'] ?? ''); ?>"
                               class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="type_paiement">Type de Paiement</label>
                        <select id="type_paiement" name="type_paiement" class="form-control" required>
                            <option value="">-- Choisir --</option>
                            <option value="TPE" <?= ($adherent['type_paiement'] == 'TPE') ? 'selected' : ''; ?>>TPE</option>
                            <option value="Virement" <?= ($adherent['type_paiement'] == 'Virement') ? 'selected' : ''; ?>>Virement</option>
                            <option value="Espèces" <?= ($adherent['type_paiement'] == 'Espèces') ? 'selected' : ''; ?>>Espèces</option>
                        </select>
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" id="tennis" name="tennis" class="form-check-input"
                               <?= ($adherent['tennis'] ?? 0) ? 'checked' : ''; ?>>
                        <label for="tennis" class="form-check-label">Tennis</label>
                    </div>

                    <div class="form-group">
                        <label for="montant">Montant</label>
                        <input type="number" id="montant" name="montant" step="0.01"
                               value="<?= htmlspecialchars($adherent['montant'] ?? ''); ?>"
                               class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Enregistrer modifications</button>
                </form>
                <?php endif; ?>

                <!-- New Payment Form -->
                <?php if ($showNewPaymentForm): ?>
                <div class="card border-left-success">
                    <div class="card-body">
                        <h5 class="text-success">Nouveau paiement pour la même adhésion</h5>
                        <p class="text-muted">Date fin d'adhésion actuelle: <?= $adherent['date_fin_adhetion'] ?></p>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label for="new_reference">Nouvelle référence</label>
                                <input type="text" id="new_reference" name="new_reference" 
                                       value="ADH-<?= date('Ymd') ?>-<?= strtoupper(substr(uniqid(), -6)) ?>"
                                       class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="new_date_paiement">Date du nouveau paiement</label>
                                <input type="date" id="new_date_paiement" name="new_date_paiement" 
                                       class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="new_date_fin_adhesion">Nouvelle date fin d'adhésion</label>
                                <div class="input-group">
                                    <input type="date" id="new_date_fin_adhesion" name="new_date_fin_adhesion" 
                                           class="form-control" required>
                                    <div class="input-group-append">
                                        <button type="button" id="addOneYear" class="btn btn-outline-secondary">
                                            +1 an
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" name="new_payment" class="btn btn-success">
                                <i class="fas fa-save"></i> Enregistrer nouveau paiement
                            </button>
                            <a href="?id=<?= $id ?>" class="btn btn-secondary">Annuler</a>
                        </form>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Calculate end date when payment date changes
                    const paymentDateInput = document.getElementById('new_date_paiement');
                    const endDateInput = document.getElementById('new_date_fin_adhesion');
                    const addYearBtn = document.getElementById('addOneYear');
                    
                    if (paymentDateInput && endDateInput) {
                        // Auto-calculate when payment date changes
                        paymentDateInput.addEventListener('change', function() {
                            if (this.value) {
                                const paymentDate = new Date(this.value);
                                const endDate = new Date(paymentDate);
                                endDate.setFullYear(endDate.getFullYear() + 1);
                                endDateInput.value = endDate.toISOString().split('T')[0];
                            }
                        });
                        
                        // +1 year button functionality
                        addYearBtn.addEventListener('click', function() {
                            if (endDateInput.value) {
                                const currentDate = new Date(endDateInput.value);
                                currentDate.setFullYear(currentDate.getFullYear() + 1);
                                endDateInput.value = currentDate.toISOString().split('T')[0];
                            } else if (paymentDateInput.value) {
                                const paymentDate = new Date(paymentDateInput.value);
                                paymentDate.setFullYear(paymentDate.getFullYear() + 1);
                                endDateInput.value = paymentDate.toISOString().split('T')[0];
                            } else {
                                const today = new Date();
                                today.setFullYear(today.getFullYear() + 1);
                                endDateInput.value = today.toISOString().split('T')[0];
                            }
                        });
                        
                        // Initialize with current date + 1 year if empty
                        if (!endDateInput.value) {
                            const today = new Date();
                            today.setFullYear(today.getFullYear() + 1);
                            endDateInput.value = today.toISOString().split('T')[0];
                        }
                    }
                });
                </script>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>