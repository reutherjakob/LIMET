<?php
require_once '../utils/_utils.php';

$lot_id = getPostInt('lotID', 0);
if (!$lot_id) exit('Kein Los-ID');

$mysqli = utils_connect_sql();  

$sql = "SELECT idtabelle_rb_aenderung, Timestamp, user, Kurzbeschreibung, Anzahl, Anzahl_copy1,
               tabelle_Lose_Intern_idtabelle_Lose_Intern, tabelle_Lose_Intern_idtabelle_Lose_Intern_copy1,
               tabelle_Lose_Extern_idtabelle_Lose_Extern, tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1
        FROM tabelle_rb_aenderung 
        WHERE (tabelle_Lose_Intern_idtabelle_Lose_Intern = ? OR tabelle_Lose_Extern_idtabelle_Lose_Extern = ?
               OR tabelle_Lose_Intern_idtabelle_Lose_Intern_copy1 = ? OR tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1 = ?)
        ORDER BY Timestamp DESC";

$stmt = mysqli_prepare($mysqli, $sql);
mysqli_stmt_bind_param($stmt, "iiii", $lot_id, $lot_id, $lot_id, $lot_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<h5>🕰️ Vollständige Änderungshistorie: Los #<?php echo $lot_id; ?></h5>
<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead class="table-dark">
        <tr>
            <th>Datum</th><th>Benutzer</th><th>Beschreibung</th><th>Änderungen</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($change = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo date('d.m.Y H:i', strtotime($change['Timestamp'])); ?></td>
                <td><?php echo h($change['user']); ?></td>
                <td><?php echo h($change['Kurzbeschreibung'] ?? '---'); ?></td>
                <td>
                    <?php
                    $changes = [];
                    if (($change['Anzahl'] ?? 0) != ($change['Anzahl_copy1'] ?? 0))
                        $changes[] = "Anzahl: " . ($change['Anzahl'] ?? '') . " → " . ($change['Anzahl_copy1'] ?? '');
                    if (($change['tabelle_Lose_Intern_idtabelle_Lose_Intern'] ?? 0) != ($change['tabelle_Lose_Intern_idtabelle_Lose_Intern_copy1'] ?? 0))
                        $changes[] = "Intern: " . ($change['tabelle_Lose_Intern_idtabelle_Lose_Intern'] ?? '') . " → " . ($change['tabelle_Lose_Intern_idtabelle_Lose_Intern_copy1'] ?? '');
                    if (($change['tabelle_Lose_Extern_idtabelle_Lose_Extern'] ?? 0) != ($change['tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1'] ?? 0))
                        $changes[] = "Extern: " . ($change['tabelle_Lose_Extern_idtabelle_Lose_Extern'] ?? '') . " → " . ($change['tabelle_Lose_Extern_idtabelle_Lose_Extern_copy1'] ?? '');
                    echo implode(', ', $changes) ?: '---';
                    ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php if (mysqli_num_rows($result) == 0): ?>
    <div class="alert alert-info">Keine weiteren Änderungen für dieses Los gefunden.</div>
<?php endif; ?>
