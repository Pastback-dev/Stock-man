<?php
include '../includes/connection.php';
include '../includes/sidebar.php';

$matricule = isset($_GET['matricule']) ? intval($_GET['matricule']) : 0;

// Get family name and type
$queryFamille = "SELECT nom_famille, type, id_mat_fam FROM famille WHERE id_mat_fam = $matricule";
$resultFam = mysqli_query($db, $queryFamille);
$famille = mysqli_fetch_assoc($resultFam);

$today = new DateTime();
$isJan1 = ($today->format('m-d') === '01-01');

// Load liste_rouges codes once
$listeRougesCodes = [];
$queryRouges = "SELECT code FROM liste_rouges";
$resultRouges = mysqli_query($db, $queryRouges);
while ($rouge = mysqli_fetch_assoc($resultRouges)) {
    $listeRougesCodes[] = $rouge['code'];
}

// STEP 1: Update en_charge and age_old
$selectMembers = "SELECT * FROM bd_active WHERE MATRICULE = $matricule";
$resultMembers = mysqli_query($db, $selectMembers);

while ($row = mysqli_fetch_assoc($resultMembers)) {
    $code = $row['CODE'];
    $birthDate = new DateTime($row['date_naissance']);
    $age = $birthDate->diff($today)->y;

    $previousAge = $row['age_old'];
    $enCharge = $row['en_charge'];
    $lien = strtolower($row['LIEN']);

    if ($lien === 'enfant' && $age >= 26 && !in_array($code, $listeRougesCodes)) {
        $nom = mysqli_real_escape_string($db, $row['NOM']);
        $prenom = mysqli_real_escape_string($db, $row['PRENOM']);
        $dateDesactivation = $today->format('Y-m-d');
        $dateActivation = '0000-00-00';
        $nombre = intval($row['nombre']);
        $motif = 'Age >= 26';
        $etat = 0;

        $insertRouge = "
            INSERT INTO liste_rouges 
            (code, matricule, nom, prenom, date_desactivation, date_activation, nombre, motif, etat)
            VALUES
            ($code, $matricule, '$nom', '$prenom', '$dateDesactivation', '$dateActivation', $nombre, '$motif', $etat)
        ";
        mysqli_query($db, $insertRouge);
        $enCharge = 0;
    }

    if ($isJan1 && $lien === 'enfant' && $age >= 21 && $age < 26) {
        $enCharge = 0;
    } else {
        if (in_array($code, $listeRougesCodes)) {
            $enCharge = 0;
        } else {
            if ($age < 21 || $lien === "agent" || $lien === "conjoint") {
                $enCharge = 1;
            
            }
        }
    }

    if ($age != $previousAge || $enCharge != $row['en_charge']) {
        $update = "
            UPDATE bd_active 
            SET en_charge = $enCharge, age_old = $age 
            WHERE CODE = $code AND MATRICULE = $matricule
        ";
        mysqli_query($db, $update);

        $updateChild = "
            UPDATE enfants_en_charge 
            SET en_charge = $enCharge, age_old = $age 
            WHERE code = $code AND matricule = $matricule
        ";
        mysqli_query($db, $updateChild);
    }
}

// STEP 2: Insert into enfants_en_charge
$selectEligible = "
    SELECT * FROM bd_active 
    WHERE MATRICULE = $matricule 
    AND LOWER(LIEN) = 'enfant'
";
$resultEligible = mysqli_query($db, $selectEligible);

while ($row = mysqli_fetch_assoc($resultEligible)) {
    $birthDate = new DateTime($row['date_naissance']);
    $age = $birthDate->diff($today)->y;
    $code = $row['CODE'];

    if ($age >= 21 && $age < 26) {
        $check = "SELECT * FROM enfants_en_charge WHERE code = $code AND matricule = $matricule";
        $resCheck = mysqli_query($db, $check);

        if (mysqli_num_rows($resCheck) == 0) {
            $insert = "
                INSERT INTO enfants_en_charge (code, matricule, nom, prenom, en_charge, age_old)
                VALUES (
                    $code, $matricule, 
                    '" . mysqli_real_escape_string($db, $row['NOM']) . "', 
                    '" . mysqli_real_escape_string($db, $row['PRENOM']) . "', 
                    0, $age
                )
            ";
            mysqli_query($db, $insert);
        }
    }
}
?>

<!-- HTML CONTENT -->
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h4 class="m-2 font-weight-bold text-primary">Membres de la Famille : 
      <?php echo htmlspecialchars($famille['nom_famille']); ?>
      <a href="bd_active_add.php?matricule=<?php echo $matricule; ?>" class="btn btn-primary float-right" style="border-radius: 0px;">
        <i class="fas fa-fw fa-plus"></i> Ajouter un membre
      </a>
    </h4>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>Matricule</th> <!-- Changed from Code -->
            <th>Nom</th>
            <th>Prénom</th>
            <th>Lien</th>
            <th>Date de Naissance</th>
            <th>Âge</th>
            <th>CIN</th>
            <th>En Charge</th>
            
            <th>Altitude à Adhésion</th>
            <th>Etat</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $query = "SELECT * FROM bd_active WHERE MATRICULE = $matricule";
          $result = mysqli_query($db, $query) or die(mysqli_error($db));

          while ($row = mysqli_fetch_assoc($result)) {
              $birthDate = new DateTime($row['date_naissance']);
              $age = $birthDate->diff(new DateTime())->y;
              $code = $row['CODE'];

              $type = strtoupper($famille['type']);
$idMatFam = htmlspecialchars($famille['id_mat_fam']);

switch ($type) {
    case 'HC':
        $prefix = 'H';
        break;
    case 'RT':
        $prefix = '';
        break;
    case 'NR':
        $prefix = '';
        break;
    default:
        $prefix = $type; // fallback in case other types exist
}

$formattedMatricule = $prefix ? $prefix .  $idMatFam : $idMatFam;

              // Determine Etat
              $etat = '';
              $checkRouge = "SELECT 1 FROM liste_rouges WHERE code = '$code' LIMIT 1";
              $resRouge = mysqli_query($db, $checkRouge);
              if (mysqli_num_rows($resRouge) > 0) {
                  $etat = '<span class="text-danger font-weight-bold">En liste rouge</span>';
              } else {
                  $checkActif = "SELECT 1 FROM liste_des_adherents WHERE matricule = $matricule AND etat_adhesion = 1 LIMIT 1";
                  $resActif = mysqli_query($db, $checkActif);
                  if (mysqli_num_rows($resActif) > 0) {
                      $etat = '<span class="text-success font-weight-bold">Actif</span>';
                  }
              }

              echo '<tr>';
              echo '<td>' . $formattedMatricule . '</td>'; // replaced column
              echo '<td>' . htmlspecialchars($row['NOM']) . '</td>';
              echo '<td>' . htmlspecialchars($row['PRENOM']) . '</td>';
              echo '<td>' . htmlspecialchars($row['LIEN']) . '</td>';
              echo '<td>';                                 // date_naissance (enfant seulement)
if (strtolower($row['LIEN']) === 'enfant') {
    echo htmlspecialchars($row['date_naissance']);
}
else{echo '</td>';} 

echo '<td>';                                 // âge (enfant seulement)
if (strtolower($row['LIEN']) === 'enfant') {
    echo $age;
}
else{echo '</td>';} 
              
              echo '<td>' . htmlspecialchars($row['CIN']) . '</td>';
              echo '<td>' . ($row['en_charge'] ? 'Oui' : 'Non') . '</td>';
              
              echo '<td>' . ($row['altitude_a_adh'] ? 'Oui' : 'Non') . '</td>';
              echo '<td>' . $etat . '</td>';
              echo '<td>
                      <div class="btn-group">
                        <a href="bd_active_edit.php?code=' . $row['CODE'] . '" class="btn btn-warning btn-sm" style="border-radius: 0px;">
                          <i class="fas fa-fw fa-edit"></i>
                        </a>
                        <a href="bd_active_delete.php?code=' . $row['CODE'] . '&matricule=' . $matricule . '" class="btn btn-danger btn-sm" style="border-radius: 0px;" onclick="return confirm(\'Voulez-vous vraiment supprimer ce membre ?\')">
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
