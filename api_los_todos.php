<?php
require_once "utils/_utils.php";
check_login();
header('Content-Type: application/json');

$mysqli = utils_connect_sql();
$action = getPostString('action');

$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    switch ($action) {
        case 'get_all_todos':
            handleGetAllTodos($mysqli, $response);
            break;
        case 'filter_todos':
            handleFilterTodos($mysqli, $response);
            break;
        case 'get_statistics':
            handleGetStatistics($mysqli, $response);
            break;
        case 'add_todo':
            handleAddTodo($mysqli, $response);
            break;
        case 'update_todo':
            handleUpdateTodo($mysqli, $response);
            break;
        case 'update_status':
            handleUpdateStatus($mysqli, $response);
            break;
        case 'delete_todo':
            handleDeleteTodo($mysqli, $response);
            break;
        default:
            throw new Exception("Unbekannte Aktion: $action");
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$mysqli->close();

function handleGetAllTodos($mysqli, &$response)
{
    $sql = "SELECT 
                t.id_tabelle_lose_ToDos, 
                p.Interne_Nr, 
                p.Projektname, 
                l.LosNr_Extern, 
                l.LosBezeichnung_Extern, 
                l.Vergabe_abgeschlossen,
                e.ElementID, 
                e.Bezeichnung, 
                t.Datum, 
                t.Ersteller,
                t.ToDo
            FROM tabelle_projekte p 
            INNER JOIN tabelle_lose_extern l ON l.tabelle_projekte_idTABELLE_Projekte = p.idTABELLE_Projekte
            INNER JOIN tabelle_lose_ToDos t ON t.id_tabelle_lose_extern = l.idtabelle_Lose_Extern
            INNER JOIN tabelle_elemente e ON e.idTABELLE_Elemente = t.id_tabelle_element
            ORDER BY p.Interne_Nr DESC, l.LosNr_Extern, e.ElementID";

    $result = $mysqli->query($sql);
    $todos = [];
    while ($row = $result->fetch_assoc()) {
        $todos[] = $row;
    }
    $response['success'] = true;
    $response['data'] = $todos;
}

function handleFilterTodos($mysqli, &$response)
{
    $projektId = getPostInt('projekt_id');
    $losId = getPostInt('los_id');
    $status = getPostString('status');

    $sql = "SELECT 
                t.id_tabelle_lose_ToDos, 
                p.Interne_Nr, 
                p.Projektname, 
                l.LosNr_Extern, 
                l.LosBezeichnung_Extern, 
                l.Vergabe_abgeschlossen,
                e.ElementID, 
                e.Bezeichnung, 
                t.Datum, 
                t.Ersteller,
                t.ToDo
            FROM tabelle_projekte p
            INNER JOIN tabelle_lose_extern l ON l.tabelle_projekte_idTABELLE_Projekte = p.idTABELLE_Projekte
            INNER JOIN tabelle_lose_ToDos t ON t.id_tabelle_lose_extern = l.idtabelle_Lose_Extern
            INNER JOIN tabelle_elemente e ON e.idTABELLE_Elemente = t.id_tabelle_element
            WHERE 1=1";

    $params = [];
    $types = "";

    if ($projektId > 0) {
        $sql .= " AND p.idTABELLE_Projekte = ?";
        $params[] = $projektId;
        $types .= "i";
    }
    if ($losId > 0) {
        $sql .= " AND l.idtabelle_Lose_Extern = ?";
        $params[] = $losId;
        $types .= "i";
    }
    if ($status !== '') {
        $sql .= " AND l.Vergabe_abgeschlossen = ?";
        $params[] = $status;
        $types .= "s";
    }

    $sql .= " ORDER BY p.Interne_Nr DESC, l.LosNr_Extern, e.ElementID";

    if (!empty($params)) {
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $mysqli->query($sql);
    }

    $todos = [];
    while ($row = $result->fetch_assoc()) {
        $todos[] = $row;
    }
    $response['success'] = true;
    $response['data'] = $todos;
}

function handleGetStatistics($mysqli, &$response)
{
    $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN l.Vergabe_abgeschlossen = 0 THEN 1 ELSE 0 END) as offen,
                SUM(CASE WHEN l.Vergabe_abgeschlossen = 1 THEN 1 ELSE 0 END) as fertig,
                SUM(CASE WHEN l.Vergabe_abgeschlossen = 2 THEN 1 ELSE 0 END) as wartend
            FROM tabelle_lose_ToDos t
            INNER JOIN tabelle_lose_extern l ON t.id_tabelle_lose_extern = l.idtabelle_Lose_Extern";

    $result = $mysqli->query($sql);
    $stats = $result->fetch_assoc();
    $response['success'] = true;
    $response['data'] = $stats;
}

function handleAddTodo($mysqli, &$response)
{
    $losId = getPostInt('los_id');
    $elementId = getPostInt('element_id');
    $datum = getPostDate('datum');
    $todoText = getPostString('todo_text');
    $ersteller = $_SESSION['username'] ?? 'Unknown';

    if ($losId <= 0 || $elementId <= 0 || empty($todoText) || empty($datum)) {
        throw new Exception("Alle Pflichtfelder müssen ausgefüllt werden!");
    }

    $stmt = $mysqli->prepare(
        "INSERT INTO tabelle_lose_ToDos 
        (id_tabelle_lose_extern, id_tabelle_element, Datum, Ersteller, ToDo) 
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param("iisss", $losId, $elementId, $datum, $ersteller, $todoText);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "ToDo erfolgreich hinzugefügt!";
        $response['data'] = ['id' => $mysqli->insert_id];
    } else {
        throw new Exception("Fehler beim Speichern: " . $stmt->error);
    }
}

function handleUpdateTodo($mysqli, &$response)
{
    $id = getPostInt('id');
    $todoText = getPostString('todo_text');
    $datum = getPostDate('datum');

    if ($id <= 0 || empty($todoText)) {
        throw new Exception("ID und ToDo-Text sind erforderlich!");
    }

    if (!empty($datum)) {
        $stmt = $mysqli->prepare(
            "UPDATE tabelle_lose_ToDos SET ToDo = ?, Datum = ? WHERE id_tabelle_lose_ToDos = ?"
        );
        $stmt->bind_param("ssi", $todoText, $datum, $id);
    } else {
        $stmt = $mysqli->prepare(
            "UPDATE tabelle_lose_ToDos SET ToDo = ? WHERE id_tabelle_lose_ToDos = ?"
        );
        $stmt->bind_param("si", $todoText, $id);
    }

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = "ToDo erfolgreich aktualisiert!";
        } else {
            throw new Exception("Kein ToDo mit dieser ID gefunden oder keine Änderungen vorgenommen");
        }
    } else {
        throw new Exception("Fehler beim Aktualisieren: " . $stmt->error);
    }
}

function handleUpdateStatus($mysqli, &$response)
{
    $id = getPostInt('id');
    $status = getPostInt('status');

    if ($id <= 0 || $status < 0 || $status > 2) {
        throw new Exception("Ungültige Parameter!");
    }

    $stmt = $mysqli->prepare("SELECT id_tabelle_lose_extern FROM tabelle_lose_ToDos WHERE id_tabelle_lose_ToDos = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $losId = $row['id_tabelle_lose_extern'];

        $stmt = $mysqli->prepare(
            "UPDATE tabelle_lose_extern SET Vergabe_abgeschlossen = ? WHERE idtabelle_Lose_Extern = ?"
        );
        $stmt->bind_param("ii", $status, $losId);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Status erfolgreich aktualisiert!";
        } else {
            throw new Exception("Fehler beim Aktualisieren: " . $stmt->error);
        }
    } else {
        throw new Exception("ToDo nicht gefunden!");
    }
}

function handleDeleteTodo($mysqli, &$response)
{
    $id = getPostInt('id');

    if ($id <= 0) {
        throw new Exception("Ungültige ToDo-ID!");
    }

    $stmt = $mysqli->prepare("DELETE FROM tabelle_lose_ToDos WHERE id_tabelle_lose_ToDos = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = "ToDo erfolgreich gelöscht!";
        } else {
            throw new Exception("Kein ToDo mit dieser ID gefunden");
        }
    } else {
        throw new Exception("Fehler beim Löschen: " . $stmt->error);
    }
}