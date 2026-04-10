/*function data2title(columnsDefinition, dataIdentifier) {
    const column = columnsDefinition.find(col => col.data === dataIdentifier);
    return column ? column.title : dataIdentifier;
}

function title2data(columnsDefinition, title) {
    const column = columnsDefinition.find(col => col.title === title);
    return column ? column.data : title;
}*/

const buttonRanges = [
    {name: 'All', start: 6, end: 170, longName: 'Alle Spalten'},
    {name: 'RA', start: 7, end: 26, longName: 'Raum'},
    {name: 'HKLS', start: 27, end: 36, longName: 'HKLS'},
    {name: 'ET', start: 37, end: 58, longName: 'Elektro'},
    {name: 'AR', start: 59, end: 64, longName: 'Architektur'},
    {name: 'MG', start: 65, end: 76, longName: 'Medgas'},
    {name: 'LAB', start: 68, end: 150, longName: 'Labor'},
    {name: '-GAS', start: 78, end: 119, longName: 'Labor-GAS'},
    {name: '-ET', start: 120, end: 131, longName: 'Labor-ET'},
    {name: '-HT', start: 132, end: 141, longName: 'Labor-HT'},
    {name: '-H2O', start: 142, end: 150, longName: 'Labor-H2O'},
    {name: 'GCP', start: 151, end: 170, longName: 'GCP'},
];


const columnsDefinition = [
    {data: 'tabelle_projekte_idTABELLE_Projekte', title: 'Projek ID', visible: false, searchable: false}, // 0 | -
    {data: 'idTABELLE_Räume', title: 'Raum ID', visible: false, searchable: false}, // 1 | -
    {
        data: 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen',
        title: 'Funktionsstellen ID',
        visible: false,
        searchable: false
    }, // 2 | -
    {
        data: 'MT-relevant', title: 'MT-rel.', name: 'MT-relevant', case: "bit",
        render: function (data) {
            return data === 1 ? 'Ja' : 'Nein';
        }
    }, // 3 | -
    {data: 'Raumbezeichnung', title: 'Raumbez'}, // 4 | -
    {data: 'Raumnr', title: 'Raumnr'}, // 5 | -
    {data: "Bezeichnung", title: "Funktionsstelle", visible: false, searchable: false, case: "Funktionsstelle"}, // 6 | All-Start
    {data: 'Funktionelle Raum Nr', title: 'Funkt.R.Nr', visible: false}, // 7  | R-Start
    {data: "Nummer", title: "DIN13080", visible: false, case: "none-edit"}, // 8  | R
    {data: "Entfallen", title: "Entfallen", name: "Entfallen", visible: false, case: "bit"}, // 9  | R
    {data: 'Raumtyp BH', title: 'Raumtype', visible: false, case: ""},                       // 10 | R
    {data: 'element_mask', title: '#Elemente', name: 'element_mask', visible: false, case: "bit"}, // 11 | R

    {data: 'Raumnummer_Nutzer', title: 'Raumnr Nutzer', visible: false}, // 12 | R
    {data: 'Raumbereich Nutzer', title: 'Raumbereich', visible: false}, // 13 | R
    {data: 'Geschoss', title: 'Geschoss', visible: false}, // 14 | R
    {data: 'Bauetappe', title: 'Bauetappe', visible: false}, // 15 | R
    {data: 'Bauabschnitt', title: 'Bauabschnitt/-teil', visible: false}, // 16 | R
    {data: 'Nutzfläche', title: 'Nutzfläche', visible: false, case: "num"}, // 17 | R
    {data: 'Abdunkelbarkeit', title: 'Abdunkelbar', visible: false, case: "abd"}, // 18 | R
    {data: 'Strahlenanwendung', title: 'Strahlenanw.', visible: false, case: "bit"}, // 19 | R
    {data: 'Laseranwendung', title: 'Laseranw.', visible: false, case: "bit"}, // 20 | R
    {data: 'Allgemeine Hygieneklasse', title: 'Allg. Hygieneklasse', visible: false}, // 21 | R
    {data: 'Raumhoehe', title: 'Raumhoehe', visible: false, case: "num"}, // 22 | R
    {data: 'Raumhoehe 2', title: 'Raumhoehe2', visible: false, case: "num"}, // 23 | R
    {data: 'Belichtungsfläche', title: 'Belichtungsfläche', visible: false, case: "num"}, // 24 | R
    {data: 'Umfang', title: 'Umfang', visible: false, case: "num"}, // 25 | R
    {data: 'Volumen', title: 'Volumen', visible: false, case: "num"}, // 26 | R-End

    {data: 'H6020', title: 'H6020', visible: false}, // 27 | HKLS-Start
    {data: 'GMP', title: 'GMP', visible: false}, // 28 | HKLS
    {data: 'ISO', title: 'ISO', visible: false}, // 29 | HKLS
    {data: 'HT_Waermeabgabe_W', title: 'Wärmeabgabe[W]', visible: false, case: ""}, // 30 | HKLS
    {data: 'HT_Raumtemp Sommer °C', title: 'Raumtemp Sommer °C', visible: false, case: "num"}, // 31 | HKLS
    {data: 'HT_Raumtemp Winter °C', title: 'Raumtemp Winter °C', visible: false, case: "num"}, // 32 | HKLS
    {data: 'HT_Spuele_Stk', title: 'Spüle [Stk]', visible: false, case: "num"}, // 33 | HKLS
    {data: 'HT_Kühlwasser', title: 'Kühlwasser', visible: false, case: "num"}, // 34 | HKLS
    {data: 'HT_Notdusche', title: 'Notdusche', visible: false, case: "num"}, // 35 | HKLS
    {data: 'HT_Tempgradient_Ch', title: 'Tempgradient C/h', visible: false, case: "num"}, // 36 | HKLS-End

    {data: 'Anwendungsgruppe', title: 'Raumgruppe 8101', visible: false}, // 37 | ET-Start
    {data: 'Fussboden OENORM B5220', title: 'B5220', visible: false}, // 38 | ET
    {data: 'AV', title: 'AV', visible: false, defaultContent: '-', case: "bit"}, // 39 | ET
    {data: 'SV', title: 'SV', visible: false, defaultContent: '-', case: "bit"}, // 40 | ET
    {data: 'ZSV', title: 'ZSV', visible: false, defaultContent: '-', case: "bit"}, // 41 | ET
    {data: 'USV', title: 'USV', visible: false, defaultContent: '-', case: "bit"}, // 42 | ET
    {data: 'EL_AV Steckdosen Stk', defaultContent: '-', title: 'AV #SSD', visible: false, case: "num"}, // 43 | ET
    {data: 'EL_SV Steckdosen Stk', defaultContent: '-', title: 'SV #SSD', visible: false, case: "num"}, // 44 | ET
    {data: 'EL_ZSV Steckdosen Stk', defaultContent: '-', title: 'ZSV #SSD', visible: false, case: "num"}, // 45 | ET
    {data: 'EL_USV Steckdosen Stk', defaultContent: '-', title: 'USV #SSD', visible: false, case: "num"}, // 46 | ET
    {data: 'EL_Roentgen 16A CEE Stk', title: 'CEE16A Röntgen', visible: false, case: "num"}, // 47 | ET
    {data: 'EL_Laser 16A CEE Stk', defaultContent: '-', title: 'CEE16A Laser', visible: false, case: "num"}, // 48 | ET
    {data: 'EL_Laser 32A Stk', title: '32A Laser', visible: false, case: "num"}, // 49 | ET
    {
        data: 'ET_Anschlussleistung_W',
        defaultContent: '-',
        title: 'Anschlussleistung Summe[W]',
        visible: false,
        case: "num"
    }, // 50 | ET
    {
        data: 'ET_Anschlussleistung_AV_W',
        defaultContent: '-',
        title: 'Anschlussleistung AV[W]',
        visible: false,
        case: "num"
    }, // 51 | ET
    {
        data: 'ET_Anschlussleistung_SV_W',
        defaultContent: '-',
        title: 'Anschlussleistung SV[W]',
        visible: false,
        case: "num"
    }, // 52 | ET
    {
        data: 'ET_Anschlussleistung_ZSV_W',
        defaultContent: '-',
        title: 'Anschlussleistung ZSV[W]',
        visible: false,
        case: "num"
    }, // 53 | ET
    {
        data: 'ET_Anschlussleistung_USV_W',
        defaultContent: '-',
        title: 'Anschlussleistung USV[W]',
        visible: false,
        case: "num"
    }, // 54 | ET
    {data: 'IT Anbindung', title: 'IT', visible: false, case: "bit"}, // 55 | ET
    {data: 'ET_RJ45-Ports', title: 'RJ45-Ports', visible: false, case: "num"}, // 56 | ET
    {data: 'Laserklasse', title: 'Laserklasse', visible: false}, // 57 | ET
    {data: 'ET_EMV_ja-nein', title: 'ET EMV', visible: false, case: "bit"}, // 58 | ET-End

    {data: 'AR_Statik_relevant', title: 'Statik relevant', visible: false, case: "bit"}, // 59 | AR-Start
    {
        data: 'AR_AP_permanent',
        title: 'AR AP permanent',
        name: 'AR AP permanent',
        visible: false,
        case: "bit",
        render: function (data) {
            return data === '1' ? 'permanenter AP' : 'kein perma AP';
        }
    }, // 60 | AR
    {data: 'AR_Empf_Breite_cm', defaultContent: '-', title: 'Empfohlene Breite [cm]', visible: false, case: "num"}, // 61 | AR
    {data: 'AR_Empf_Tiefe_cm', defaultContent: '-', title: 'Empfohlene Tiefe [cm]', visible: false, case: "num"}, // 62 | AR
    {data: 'AR_Empf_Hoehe_cm', defaultContent: '-', title: 'Empfohlene Höhe [cm]', visible: false, case: "num"}, // 63 | AR
    {data: 'AR_Flaechenlast_kgcm2', defaultContent: '-', title: 'Flaechenlast [kg/cm2]', visible: false, case: "num"}, // 64 | AR-End

    {data: '1 Kreis O2', title: '1_K O2', visible: false, case: "bit"}, // 65 | MG-Start
    {data: '2 Kreis O2', title: '2_K O2', visible: false, case: "bit"}, // 66 | MG
    {data: 'CO2', title: 'CO2', visible: false, case: "bit"}, // 67 | MG
    {data: '1 Kreis Va', title: '1_K Va', visible: false, case: "bit"}, // 68 | MG / LAB-Start
    {data: '2 Kreis Va', title: '2_K Va', visible: false, case: "bit"}, // 69 | MG / LAB
    {data: '1 Kreis DL-5', title: '1_K DL5', visible: false, case: "bit"}, // 70 | MG / LAB
    {data: '2 Kreis DL-5', title: '2_K DL5', visible: false, case: "bit"}, // 71 | MG / LAB
    {data: 'DL-10', title: 'DL-10', visible: false, case: "bit"}, // 72 | MG / LAB
    {data: 'DL-tech', title: 'DL-tech', visible: false, case: "bit"}, // 73 | MG / LAB
    {data: 'NGA', title: 'NGA', visible: false, case: "bit"}, // 74 | MG / LAB
    {data: 'N2O', title: 'N2O', visible: false, case: "bit"}, // 75 | MG / LAB
    {data: 'VEXAT_Zone', title: 'VEXAT Zone', visible: false, case: ""}, // 76 | MG-END/ LAB
    {data: 'O2', title: 'O2', visible: false, case: "bit"}, // 77 |  LAB
    {data: 'O2 l/min', title: 'O2_l/min', visible: false, case: "num"}, // 78 | LAB / -GAS-Start
    {data: 'O2 Reinheit', title: 'O2 Reinheit', visible: false, case: ""}, // 79 | LAB / -GAS
    {data: 'CO2 l/min', title: 'CO2_l/min', visible: false}, // 80 | LAB / -GAS
    {data: 'CO2 Reinheit', title: 'CO2 Reinheit', visible: false, case: ""}, // 81 | LAB / -GAS
    {data: 'VA', title: 'VA', visible: false, case: "bit"}, // 82 | LAB / -GAS
    {data: 'VA l/min', title: 'VA_l/min', visible: false, case: "num"}, // 83 | LAB / -GAS
    {data: 'H2', title: 'H2', visible: false, case: "bit"}, // 84 | LAB / -GAS
    {data: 'H2 Reinheit', title: 'H2 Reinheit', visible: false, case: ""}, // 85 | LAB / -GAS
    {data: 'H2 l/min', title: 'H2_l/min', visible: false, case: "num"}, // 86 | LAB / -GAS
    {data: 'He', title: 'He', visible: false, case: "bit"}, // 87 | LAB / -GAS
    {data: 'He Reinheit', title: 'He Reinheit', visible: false, case: ""}, // 88 | LAB / -GAS
    {data: 'He l/min', title: 'He_l/min', visible: false, case: "num"}, // 89 | LAB / -GAS
    {data: 'He-RF', title: 'He-RF', visible: false, case: "bit"}, // 90 | LAB / -GAS
    {data: 'LHe', title: 'LHe', visible: false, case: "bit"}, // 91 | LAB / -GAS
    {data: 'Ar', title: 'Ar', visible: false, case: "bit"}, // 92 | LAB / -GAS
    {data: 'Ar Reinheit', title: 'Ar Reinheit', visible: false, case: ""}, // 93 | LAB / -GAS
    {data: 'Ar l/min', title: 'Ar_l/min', visible: false, case: "num"}, // 94 | LAB / -GAS
    {data: 'LN', title: 'LN', visible: false, case: "bit"}, // 95 | LAB / -GAS
    {data: 'LN l/Tag', title: 'LN l/Tag', visible: false, case: "num"}, // 96 | LAB / -GAS
    {data: 'N2', title: 'N2', visible: false, case: "bit"}, // 97 | LAB / -GAS
    {data: 'N2 Reinheit', title: 'N2 Reinheit', visible: false, case: ""}, // 98 | LAB / -GAS
    {data: 'N2 l/min', title: 'N2_l/min', visible: false, case: "num"}, // 99 | LAB / -GAS
    {data: 'DL-5', title: 'DL-5', visible: false, case: "bit"}, // 100 | LAB / -GAS
    {data: 'DL ISO 8573', title: 'DL_ISO 8573', visible: false, case: "bit"}, // 101 | LAB / -GAS
    {data: 'DL l/min', title: 'DL_l/min', visible: false, case: "num"}, // 102 | LAB / -GAS
    {data: 'Kr', title: 'Kr', visible: false, case: 'bit'}, // 103 | LAB / -GAS
    {data: 'Ne', title: 'Ne', visible: false, case: 'bit'}, // 104 | LAB / -GAS
    {data: 'NH3', title: 'NH3', visible: false, case: 'bit'}, // 105 | LAB / -GAS
    {data: 'C2H2', title: 'C2H2', visible: false, case: 'bit'}, // 106 | LAB / -GAS
    {data: 'Propan_Butan', title: 'Propan_Butan', visible: false, case: 'num'}, // 107 | LAB / -GAS
    {data: 'N2H2', title: 'N2H2', visible: false, case: 'num'}, // 108 | LAB / -GAS
    {data: 'Inertgas', title: 'Inertgas', visible: false, case: 'num'}, // 109 | LAB / -GAS
    {data: 'Ar_CO2_Mix', title: 'AR CO2 Mix', visible: false, case: 'num'}, // 110 | LAB / -GAS
    {data: 'ArCal15', title: 'ArCal15', visible: false, case: 'num'}, // 111 | LAB / -GAS
    {data: 'O2_Mangel', title: 'O2_Mangel', visible: false, case: 'num'}, // 112 | LAB / -GAS
    {data: 'CO2_Melder', title: 'CO2_Melder', visible: false, case: 'num'}, // 113 | LAB / -GAS
    {data: 'NH3_Sensor', title: 'NH3_Sensor', visible: false, case: 'num'}, // 114 | LAB / -GAS
    {data: 'H2_Sensor', title: 'H2_Sensor', visible: false, case: 'num'}, // 115 | LAB / -GAS
    {data: 'O2_Sensor', title: 'O2_Sensor', visible: false, case: 'num'}, // 116 | LAB / -GAS
    {data: 'Acetylen_Melder', title: 'Acetylen_Melder', visible: false, case: 'num'}, // 117 | LAB / -GAS
    {data: 'Blitzleuchte', title: 'Blitzleuchte', visible: false, case: 'num'}, // 118 | LAB / -GAS
    {data: 'ET_PA_Stk', title: 'ET PA Stk', visible: false, case: "num"}, // 119 | LAB / -GAS-End
    {data: 'ET_16A_3Phasig_Einzelanschluss', title: '16A 3Ph Einzelanschluss', visible: false, case: "num"}, // 120 | LAB / -ET-Start
    {data: 'ET_32A_3Phasig_Einzelanschluss', title: '32A 3Ph Einzelanschluss', visible: false, case: "num"}, // 121 | LAB / -ET
    {data: 'ET_64A_3Phasig_Einzelanschluss', title: '64A 3Ph Einzelanschls', visible: false, case: "num"}, // 122 | LAB / -ET
    {data: 'ET_Digestorium_MSR_230V_SV_Stk', title: 'Digestorium_MSR 230V_SV_Stk', visible: false, case: "num"}, // 123 | LAB / -ET
    {data: 'ET_5x10mm2_Digestorium_Stk', title: '5x10mm2 Digestorium_Stk', visible: false, case: "num"}, // 124 | LAB / -ET
    {data: 'ET_5x10mm2_USV_Stk', title: '5x10mm2 USV_Stk', visible: false, case: "num"}, // 125 | LAB / -ET
    {data: 'ET_5x10mm2_SV_Stk', title: '5x10mm2 SV_Stk', visible: false, case: "num"}, // 126 | LAB / -ET
    {data: 'ET_5x10mm2_AV_Stk', title: '5x10mm2 AV_Stk', visible: false, case: "num"}, // 127 | LAB / -ET
    {data: 'EL_Not_Aus_Funktion', title: 'Not Aus Funktion', visible: false, case: ""}, // 128 | LAB / -ET
    {data: 'EL_Not_Aus', title: 'Not Aus Stk', visible: false, case: 'num'}, // 129 | LAB / -ET
    {data: 'EL_Signaleinrichtung', title: 'Signaleinrichtung', visible: false, case: 'bit'}, // 130 | LAB / -ET
    {data: 'HT_Luftwechsel 1/h', title: 'Luftwechsel l/h', visible: false, case: "num"}, // 131 | LAB / -ET-End
    {data: 'HT_Abluft_Vakuumpumpe', title: 'Abluft Vakuumpumpe', visible: false, case: "num"}, // 132 | LAB / -HT-Start
    {data: 'HT_Abluft_Sicherheitsschrank_Stk', title: 'Abluft Sicherheitsschrank Stk', visible: false, case: "num"}, // 133 | LAB / -HT
    {data: 'HT_Abluft_Schweissabsaugung_Stk', title: 'Abluft Schweissabsaugung_Stk', visible: false, case: "num"}, // 134 | LAB / -HT
    {data: 'HT_Abluft_Esse_Stk', title: 'Abluft Esse_Stk', visible: false, case: "num"}, // 135 | LAB / -HT
    {data: 'HT_Abluft_Rauchgasabzug_Stk', title: 'Abluft Rauchgasabzug_Stk', visible: false, case: "num"}, // 136 | LAB / -HT
    {data: 'HT_Abluft_Digestorium_Stk', title: 'Abluft Digestorium_Stk', visible: false, case: "num"}, // 137 | LAB / -HT
    {data: 'HT_Punktabsaugung_Stk', title: 'Punktabsaugung_Stk', visible: false, case: "num"}, // 138 | LAB / -HT
    {
        data: 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk',
        title: 'Abluft Sicherheitsschrank_Unterbau_Stk',
        visible: false,
        case: "num"
    }, // 139 | LAB / -HT
    {data: 'HT_Abwasser_Stk', title: 'Abwasser Stk', visible: false, case: "num"}, // 140 | LAB / -HT
    {data: 'HT_Abluft_Geraete', title: 'Abluft Geräte', visible: false, case: "num"}, // 141 | LAB / -HT-End
    {data: 'VE_Wasser', title: 'VE_Wasser', visible: false, case: 'bit'}, // 142 | LAB / -H2O-Start
    {data: 'HT_Warmwasser', title: 'Warmwasser', visible: false, case: "num"}, // 143 | LAB / -H2O
    {data: 'HT_Kaltwasser', title: 'Kaltwasser', visible: false, case: "num"}, // 144 | LAB / -H2O
    {data: 'Wasser Qual 1 l/Tag', title: 'H20_Q1 l/Tag', visible: false, case: "num"}, // 145 | LAB / -H2O
    {data: 'Wasser Qual 2 l/Tag', title: 'H20_Q2 l/Tag', visible: false, case: "num"}, // 146 | LAB / -H2O
    {data: 'Wasser Qual 3 l/min', title: 'H2O_Q3 l/min', visible: false, case: "num"}, // 147 | LAB / -H2O
    {data: 'Wasser Qual 3', title: 'H20 Q3', visible: false, case: "bit"}, // 148 | LAB / -H2O
    {data: 'Wasser Qual 2', title: 'H20 Q2', visible: false, case: "bit"}, // 149 | LAB / -H2O
    {data: 'Wasser Qual 1', title: 'H20 Q1', visible: false, case: "bit"}, // 150 | LAB-End / -H2O-End

    // GCP STISSL...
    {data: 'Fussboden', title: 'AR Fussboden', visible: false, case: ""}, // 151 | GCP-Start
    {data: 'Decke', title: 'AR Decke', visible: false, case: ""}, // 152 | GCP
    {data: 'Anmerkung AR', title: 'AR Anmerkung', visible: false, case: ""}, // 153 | GCP
    {data: 'Taetigkeiten', title: 'AR Taetigkeiten', visible: false, case: ""}, // 154 | GCP
    {data: 'AR_APs', title: 'APs Anzahl', visible: false, case: ""}, // 155 | GCP
    {data: 'AP_Gefaehrdung', title: 'AP_Gefaehrdung', visible: false, case: ""}, // 156 | GCP
    {data: 'AP_Geistige', title: 'AP Geistige', visible: false, case: ""}, // 157 | GCP
    {data: 'AR_Schwingungsklasse', title: 'AR Schwingungsklasse', visible: false, case: ""}, // 158 | GCP
    {data: 'Spezialgase', title: 'Spezialgase', visible: false, case: ""}, // 159 | GCP
    {data: 'Gaswarneinrichtung-Art', title: 'Gaswarneinrichtung-Art', visible: false, case: ""}, // 160 | GCP
    {data: 'EL_Beleuchtungsstaerke', title: 'EL_Beleuchtungsstaerke', visible: false, case: ""}, // 161 | GCP
    {data: 'ET_EMV', title: 'ET_E EMV Maßnahme Txt', visible: false, case: ""}, // 162 | GCP
    {data: 'HT_Luftmenge m3/h', title: 'HT_Luftmenge m3/h', visible: false, case: ""}, // 163 | GCP
    {data: 'HT_Kuehlung', title: 'HT_Kuehlung', visible: false, case: ""}, // 164 | GCP
    {data: 'HT_Kaelteabgabe_Typ', title: 'HT_Kaelteabgabe_Typ', visible: false, case: ""}, // 165 | GCP
    {data: 'HT_Heizung', title: 'HT_Heizung', visible: false, case: ""}, // 166 | GCP
    {data: 'HT_Waermeabgabe_Typ', title: 'HT_Waermeabgabe_Typ', visible: false, case: ""}, // 167 | GCP
    {data: 'HT_Belueftung', title: 'HT_Belueftung', visible: false, case: ""}, // 168 | GCP
    {data: 'HT_Entlueftung', title: 'HT_Entlueftung', visible: false, case: ""}, // 169 | GCP
    {data: 'PHY_Akustik_Schallgrad', title: 'PHY_Akustik_Schallgrad', visible: false, case: ""}, // 170 | GCP-End
]