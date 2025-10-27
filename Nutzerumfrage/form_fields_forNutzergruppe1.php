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
        "0" => "keine Anforderung", "1" => "BSL-1", "2" => "BSL-2", "3" => "BSL-3"], "default_value" => "0"],

    ["type" => "text", "kathegorie" => "HT", "name" => "temperatur", "label" => "Besondere Anforderungen an die Raumtemperatur? <br> (z.B min./max.°C, max. Temperaturschwankung °C)", "default_value" => "Nein"],

    ["type" => "select", "kathegorie" => "HT", "name" => "temperatur_min", "label" => "Besondere Anforderungen an die Raumtemperatur? <br> (min.°C)", "default_value" => ["Lager" => 15, "Labor" => 22], "options" => [14 => "<15", 15 => "15", 16 => "16", 17 => "17", 18 => "18", 19 => "19", 20 => "20", 21 => "21", 22 => "22", 23 => "22<"]],
    ["type" => "select", "kathegorie" => "HT", "name" => "temperatur_max", "label" => "Besondere Anforderungen an die Raumtemperatur? <br> (max.°C)", "default_value" => ["Lager" => 27, "Labor" => 26], "options" => [19 => "<22", 22 => "22", 23 => "23", 24 => "24", 25 => "25", 26 => "26", 27 => "26<"],],
    ["type" => "yesno", "kathegorie" => "HT", "name" => "temperatur_schwankungen_relevant", "label" => "Besondere Anforderungen an die maximal zulässigen Temperaturschwankungen? "],

    ["type" => "text", "kathegorie" => "HT", "name" => "luftfeuchtigkeit_besonders", "label" => "Besondere Anforderungen an die Luftfeuchtigkeit (min./max.)? ", "default_value" => "Keine"],

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

    ["type" => "yesno", "kathegorie" => "HKLS", "name" => "VE_Wasser", "label" => "Verwendung von vollentsalztem/deionisiertem Wasser?" , "default_value" => 0, "options"=> [0=> "Nein", 1=> "Ja, aus der Leitung", 2=> "Ja, über zusätliche dezentrale Aufbereitung."]],
    ["type" => "yesno", "kathegorie" => "HKLS", "name" => "kuehlwasser", "label" => "Sind Geräte in Verwendung welche mittels Kühlwasser gekühlt werden könnten?"],
    ["type" => "yesno", "kathegorie" => "HKLS", "name" => "abwasser_sonderfall", "label" => "Ist Abwasser vorhanden welches nicht in die Kanalisation geleitet werden darf?"]
];

