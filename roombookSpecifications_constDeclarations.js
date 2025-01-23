function data2title(columnsDefinition, dataIdentifier) {
    const column = columnsDefinition.find(col => col.data === dataIdentifier);
    return column ? column.title : dataIdentifier;
}

function title2data(columnsDefinition, title) {
    const column = columnsDefinition.find(col => col.title === title);
    return column ? column.data : title;
}


const buttonRanges = [
    {name: 'All', start: 6, end: 146, longName: 'Alle Spalten'},
    {name: 'R', start: 7, end: 24, longName: 'Raum'},
    {name: 'HKLS', start: 25, end: 34, longName: 'HKLS'},
    {name: 'ET', start: 35, end: 55, longName: 'Elektro'},
    {name: 'AR', start: 56, end: 61, longName: 'Architektur'},
    {name: 'MG', start: 62, end: 73, longName: 'Medgas'},
    {name: 'LAB', start: 64, end: 146, longName: 'Labor'},
    {name: '-GAS', start: 74, end: 115, longName: 'Labor-GAS'},
    {name: '-ET', start: 116, end: 126, longName: 'Labor-ET'},
    {name: '-HT', start: 127, end: 137, longName: 'Labor-HT'},
    {name: '-H2O', start: 138, end: 146, longName: 'Labor-H2O'}
];

const columnsDefinition = [
    {index: 0, data: 'tabelle_projekte_idTABELLE_Projekte', title: 'Projek ID', visible: false, searchable: false},
    {index: 1, data: 'idTABELLE_Räume', title: 'Raum ID', visible: false, searchable: false},
    {
        index: 2,
        data: 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen',
        title: 'Funktionsstellen ID',
        visible: false,
        searchable: false
    },
    {
        index: 3, data: 'MT-relevant', title: 'MT-rel.', name: 'MT-relevant', case: "bit", render: function (data) {
            return data === '1' ? 'Ja' : 'Nein';
        }
    },
    {index: 4, data: 'Raumbezeichnung', title: 'Raumbez.'},
    {index: 5, data: 'Raumnr', title: 'Raumnr'},
    {index: 6, data: "Bezeichnung", title: "Funktionsstelle", visible: true, case: "none-edit"},
    {index: 7, data: 'Funktionelle Raum Nr', title: 'Funkt.R.Nr'},
    {index: 8, data: "Nummer", title: "DIN13080", visible: false, case: "none-edit"},
    {index: 9, data: "Entfallen", title: "Entfallen", name: "Entfallen", visible: false, case: "bit"},
    {index: 10, data: 'Raumnummer_Nutzer', title: 'Raumnr Nutzer', visible: false},
    {index: 11, data: 'Raumbereich Nutzer', title: 'Raumbereich', visible: false},
    {index: 12, data: 'Geschoss', title: 'Geschoss', visible: false},
    {index: 13, data: 'Bauetappe', title: 'Bauetappe', visible: false},
    {index: 14, data: 'Bauabschnitt', title: 'Bauabschnitt', visible: false},
    {index: 15, data: 'Nutzfläche', title: 'Nutzfläche', visible: false, case: "num"},
    {
        index: 16, data: 'Abdunkelbarkeit', title: 'Abdunkelbar', visible: false, case: "abd", render: function (data) {
            if (data === "0") {
                return "kein Anspruch";
            } else if (data === "1") {
                return "vollverdunkelbar";
            } else {
                return "abdunkelbar";
            }
        }
    },
    {index: 17, data: 'Strahlenanwendung', title: 'Strahlenanw.', visible: false, case: "bit"},
    {index: 18, data: 'Laseranwendung', title: 'Laseranw.', visible: false, case: "bit"},
    {index: 19, data: 'Allgemeine Hygieneklasse', title: 'Allg. Hygieneklasse', visible: false},
    {index: 20, data: 'Raumhoehe', title: 'Raumhoehe', visible: false, case: "num"},
    {index: 21, data: 'Raumhoehe 2', title: 'Raumhoehe2', visible: false, case: "num"},
    {index: 22, data: 'Belichtungsfläche', title: 'Belichtungsfläche', visible: false, case: "num"},
    {index: 23, data: 'Umfang', title: 'Umfang', visible: false, case: "num"},
    {index: 24, data: 'Volumen', title: 'Volumen', visible: false, case: "num"},
    {index: 25, data: 'H6020', title: 'H6020', visible: false},
    {index: 26, data: 'GMP', title: 'GMP', visible: false},
    {index: 27, data: 'ISO', title: 'ISO', visible: false},
    {index: 28, data: 'HT_Waermeabgabe_W', title: 'Wärmeabgabe[W]', visible: false, case: ""},
    {index: 29, data: 'HT_Raumtemp Sommer °C', title: 'Raumtemp Sommer °C', visible: false, case: "num"},
    {index: 30, data: 'HT_Raumtemp Winter °C', title: 'Raumtemp Winter °C', visible: false, case: "num"},
    {index: 31, data: 'HT_Spuele_Stk', title: 'Spüle [Stk]', visible: false, case: "num"},
    {index: 32, data: 'HT_Kühlwasser', title: 'Kühlwasser', visible: false, case: "num"},
    {index: 33, data: 'HT_Notdusche', title: 'Notdusche', visible: false, case: "num"},
    {index: 34, data: 'HT_Tempgradient_Ch', title: 'Tempgradient C/h', visible: false, case: "num"},
    {index: 35, data: 'Anwendungsgruppe', title: 'RG', visible: false},
    {index: 36, data: 'Fussboden OENORM B5220', title: 'B5220', visible: false},
    {index: 37, data: 'AV', title: 'AV', visible: false, defaultContent: '-', case: "bit"},
    {index: 38, data: 'SV', title: 'SV', visible: false, defaultContent: '-', case: "bit"},
    {index: 39, data: 'ZSV', title: 'ZSV', visible: false, defaultContent: '-', case: "bit"},
    {index: 40, data: 'USV', title: 'USV', visible: false, defaultContent: '-', case: "bit"},
    {index: 41, data: 'EL_AV Steckdosen Stk', defaultContent: '-', title: 'AV #SSD', visible: false, case: "num"},
    {index: 42, data: 'EL_SV Steckdosen Stk', defaultContent: '-', title: 'SV #SSD', visible: false, case: "num"},
    {index: 43, data: 'EL_ZSV Steckdosen Stk', defaultContent: '-', title: 'ZSV #SSD', visible: false, case: "num"},
    {index: 44, data: 'EL_USV Steckdosen Stk', defaultContent: '-', title: 'USV #SSD', visible: false, case: "num"},
    {index: 45, data: 'EL_Roentgen 16A CEE Stk', title: 'CEE16A Röntgen', visible: false, case: "num"},
    {index: 46, data: 'EL_Laser 16A CEE Stk', defaultContent: '-', title: 'CEE16A Laser', visible: false, case: "num"},
    {
        index: 47,
        data: 'ET_Anschlussleistung_W',
        defaultContent: '-',
        title: 'Anschlussleistung Summe[W]',
        visible: false,
        case: "num"
    },
    {
        index: 48,
        data: 'ET_Anschlussleistung_AV_W',
        defaultContent: '-',
        title: 'Anschlussleistung AV[W]',
        visible: false,
        case: "num"
    },
    {
        index: 49,
        data: 'ET_Anschlussleistung_SV_W',
        defaultContent: '-',
        title: 'Anschlussleistung SV[W]',
        visible: false,
        case: "num"
    },
    {
        index: 50,
        data: 'ET_Anschlussleistung_ZSV_W',
        defaultContent: '-',
        title: 'Anschlussleistung ZSV[W]',
        visible: false,
        case: "num"
    },
    {
        index: 51,
        data: 'ET_Anschlussleistung_USV_W',
        defaultContent: '-',
        title: 'Anschlussleistung USV[W]',
        visible: false,
        case: "num"
    },
    {index: 52, data: 'IT Anbindung', title: 'IT', visible: false, case: "bit"},
    {index: 53, data: 'ET_RJ45-Ports', title: 'RJ45-Ports', visible: false, case: "num"},
    {index: 54, data: 'Laserklasse', title: 'Laserklasse', visible: false},
    {index: 55, data: 'ET_EMV_ja-nein', title: 'ET EMV', visible: false, case: "bit"},
    {
        index: 56,
        data: 'AR_AP_permanent',
        title: 'AR AP permanent',
        name: 'AR AP permanent ',
        visible: false,
        case: "bit",
        render: function (data) {
            return data === '1' ? 'permanenter AP' : 'kein perma AP';
        }
    },
    {
        index: 57,
        data: 'AR_Empf_Breite_cm',
        defaultContent: '-',
        title: 'Empf. Breite [cm]',
        visible: false,
        case: "num"
    },
    {index: 58, data: 'AR_Empf_Tiefe_cm', defaultContent: '-', title: 'Empf. Tiefe [cm]', visible: false, case: "num"},
    {index: 59, data: 'AR_Empf_Hoehe_cm', defaultContent: '-', title: 'Empf. Höhe [cm]', visible: false, case: "num"},
    {
        index: 60,
        data: 'AR_Flaechenlast_kgcm2',
        defaultContent: '-',
        title: 'Flaechenlast [kg/cm2]',
        visible: false,
        case: "num"
    },
    {index: 61, data: '1 Kreis O2', title: '1_K O2', visible: false, case: "bit"},
    {index: 62, data: '2 Kreis O2', title: '2_K O2', visible: false, case: "bit"},
    {index: 63, data: 'CO2', title: 'CO2', visible: false, case: "bit"},
    {index: 64, data: '1 Kreis Va', title: '1_K Va', visible: false, case: "bit"},
    {index: 65, data: '2 Kreis Va', title: '2_K Va', visible: false, case: "bit"},
    {index: 66, data: '1 Kreis DL-5', title: '1_K DL5', visible: false, case: "bit"},
    {index: 67, data: '2 Kreis DL-5', title: '2_K DL5', visible: false, case: "bit"},
    {index: 68, data: 'DL-10', title: 'DL-10', visible: false, case: "bit"},
    {index: 69, data: 'DL-tech', title: 'DL-tech', visible: false, case: "bit"},
    {index: 70, data: 'NGA', title: 'NGA', visible: false, case: "bit"},
    {index: 71, data: 'N2O', title: 'N2O', visible: false, case: "bit"},
    {index: 72, data: 'VEXAT_Zone', title: 'VEXAT Zone', visible: false, case: "bit"},
    {index: 73, data: 'O2', title: 'O2', visible: false, case: "bit"},
    {index: 74, data: 'O2 l/min', title: 'O2_l/min', visible: false, case: "num"},
    {index: 75, data: 'O2 Reinheit', title: 'O2 Reinheit', visible: false, case: ""},
    {index: 76, data: 'CO2 l/min', title: 'CO2_l/min', visible: false},
    {index: 77, data: 'CO2 Reinheit', title: 'CO2 Reinheit', visible: false, case: ""},
    {index: 78, data: 'VA', title: 'VA', visible: false, case: "bit"},
    {index: 79, data: 'VA l/min', title: 'VA_l/min', visible: false, case: "num"},
    {index: 80, data: 'H2', title: 'H2', visible: false, case: "bit"},
    {index: 81, data: 'H2 Reinheit', title: 'H2 Reinheit', visible: false, case: ""},
    {index: 82, data: 'H2 l/min', title: 'H2_l/min', visible: false, case: "num"},
    {index: 83, data: 'He', title: 'He', visible: false, case: "bit"},
    {index: 84, data: 'He Reinheit', title: 'He Reinheit', visible: false, case: ""},
    {index: 85, data: 'He l/min', title: 'He_l/min', visible: false, case: "num"},
    {index: 86, data: 'He-RF', title: 'He-RF', visible: false, case: "bit"},
    {index: 87, data: 'LHe', title: 'LHe', visible: false, case: "bit"},
    {index: 88, data: 'Ar', title: 'Ar', visible: false, case: "bit"},
    {index: 89, data: 'Ar Reinheit', title: 'Ar Reinheit', visible: false, case: ""},
    {index: 90, data: 'Ar l/min', title: 'Ar_l/min', visible: false, case: "num"},
    {index: 91, data: 'LN', title: 'LN', visible: false, case: "bit"},
    {index: 92, data: 'LN l/Tag', title: 'LN l/Tag', visible: false, case: "num"},
    {index: 93, data: 'N2', title: 'N2', visible: false, case: "bit"},
    {index: 94, data: 'N2 Reinheit', title: 'N2 Reinheit', visible: false, case: ""},
    {index: 95, data: 'N2 l/min', title: 'N2_l/min', visible: false, case: "num"},
    {index: 96, data: 'DL-5', title: 'DL-5', visible: false, case: "bit"},
    {index: 97, data: 'DL ISO 8573', title: 'DL_ISO 8573', visible: false, case: "bit"},
    {index: 98, data: 'DL l/min', title: 'DL_l/min', visible: false, case: "num"},
    {index: 99, data: 'Kr', title: 'Kr', visible: false, case: 'bit'},
    {index: 100, data: 'Ne', title: 'Ne', visible: false, case: 'bit'},
    {index: 101, data: 'NH3', title: 'NH3', visible: false, case: 'bit'},
    {index: 102, data: 'C2H2', title: 'C2H2', visible: false, case: 'bit'},
    {index: 103, data: 'Propan_Butan', title: 'Propan_Butan', visible: false, case: 'num'},
    {index: 104, data: 'N2H2', title: 'N2H2', visible: false, case: 'num'},
    {index: 105, data: 'Inertgas', title: 'Inertgas', visible: false, case: 'num'},
    {index: 106, data: 'Ar_CO2_Mix', title: 'AR CO2 Mix', visible: false, case: 'num'},
    {index: 107, data: 'ArCal15', title: 'ArCal15', visible: false, case: 'num'},
    {index: 108, data: 'O2_Mangel', title: 'O2_Mangel', visible: false, case: 'num'},
    {index: 109, data: 'CO2_Melder', title: 'CO2_Melder', visible: false, case: 'num'},
    {index: 110, data: 'NH3_Sensor', title: 'NH3_Sensor', visible: false, case: 'num'},
    {index: 111, data: 'H2_Sensor', title: 'H2_Sensor', visible: false, case: 'num'},
    {index: 112, data: 'O2_Sensor', title: 'O2_Sensor', visible: false, case: 'num'},
    {index: 113, data: 'Acetylen_Melder', title: 'Acetylen_Melder', visible: false, case: 'num'},
    {index: 114, data: 'Blitzleuchte', title: 'Blitzleuchte', visible: false, case: 'num'},
    {index: 115, data: 'ET_PA_Stk', title: 'ET PA Stk', visible: false, case: "num"},
    {index: 116, data: 'ET_16A_3Phasig_Einzelanschluss', title: '16A 3Ph Einzelanschluss', visible: false, case: "num"},
    {index: 117, data: 'ET_32A_3Phasig_Einzelanschluss', title: '32A 3Ph Einzelanschluss', visible: false, case: "num"},
    {index: 118, data: 'ET_64A_3Phasig_Einzelanschluss', title: '64A 3Ph Einzelanschls', visible: false, case: "num"},
    {
        index: 119,
        data: 'ET_Digestorium_MSR_230V_SV_Stk',
        title: 'Digestorium_MSR 230V_SV_Stk',
        visible: false,
        case: "num"
    }, {index: 120, data: 'ET_5x10mm2_Digestorium_Stk', title: '5x10mm2 Digestorium_Stk', visible: false, case: "num"},
    {index: 121, data: 'ET_5x10mm2_USV_Stk', title: '5x10mm2 USV_Stk', visible: false, case: "num"},
    {index: 122, data: 'ET_5x10mm2_SV_Stk', title: '5x10mm2 SV_Stk', visible: false, case: "num"},
    {index: 123, data: 'ET_5x10mm2_AV_Stk', title: '5x10mm2 AV_Stk', visible: false, case: "num"},
    {index: 124, data: 'EL_Not_Aus_Funktion', title: 'Not Aus Funktion', visible: false, case: ""},
    {index: 125, data: 'EL_Not_Aus', title: 'Not Aus Stk', visible: false, case: 'num'},
    {index: 126, data: 'HT_Luftwechsel 1/h', title: 'Luftwechsel l/h', visible: false, case: "num"},
    {index: 127, data: 'HT_Abluft_Vakuumpumpe', title: 'Abluft Vakuumpumpe', visible: false, case: "num"},
    {
        index: 128,
        data: 'HT_Abluft_Sicherheitsschrank_Stk',
        title: 'Abluft Sicherheitsschrank Stk',
        visible: false,
        case: "num"
    },
    {
        index: 129,
        data: 'HT_Abluft_Schweissabsaugung_Stk',
        title: 'Abluft Schweissabsaugung_Stk',
        visible: false,
        case: "num"
    },
    {index: 130, data: 'HT_Abluft_Esse_Stk', title: 'Abluft Esse_Stk', visible: false, case: "num"},
    {index: 131, data: 'HT_Abluft_Rauchgasabzug_Stk', title: 'Abluft Rauchgasabzug_Stk', visible: false, case: "num"},
    {index: 132, data: 'HT_Abluft_Digestorium_Stk', title: 'Abluft Digestorium_Stk', visible: false, case: "num"},
    {index: 133, data: 'HT_Punktabsaugung_Stk', title: 'Punktabsaugung_Stk', visible: false, case: "num"},
    {
        index: 134,
        data: 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk',
        title: 'Abluft Sicherheitsschrank_Unterbau_Stk',
        visible: false,
        case: "num"
    },
    {index: 135, data: 'HT_Abwasser_Stk', title: 'Abwasser Stk', visible: false, case: "num"},
    {index: 136, data: 'HT_Abluft_Geraete', title: 'Abluft Geräte', visible: false, case: "num"},
    {index: 137, data: 'VE_Wasser', title: 'VE_Wasser', visible: false, case: 'num'},
    {index: 138, data: 'HT_Warmwasser', title: 'Warmwasser', visible: false, case: "num"},
    {index: 139, data: 'HT_Kaltwasser', title: 'Kaltwasser', visible: false, case: "num"},
    {index: 140, data: 'Wasser Qual 1 l/Tag', title: 'H20_Q1 l/Tag', visible: false, case: "num"},
    {index: 141, data: 'Wasser Qual 2 l/Tag', title: 'H20_Q2 l/Tag', visible: false, case: "num"},
    {index: 142, data: 'Wasser Qual 3 l/min', title: 'H2O_Q3 l/min', visible: false, case: "num"},
    {index: 143, data: 'Wasser Qual 3', title: 'H20 Q3', visible: false, case: "bit"},
    {index: 144, data: 'Wasser Qual 2', title: 'H20 Q2', visible: false, case: "bit"},
    {index: 145, data: 'Wasser Qual 1', title: 'H20 Q1', visible: false, case: "bit"}
];


