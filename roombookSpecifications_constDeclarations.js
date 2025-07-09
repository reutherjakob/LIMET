function data2title(columnsDefinition, dataIdentifier) {
    const column = columnsDefinition.find(col => col.data === dataIdentifier);
    return column ? column.title : dataIdentifier;
}

function title2data(columnsDefinition, title) {
    const column = columnsDefinition.find(col => col.title === title);
    return column ? column.data : title;
}


const buttonRanges = [
    {name: 'All', start: 6, end: 148, longName: 'Alle Spalten'},
    {name: 'R', start: 7, end: 24, longName: 'Raum'},
    {name: 'HKLS', start: 25, end: 34, longName: 'HKLS'},
    {name: 'ET', start: 35, end: 55, longName: 'Elektro'},
    {name: 'AR', start: 56, end: 61, longName: 'Architektur'},
    {name: 'MG', start: 62, end: 74, longName: 'Medgas'},
    {name: 'LAB', start: 65, end: 147, longName: 'Labor'},
    {name: '-GAS', start: 75, end: 116, longName: 'Labor-GAS'},
    {name: '-ET', start: 117, end: 128, longName: 'Labor-ET'},
    {name: '-HT', start: 129, end: 139, longName: 'Labor-HT'},
    {name: '-H2O', start: 140, end: 148, longName: 'Labor-H2O'},
    {name: 'GCP', start: 148, end: 168, longName: ''}
];
const columnsDefinition = [
    {data: 'tabelle_projekte_idTABELLE_Projekte', title: 'Projek ID', visible: false, searchable: false}, // 0
    {data: 'idTABELLE_Räume', title: 'Raum ID', visible: false, searchable: false}, // 1
    {
        data: 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen',
        title: 'Funktionsstellen ID',
        visible: false,
        searchable: false
    }, // 2
    {
        data: 'MT-relevant', title: 'MT-rel.', name: 'MT-relevant', case: "bit", render: function (data) {
            return data === '1' ? 'Ja' : 'Nein';
        }
    }, // 3
    {data: 'Raumbezeichnung', title: 'Raumbez'}, // 4

    {data: 'Raumnr', title: 'Raumnr'}, // 5
    {data: "Bezeichnung", title: "Funktionsstelle", visible: false, case: "none-edit"}, // 6
    {data: 'Funktionelle Raum Nr', title: 'Funkt.R.Nr'}, // 7
    {data: "Nummer", title: "DIN13080", visible: false, case: "none-edit"}, // 8
    {data: "Entfallen", title: "Entfallen", name: "Entfallen", visible: false, case: "bit"}, // 9
    {data: 'Raumnummer_Nutzer', title: 'Raumnr Nutzer', visible: false}, // 10
    {data: 'Raumbereich Nutzer', title: 'Raumbereich', visible: false}, // 11
    {data: 'Geschoss', title: 'Geschoss', visible: false}, // 12
    {data: 'Bauetappe', title: 'Bauetappe', visible: false}, // 13
    {data: 'Bauabschnitt', title: 'Bauabschnitt', visible: false}, // 14
    {data: 'Nutzfläche', title: 'Nutzfläche', visible: false, case: "num"}, // 15
    {
        data: 'Abdunkelbarkeit', title: 'Abdunkelbar', visible: false, case: "abd", render: function (data) {
            if (data === "0") {
                return "kein Anspruch";
            } else if (data === "1") {
                return "vollverdunkelbar";
            } else {
                return "abdunkelbar";
            }
        }
    }, // 16
    {data: 'Strahlenanwendung', title: 'Strahlenanw.', visible: false, case: "bit"}, // 17
    {data: 'Laseranwendung', title: 'Laseranw.', visible: false, case: "bit"}, // 18
    {data: 'Allgemeine Hygieneklasse', title: 'Allg. Hygieneklasse', visible: false}, // 19
    {data: 'Raumhoehe', title: 'Raumhoehe', visible: false, case: "num"}, // 20
    {data: 'Raumhoehe 2', title: 'Raumhoehe2', visible: false, case: "num"}, // 21
    {data: 'Belichtungsfläche', title: 'Belichtungsfläche', visible: false, case: "num"}, // 22
    {data: 'Umfang', title: 'Umfang', visible: false, case: "num"}, // 23
    {data: 'Volumen', title: 'Volumen', visible: false, case: "num"}, // 24
    {data: 'H6020', title: 'Lüftungsnorm [H6020]', visible: false}, // 25
    {data: 'GMP', title: 'GMP', visible: false}, // 26
    {data: 'ISO', title: 'ISO', visible: false}, // 27
    {data: 'HT_Waermeabgabe_W', title: 'Wärmeabgabe[W]', visible: false, case: ""}, // 28
    {data: 'HT_Raumtemp Sommer °C', title: 'Raumtemp Sommer °C', visible: false, case: "num"}, // 29
    {data: 'HT_Raumtemp Winter °C', title: 'Raumtemp Winter °C', visible: false, case: "num"}, // 30
    {data: 'HT_Spuele_Stk', title: 'Spüle [Stk]', visible: false, case: "num"}, // 31
    {data: 'HT_Kühlwasser', title: 'Kühlwasser', visible: false, case: "num"}, // 32
    {data: 'HT_Notdusche', title: 'Notdusche', visible: false, case: "num"}, // 33
    {data: 'HT_Tempgradient_Ch', title: 'Tempgradient C/h', visible: false, case: "num"}, // 34

    {data: 'Anwendungsgruppe', title: 'Raumgruppe 8101', visible: false}, // 35
    {data: 'Fussboden OENORM B5220', title: 'B5220', visible: false}, // 36
    {data: 'AV', title: 'AV', visible: false, defaultContent: '-', case: "bit"}, // 37
    {data: 'SV', title: 'SV', visible: false, defaultContent: '-', case: "bit"}, // 38
    {data: 'ZSV', title: 'ZSV', visible: false, defaultContent: '-', case: "bit"}, // 39
    {data: 'USV', title: 'USV', visible: false, defaultContent: '-', case: "bit"}, // 40
    {data: 'EL_AV Steckdosen Stk', defaultContent: '-', title: 'AV #SSD', visible: false, case: "num"}, // 41
    {data: 'EL_SV Steckdosen Stk', defaultContent: '-', title: 'SV #SSD', visible: false, case: "num"}, // 42
    {data: 'EL_ZSV Steckdosen Stk', defaultContent: '-', title: 'ZSV #SSD', visible: false, case: "num"}, // 43
    {data: 'EL_USV Steckdosen Stk', defaultContent: '-', title: 'USV #SSD', visible: false, case: "num"}, // 44
    {data: 'EL_Roentgen 16A CEE Stk', title: 'CEE16A Röntgen', visible: false, case: "num"}, // 45
    {data: 'EL_Laser 16A CEE Stk', defaultContent: '-', title: 'CEE16A Laser', visible: false, case: "num"}, // 46
    {
        data: 'ET_Anschlussleistung_W',
        defaultContent: '-',
        title: 'Anschlussleistung Summe[W]',
        visible: false,
        case: "num"
    }, // 47
    {
        data: 'ET_Anschlussleistung_AV_W',
        defaultContent: '-',
        title: 'Anschlussleistung AV[W]',
        visible: false,
        case: "num"
    }, // 48
    {
        data: 'ET_Anschlussleistung_SV_W',
        defaultContent: '-',
        title: 'Anschlussleistung SV[W]',
        visible: false,
        case: "num"
    }, // 49
    {
        data: 'ET_Anschlussleistung_ZSV_W',
        defaultContent: '-',
        title: 'Anschlussleistung ZSV[W]',
        visible: false,
        case: "num"
    }, // 50
    {
        data: 'ET_Anschlussleistung_USV_W',
        defaultContent: '-',
        title: 'Anschlussleistung USV[W]',
        visible: false,
        case: "num"
    }, // 51
    {data: 'IT Anbindung', title: 'IT', visible: false, case: "bit"}, // 52
    {data: 'ET_RJ45-Ports', title: 'RJ45-Ports', visible: false, case: "num"}, // 53
    {data: 'Laserklasse', title: 'Laserklasse', visible: false}, // 54
    {data: 'ET_EMV_ja-nein', title: 'ET EMV', visible: false, case: "bit"}, // 55

    {data: 'AR_Statik_relevant', title: 'Statik relevant', visible: false, case: "bit"}, // 56
    {
        data: 'AR_AP_permanent',
        title: 'AR AP permanent',
        name: 'AR AP permanent ',
        visible: false,
        case: "bit",
        render: function (data) {
            return data === '1' ? 'permanenter AP' : 'kein perma AP';
        }
    }, // 57
    {
        data: 'AR_Empf_Breite_cm',
        defaultContent: '-',
        title: 'Empfohlene Breite [cm]',
        visible: false,
        case: "num"
    }, // 58
    {data: 'AR_Empf_Tiefe_cm', defaultContent: '-', title: 'Empfohlene Tiefe [cm]', visible: false, case: "num"}, // 59
    {data: 'AR_Empf_Hoehe_cm', defaultContent: '-', title: 'Empfohlene Höhe [cm]', visible: false, case: "num"}, // 60
    {
        data: 'AR_Flaechenlast_kgcm2',
        defaultContent: '-',
        title: 'Flaechenlast [kg/cm2]',
        visible: false,
        case: "num"
    }, // 61
    {data: '1 Kreis O2', title: '1_K O2', visible: false, case: "bit"}, // 62
    {data: '2 Kreis O2', title: '2_K O2', visible: false, case: "bit"}, // 63
    {data: 'CO2', title: 'CO2', visible: false, case: "bit"}, // 64
    {data: '1 Kreis Va', title: '1_K Va', visible: false, case: "bit"}, // 65
    {data: '2 Kreis Va', title: '2_K Va', visible: false, case: "bit"}, // 66
    {data: '1 Kreis DL-5', title: '1_K DL5', visible: false, case: "bit"}, // 67
    {data: '2 Kreis DL-5', title: '2_K DL5', visible: false, case: "bit"}, // 68
    {data: 'DL-10', title: 'DL-10', visible: false, case: "bit"}, // 69
    {data: 'DL-tech', title: 'DL-tech', visible: false, case: "bit"}, // 70
    {data: 'NGA', title: 'NGA', visible: false, case: "bit"}, // 71
    {data: 'N2O', title: 'N2O', visible: false, case: "bit"}, // 72
    {data: 'VEXAT_Zone', title: 'VEXAT Zone', visible: false, case: ""}, // 73
    {data: 'O2', title: 'O2', visible: false, case: "bit"}, // 74
    {data: 'O2 l/min', title: 'O2_l/min', visible: false, case: "num"}, // 75
    {data: 'O2 Reinheit', title: 'O2 Reinheit', visible: false, case: ""}, // 76
    {data: 'CO2 l/min', title: 'CO2_l/min', visible: false}, // 77
    {data: 'CO2 Reinheit', title: 'CO2 Reinheit', visible: false, case: ""}, // 78
    {data: 'VA', title: 'VA', visible: false, case: "bit"}, // 79
    {data: 'VA l/min', title: 'VA_l/min', visible: false, case: "num"}, // 80
    {data: 'H2', title: 'H2', visible: false, case: "bit"}, // 81
    {data: 'H2 Reinheit', title: 'H2 Reinheit', visible: false, case: ""}, // 82
    {data: 'H2 l/min', title: 'H2_l/min', visible: false, case: "num"}, // 83
    {data: 'He', title: 'He', visible: false, case: "bit"}, // 84
    {data: 'He Reinheit', title: 'He Reinheit', visible: false, case: ""}, // 85
    {data: 'He l/min', title: 'He_l/min', visible: false, case: "num"}, // 86
    {data: 'He-RF', title: 'He-RF', visible: false, case: "bit"}, // 87
    {data: 'LHe', title: 'LHe', visible: false, case: "bit"}, // 88
    {data: 'Ar', title: 'Ar', visible: false, case: "bit"}, // 89
    {data: 'Ar Reinheit', title: 'Ar Reinheit', visible: false, case: ""}, // 90
    {data: 'Ar l/min', title: 'Ar_l/min', visible: false, case: "num"}, // 91
    {data: 'LN', title: 'LN', visible: false, case: "bit"}, // 92
    {data: 'LN l/Tag', title: 'LN l/Tag', visible: false, case: "num"}, // 93
    {data: 'N2', title: 'N2', visible: false, case: "bit"}, // 94
    {data: 'N2 Reinheit', title: 'N2 Reinheit', visible: false, case: ""}, // 95
    {data: 'N2 l/min', title: 'N2_l/min', visible: false, case: "num"}, // 96
    {data: 'DL-5', title: 'DL-5', visible: false, case: "bit"},
    {data: 'DL ISO 8573', title: 'DL_ISO 8573', visible: false, case: "bit"},
    {data: 'DL l/min', title: 'DL_l/min', visible: false, case: "num"},
    {data: 'Kr', title: 'Kr', visible: false, case: 'bit'}, // 100
    {data: 'Ne', title: 'Ne', visible: false, case: 'bit'}, // 101
    {data: 'NH3', title: 'NH3', visible: false, case: 'bit'}, // 102
    {data: 'C2H2', title: 'C2H2', visible: false, case: 'bit'}, // 103
    {data: 'Propan_Butan', title: 'Propan_Butan', visible: false, case: 'num'}, // 104
    {data: 'N2H2', title: 'N2H2', visible: false, case: 'num'}, // 105
    {data: 'Inertgas', title: 'Inertgas', visible: false, case: 'num'}, // 106
    {data: 'Ar_CO2_Mix', title: 'AR CO2 Mix', visible: false, case: 'num'}, // 107
    {data: 'ArCal15', title: 'ArCal15', visible: false, case: 'num'}, // 108
    {data: 'O2_Mangel', title: 'O2_Mangel', visible: false, case: 'num'}, // 109
    {data: 'CO2_Melder', title: 'CO2_Melder', visible: false, case: 'num'}, // 110
    {data: 'NH3_Sensor', title: 'NH3_Sensor', visible: false, case: 'num'}, // 111
    {data: 'H2_Sensor', title: 'H2_Sensor', visible: false, case: 'num'}, // 112
    {data: 'O2_Sensor', title: 'O2_Sensor', visible: false, case: 'num'}, // 113
    {data: 'Acetylen_Melder', title: 'Acetylen_Melder', visible: false, case: 'num'}, // 114
     {data: 'Blitzleuchte', title: 'Blitzleuchte', visible: false, case: 'num'}, // 115
    {data: 'ET_PA_Stk', title: 'ET PA Stk', visible: false, case: "num"}, // 116
    {data: 'ET_16A_3Phasig_Einzelanschluss', title: '16A 3Ph Einzelanschluss', visible: false, case: "num"}, // 117
    {data: 'ET_32A_3Phasig_Einzelanschluss', title: '32A 3Ph Einzelanschluss', visible: false, case: "num"}, // 118
    {data: 'ET_64A_3Phasig_Einzelanschluss', title: '64A 3Ph Einzelanschls', visible: false, case: "num"}, // 119
    {
        data: 'ET_Digestorium_MSR_230V_SV_Stk',
        title: 'Digestorium_MSR 230V_SV_Stk',
        visible: false,
        case: "num"
    }, // 120
    {data: 'ET_5x10mm2_Digestorium_Stk', title: '5x10mm2 Digestorium_Stk', visible: false, case: "num"}, // 121
    {data: 'ET_5x10mm2_USV_Stk', title: '5x10mm2 USV_Stk', visible: false, case: "num"}, // 122
    {data: 'ET_5x10mm2_SV_Stk', title: '5x10mm2 SV_Stk', visible: false, case: "num"}, // 123
    {data: 'ET_5x10mm2_AV_Stk', title: '5x10mm2 AV_Stk', visible: false, case: "num"}, // 124
    {data: 'EL_Not_Aus_Funktion', title: 'Not Aus Funktion', visible: false, case: ""}, // 125
    {data: 'EL_Not_Aus', title: 'Not Aus Stk', visible: false, case: 'num'}, // 126
    {data: 'EL_Signaleinrichtung', title: 'Signaleinrichtung', visible: false, case: 'bit'}, // 127
    {data: 'HT_Luftwechsel 1/h', title: 'Luftwechsel l/h', visible: false, case: "num"}, // 127 +1
    {data: 'HT_Abluft_Vakuumpumpe', title: 'Abluft Vakuumpumpe', visible: false, case: "num"}, // 128+1
    {
        data: 'HT_Abluft_Sicherheitsschrank_Stk',
        title: 'Abluft Sicherheitsschrank Stk',
        visible: false,
        case: "num"
    }, // 129              +1
    {
        data: 'HT_Abluft_Schweissabsaugung_Stk',
        title: 'Abluft Schweissabsaugung_Stk',
        visible: false,
        case: "num"
    }, // 130
    {data: 'HT_Abluft_Esse_Stk', title: 'Abluft Esse_Stk', visible: false, case: "num"}, // 131                         +1
    {data: 'HT_Abluft_Rauchgasabzug_Stk', title: 'Abluft Rauchgasabzug_Stk', visible: false, case: "num"}, // 132       +1
    {data: 'HT_Abluft_Digestorium_Stk', title: 'Abluft Digestorium_Stk', visible: false, case: "num"}, // 133           +1
    {data: 'HT_Punktabsaugung_Stk', title: 'Punktabsaugung_Stk', visible: false, case: "num"}, // 134                   +1
    {
        data: 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk',
        title: 'Abluft Sicherheitsschrank_Unterbau_Stk',
        visible: false,
        case: "num"
    }, // 135                                      +1
    {data: 'HT_Abwasser_Stk', title: 'Abwasser Stk', visible: false, case: "num"}, // 136     +1
    {data: 'HT_Abluft_Geraete', title: 'Abluft Geräte', visible: false, case: "num"}, // 137  +1
    {data: 'VE_Wasser', title: 'VE_Wasser', visible: false, case: 'num'}, // 138              +1
    {data: 'HT_Warmwasser', title: 'Warmwasser', visible: false, case: "num"}, // 139         +1
    {data: 'HT_Kaltwasser', title: 'Kaltwasser', visible: false, case: "num"}, // 140         +1
    {data: 'Wasser Qual 1 l/Tag', title: 'H20_Q1 l/Tag', visible: false, case: "num"}, // 141 +1
    {data: 'Wasser Qual 2 l/Tag', title: 'H20_Q2 l/Tag', visible: false, case: "num"}, // 142 +1
    {data: 'Wasser Qual 3 l/min', title: 'H2O_Q3 l/min', visible: false, case: "num"}, // 143 +1
    {data: 'Wasser Qual 3', title: 'H20 Q3', visible: false, case: "bit"}, // 144 +1
    {data: 'Wasser Qual 2', title: 'H20 Q2', visible: false, case: "bit"}, // 145 +1
    {data: 'Wasser Qual 1', title: 'H20 Q1', visible: false, case: "bit"}, // 146 +1

    // GCP STISSL... 
   {data: 'Belichtungsfläche', title: 'AR Belichtungsfläche', visible: false, case: ""},
   {data: 'Fussboden', title: 'AR Fussboden', visible: false, case: ""},
   {data: 'Decke', title: 'AR Decke', visible: false, case: ""},
   {data: 'Anmerkung AR', title: 'AR Anmerkung', visible: false, case: ""},
   {data: 'Taetigkeiten', title: 'AR Taetigkeiten', visible: false, case: ""},
   {data: 'AR_APs', title: 'APs Anzahl', visible: false, case: ""},
   {data: 'AP_Gefaehrdung', title: 'AP_Gefaehrdung', visible: false, case: ""},
   {data: 'AP_Geistige', title: 'AP Geistige', visible: false, case: ""},
   {data: 'AR_Schwingungsklasse', title: 'AR Schwingungsklasse', visible: false, case: ""},
   {data: 'Spezialgase', title: 'Spezialgase', visible: false, case: ""},
   {data: 'Gaswarneinrichtung-Art', title: 'Gaswarneinrichtung-Art', visible: false, case: ""},
   {data: 'EL_Beleuchtungsstaerke', title: 'EL_Beleuchtungsstaerke', visible: false, case: ""},
   {data: 'ET_EMV', title: 'ET_E EMV Maßnahme Txt', visible: false, case: ""},    //148
   {data: 'HT_Luftmenge m3/h', title: 'HT_Luftmenge m3/h', visible: false, case: ""},
   {data: 'HT_Kuehlung', title: 'HT_Kuehlung', visible: false, case: ""},
   {data: 'HT_Kaelteabgabe_Typ', title: 'HT_Kaelteabgabe_Typ', visible: false, case: ""},
   {data: 'HT_Heizung', title: 'HT_Heizung', visible: false, case: ""},
   {data: 'HT_Waermeabgabe_Typ', title: 'HT_Waermeabgabe_Typ', visible: false, case: ""}, {
       data: 'HT_Heizung',
       title: 'HT_Heizung',
       visible: false,
       case: ""
   },
   {data: 'HT_Belueftung', title: 'HT_Belueftung', visible: false, case: ""},
   {data: 'HT_Entlueftung', title: 'HT_Entlueftung', visible: false, case: ""},
   {data: 'PHY_Akustik_Schallgrad', title: 'PHY_Akustik_Schallgrad', visible: false, case: ""}
]
