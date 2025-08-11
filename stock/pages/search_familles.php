<?php
include '../includes/connection.php';

$term = isset($_GET['term']) ? $_GET['term'] : '';
$term = mysqli_real_escape_string($db, $term);

$sql = "SELECT id_mat_fam, nom_famille, type 
        FROM famille 
        WHERE nom_famille LIKE '%$term%' OR id_mat_fam LIKE '%$term%' 
        ORDER BY nom_famille ASC";

$result = mysqli_query($db, $sql);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'label' => $row['nom_famille'] . ' (' . $row['id_mat_fam'] . ' - ' . $row['type'] . ')',
        'id'    => $row['id_mat_fam'],
        'type'  => $row['type']
    ];
}

echo json_encode($data);
