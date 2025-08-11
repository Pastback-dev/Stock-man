<?php
include '../includes/connection.php';
include '../includes/sidebar.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $matricule = intval($_POST['matricule']);
    $applyAll = isset($_POST['all_members']);
    $code = isset($_POST['code']) ? intval($_POST['code']) : 0;
    $date_desactivation = $_POST['date_desactivation'];
    $date_activation = $_POST['date_activation'];
    $motif = mysqli_real_escape_string($db, $_POST['motif']);
    $nombre = 0;

    if ($date_desactivation >= $date_activation) {
        echo "<div class='alert alert-danger'>Erreur : La date de désactivation doit être antérieure à la date d'activation.</div>";
    } else {
        $today = date('Y-m-d');
        $etat = ($today > $date_desactivation && $today < $date_activation) ? 1 : 0;

        if ($applyAll) {
            $members = mysqli_query($db, "SELECT CODE, NOM, PRENOM FROM bd_active WHERE MATRICULE = $matricule");
            while ($m = mysqli_fetch_assoc($members)) {
                $code = $m['CODE'];
                $nom = mysqli_real_escape_string($db, $m['NOM']);
                $prenom = mysqli_real_escape_string($db, $m['PRENOM']);

                $insert = "INSERT INTO liste_rouges (code, matricule, nom, prenom, date_desactivation, date_activation, nombre, motif, etat)
                           VALUES ($code, $matricule, '$nom', '$prenom', '$date_desactivation', '$date_activation', $nombre, '$motif', $etat)";
                mysqli_query($db, $insert);
            }
            echo "<div class='alert alert-success'>Tous les membres ont été ajoutés à la liste rouge.</div>";
        } else {
            $nom = mysqli_real_escape_string($db, $_POST['nom']);
            $prenom = mysqli_real_escape_string($db, $_POST['prenom']);

            $insert = "INSERT INTO liste_rouges (code, matricule, nom, prenom, date_desactivation, date_activation, nombre, motif, etat)
                       VALUES ($code, $matricule, '$nom', '$prenom', '$date_desactivation', '$date_activation', $nombre, '$motif', $etat)";
            if (mysqli_query($db, $insert)) {
                echo "<div class='alert alert-success'>Ajouté avec succès à la liste rouge.</div>";
            } else {
                echo "<div class='alert alert-danger'>Erreur : " . mysqli_error($db) . "</div>";
            }
        }
    }
}
?>

<!-- HTML Form -->
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h4 class="m-2 font-weight-bold text-danger">Ajouter à la Liste Rouge</h4>
  </div>
  <div class="card-body">
    <form method="post" action="">

      <!-- Famille selection with datalist for search -->
      <div class="form-group">
        <label>Famille</label>
        <input list="famillesList" name="matricule" class="form-control" id="familleSelect" required placeholder="Rechercher par matricule ou nom">
        <datalist id="famillesList">
          <?php
          $familles = mysqli_query($db, "SELECT id_mat_fam, nom_famille FROM famille");
          while ($f = mysqli_fetch_assoc($familles)) {
              echo "<option value='{$f['id_mat_fam']}'>Matricule: {$f['id_mat_fam']} - {$f['nom_famille']}</option>";
          }
          ?>
        </datalist>
      </div>

      <!-- Member selection -->
      <div class="form-group">
        <label>Membre</label>
        <select name="code" class="form-control" id="memberSelect" onchange="updateNomPrenom(this)">
          <option value="">-- Choisir un membre (facultatif si tous) --</option>
        </select>
        <input type="checkbox" name="all_members" id="allMembers">
        <label for="allMembers">Ajouter pour tous les membres</label>
      </div>

      <input type="hidden" name="nom" id="nomField">
      <input type="hidden" name="prenom" id="prenomField">

      <div class="form-group">
        <label>Date Désactivation</label>
        <input type="date" name="date_desactivation" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Date Activation</label>
        <input type="date" name="date_activation" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Motif</label>
        <textarea name="motif" class="form-control" required></textarea>
      </div>

      <button type="submit" class="btn btn-danger">Ajouter</button>
      <a href="liste_rouges.php" class="btn btn-secondary">Retour</a>
    </form>
  </div>
</div>

<!-- All members preloaded in JS -->
<script>
const allMembers = {
<?php
$members = mysqli_query($db, "SELECT CODE, NOM, PRENOM, MATRICULE FROM bd_active");
$grouped = [];
while ($row = mysqli_fetch_assoc($members)) {
    $mat = $row['MATRICULE'];
    if (!isset($grouped[$mat])) $grouped[$mat] = [];
    $grouped[$mat][] = $row;
}
foreach ($grouped as $matricule => $members) {
    echo "$matricule: [";
    foreach ($members as $m) {
        $code = $m['CODE'];
        $nom = addslashes($m['NOM']);
        $prenom = addslashes($m['PRENOM']);
        echo "{CODE: $code, NOM: '$nom', PRENOM: '$prenom'},";
    }
    echo "],";
}
?>
};

document.getElementById("familleSelect").addEventListener("change", function () {
    const matricule = this.value;
    const memberSelect = document.getElementById("memberSelect");
    memberSelect.innerHTML = '<option value="">-- Choisir un membre (facultatif si tous) --</option>';

    if (allMembers[matricule]) {
        allMembers[matricule].forEach(member => {
            const opt = document.createElement("option");
            opt.value = member.CODE;
            opt.text = member.CODE + " - " + member.NOM + " " + member.PRENOM;
            opt.setAttribute('data-nom', member.NOM);
            opt.setAttribute('data-prenom', member.PRENOM);
            memberSelect.appendChild(opt);
        });
    }
});

function updateNomPrenom(select) {
    const selected = select.options[select.selectedIndex];
    document.getElementById("nomField").value = selected.getAttribute('data-nom') || '';
    document.getElementById("prenomField").value = selected.getAttribute('data-prenom') || '';
}
</script>

<?php include '../includes/footer.php'; ?>
