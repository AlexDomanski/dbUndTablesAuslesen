<?php 
    require("includes/config.inc.php");
    require("includes/common.inc.php");
    require("includes/db.inc.php");

    $conn = dbConnect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My databases</title>
</head>
<body>

<?php 
    $sql = "SHOW DATABASES";
    $result = dbQuery($conn, $sql);

    if ($result->num_rows > 0) {
        echo('<form method="post">');
        echo('Waehlen Sie einen Datenbank aus:<br>');
        echo '<select name="databases" id="databases" onchange="loadTables()">';
        while ($row = $result -> fetch_object()) {
            $dbname = $row->Database;
            if ($dbname != 'information_schema' && $dbname != 'mysql' && $dbname != 'performance_schema' && $dbname != 'sys' && $dbname != 'phpmyadmin' && $dbname != 'test') {
                echo "<option value='$dbname'>$dbname</option>";
            }
        }
        echo '</select><br>';
    } else {
        echo "No databases found.";
    }
?>

<!-- Hier wird das Dropdown für die Tabellen erscheinen -->
<label for="tables">Wählen Sie eine Tabelle:</label><br>
<select name="tables" id="tables" onchange="loadColumns()">
    <option value="">Wählen Sie eine Tabelle aus</option>
</select>

<!-- Hier wird das Formular mit den Spalten und den leeren Textfeldern erscheinen -->
<div id="columnsForm"></div>
<br><br>
<script>
// Funktion, die beim Ändern der Datenbank im ersten Dropdown aufgerufen wird
function loadTables() {
    var database = document.getElementById("databases").value; // ausgewählte Datenbank
    var tablesDropdown = document.getElementById("tables"); // das Dropdown für Tabellen
    
    // Falls keine Datenbank ausgewählt wurde, leere das Tabellen-Dropdown
    if (database == "") {
        tablesDropdown.innerHTML = '<option value="">Wählen Sie eine Tabelle aus</option>';
        return;
    }

    // Erstelle eine Anfrage mit AJAX
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "get_tables.php?database=" + database, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var tables = JSON.parse(xhr.responseText); // Antwort als JSON
            tablesDropdown.innerHTML = '<option value="">Wählen Sie eine Tabelle aus</option>'; // Setze Standardoption
            
            // Füge jede Tabelle als Option hinzu
            tables.forEach(function(table) {
                var option = document.createElement("option");
                option.value = table;
                option.textContent = table;
                tablesDropdown.appendChild(option);
            });
        }
    };
    xhr.send(); // Anfrage senden
}

// Funktion, die beim Ändern der Tabelle im Dropdown aufgerufen wird
function loadColumns() {
    var database = document.getElementById("databases").value; // ausgewählte Datenbank
    var table = document.getElementById("tables").value; // ausgewählte Tabelle
    
    // Falls keine Tabelle ausgewählt wurde, leere das Formular
    if (table == "") {
        document.getElementById("columnsForm").innerHTML = '';
        return;
    }

    // Erstelle eine Anfrage mit AJAX
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "get_columns.php?database=" + database + "&table=" + table, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var columns = JSON.parse(xhr.responseText); // Antwort als JSON
            var form = document.getElementById("columnsForm");
            form.innerHTML = ''; // Leere das Formular bevor neue Felder hinzugefügt werden
            
            // Generiere ein Textfeld für jede Spalte
            columns.forEach(function(column) {
                var label = document.createElement("label");
                label.textContent = column;
                var input = document.createElement("input");
                input.type = "text";
                input.name = column;
                input.placeholder = "Geben Sie einen Wert für " + column + " ein";
                
                form.appendChild(label);
                form.appendChild(document.createElement("br"));
                form.appendChild(input);
                form.appendChild(document.createElement("br"));
                form.appendChild(document.createElement("br"));
            });
        }
    };
    xhr.send(); // Anfrage senden
}
</script>
<button type="submit">Submit</button>
</form>
</body>
</html>