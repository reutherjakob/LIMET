 <?php
$formFields = [
    // HIDDEN
    ["type" => "texthidden", "name" => "roomname", "label" => "Raumname", "required" => true],
    ["type" => "texthidden", "name" => "username", "label" => "Username", "required" => true],
    ["type" => "texthidden", "kategorie" => "Raum", "name" => "roomID", "label" => "Room ID"],
    ["type" => "texthidden", "kategorie" => "Raum", "name" => "raumnr", "label" => "Raum Nr."],
    ["type" => "texthidden", "kategorie" => "Raum", "name" => "raumbereich_nutzer", "label" => "Raumbereich Nutzer"],
    ["type" => "texthidden", "kategorie" => "Raum", "name" => "nf", "label" => "Nutzfläche (NF)"],

    [
        "type" => "text_non_editable",
        "kategorie" => "Raum",
        "name" => "raumkategorieAbfrage",
        "label" => "Labor-Raumtyp lt. RUF"
    ],
    [
        "type" => "select",
        "options" => [0 => "Nein", 1 => "Ja"],
        "default_value" => 0,
        "kategorie" => "Raum",
        "name" => "verdunkelung",
        "label" => "Verdunkelung für die Verwendung von lichtempfindlichen Geräten jenseits der Standard-Jalousien erforderlich?",
        "optional_comment_label" => "Für welche Geräte?",
    ],
    [
        "type" => "select",
        "options" => [0 => "Nein", 1 => "Ja"],
        "default_value" => 0,
        "kategorie" => "Raum",
        "name" => "lichtsteuerung",
        "label" => "Lichtsteuerung jenseits von Ein/Aus erforderlich?",
        "optional_comment_label" => "Welche Steuerung wird benötigt?",
    ],
    [
        "type" => "select",
        "options" => [0 => "Nein", 1 => "Ja"],
        "default_value" => 0,
        "kategorie" => "Ausstattung",
        "name" => "hoehenverstellbare_labortische",
        "label" => "Sind höhenverstellbare Labortische für Labortätigkeiten zwingend notwendig?",
        "optional_comment_label" => "Wofür werden sie benötigt?",
    ],
    [
        "type" => "select",
        "options" => [
            "Nein" => "Nein",
            "Unterbau_Digestor" => "Ja, Unterbau unter Digestor reicht",
            "Eigener_Schrank" => "Ja, eigener Schrank im Raum nötig",
        ],
        "default_value" => "Nein",
        "kategorie" => "Ausstattung",
        "name" => "giftschrank",
        "label" => "Giftschrank bzw. Säure- und Laugenschrank im Raum erforderlich?",
    ],
    [
        "type" => "select",
        "options" => [0 => "Nein", 1 => "Ja"],
        "default_value" => 0,
        "kategorie" => "Raum",
        "name" => "akkreditierung",
        "label" => "Hat der Raum besondere Akkreditierungen?",
        "optional_comment_label" => "Welche?",
    ],
    [
        "type" => "select",
        "options" => [0 => "Nein", 1 => "Ja"],
        "default_value" => 0,
        "kategorie" => "HKLS",
        "name" => "ve_wasser",
        "label" => "Wird vollentsalztes (VE) / deionisiertes Wasser verwendet?",
        "optional_comment_label" => "Menge angeben",
    ]
];