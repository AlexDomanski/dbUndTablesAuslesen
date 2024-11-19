<?php 
    require("includes/config.inc.php");
    require("includes/common.inc.php");
    require("includes/db.inc.php");

    $conn = dbConnect();

    // Datenbank aus der URL-Parameter
    $database = isset($_GET['database']) ? $_GET['database'] : '';

    if ($database) {
        // Setze die ausgewählte Datenbank als aktive Datenbank
        $conn->select_db($database);
        // Hole alle Tabellen dieser Datenbank
        $sql = "SHOW TABLES";
        $result = dbQuery($conn, $sql);
        
        $tables = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_row()) {
                $tables[] = $row[0]; // Füge den Tabellennamen zu dem Array hinzu
            }
        }
        
        // Gebe die Tabellen als JSON zurück
        echo json_encode($tables);
    } else {
        echo json_encode([]); // Keine Datenbank ausgewählt
    }

    $conn->close();
?>