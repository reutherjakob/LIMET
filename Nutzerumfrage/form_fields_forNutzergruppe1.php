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
        "options" => [
            0 => "Nein",
            1 => "Ja",
        ],
        "default_value" => 0,
        "kategorie" => "Raum",
        "name" => "doppelfluegeltuer",
        "label" => "Ist eine Doppelflügeltür (1,8m) zwingend erforderlich?",
        "info" => "Standard Breite lt. Raumtypen ist mind. 1m.",
    ],


    [
        "type" => "select",
        "options" => [
            0 => "Nein",
            1 => "Ja",
        ],
        "default_value" => 0,
        "kategorie" => "Raum",
        "name" => "vibrationsempfindlich_bodenstehend",
        "label" => "Sind Geräte in Verwendung, welche vibrationsempfindlich und bodenstehend sind?",

        "info" => "Geräte, die nicht auf einem entkoppleten Tisch platziert sind. Keine Wagen z.B. - diese sind auf einem Wägetisch.",
        'optional_comment_label' => "Gerät spezifizieren."
    ],


    [
        "type" => "select",
        "options" => [
            0 => "Nein",
            1 => "Ja",
        ],
        "default_value" => 0,
        "kategorie" => "Raum",
        "name" => "explosionsschutz",
        "label" => "Ist Explosionsschutz für den gesamten Raum erforderlich?",
        "info" => "",
        //  "Zone 0 – Explosionsgefährliche Atmosphäre ständig/langzeitig vorhanden; Zone 1 – Gelegentliches Auftreten im Normalbetrieb; Zone 2 – Seltenes/kurzzeitiges Auftreten (nur bei Störungen)",
    ],


    [
        "type" => "select",
        "kategorie" => "Ausstattung",
        "name" => "abluftwaescher",
        "options" =>
            [0 => "0",
                1 => "1",
                2 => "2",
                3 => "3",
                4 => "4",
                5 => "5",
                6 => "6"],
        "label" => "Werden Abzüge mit Säurewäscher benötigt?",
        "default_value" => 0,
        "optional_comment_label" => "",
    ],


    [
        "type" => "multiselect",
        "kategorie" => "Gas",
        "name" => "spezialgas",
        "label" => "Welche dezentralen Sondergase sind erfolderlich? <br> (Mehrfachauswahl möglich)",
        "options" => [
            "Argon" => "Argon",
            "CO2" => "CO2",
            "H2" => "H2",
            "He" => "He",
            "Acetylen" => "Acetylen",
            "Ammoniak" => "Ammoniak",
            "Synth_Luft" => "Synthetische Luft",
            "O2" => "O2",
            // "unbekannt" => "unbekannt",
        ],
        "info" => "Zentrale Stickstoff- und Druckluftversorgung je nach Raumtyp.",
        "optional_comment_label" => "",

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
        ], "default_value" => 0,
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
        "type" => "select",
        "kategorie" => "Abluft",
        "name" => "raumabluft_besonders",
        "label" => "Besondere Anforderungen an die Raumabluft?",
        "options" => ["Nein" => "Nein", "Staubfilter" => "Staubfilter", "HEPA" => "HEPA Filter", "Veraschung" => "Veraschung"],
        "default_value" => "Nein"
    ],

    [
        "type" => "select",
        "kategorie" => "Abluft",
        "name" => "raumzuluft_besonders",
        "label" => "Filterung der Zuluft erforderlich?",
        "options" => ["Nein" => "Nein", "HEPA" => "HEPA Filter"],
        "default_value" => "Nein"
    ],

    [
        "type" => "select",
        "options" => [
            0 => "Nein",
            1 => "Ja",
        ],
        "default_value" => 0,
        "kategorie" => "HKLS",
        "name" => "nutzwasser",
        "label" => "Ist Nutzwasser erforderlich?",
        "info" => "(Brunnen-)Wasser, welches nicht die Qualität von Trinkwasser aufweist.",

        "optional_comment_label" => "Wofür wird dieses genutzt?",
    ],

    [
        "type" => "select",
        "options" => [
            0 => "Nein",
            1 => "Ja",
        ],
        "default_value" => 0,
        "kategorie" => "HKLS",
        "name" => "kuehlwasser",
        "label" => "Ist Kühlwasser für Geräte erforderlich?",
        //"info" => "",
        "optional_comment_label" => "Falls bekannt: Menge Spitzenentnahme, Stundenverbrauch angeben."
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
            "Anderes" => "Anderes",

        ],
        "info" => "Wasser, welches in Sedimentationsanlage oder Neutralistionsanlage geleitet werden muss.",
        "optional_comment_label" => "Wenn `Anderes` ausgewählt wurde, bitte spezifizieren"
    ],

    [
        "type" => "select",
        "options" => [
            0 => "Nein",
            1 => "Ja",
        ],
        "default_value" => 0,
        "kategorie" => "HKLS",
        "name" => "raumtemp",

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
        "default_value" => 0,
        "kategorie" => "HKLS",
        "name" => "luftf",
        "label" => "Besondere Anforderungen an die Luftfeuchtigkeit?",
        "info" => "",
        "optional_comment_label" => "Soll und Schwankung angeben (x% +-y%)"
    ],

    [
        "type" => "textarea",
        "kategorie" => "",
        "name" => "allgemeiner_kommentar",
        "label" => "Kommentar zum Raum",
        "default_value" => "",
    ],

];