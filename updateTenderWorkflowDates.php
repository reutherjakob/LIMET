<?php
    session_start();

    if(!isset($_SESSION["username"]))
    {
        echo "Bitte erst <a href=\"index.php\">einloggen</a>";
        exit;
    }
    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
    if ($mysqli ->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $mysqli->query("SET NAMES 'utf8'");

    /* change character set to utf8 */
    if (!$mysqli->set_charset("utf8")) {
        echo "Error loading character set utf8: " . $mysqli->error;
        exit();
    } 	
    
    //------------------------------------------------------------------------------------
    $sql = "SELECT tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer, DATE_FORMAT(DATE(tabelle_lot_workflow.Timestamp_Soll), '%Y-%m-%d') as Timestamp_Soll, tabelle_workflow_has_tabelle_wofklowteil.TageMinDanach, tabelle_lot_workflow.tabelle_lose_extern_idtabelle_Lose_Extern, tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow, tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil
            FROM tabelle_lot_workflow INNER JOIN tabelle_workflow_has_tabelle_wofklowteil ON (tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow = tabelle_workflow_has_tabelle_wofklowteil.tabelle_workflow_idtabelle_workflow) AND (tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil = tabelle_workflow_has_tabelle_wofklowteil.tabelle_wofklowteil_idtabelle_wofklowteil)
            WHERE (((tabelle_lot_workflow.tabelle_lose_extern_idtabelle_Lose_Extern)=".filter_input(INPUT_GET, 'lotID')."))
            ORDER BY tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer DESC;";

    $result = $mysqli->query($sql); 
    $workflowTeile = array();
    $counter = 0;
    while ($row = $result->fetch_assoc()) {
        $workflowTeile[$counter]['tabelle_workflow_idtabelle_workflow'] = $row['tabelle_workflow_idtabelle_workflow'];
        $workflowTeile[$counter]['tabelle_wofklowteil_idtabelle_wofklowteil'] = $row['tabelle_wofklowteil_idtabelle_wofklowteil'];
        $workflowTeile[$counter]['Timestamp_Soll'] = $row['Timestamp_Soll'];    
        $workflowTeile[$counter]['TageMinDanach'] = $row['TageMinDanach'];  
        $counter++;
    }
    //-----------------------------------------------------------------------------------        
    $counter = 0;
    $tageDanach = 0;
    $oldDate = 0;
    foreach($workflowTeile as $array) {                   
        if ($counter > 0){
            $newDate = date('Y-m-d', strtotime($oldDate. " - {$array['TageMinDanach']} days"));            
            $ausgabe = $ausgabe." ".$oldDate." ".$array['TageMinDanach']." ".date('N', strtotime($newDate)); 
            //ABfrage ob Samstag oder Sonntag     
            $wochentag = date('N', strtotime($newDate));
            if ($wochentag == 6){
                $newDate = date('Y-m-d', strtotime($newDate. " - 1 days")); 
            }
            else{
                if($wochentag == 7){
                    $newDate = date('Y-m-d', strtotime($newDate. " - 2 days")); 
                }
            }
            $ausgabe = $ausgabe." ".$newDate;
            $sql1 = "UPDATE `LIMET_RB`.`tabelle_lot_workflow`
                SET
                `Timestamp_Soll` = '".date('Y-m-d', strtotime($newDate))."'  
                WHERE 
                `tabelle_lose_extern_idtabelle_Lose_Extern` = ".filter_input(INPUT_GET, 'lotID')." 
                AND 
                `tabelle_workflow_idtabelle_workflow` = ".$array['tabelle_workflow_idtabelle_workflow']." 
                AND
                `tabelle_wofklowteil_idtabelle_wofklowteil` = ".$array['tabelle_wofklowteil_idtabelle_wofklowteil'].";";
            
            if ($mysqli->query($sql1) === TRUE) {
                $ausgabe = $ausgabe." Workflowteil ".$array['tabelle_wofklowteil_idtabelle_wofklowteil']." erfolgreich aktualisiert! \n";
            } 
            else {
                $ausgabe = $ausgabe." Error: " . $sql . "<br>" . $mysqli->error;
            }
            $oldDate = $newDate;
        }
        else{
            $oldDate = $array['Timestamp_Soll'];
        }        
        $counter++;             
    }           
    $mysqli ->close();
    echo $ausgabe;
?>
