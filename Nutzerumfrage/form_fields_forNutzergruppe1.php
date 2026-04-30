<?php
$formFields = [
    // =========================================================
    // HIDDEN
    // =========================================================
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
        "options" => [
            0 => "Nein",
            1 => "Ja",
        ],
        "kategorie" => "Raum",
        "name" => "doppelfluegeltuer",
        "label" => "Doppelflügeltür (1,8m) erforderlich?",
        "info" => "Standard Breite ist mind. 1m.",
        'default_value' => 0,
    ],

    [
        "type" => "select",
        "options" => [
            0 => "Nein",
            1 => "Ja",
        ],
        "kategorie" => "Raum",
        "name" => "vibrationsempfindlich_bodenstehend",
        "label" => "Sind Geräte in Verwendung, welche vibrationsempfindlich und bodenstehend sind?",
        "default_value" => 0,
        "info" => "Nicht auf einem entkoppleten Tisch platziert.",
        'optional_comment_label' => "Bitte Gerät spezifizieren."
    ],

    [
        "type" => "select",
        "options" => [
            0 => "Nein",
            1 => "Ja",
        ],
        "kategorie" => "Raum",
        "name" => "explosionsschutz",
        "label" => "Ist Explosionsschutz im gesamten Raum erforderlich?",
        # "options" => ["Nein" => "Nein", "Zone 0" => "Zone 0", "Zone 1" => "Zone 1", "Zone 2" => "Zone 2",],
        "default_value" => "0",
        "info" => "",
        //  "Zone 0 – Explosionsgefährliche Atmosphäre ständig/langzeitig vorhanden; Zone 1 – Gelegentliches Auftreten im Normalbetrieb; Zone 2 – Seltenes/kurzzeitiges Auftreten (nur bei Störungen)",
        //'optional_comment_label' => "Falls bekannt: Mit welcher Abluftmenge?"
    ],


    [
        "type" => "select",
        "kategorie" => "Ausstattung",
        "name" => "abluftwaescher",
        "options" => [0 => "0", 1 => "1", 2 => "2", 3 => "3", 4 => "4"],
        "label" => "Werden Abzüge mit Säurewäscher benötigt?",
        "default_value" => 0,
        // "optional_comment_label" => "",
    ],

    [
        "type" => "multiselect",
        "kategorie" => "Gas",
        "name" => "     ",
        "label" => "Welche dezentralen Sondergase sind erfolderlich? <br><small>(Mehrfachauswahl möglich)</small>",
        "default_value" => 0,
        "options" => [
            "Argon" => "Ar",
            "O2" => "O2",
            "CO2" => "CO2",
            "H2" => "H2",
            "He" => "He",
            "Acetylen" => "Acetylen",
            "Ammoniak" => "Ammoniak",
            "Synth_Luft" => "Synth. Luft",
            "Anderes" => "Anderes",

        ],
        "info" => "Zentrale Stickstoff- und Druckluftversorgung ist je nach Raumtyp Standard.",
        "optional_comment_label" => "Anderes spezifizieren.",

    ],

    [
        "type" => "select",
        "options" => [
            0 => "Nein",
            1 => "Ja",
        ],
        "default_value" => 0,
        "kategorie" => "HKLS",
        "name" => "DL",
        "label" => "Druckluft erforderlich? "
    ],

    [
        "type" => "select",
        "options" => [
            0 => "Nein",
            1 => "Ja",
        ],
        "default_value" => 0,
        "kategorie" => "HKLS",
        "name" => "N2",
        "label" => "Stickstoff erforderlich?"
    ],

    [
        "type" => "select",
        "options" => [
            0 => "Nein",
            1 => "Ja",
        ],
        "default_value" => 0,
        "kategorie" => "HKLS",
        "name" => "Vakuum",
        "label" => "Vakuum Versorgung erforderlich?"

    ],

    [
        "type" => "multiselect",
        "kategorie" => "Abluft",
        "name" => "raumabluft_besonders",
        "label" => "Besondere Anforderungen an die Raumabluft?<br><small>(Mehrfachauswahl möglich)</small>",
        "options" => [0 => "Nein", "Staubfilter" => "Staubfilter", "HEPA" => "HEPA Filter", "Veraschung" => "Veraschung",],
        "default_value" => 0
    ],

    [
        "type" => "select",
        "kategorie" => "Abluft",
        "name" => "raumzuluft_besonders",
        "label" => "Filterung der Zuluft erforderlich?",
        "options" => [0 => "Nein", "HEPA" => "HEPA Filter"],
        "default_value" => 0
    ],


    [
        "type" => "select",
        "options" => [
            0 => "Nein",
            1 => "Ja",
        ],
        "kategorie" => "HKLS",
        "name" => "nutzwasser",
        "label" => "Ist Nutzwasser erforderlich?",
        "info" => "(Brunnen-)Wasser, welches nicht die Qualität von Trinkwasser aufweist.",
        'default_value' => 0,
        "optional_comment_label" => "Wofür wird dieses genutzt?",
    ],

    [
        "type" => "select",
        "options" => [
            0 => "Nein",
            1 => "Ja",
        ],
        "kategorie" => "HKLS",
        "name" => "kuehlwasser",
        'default_value' => 0,
        "label" => "Ist Kühlwasser für Geräte erforderlich?",
        //"info" => "",
        "optional_comment_label" => "Bitte Gerät spezifizieren.",
    ],

    [
        "type" => "select",
        "kategorie" => "HKLS",
        "name" => "spezialabwasser",
        "label" => "Abwasser vorhanden welches einer speziellen Behandlung bedarf?",
        "default_value" => "Nein",
        "options" => [
            "Nein" => "Nein",
            "Sedimentationsanlage" => "Sedimentationsanlage",
            "Neutralistionsanlage" => "Neutralistionsanlage",
            "Chemische_Behandlung" => "Chemische Behandlung",
        ],
        "info" => "Wasser, welches in Sedimentationsanlage oder Neutralistionsanlage geleitet werden muss.",
        "optional_comment_label" => "Wenn `Chemische Behandlung` ausgewählt wurde, bitte spezifizieren:"
    ],

    [   // was mit klimakammern ?
        "type" => "select",
        "options" => [
            0 => "Nein",
            1 => "Ja",
        ],
        "kategorie" => "HKLS",
        "name" => "raumtemp",
        'default_value' => 0,
        "label" => "Besondere Anforderungen an die Raumtemperatur?",
        "info" => "Solltemperatur Sommer ist 24°C +-1°C. Solltemperatur Winter ist im Raumtyp spezifiziert.",
        "optional_comment_label" => "Solltemperatur und Schwankung angeben (x°C +-y°C)"
    ],

    [
        "type" => "select",
        "options" => [
            0 => "Nein",
            1 => "Ja",
        ],
        "kategorie" => "HKLS",
        "name" => "luftf",
        'default_value' => 0,
        "label" => "Besondere Anforderungen an die Luftfeuchtigkeit?",
        "info" => "Derzeitiger Standard ist je nach Raumtype definiert. Für Labore im Bereich 40-60% (+-5%).",
        "optional_comment_label" => "Soll und Schwankung angeben (x% +-y%)"
    ]
];

