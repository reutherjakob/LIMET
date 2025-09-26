<?php
$formFields = [
    //["type" => "text", "name" => "roomname", "label" => "Raumname", "required" => true],
  // ["type" => "text", "name" => "username", "label" => "Username", "required" => true],

    ["type" => "yesno", "kathegorie" =>"Raum", "name" => "fussboden_onorm_b5220", "label" => "Fußboden ÖNORM B5220"],
    ["type" => "yesno", "kathegorie" =>"Raum", "name" => "verdunkelung", "label" => "Verdunkelung"],
    ["type" => "yesno", "kathegorie" =>"Raum", "name" => "schallschutzanforderung", "label" => "Erhöhte Schallschutzanforderung"],
   ["type" => "select", "kathegorie" =>"Raum", "name" => "vc_klasse", "label" => "VC-Klassen", "options" => ["0" => "keine Anforderung", "1" => "Klasse 1", "2" => "Klasse 2", "3" => "Klasse 3"]],
    ["type" => "yesno", "kathegorie" =>"Raum", "name" => "chemikalienliste", "label" => "Chemikalienliste"],
   ["type" => "select", "kathegorie" =>"Raum", "name" => "vexat_zone", "label" => "VEXAT Zone", "options" => ["0" => "Zone 0", "1" => "Zone 1", "2" => "Zone 2"]],
   ["type" => "select", "kathegorie" =>"Raum", "name" => "bsl_level", "label" => "Biosafety Level (BSL)", "options" => ["0" => "Nein", "1" => "Ja"]],
    ["type" => "yesno", "kathegorie" =>"Raum", "name" => "laser", "label" => "Laser"],

    ["type" => "yesno", "kathegorie" => "Gas", "name" => "o2", "label" => "O2"],
    ["type" => "yesno", "kathegorie" => "Gas", "name" => "va", "label" => "VA"],
    ["type" => "yesno", "kathegorie" => "Gas", "name" => "dl", "label" => "DL"],
    ["type" => "yesno", "kathegorie" => "Gas", "name" => "co2", "label" => "CO2"],
    ["type" => "yesno", "kathegorie" => "Gas", "name" => "h2", "label" => "H2"],
    ["type" => "yesno", "kathegorie" => "Gas", "name" => "he", "label" => "He"],
    ["type" => "yesno", "kathegorie" => "Gas", "name" => "he_rf", "label" => "He-RF"],
    ["type" => "yesno", "kathegorie" => "Gas", "name" => "ar", "label" => "Ar"],
    ["type" => "yesno", "kathegorie" => "Gas", "name" => "n2", "label" => "N2"],
    ["type" => "yesno", "kathegorie" => "Gas", "name" => "ln", "label" => "LN"],
     ["type" => "text", "kathegorie" => "Gas", "name" => "spezialgas", "label" => "Spezialgas lt. Anwender"],

    ["type" => "yesno", "kathegorie" =>"ET", "name" => "sv_geraete", "label" => "SV versorgte Geräte"],
    ["type" => "yesno", "kathegorie" =>"ET", "name" => "usv_geraete", "label" => "USV versorgte Geräte"],
    ["type" => "yesno", "kathegorie" =>"ET", "name" => "alarm_glt", "label" => "Alarmaufschaltung auf Gebäudeleittechnik"],

    ["type" => "yesno", "kathegorie" => "Wasser", "name" => "kuehlwasser", "label" => "Kühlwasser"],
    ["type" => "yesno", "kathegorie" => "Wasser", "name" => "VE_Wasser", "label" => "VE Wasser"],
    ["type" => "yesno", "kathegorie" => "Wasser", "name" => "geraete_abfluss", "label" => "Geräte mit Wasserabflüssen"],

    ["type" => "yesno", "kathegorie" =>"Abluft", "name" => "punktabsaugung", "label" => "Punktabsaugung vorhanden"],
    ["type" => "yesno", "kathegorie" =>"Abluft", "name" => "abluft_sicherheitsschrank", "label" => "Abluft Sicherheitsschrank vorhanden"],
    ["type" => "yesno", "kathegorie" =>"Abluft", "name" => "abluft_vakuumpumpe", "label" => "Abluft Vakuumpumpe vorhanden"],
    ["type" => "yesno", "kathegorie" =>"Abluft", "name" => "abrauchabzuege", "label" => "Abrauchabzüge/Veraschung"],
    ["type" => "yesno", "kathegorie" =>"Abluft", "name" => "sonderabluft", "label" => "Sonderabluft vorhanden"]


];