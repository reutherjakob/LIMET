<?php
require_once 'utils/_utils.php';
check_login();
$id = getPostInt('id', 0);
$lotID = getPostInt('lotID', 0);

if (!$id && !$lotID) exit('ID oder lotID fehlt');

$mysqli = utils_connect_sql();

if ($id) {
    // EINZELNE ÄNDERUNG + Los-ID extrahieren
    $sqlSingle = "SELECT * FROM tabelle_rb_aenderung WHERE idtabelle_rb_aenderung = ?";
    $stmtSingle = mysqli_prepare($mysqli, $sqlSingle);
    mysqli_stmt_bind_param($stmtSingle, 'i', $id);
    mysqli_stmt_execute($stmtSingle);
    $resultSingle = mysqli_stmt_get_result($stmtSingle);
    $row = mysqli_fetch_assoc($resultSingle);

    if (!$row) exit('Kein Eintrag gefunden');

    // Los-ID extrahieren (Intern/Extern > Copy-Felder)
    $lotid_intern = intval($row['tabelle_Lose_Intern_idtabelle_Lose_Intern'] ?? 0);
    $lotid_extern = intval($row['tabelle_Lose_Extern_idtabelle_Lose_Extern'] ?? 0);
    $lotID = $lotid_intern ?: $lotid_extern;

    if (!$lotID) {
        $lotid_intern_copy = intval($row['tabelle_Lose_Intern_idtabelle_Lose_Intern_copy1'] ?? 0);
        $lotid_extern_copy = intval($row['tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1'] ?? 0);
        $lotID = $lotid_intern_copy ?: $lotid_extern_copy;
    }
    if (!$lotID) exit('Kein Los referenziert');
}

// AKTUELLE ÄNDERUNG
?>
<div class="row">
    <div class="col-md-6">
        <h6>Aktuelle Änderung #<?php echo h($row['id'] ?? '?'); ?> Los <?php echo $lotID; ?></h6>
        <table class="table table-sm table-bordered">
            <tr><th>ID</th><td><?php echo h($row['id'] ?? '-'); ?></td></tr>
            <tr><th>Beschreibung</th><td><?php echo h($row['Kurzbeschreibung'] ?? '-'); ?></td></tr>
            <tr><th>Anzahl</th><td><?php echo h($row['Anzahl'] ?? '-'); ?></td></tr>
            <tr><th>Anschaffung</th><td><?php echo h($row['Anschaffung'] ?? '-'); ?></td></tr>
            <tr><th>Benutzer</th><td><?php echo h($row['user'] ?? '-'); ?></td></tr>
            <tr><th>Datum</th><td><?php echo date('d.m.Y H:i', strtotime($row['Timestamp'] ?? 'now')); ?></td></tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6 class="text-danger">Diese Änderung</h6>
        <?php
        $fields = [
            'tabelle_Lose_Intern_idtabelle_Lose_Intern' => 'Los Intern',
            'tabelle_Lose_Extern_idtabelle_Lose_Extern' => 'Los Extern',
            'Anzahl' => 'Anzahl',
            'Standort' => 'Standort',
            'Verwendung' => 'Verwendung',
            'Anschaffung' => 'Anschaffung',
            'NeuBestand' => 'NeuBestand',
            'lieferdatumalt' => 'Lieferdatum alt',
            'raumIDalt' => 'Raum alt',
            'statusalt' => 'Status alt'
        ];
        foreach ($fields as $field => $label) {
            $old = $row[$field] ?? null;
            $new = $row[str_replace('alt', 'neu', $field) . '_copy1'] ?? $row[str_replace('alt', 'neu', $field)] ?? null;
            if ($old != $new) {
                echo '<div class="alert alert-warning alert-sm mb-1">
                    <strong>' . h($label) . '</strong>: ' . h($old) . ' → ' . h($new) . '
                </div>';
            }
        }
        ?>
    </div>
</div>
<hr>
<?php
// VOLLE LOS-HISTORIE (immer laden wenn lotID da)
if ($lotID) {
    $sql = "SELECT idtabelle_rb_aenderung, Timestamp, `user`, Kurzbeschreibung, Anzahl, Anzahl_copy1,
            Standort, Standort_copy1, Verwendung, Verwendung_copy1, Anschaffung, Anschaffung_copy1,
            lieferdatum_alt, lieferdatum_neu, raumID_alt, raumID_neu, status_alt, status_neu,
            tabelle_Lose_Intern_idtabelle_Lose_Intern, tabelle_Lose_Intern_idtabelle_Lose_Intern_copy1,
            tabelle_Lose_Extern_idtabelle_Lose_Extern, tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1
            FROM tabelle_rb_aenderung
            WHERE tabelle_Lose_Intern_idtabelle_Lose_Intern = ? OR tabelle_Lose_Extern_idtabelle_Lose_Extern = ?
            OR tabelle_Lose_Intern_idtabelle_Lose_Intern_copy1 = ? OR tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1 = ?
            ORDER BY Timestamp DESC";

    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, 'iiii', $lotID, $lotID, $lotID, $lotID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    ?>
    <h5 class="text-primary">Vollständige Änderungshistorie Los <?php echo $lotID; ?></h5>
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead class="table-dark">
            <tr><th>Datum</th><th>Benutzer</th><th>Beschreibung</th><th>Änderungen</th></tr>
            </thead>
            <tbody>
            <?php while ($change = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo date('d.m.Y H:i', strtotime($change['Timestamp'])); ?></td>
                    <td><?php echo h($change['user']); ?></td>
                    <td><?php echo h($change['Kurzbeschreibung'] ?? '---'); ?></td>
                    <td>
                        <?php
                        $changeFields = ['Anzahl', 'Anzahl_copy1', 'Standort', 'Standort_copy1', 'Verwendung', 'Verwendung_copy1',
                            'Anschaffung', 'Anschaffung_copy1', 'lieferdatumalt', 'lieferdatumneu',
                            'raumIDalt', 'raumIDneu', 'statusalt', 'statusneu'];
                        $changes = [];
                        foreach ($changeFields as $fields) {
                            $old = $change[$fields . '0'] ?? null;
                            $new = $change[$fields . '1'] ?? null;
                            if ($old != $new) {
                                $changes[] = ($fields . '0') . ': ' . ($old ?? 'NULL') . ' → ' . ($new ?? 'NULL');
                            }
                        }
                        echo implode(', ', $changes) ?: '---';
                        ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>
