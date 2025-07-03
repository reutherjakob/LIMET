<?php
$parameterFieldConfig = [
    // Geometrie (1)
    'Geometrie|Länge|Einheit' => ["mm", "cm", "m", ""],
    'Geometrie|Breite|Einheit' => ["mm", "cm", "m", ""],
    'Geometrie|Höhe|Einheit' => ["mm", "cm", "m", ""],
    'Geometrie|Tiefe|Einheit' => ["mm", "cm", "m", ""],
    'Geometrie|Gewicht|Einheit' => ["g", "kg", "t", ""],
    'Geometrie|Kammerbreite|Einheit' => ["mm", "cm", "m", ""],
    'Geometrie|Kammerhöhe|Einheit' => ["mm", "cm", "m", ""],
    'Geometrie|Kammertiefe|Einheit' => ["mm", "cm", "m", ""],
    'Geometrie|Biegemoment|Einheit' => ["Nm", "kNm", ""],
    'Geometrie|Armlänge 1|Einheit' => ["cm", "m", ""],
    'Geometrie|Armlänge 2|Einheit' => ["cm", "m", ""],

    // Elektro (2)
    'Elektro|Stromstärke|Einheit' => ["A", "mA", "kA", ""],
    'Elektro|Spannung|Einheit' => ["V", "kV", ""],
    'Elektro|Nennleistung|Einheit' => ["W", "kW", "VA", "kVA", ""],
    'Elektro|PA|Einheit' => ["Ja", "Stk"],
    'Elektro|USV|Wert' => ["Ja", "Nein", ""],
    'Elektro|Netzwerk|Wert' => ["Ja", "Nein", "optional", ""],
    'Elektro|RJ45 Ports|Wert' => ["Ja","0", "1", "2", "3", "4", "5", "6", "7", "8", "", "Stk"],
    'Elektro|Steckdosen MVE-DVE AV|Wert' => ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", ""],
    'Elektro|Steckdosen MVE-DVE SV|Wert' => ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", ""],
    'Elektro|Steckdosen MVE-DVE ZSV|Wert' => ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", ""],
    'Elektro|Steckdosen_MVE_DVE_Roentgen|Wert' => ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", ""],
    'Elektro|Steckdosen_MVE_DVE_Laser|Wert' => ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", ""],
    'Elektro|Steckdosen MVE-DVE AV|Einheit' => ["Stk", ""],
    'Elektro|Steckdosen MVE-DVE SV|Einheit' => ["Stk", ""],
    'Elektro|Steckdosen MVE-DVE ZSV|Einheit' => ["Stk", ""],
    'Elektro|Steckdosen_MVE_DVE_Roentgen|Einheit' => ["Stk", ""],
    'Elektro|Steckdosen_MVE_DVE_Laser|Einheit' => ["Stk", ""],
    'Elektro|Innenwiderstand_Zuleitung|Einheit' => ["Ω", "mΩ", ""],
    'Elektro|Netzart|Wert' => ["", "AV", "SV", "ZSV", "USV", "AV/SV", "SV/ZSV", "ZSV/USV", "Akku"],
    'Elektro|Netzart|Einheit' => ["", "/Akku"],
    'Elektro|Frequenzbereich|Einheit' => ["Hz", "kHz", "MHz", ""],
    'Elektro|Explosionsschutzzone|Wert' => ["0", "1", "2", "Keine", ""],
    'Elektro|IT-Anbindung erforderlich|Wert' => ["Ja", "Nein"],
    'Elektro|Reinraummonitoring|Einheit' => ["Ja", "Nein"],
    'Elektro|Direktanschluss|Einheit' => ["Ja", "Nein"],
    'Elektro|Video-Routing|Wert' => ["Ja", "Nein", ""],
    'Elektro|Kreise AV|Wert' => ["1", "2", "3", "4", ""],
    'Elektro|Kreise SV|Wert' => ["1", "2", "3", "4", ""],
    'Elektro|Kreise ZSV|Wert' => ["1", "2", "3", "4", ""],

    // HKLS (3)
    'HKLS|Abwärme|Einheit' => ["W", "kW", ""],
    'HKLS|Warmwasser_Strom|Einheit' => ["l/min", "m³/h", ""],
    'HKLS|Kaltwasser_Strom|Einheit' => ["l/min", "m³/h", ""],
    'HKLS|Voll_entsalztes Wasser_Strom|Einheit' => ["l/min", "m³/h", ""],
    'HKLS|Abluftstrom|Einheit' => ["m³/h", ""],
    'HKLS|Abflussstrom|Einheit' => ["l/min", "m³/h", ""],
    'HKLS|Abflussdurchmesser|Einheit' => ["mm", "cm", "Zoll", '"', ""],
    'HKLS|Ablufttemperatur|Einheit' => ["°C", "K", ""],
    'HKLS|Kaltwasser_Anschluss|Einheit' => ["DN", "Zoll", '"', ""],
    'HKLS|Warmwasser_Anschluss|Einheit' => ["DN", "Zoll", '"', ""],
    'HKLS|Abluftdurchmesser|Einheit' => ["mm", "cm", "Zoll", '"', ""],
    'HKLS|Zuluftstrom|Einheit' => ["m³/h", ""],
    'HKLS|Fortluftstrom|Einheit' => ["m³/h",   ""],
    'HKLS|Druckluftanschluss|Einheit' => ["DN", "Zoll", '"', ""],
    'HKLS|Druckluft_Strom|Einheit' => ["l/min", "m³/h", ""],
    'HKLS|Druckluft_Druck|Einheit' => ["bar", "mbar", ""],
    'HKLS|Druckluftqualität|Wert' => ["ISO-Klasse", ""],
    'HKLS|WFI_Strom|Einheit' => ["l/min", "m³/h", ""],
    'HKLS|Voll_entsalztes Wasser_Temp_Max|Einheit' => ["°C", "K", ""],
    'HKLS|Warmwasser_Temp_Max|Einheit' => ["°C", "K", ""],
    'HKLS|Kaltwasser_Temp_ Max|Einheit' => ["°C", "K", ""],
    'HKLS|Kühlwasser_Anschluss|Einheit' => ["DN", "Zoll", '"', ""],
    'HKLS|Kühlwasser_Strom|Einheit' => ["l/min", "m³/h", ""],
    'HKLS|Sterilisierdampf_Anschluss|Einheit' => ["DN", "Zoll", '"', ""],
    'HKLS|Sterilisierdampf_Strom|Einheit' => ["kg/h", ""],
    'HKLS|Abdampf_Anschluss|Einheit' => ["DN", "Zoll", '"', ""],
    'HKLS|Abdampf_Strom|Einheit' => ["kg/h", ""],
    'HKLS|WFI_Anschluss|Einheit' => ["DN", "Zoll", '"', ""],
    'HKLS|Schwarzdampf_Anschluss|Einheit' => ["DN", "Zoll", '"', ""],
    'HKLS|Schwarzdampf_Strom|Einheit' => ["kg/h", ""],
    'HKLS|Voll_entsalztes Wasser_Anschluss|Einheit' => ["DN", "Zoll", '"', ""],
    'HKLS|Voll_entsalztes Wasser_Verbrauch|Einheit' => ["l/h", "m³/h", ""],
    'HKLS|Zuluftdurchmesser|Einheit' => ["mm", "cm", ""],
    'HKLS|PW_Strom|Einheit' => ["l/min", "m³/h", ""],
    'HKLS|PW_Anschluss|Einheit' => ["DN", "Zoll", '"', ""],
    'HKLS|Abflusstemperatur|Einheit' => ["°C", "K", ""],
    'HKLS|WFI_Temperatur|Einheit' => ["°C", "K", ""],
    'HKLS|PW_Temperatur|Einheit' => ["°C", "K", ""],
    'HKLS|Abluftventilator|Wert' => ["Ja", "Nein", ""],
    'HKLS|Kühlwasser_Abwärme|Einheit' => ["W", "kW", ""],
    'HKLS|Abfluss_Boden|Einheit' => ["DN", "Zoll", '"', ""],
    'HKLS|Kondensat_Anschluss|Einheit' => ["DN", "Zoll", '"', ""],
    'HKLS|Kühlwasser_Temperatur|Einheit' => ["°C", "K", ""],
    'HKLS|Kühlwasser_Vorlauf_Anschluss|Einheit' => ["DN", "Zoll", '"', ""],
    'HKLS|Kühlwasser_Rücklauf_Anschluss|Einheit' => ["DN", "Zoll", '"', ""],
    'HKLS|Trennung EN1717|Wert' => ["Ja", "Nein", ""],
    'HKLS|Temperaturbereich Primärseite|Einheit' => ["°C", "K", ""],
    'HKLS|Temperaturbereich Sekundärseite|Einheit' => ["°C", "K", ""],
    'HKLS|Warmwasser_Temp_Min|Einheit' => ["°C", "K", ""],
    'HKLS|Kaltwasser_Wasserhaerte|Einheit' => ["°dH", "mmol/l", ""],
    'HKLS|Warmwasser_Wasserhaerte|Einheit' => ["°dH", "mmol/l", ""],

    // Ergonomie (4)
    'Ergonomie|Lärm|Einheit' => ["dB(A)", ""],

    // Allgemein (5)


    'Allgemein|Volumen|Einheit' => ["ml", "l", "m³", ""],
    'Allgemein|Kosten_unbestätigt|Einheit' => ["€", ""],
    'Allgemein|Vergabe abgeschlossen|Wert' => ["Ja", "Nein", ""],

    // Infusionstechnik (6)
    'Infusionstechnik|Fördergenauigkeit|Einheit' => ["%", "ml/h", ""],
    'Infusionstechnik|Akkulaufzeit_20ml/h|Einheit' => ["h", "min", ""],
    'Infusionstechnik|Förderratenauflösung|Einheit' => ["ml/h", "l/h", ""],
    'Infusionstechnik|Geräuschpegel_999 ml/h|Einheit' => ["dB(A)", ""],

    // Kühlschrank/Klimaschrank (7)
    'Kühlschrank/Klimaschrank|Temperaturbereich|Einheit' => ["°C", "K", ""],
    'Kühlschrank/Klimaschrank|rel. Feuchtigkeitsbereich|Einheit' => ["%", ""],

    // Labor (8)
    'Labor|Anschluss_Medienzelle|Einheit' => ["DN", "Zoll", '"', ""],

    // Endoskope (9)
    'Endoskope|Außendurchmesser_Schlauch|Einheit' => ["mm", "cm", ""],
    'Endoskope|Durchmesser_Instrumentierkanal|Einheit' => ["mm", "cm", ""],
    'Endoskope|Arbeitslänge|Einheit' => ["mm", "cm", "m", ""],
    'Endoskope|Gesamtlänge|Einheit' => ["mm", "cm", "m", ""],
    'Endoskope|Blickwinkel_Sichtfeld|Einheit' => ["°", ""],
    'Endoskope|Fokusbereich_Schärfentiefe|Einheit' => ["mm", "cm", ""],
    'Endoskope|Winkelung_Auf_Ab|Einheit' => ["°", ""],
    'Endoskope|Winkelung_Rechts_Links|Einheit' => ["°", ""],

    // Monitor (10)
    'Monitor|Auflösung|Einheit' => ["px", "dpi", ""],
    'Monitor|Monitordiagonale|Einheit' => ["cm", "inch", ""],
    'Monitor|Seitenverhältnis|Einheit' => ["4:3", "16:9", ""],
    'Monitor|Blickwinkel|Einheit' => ["°", ""],
    'Monitor|Kontrast|Einheit' => ["Verhältnis", ""],
    'Monitor|Helligkeit|Einheit' => ["cd/m²", ""],

    // Laser (11)
    'Laser|Wellenlänge|Einheit' => ["nm", "μm", ""],
    'Laser|Laserleistung|Einheit' => ["W", "mW", ""],
    'Laser|Laserklasse|Wert' => ["1", "1M", "2", "2M", "3R", "3B", "4", ""],


    // MedGas (12)
    'MedGas|Stickstoff_Strom|Einheit' => ["l/min", "m³/h", ""],
    'MedGas|Argon_Strom|Einheit' => ["l/min", "m³/h", ""],
    
    'MedGas|O2 Anschluss|Wert' => ["Ja", "Nein"],
    'MedGas|DL-5 Anschluss|Wert' => ["Ja", "Nein"],
    'MedGas|VAC Anschluss|Wert' => ["Ja", "Nein"],
    'MedGas|DL-10 Anschluss|Wert' => ["Ja", "Nein"],
    'MedGas|NGA Anschluss|Wert' => ["Ja", "Nein"],
    'MedGas|N2O Anschluss|Wert' => ["Ja", "Nein"],
    'MedGas|CO2 Anschluss|Wert' => ["Ja", "Nein"],

    // Beleuchtung (13)
    'Beleuchtung|Beleuchtungsstärke|Einheit' => ["lx", ""],

    // Statik (14)
    'Statik|Punktlast|Einheit' => ["kg", "N", ""],
    'Statik|Wandverstärkung|Wert' => ["50", "100", "150", "200", "Ja", ""],
    'Statik|Wandverstärkung|Einheit' => ["kg/lfm", ""],

    // US-Sonden (15)
    'US-Sonden|Frequenzbereich|Einheit' => ["Hz", "kHz", "MHz", "GHz"],

    // Waage (16)
    'Waage|Mindesteinwaage|Einheit' => ["g", "mg", "μg"],
    'Waage|Maximaleinwaage|Einheit' => ["g", "kg", "t", ""],
    'Waage|Ablesbarkeit|Einheit' => ["g", "mg", "dg", "kg"],

    // MSR (17)
    'MSR|GLT|Einheit' => ["Ja", ""],

    // Geometrie_Einbringung (18)
    'Geometrie_Einbringung|Einbringweg_Breite|Einheit' => ["mm", "cm", "m", ""],
    'Geometrie_Einbringung|Einbringweg_Höhe|Einheit' => ["mm", "cm", "m", ""],
    'Geometrie_Einbringung|Einbringweg_Tiefe|Einheit' => ["mm", "cm", "m", ""],
    'Geometrie_Einbringung|Einbringweg_Gewicht|Einheit' => ["kg", "t", ""],
    'Geometrie_Einbringung|Einbringweg_Flächenlast|Einheit' => ["kg/m²", "N/m²", "kg/cm²"],
    'Geometrie_Einbringung|Einbringweg_Breite_2|Einheit' => ["mm", "cm", "m", ""],
    'Geometrie_Einbringung|Einbringweg_Höhe_2|Einheit' => ["mm", "cm", "m", ""],
    'Geometrie_Einbringung|Einbringweg_Tiefe_2|Einheit' => ["mm", "cm", "m", ""],
];
