<?php 
    require("includes/config.inc.php");
    require("includes/common.inc.php");
    require("includes/db.inc.php");

    $conn = dbConnect();

    // Datenbank und Tabelle aus der URL-Parameter
    $database = isset($_GET['database']) ? $_GET['database'] : '';
    $table = isset($_GET['table']) ? $_GET['table'] : '';

    if ($database) {
        // Setze die ausgewählte Datenbank als aktive Datenbank
        $conn->select_db($database);
         // Hole die Spaltennamen der Tabelle
        $sql_columns = "DESCRIBE `$table`";
        $result_columns = dbQuery($conn, $sql_columns);
        
        $columns = [];
    if ($result_columns->num_rows > 0) {
        while ($row = $result_columns->fetch_object()) {
            $columns[] = $row->Field; // Füge den Spaltennamen zum Array hinzu
        }
    }

    // Gebe die Spalten als JSON zurück
    echo json_encode($columns);
    } else {
        echo json_encode([]); // Keine Datenbank oder Tabelle ausgewählt
    }

    $conn->close();
?>