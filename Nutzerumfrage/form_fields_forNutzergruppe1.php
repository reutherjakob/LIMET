<?php
$formFields = [
    //HIDDEN ONES
    ["type" => "texthidden", "name" => "roomname", "label" => "Raumname", "required" => true],
    ["type" => "texthidden", "name" => "username", "label" => "Username", "required" => true],
    ["type" => "texthidden", "kathegorie" => "Raum", "name" => "roomID", "label" => "Room ID"],
    ["type" => "texthidden", "kathegorie" => "Raum", "name" => "raumnr", "label" => "Raum Nr."],
    ["type" => "texthidden", "kathegorie" => "Raum", "name" => "raumbereich_nutzer", "label" => "Raumbereich Nutzer"],
    //["type" => "texthidden", "kathegorie" => "Raum", "name" => "ebene", "label" => "Ebene"],
    ["type" => "texthidden", "kathegorie" => "Raum", "name" => "nf", "label" => "Nutzfläche (NF)"],

    ["type" => "KathegorieDropdowner", "kathegorie" => "Raum", "name" => "raumkategorie", "label" => "Raumkategorie (spezifisch)?", "info" => "Die Planung sieht eine Trennung von Büros, Messräumen, Wägeräumen, Lagern, Labors vor."],

    ["type" => "select", "kathegorie" => "Raum", "name" => "mitarbeiter_anzahl", "label" => "Anzahl der im Raum ständig tätigen MitarbeiterInnen?", "options" => [0 => "0", 1 => "1", 2 => "2", 3 => "3", 4 => "4", 5 => "5", 6 => "6", 7 => "7", 8 => "8"]],

    ["type" => "yesno", "kathegorie" => "Raum", "name" => "oberflaechenbestaendigkeit", "label" => "Erhöhte Anforderungen an die Oberflächenbeständigkeit <br> (z.B. Einsatz von Säuren)?"],

    ["type" => "yesno", "kathegorie" => "Raum", "name" => "gewicht_ueber_500kg", "label" => "Geräte/Elemente mit Gewicht größer 500kg in Verwendung?"],

    ["type" => "yesno", "kathegorie" => "Raum", "name" => "verdunkelung", "label" => "Verdunkelung für lichtempfindliche Geräte erforderlich?", "info" => "Bessere Verdunkelung als die Standard Jalousine bieten."],

    ["type" => "yesno", "kathegorie" => "Raum", "name" => "vibrationsempfindliche_geraete", "label" => "Vibrationsempfindliche Geräte in Verwendung <br> (z.B. Präzisionswaagen, REM)?"],

    ["type" => "yesno", "kathegorie" => "Raum", "name" => "chemikalienliste", "label" => "Tätigkeiten mit gefährlichen oder giftigen Chemikalien?"],

    ["type" => "yesno", "kathegorie" => "Raum", "name" => "explosionsschutz", "label" => "Explosionsschutz im Raum erforderlich?"],

    ["type" => "select", "kathegorie" => "Raum", "name" => "bsl_level", "label" => "Biosafety Level (BSL)", "options" => [
        "0" => "keine Anforderung", "1" => "BSL-1", "2" => "BSL-2", "3" => "BSL-3", "4" => "BSL-4"], "default_value" => "0"],

    ["type" => "text", "kathegorie" => "HT", "name" => "temperatur", "label" => "Besondere Anforderungen an die Raumtemperatur? <br> (z.B min./max.°C, max. Temperaturschwankung °C)", "default_value" => "Nein"],

    ["type" => "select", "kathegorie" => "HT", "name" => "temperatur_min", "label" => "Besondere Anforderungen an die Raumtemperatur? <br> (min.°C)", "default_value" => ["Lager" => 15, "Labor" => 22], "options" => [14 => "<15", 15 => "15", 16 => "16", 17 => "17", 18 => "18", 19 => "19", 20 => "20", 21 => "21", 22 => "22", 23 => "22<"]],
    ["type" => "select", "kathegorie" => "HT", "name" => "temperatur_max", "label" => "Besondere Anforderungen an die Raumtemperatur? <br> (max.°C)", "default_value" => ["Lager" => 27, "Labor" => 26], "options" => [19 => "<22", 22 => "22", 23 => "23", 24 => "24", 25 => "25", 26 => "26", 27 => "26<"],],
    ["type" => "yesno", "kathegorie" => "HT", "name" => "temperatur_schwankungen_relevant", "label" => "Besondere Anforderungen an die maximal zulässigen Temperaturschwankungen? "],

    ["type" => "text", "kathegorie" => "HT", "name" => "luftfeuchtigkeit_besonders", "label" => "Besondere Anforderungen an die Luftfeuchtigkeit (min./max.)? ", "default_value" => "Keine"],
    // ["type" => "text", "kathegorie" => "HT", "name" => "luftfeuchtigkeit_besonders", "label" => "Min. Luftfeuchtigkeit?"],
    // ["type" => "text", "kathegorie" => "HT", "name" => "luftfeuchtigkeit_besonders", "label" => "Max. Luftfeuchtigkeit?"],

    ["type" => "select", "kathegorie" => "HT", "name" => "druckregelung_besonders", "label" => "Besondere Anforderungen an die Druckregelung der Lüftung?",
        "options" => [1 => "Keine", 2 => "Überdruck", 3 => "Unterdruck"]],

    ["type" => "yesno", "kathegorie" => "HT", "name" => "abluftwaescher", "label" => "Abzüge mit Abluftwäscher/Säurewäscher in Verwendung? "],

    ["type" => "select", "kathegorie" => "Abluft", "name" => "raumabluft_besonders", "label" => "Besondere Anforderungen an die Raumabluft?",
        "options" => [1 => "keine", 2 => "Hepa Filter", 3 => "Sonderabluft"],
        "info" => "Überdruck, fals nichts von außen in diesen Raum hinein kommen darf. Unterdruck, fals nichts aus dem Raum nach außen gelangen darf."],
    //Komemntar Feld ?

    ["type" => "text", "kathegorie" => "Abluft", "name" => "sonderabluft", "label" => "Sonderabluft (z.B. Veraschung) von Nöten?", "default_value" => "Nein"],

    ["type" => "yesno", "kathegorie" => "Gas", "name" => "spezialgas", "label" => "Sondergase in Verwendung (dezentrale Versorgung)?",
        "info" => "Stickstoff und Druckluft sind Standard und folglich keine Sondergase."],
    //Kommmntar Feld

    ["type" => "yesno", "kathegorie" => "ET", "name" => "anschlussleistung_hoch", "label" => "Geräte mit besonders hohen Anschlussleistungen in Verwendung? (<500 W)"],

    ["type" => "yesno", "kathegorie" => "ET", "name" => "usv_geraete", "label" => "Unterbrechungsfreie Stromversorgung (USV) für empfindliche Geräte von Nöten?"],

    ["type" => "yesno", "kathegorie" => "Wasser", "name" => "VE_Wasser", "label" => "Verwendung von vollentsalztem/deionisiertem Wasser?"],

    ["type" => "yesno", "kathegorie" => "Wasser", "name" => "kuehlwasser", "label" => "Sind Geräte in Verwendung welche mittels Kühlwasser gekühlt werden könnten?"],

    ["type" => "yesno", "kathegorie" => "Wasser", "name" => "abwasser_sonderfall", "label" => "Abwasser vorhanden welches nicht in die Kanalisation geleitet werden darf?"]


];

/*

    ["type" => "select", "default_value" => "0", "kathegorie" => "Raum", "name" => "fussboden_onorm_b5220", "label" => "Fußboden ÖNORM B5220", "options" => ["0" => "keine Anforderung", "1" => "Klasse 1", "2" => "Klasse 2", "3" => "Klasse 3"]],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Raum Kommentar", "name" => "fussboden_onorm_b5220_comment", "label" => "Kommentar Fußboden ÖNORM B5220"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Raum", "name" => "verdunkelung", "label" => "Verdunkelung"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Raum Kommentar", "name" => "verdunkelung_comment", "label" => "Kommentar Verdunkelung"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Raum", "name" => "schallschutzanforderung", "label" => "Erhöhte Schallschutzanforderung"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Raum Kommentar", "name" => "schallschutzanforderung_comment", "label" => "Kommentar Erhöhte Schallschutzanforderung"],

    ["type" => "select", "default_value" => "0", "kathegorie" => "Raum", "name" => "vc_klasse", "label" => "VC-Klassen", "options" => [1 => "VC-A", 2 => "VC-B", 3 => "VC-C", 4 => "VC-D", 5 => "VC-E", 6 => "VC-F", 7 => "VC-G",]],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Raum Kommentar", "name" => "vc_klasse_comment", "label" => "Kommentar VC-Klassen"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Raum", "name" => "chemikalienliste", "label" => "Chemikalienliste"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Raum Kommentar", "name" => "chemikalienliste_comment", "label" => "Kommentar Chemikalienliste"],

    ["type" => "select", "default_value" => "0", "kathegorie" => "Raum", "name" => "vexat_zone", "label" => "VEXAT Zone", "options" => ["0" => "Zone 0", "1" => "Zone 1", "2" => "Zone 2"]],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Raum Kommentar", "name" => "vexat_zone_comment", "label" => "Kommentar VEXAT Zone"],

    ["type" => "text", "default_value" => "", "kathegorie" => "Raum", "name" => "bsl_level", "label" => "Biosafety Level (BSL)"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Raum Kommentar", "name" => "bsl_level_comment", "label" => "Kommentar Biosafety Level"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Raum", "name" => "laser", "label" => "Laser"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Raum Kommentar", "name" => "laser_comment", "label" => "Kommentar Laser"],

// Analog die Gas-Felder:
    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Gas", "name" => "o2", "label" => "O2"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Gas Kommentar", "name" => "o2_comment", "label" => "Kommentar O2"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Gas", "name" => "va", "label" => "VA"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Gas Kommentar", "name" => "va_comment", "label" => "Kommentar VA"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Gas", "name" => "dl", "label" => "DL"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Gas Kommentar", "name" => "dl_comment", "label" => "Kommentar DL"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Gas", "name" => "co2", "label" => "CO2"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Gas Kommentar", "name" => "co2_comment", "label" => "Kommentar CO2"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Gas", "name" => "h2", "label" => "H2"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Gas Kommentar", "name" => "h2_comment", "label" => "Kommentar H2"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Gas", "name" => "he", "label" => "He"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Gas Kommentar", "name" => "he_comment", "label" => "Kommentar He"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Gas", "name" => "he_rf", "label" => "He-RF"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Gas Kommentar", "name" => "he_rf_comment", "label" => "Kommentar He-RF"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Gas", "name" => "ar", "label" => "Ar"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Gas Kommentar", "name" => "ar_comment", "label" => "Kommentar Ar"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Gas", "name" => "n2", "label" => "N2"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Gas Kommentar", "name" => "n2_comment", "label" => "Kommentar N2"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Gas", "name" => "ln", "label" => "LN"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Gas Kommentar", "name" => "ln_comment", "label" => "Kommentar LN"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Gas", "name" => "spezialgas", "label" => "Spezialgas lt. Anwender"],

// ET-Felder
    ["type" => "yesno", "default_value" => "0", "kathegorie" => "ET", "name" => "sv_geraete", "label" => "SV versorgte Geräte"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "ET Kommentar", "name" => "sv_geraete_comment", "label" => "Kommentar SV versorgte Geräte"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "ET", "name" => "usv_geraete", "label" => "USV versorgte Geräte"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "ET Kommentar", "name" => "usv_geraete_comment", "label" => "Kommentar USV versorgte Geräte"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "ET", "name" => "alarm_glt", "label" => "Alarmaufschaltung auf Gebäudeleittechnik"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "ET Kommentar", "name" => "alarm_glt_comment", "label" => "Kommentar Alarmaufschaltung"],

// Wasser-Felder
    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Wasser", "name" => "kuehlwasser", "label" => "Kühlwasser"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Wasser Kommentar", "name" => "kuehlwasser_comment", "label" => "Kommentar Kühlwasser"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Wasser", "name" => "VE_Wasser", "label" => "VE Wasser"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Wasser Kommentar", "name" => "VE_Wasser_comment", "label" => "Kommentar VE Wasser"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Wasser", "name" => "geraete_wasser_abfluss", "label" => "Geräte mit Wasserabflüssen"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Wasser Kommentar", "name" => "geraete_wasser_abfluss_comment", "label" => "Kommentar Geräte mit Wasserabflüssen"],

// Abluft-Felder
    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Abluft", "name" => "punktabsaugung", "label" => "Punktabsaugung vorhanden"],
    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Abluft", "name" => "abluft_sicherheitsschrank", "label" => "Abluft Sicherheitsschrank vorhanden"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Abluft Kommentar", "name" => "abluft_sicherheitsschrank_comment", "label" => "Kommentar Abluft Sicherheitsschrank"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Abluft", "name" => "abluft_vakuumpumpe", "label" => "Abluft Vakuumpumpe vorhanden"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Abluft Kommentar", "name" => "abluft_vakuumpumpe_comment", "label" => "Kommentar Abluft Vakuumpumpe"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Abluft", "name" => "abrauchabzuege", "label" => "Abrauchabzüge/Veraschung"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Abluft Kommentar", "name" => "abrauchabzuege_comment", "label" => "Kommentar Abrauchabzüge"],

    ["type" => "yesno", "default_value" => "0", "kathegorie" => "Abluft", "name" => "sonderabluft", "label" => "Sonderabluft vorhanden"],
    ["type" => "textarea", "default_value" => "", "kathegorie" => "Abluft Kommentar", "name" => "sonderabluft_comment", "label" => "Kommentar Sonderabluft"],

    ["type" => "textarea", "default_value" => "", "kathegorie" => "Kommentar", "name" => "room_comment", "label" => "Allgemeiner Raum Kommentar"],
];

*/
