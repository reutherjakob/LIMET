<?php 
    session_start();
    
    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    } else {
        //echo "Connected successfully";
    }
    
    if (!$mysqli->set_charset("utf8")) {
        printf("Error loading character set utf8: %s\n", $mysqli->error);
        exit();
    }	

 $sql = " SELECT tabelle_räume.tabelle_projekte_idTABELLE_Projekte, 
        tabelle_räume.idTABELLE_Räume, 
        tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, 
        tabelle_räume.Raumnr, 
        tabelle_räume.Raumbezeichnung, 
        tabelle_räume.`Funktionelle Raum Nr`, 
        tabelle_räume.Raumnummer_Nutzer, 
        tabelle_räume.`Raumbereich Nutzer`, 
        tabelle_räume.Geschoss, 
        tabelle_räume.Bauetappe, 
        tabelle_räume.Bauabschnitt, 
        tabelle_räume.Nutzfläche, 
        tabelle_räume.Abdunkelbarkeit, 
        tabelle_räume.Strahlenanwendung, 
        tabelle_räume.Laseranwendung, 
        tabelle_räume.H6020, 
        tabelle_räume.GMP, 
        tabelle_räume.ISO, 
        tabelle_räume.`1 Kreis O2`, 
        tabelle_räume.`2 Kreis O2`, 
        tabelle_räume.O2, 
        tabelle_räume.`1 Kreis Va`, 
        tabelle_räume.`2 Kreis Va`, 
        tabelle_räume.VA, 
        tabelle_räume.`1 Kreis DL-5`, 
        tabelle_räume.`2 Kreis DL-5`, 
        tabelle_räume.`DL-5`, 
        tabelle_räume.`DL-10`, 
        tabelle_räume.`DL-tech`,
        tabelle_räume.CO2, 
        tabelle_räume.H2, 
        tabelle_räume.He, 
        tabelle_räume.`He-RF`, 
        tabelle_räume.Ar, 
        tabelle_räume.N2, 
        tabelle_räume.NGA, 
        tabelle_räume.N2O, 
        tabelle_räume.AV, 
        tabelle_räume.SV, 
        tabelle_räume.ZSV, 
        tabelle_räume.USV, 
        tabelle_räume.`IT Anbindung`, 
        tabelle_räume.Anwendungsgruppe, 
        tabelle_räume.`Allgemeine Hygieneklasse`, 
        tabelle_räume.Raumhoehe, 
        tabelle_räume.`MT-relevant`, 
        tabelle_räume.`Raumhoehe 2`, 
        tabelle_räume.Belichtungsfläche, 
        tabelle_räume.Umfang, 
        tabelle_räume.Volumen, 
        tabelle_räume.ET_Anschlussleistung_W, 
        tabelle_räume.HT_Waermeabgabe_W, 
        tabelle_räume.VEXAT_Zone, 
        tabelle_räume.HT_Abluft_Vakuumpumpe, 
        tabelle_räume.HT_Abluft_Schweissabsaugung_Stk, 
        tabelle_räume.HT_Abluft_Esse_Stk, 
        tabelle_räume.HT_Abluft_Rauchgasabzug_Stk, 
        tabelle_räume.HT_Abluft_Digestorium_Stk, 
        tabelle_räume.HT_Punktabsaugung_Stk, 
        tabelle_räume.HT_Abluft_Sicherheitsschrank_Unterbau_Stk,
        tabelle_räume.HT_Abluft_Sicherheitsschrank_Stk, 
        tabelle_räume.HT_Spuele_Stk, 
        tabelle_räume.HT_Kühlwasser, tabelle_räume.O2_Mangel, 
        tabelle_räume.CO2_Melder, tabelle_räume.`ET_RJ45-Ports`, 
        tabelle_räume.ET_64A_3Phasig_Einzelanschluss, 
        tabelle_räume.ET_32A_3Phasig_Einzelanschluss, 
        tabelle_räume.ET_16A_3Phasig_Einzelanschluss, 
        tabelle_räume.ET_Digestorium_MSR_230V_SV_Stk, 
        tabelle_räume.ET_5x10mm2_Digestorium_Stk, 
        tabelle_räume.ET_5x10mm2_USV_Stk, 
        tabelle_räume.ET_5x10mm2_SV_Stk, 
        tabelle_räume.ET_5x10mm2_AV_Stk, 
        tabelle_räume.`Wasser Qual 3 l/min`, 
        tabelle_räume.`Wasser Qual 2 l/Tag`, 
        tabelle_räume.`Wasser Qual 1 l/Tag`, 
        tabelle_räume.`Wasser Qual 3`, 
        tabelle_räume.`Wasser Qual 2`, 
        tabelle_räume.`Wasser Qual 1`, 
        tabelle_räume.LHe, 
        tabelle_räume.`LN l/Tag`,
        tabelle_räume.LN, 
        tabelle_räume.`N2 Reinheit`, 
        tabelle_räume.`N2 l/min`, 
        tabelle_räume.`Ar Reinheit`,
        tabelle_räume.`Ar l/min`, 
        tabelle_räume.`He Reinheit`,
        tabelle_räume.`He l/min`, 
        tabelle_räume.`H2 Reinheit`,
        tabelle_räume.`H2 l/min`, 
        tabelle_räume.`DL ISO 8573`, 
        tabelle_räume.`DL l/min`, 
        tabelle_räume.`VA l/min`,
        tabelle_räume.`CO2 l/min`, 
        tabelle_räume.`CO2 Reinheit`, 
        tabelle_räume.`O2 l/min`, 
        tabelle_räume.`O2 l/min`,
        tabelle_räume.`O2 Reinheit`, 
        tabelle_räume.Laserklasse
        FROM tabelle_räume
        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=".$_SESSION["projectID"]."))
        ORDER BY tabelle_räume.Raumnr;";
    
    if (!$mysqli->query($sql)) {
        echo "Error executing query: " . $mysqli->error;
    }   
    else{
       $result = $mysqli->query($sql);
    }
    $mysqli->close();

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($data);
    
?>
