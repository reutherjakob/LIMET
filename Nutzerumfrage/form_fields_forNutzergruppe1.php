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
    ["type" => "text_non_editable",
        "kategorie" => "Raum",
        "name" => "raumkategorieAbfrage",
        "label" => "Labor-Raumtyp",
        "info" => "Im RUF festgelegt."],

    ["type" => "yesno", "kategorie" => "Raum", "name" => "doppelfluegeltuer",
        "label" => "Doppelflügeltür (1,8m) erforderlich?",
        "info" => "Standard Breite ist 1m.",
        'default_value' => 'Nein',
    ],


    ["type" => "yesno", "kategorie" => "Raum", "name" => "verbindungsgang",
        "label" => "Ist ein Verbindungsgang zu angrenzenden Labor erforderlich?",
        "default_value" => "Nein",
        "optional_comment_label" => "Bitte Nebenraum(e) spezifizieren."
    ],

    ["type" => "yesno", "kategorie" => "Raum", "name" => "vibrationsempfindlich_bodenstehend",
        "label" => "Geräte in Verwendung welche vibrationsempfindlich und bodenstehend sind?",
        "default" => "Nein",
        # "info" => "",
        'optional_comment_label' => "Optional."],

    ["type" => "yesno",
        "kategorie" => "Raum",
        "name" => "explosionsschutz",
        "label" => "Explosionsschutz im Raum erforderlich?",
        "options" => ["Nein" => "Nein", "Zone 0" => "Zone 0", "Zone 1" => "Zone 1", "Zone 2" => "Zone 2",],
        "default_value" => "Nein",
        "info" => "",
        //  "Zone 0 – Explosionsgefährliche Atmosphäre ständig/langzeitig vorhanden; Zone 1 – Gelegentliches Auftreten im Normalbetrieb; Zone 2 – Seltenes/kurzzeitiges Auftreten (nur bei Störungen)",
        //'optional_comment_label' => "Falls bekannt: Mit welcher Abluftmenge?"
    ],


    ["type" => "select", "kategorie" => "Ausstattung", "name" => "abluftwaescher",
        "options" => [0 => "Nein", 1 => "1", 2 => "2"],
        "label" => "Sind Abzüge mit Abluftwäscher / Säurewäscher in Verwendung?",
        "default_value" => 0
    ],


    ["type" => "multiselect", "kategorie" => "Gas", "name" => "spezialgas",
        "label" => "Welche dezentralen Sondergase sind erfolderlich? ",
        "options" => [
            "Argon" => "Argon",
            "CO2" => "CO2",
            "H2" => "H2",
            "He" => "He",
            "Acetylen" => "Acetylen",
            "Ammoniak" => "Ammoniak",
            "Synth_Luft" => "Synthetische Luft",
        ],
        "info" => "Zentrale Stickstoff- und Druckluftversorgung sind in best. Raumtypen standardmäßig vorgesehen und folglich keine dezentrale Sondergase."
    ],

    ["type" => "select", "kategorie" => "ET", "name" => "starkstromanschluss_anzahl",
        "label" => "Sind Geräte in Verwendung welche einen Starkstromanschluss benötigen?",
        "options" => [0 => "Nein", 1 => "1", 2 => "2", 3 => "3", 4 => "4", 5 => "5"],
        "default_value" => 0,
        "optional_comment_label" => "Falls bekannt: Welche CEE, Stromstärke, Leistung?",
        //"info" => ""
    ],
    ["type" => "select", "kategorie" => "Abluft", "name" => "raumabluft_besonders",
        "label" => "Besondere Anforderungen an die Raumabluft?",
        "options" => ["Nein" => "Nein", "HEPA" => "HEPA Filter",],
        "default_value" => "Nein"
    ],
    ["type" => "select", "kategorie" => "Abluft", "name" => "raumzuluft_besonders",
        "label" => "Besondere Anforderungen an die Zuluft?",
        "options" => ["Nein" => "Nein", "HEPA" => "HEPA Filter",],
        "info" => "Iodfilter für Raumtype Messraum, radiochemisch vorgesehen.",
        "default_value" => "Nein"
    ],

    ["type" => "select", "kategorie" => "Abluft", "name" => "sonderabluft",
        "label" => "Ist eine Sonderabluft im Raum?",
        "options" => [
            "Nein" => "Nein",
            "Veraschung" => "Veraschung",
            "Sonstige" => "Sonstige",
            "Sonstige_und_Veraschung" => "Veraschung & Sonstige",
        ],
        "default_value" => "Nein",
        //"optional_comment_label" => "Falls bekannt: Welche Luftmenge?:",
    ],
    ["type" => "yesno",
        "kategorie" => "HKLS",
        "name" => "nutzwasser",
        "label" => "Ist Nutzwasser erforderlich?",
        "info" => "Verbrauchswasser, welches nicht die Qualität von Trinkwasser aufweist",
        "default_value" => "Nein",
        "optional_comment_label" => "Optional.",
    ],
    ["type" => "yesno", "kategorie" => "HKLS", "name" => "spezialabwasser",
        "label" => "Abwasser vorhanden welches nicht in die Kanalisation geleitet werden darf?",
        "default_value" => "Nein",
        "info" => "Wasser, welches in Sedimentationsanlage oder Neutralistionsanlage geleitet werden muss.",
        "optional_comment_label" => "Bitte Neutralistaion- oder Sedimentationsanlage spezifizieren"
    ],

    //["type" => "text", "kategorie" => "HKLS", "name" => "kaltwasser_stundenverbrauch",
    //    "label" => "Geschätzter Kaltwasser Stundenverbrauch [l/h]?",
    //    "info" => "Nur gefragt bei Räumen, die lt. Type Kaltwasser haben.",
    //    "default_value" => ""],

    //["type" => "text", "kategorie" => "HKLS", "name" => "kaltwasser_spitzenverbrauch",
    //    "label" => "Geschätzte Kaltwasser Spitzenentnahme [l/s]",
    //    "info" => "Nur gefragt bei Räumen, die lt. Type Kaltwasser haben.",
    //    "default_value" => ""],


    ["type" => "number", "kategorie" => "HKLS", "name" => "kaltwasser_stundenverbrauch",
        "label" => "Geschätzter Kaltwasser Stundenverbrauch [l/h]?",
        "info" => "Nur gefragt bei Räumen, die lt. Type Kaltwasser haben.",
        "default_value" => ""],

    ["type" => "number", "kategorie" => "HKLS", "name" => "kaltwasser_spitzenverbrauch",
        "label" => "Geschätzte Kaltwasser Spitzenentnahme [l/s]",
        "info" => "Nur gefragt bei Räumen, die lt. Type Kaltwasser haben.",
        "default_value" => ""],




    //["type" => "select", "kategorie" => "Raum", "name" => "mitarbeiter_anzahl",
    //    "label" => "Anzahl der im Raum tätigen MitarbeiterInnen?",
    //    "options" => [0 => "0", 1 => "1", 2 => "2", 3 => "3", 4 => "4", 5 => "5", 6 => "6", 7 => "7", 8 => "8", 9 => "9"]],


    //  ["type" => "yesno", "kategorie" => "Raum", "name" => "oberflaechenbestaendigkeit",
    //      "label" => "Erhöhte Anforderungen an die Oberflächenbeständigkeit von Arbeitsflächen?",
    //      "info" => "z.B. wegen dem Einsatz von Säuren und Laugen.",
    //      //'optional_comment_label' => ""
    //  ],

    // ["type" => "yesno", "kategorie" => "Raum", "name" => "verdunkelung",
    //     "label" => "Verdunkelung für die Verwendung von lichtempfindlichen Geräten erforderlich?",
    //     "info" => "Ja entspricht hier einer vollverdunkelung über den Standard Sonnenschutz hinaus."
    // ],

    // ["type" => "yesno", "kategorie" => "Raum", "name" => "schallschutzanforderung",
    //     "label" => "Ist aufgrund der im Raum verwendeten Geräte ein erhöhter Lärmschutz erforderlich?",
    //     // 'optional_comment_label' => ""
    // ],

    // ["type" => "yesno", "kategorie" => "Raum", "name" => "vibrationsempfindliche_geraete",
    //     "label" => "Sind Geräte in Verwendung welche vibrationsempfindlich sind und auf einem entkoppelten Tisch stehen?",
    //     "info" => "z.B. Präzisionswaagen, Rasterelektronenmikroskope"],

    //  ["type" => "yesno", "kategorie" => "Raum", "name" => "vibrationen_abgebend",
    //      "label" => "Geräte in Verwendung welche Vibrationen an das Gebäude abgeben?",
    //      "info" => "z.B. Siebmaschinen"],


    //["type" => "select", "kategorie" => "Raum", "name" => "bsl_level",
    //    "label" => "Biosafety Level?",
    //    "options" => ["0" => "keine Anforderung", "BSL1" => "BSL1", "BSL2" => "BSL2", "BSL3" => "BSL3"],
    //    "default_value" => "0"],

    // =========================================================
    // LABOR-AUSSTATTUNG
    //  ["type" => "yesno", "kategorie" => "Ausstattung", "name" => "giftschrank",
    //      "label" => "Ist ein Gift- oder Säure- oder Laugenschrank erforderlich?",
    //      "default_value" => "Nein",
    //      "info" => "Sicherheitsschränke",
    //      'optional_comment_label' => "Falls bekannt: Mit welcher Abluftmenge?"
    //  ],
    //  ["type" => "yesno", "kategorie" => "Ausstattung", "name" => "schrank_absaugung",
    //      "label" => "Ist ein Schrank mit Absaugung erforderlich?",
    //      "info" => "Beispiel: Lagerung extrem geruchsintensiver Stoffe, ",
    //      "default_value" => "Nein",
    //      'optional_comment_label' => "Falls bekannt: Mit welcher Abluftmenge und Anforderungen an Leitungsnetz?"
    //  ],

    //  ["type" => "text", "kategorie" => "Ausstattung", "name" => "hoehenverstellbare_labortische",
    //      "label" => "Höhenverstellbare Labortische zwingend notwendig für Labortätigkeiten?",
    //      "default_value" => "Nein",
    //      "info" => "FallsJa, wofür? "],

    //  ["type" => "yesno", "kategorie" => "Ausstattung", "name" => "zusaetzliche_spuele",
    //      "label" => "Ist eine zusätzliche Spüle notwendig?",
    //      "default_value" => "Nein",
    //      "info" => "Standard: 3/6-Achser = 1 Spüle, 9-Achser = 2 Spülen"],

    // =========================================================
    // GAS
    // =========================================================

    //["type" => "yesno", "kategorie" => "Gas", "name" => "stickstoff_gas",
    //    "label" => "Ist flüssiger Stickstoff erforderlich?",
    //    "default_value" => "Nein",
    //    "info" => "Im `Messraum, radiochemisch` ist dies Standard"
    //],

    // ["type" => "yesno", "kategorie" => "Gas", "name" => "gaswarnanlage",
    //     "label" => "Ist Gaswarnanlage erforderlich?",
    //     "default_value" => "Nein",
    //     "optional_comment_label" => "Für welche Gasart(en)?",
    // ],

    // =========================================================
    // ELEKTRO / ET
    // =========================================================

    //["type" => "select", "kategorie" => "ET", "name" => "anschlussleistung_hoch",
    //    "label" => "Sind Geräte mit besonders hohen Anschlussleistungen in Verwendung (>1 kW)?",
    //    "options" => [0 => "Nein", 1 => "1", 2 => "2", 3 => "3", 4 => "4", 5 => "5", 6 => "6"],
    //    "default_value" => 0,
    //    "info" => "Anzahl der Geräte."],


    // ["type" => "yesno", "kategorie" => "ET", "name" => "usv_geraete",
    //     "default_value" => "Nein",
    //     "label" => "Ist eine Unterbrechungsfreie Stromversorgung (USV) für empfindliche Geräte erforderlich?",
    //     //"info" => "",
    //     "optional_comment_label" => "Wie viele Geräte bzw. Wie viele Steckdosen? Falls bekannt: Mit welcher Leistung? "
    // ],
//
    // ["type" => "text", "kategorie" => "ET", "name" => "erhoehte_waermeabgabe",
    //     "label" => "Erhöhte Wärmeabgabe von Geräten im Raum?",
    //     "info" => "Über Standardwärmelast lt. Raumtype  hinausgehend?", // könte hier in die Info oder Frage vllt den Default value laden?
    //     "default_value" => "Nein"],
//
    // =========================================================
    // HT / KLIMA
    // =========================================================
    //["type" => "select", "kategorie" => "HT", "name" => "temperatur_min",
    //    "label" => "Besondere Anforderungen an die minimale Raumtemperatur (im Winter) [°C]? ",
    //    "default_value" => ["Lager" => 15, "Labor" => 22],
    //    "options" => [14 => "<15", 15 => "15", 16 => "16", 17 => "17", 18 => "18", 19 => "19", 20 => "20", 21 => "21", 22 => "22", 23 => "22<"]],

    //["type" => "select", "kategorie" => "HT", "name" => "temperatur_max",
    //    "label" => "Besondere Anforderungen an die maximale Raumtemperatur (im Sommer)[°C]?",
    //    "default_value" => ["Lager" => 27, "Labor" => 26],
    //    "options" => [19 => "<22", 22 => "22", 23 => "23", 24 => "24", 25 => "25", 26 => "26", 27 => "26<"]],

    //["type" => "text", "kategorie" => "HT", "name" => "temperatur_gradient",
    //    "label" => "Erhöhte Anforderungen genüber der regulären Temperaturschwankung der Raumkonditionierung? [±K]",
    //    "info" => " Wie viel darf die Temperatur kurzzeitig außererhalb oben angegebenen min/max Werte sein? Als akzeptiertbare regelungsbedingte Abweichung wurden ±2K definiert.",
    //    "default_value" => "Nein"],

    //["type" => "text", "kategorie" => "HT", "name" => "temperatur_hysterese",
    //    "info" => "Wie viel darf die Temperatur über 24h schwanken?",
    //    "label" => "Besondere Anforderungen an die tagesbezogenen Temeratur Hysterese? [±K/Tag]",
    //    "default_value" => ""],

    //["type" => "text", "kategorie" => "HT", "name" => "luftfeuchtigkeit_besonders",
    //    "label" => "Besondere Anforderungen an die Luftfeuchtigkeit?",
    //    "info" => "min. im Winter / max. im Sommer, z.B. min 40% / max 60%  oder Keine",
    //    "default_value" => "min 40% / max 60%"],

    //["type" => "text", "kategorie" => "HT", "name" => "luftfeuchtigkeit_enge_toleranz",
    //    "label" => "Ist eine geringere Toleranz der Luftfeuchtigkeit als ±5% erforderlich?",
    //    // "info" => "",
    //    "default_value" => "±5%"],


    // ["type" => "select", "kategorie" => "HT", "name" => "druckregelung_besonders",
    //     "label" => "Besondere Anforderungen an die Druckregelung der Lüftung?",
    //     "options" => [1 => "Keine", 2 => "Überdruck", 3 => "Unterdruck"]],

    // // =========================================================
    // ABLUFT
    // =========================================================


    //  ["type" => "select", "kategorie" => "Abluft", "name" => "filteranlagen_staub",
    //      "label" => "Besondere Filteranlage für die Zu- und Abluft (Staubklasse)? <br> @Schermann: Brauchts hier noch mehr Antwortoptionen? ",
    //      "options" => [
    //          "Nein" => "Nein",
    //          "Zone_20" => "Zone 20",
    //          "Zone_21" => "Zone 21",
    //          "Zone_22" => "Zone 22",
    //      ],
    //      "info" => "Zone 20 – Staub-Explosionsatmosphäre ständig vorhanden; Zone 21 – gelegentliches Auftreten; Zone 22 – seltenes/kurzzeitiges Auftreten.",
    //      "default_value" => "Nein"],

    // =========================================================
    // HKLS / WASSER
    // =========================================================
    //  ["type" => "select", "kategorie" => "HKLS", "name" => "VE_Wasser",
    //      "label" => "Verwendung von vollentsalztem (VE) / deionisiertem Wasser?",
    //      "default_value" => "0",
    //      "options" => [0 => "Nein", 1 => "Aus der Leitung", 2 => "Über zusätzliche dezentrale Aufbereitung"],
    //      "optional_comment_label" => "Falls bekannt: Spitzenentnahme & Stundenverbrauch (l/min & l/Tag):",
    //  ],


    //  ["type" => "yesno", "kategorie" => "HKLS", "name" => "warmwasser_erhoehter_bedarf",
    //      "label" => "Erhöhter Warmwasserbedarf?",
    //      "info" => "Bedarf, der nicht über (Untertisch-)Durchlauferhitzer gedeckt werden kann? Dieser liefert ca. 5L/min (bei 400V/16A, 11W).",
    //      "default_value" => "Nein",
    //      // "optional_comment_label" =>  "Anm."
    //  ],

    //  ["type" => "yesno", "kategorie" => "HKLS", "name" => "kuehlwasser",
    //      "default_value" => "Nein",
    //      "label" => "Sind Geräte in Verwendung welche mittels Kühlwasser gekühlt werden?",
    //      //"info" => "",
    //      "optional_comment_label" => "Falls bekannt: Menge Spitzenentnahme, Stundenverbrauch angeben."
    //  ],


//
    //  ["type" => "yesno", "kategorie" => "HKLS", "name" => "bodenablauf",
    //      "label" => "Ist ein Bodenablauf (Gully) zwingend erforderlich?",
    //      "info" => "Die Hygiene ist bei Nichtnutzung sehr schwierig."
    //  ],

    // =========================================================
    // RAUM-BESONDERHEITEN
    // =========================================================
//   ["type" => "yesno", "kategorie" => "Raum", "name" => "raum_begasbar",
//       "label" => "Muss der Raum begasbar sein?"],

//   ["type" => "yesno", "kategorie" => "Raum", "name" => "zwischendecke",
//       "label" => "Ist eine Zwischendecke (geschlossene Decke) notwendig?"],


//   ["type" => "text", "kategorie" => "Raum", "name" => "lichtsteuerung",
//       "label" => "Besondere Lichtsteuerung notwendig?"],


//   ["type" => "yesno", "kategorie" => "HKLS", "name" => "DL",
//       "label" => "Druckluft notwendig? "],
//   ["type" => "yesno", "kategorie" => "HKLS", "name" => "N2",
//       "label" => "Stickstoff notwendig?"],


];