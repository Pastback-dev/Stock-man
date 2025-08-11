<?php
function getMappedId($db, $varcharId) {
    // Check if mapping exists
    $query = "SELECT id_int FROM table_mappage WHERE id_varchar_hc = '$varcharId'";
    $result = mysqli_query($db, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['id_int'];
    }
    
    // Create new mapping if it doesn't exist
    // Find next available ID (start from 100000 to avoid conflicts with regular families)
    $query = "SELECT COALESCE(MAX(id_int), 100000) + 1 AS next_id FROM table_mappage";
    $result = mysqli_query($db, $query);
    $row = mysqli_fetch_assoc($result);
    $newIntId = $row['next_id'];
    
    // Insert new mapping
    $insert = "INSERT INTO table_mappage (id_varchar_hc, id_int) VALUES ('$varcharId', $newIntId)";
    if (!mysqli_query($db, $insert)) {
        die("Error creating mapping: " . mysqli_error($db));
    }
    
    return $newIntId;
}

function ensureMappingExists($db, $varcharId) {
    getMappedId($db, $varcharId); // This will create if doesn't exist
}
?>