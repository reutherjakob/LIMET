

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

const columnsDefinition = [// NEW FIEL? - ADD Here, In get_rb_specs_data.php and the CPY/save methods
    {data: 'tabelle_projekte_idTABELLE_Projekte', title: 'Projek ID', visible: false, searchable: false},
    {data: 'idTABELLE_Räume', title: 'Raum ID', visible: false, searchable: false},
    {data: 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', title: 'Funktionsstellen ID', visible: false, searchable: false},
    {data: 'MT-relevant', title: 'MT-rel.', name: 'MT-relevant', case: "bit", render: function (data) {
            return data === '1' ? 'Ja' : 'Nein';
        }},
    {data: 'Raumbezeichnung', title: 'Raumbez.'},
    {data: 'Raumnr', title: 'Raumnr'},

    {data: "Bezeichnung", title: "Funktionsstelle", visible: true, case: "none-edit"}, //#7
    {data: 'Funktionelle Raum Nr', title: 'Funkt.R.Nr'},
    {data: "Nummer", title: "DIN13080", visible: false, case: "none-edit"},

    {data: "Entfallen", title: "Entfallen", name: "Entfallen", visible: false, case: "bit"},

    {data: 'Raumnummer_Nutzer', title: 'Raumnr Nutzer', visible: false},
    {data: 'Raumbereich Nutzer', title: 'Raumbereich', visible: false},
    {data: 'Geschoss', title: 'Geschoss', visible: false},
    {data: 'Bauetappe', title: 'Bauetappe', visible: false},
    {data: 'Bauabschnitt', title: 'Bauabschnitt', visible: false},
    {data: 'Nutzfläche', title: 'Nutzfläche', visible: false, case: "num"},
    {data: 'Abdunkelbarkeit', title: 'Abdunkelbar', visible: false, case: "bit"},
    {data: 'Strahlenanwendung', title: 'Strahlenanw.', visible: false, case: "bit"},
    {data: 'Laseranwendung', title: 'Laseranw.', visible: false, case: "bit"},
    {data: 'Allgemeine Hygieneklasse', title: 'Allg. Hygieneklasse', visible: false},
    {data: 'Raumhoehe', title: 'Raumhoehe', visible: false, case: "num"},
    {data: 'Raumhoehe 2', title: 'Raumhoehe2', visible: false, case: "num"},
    {data: 'Belichtungsfläche', title: 'Belichtungsfläche', visible: false, case: "num"},
    {data: 'Umfang', title: 'Umfang', visible: false, case: "num"},
    {data: 'Volumen', title: 'Volumen', visible: false, case: "num"},
    //HKLS
    {data: 'H6020', title: 'H6020', visible: false},
    {data: 'GMP', title: 'GMP', visible: false},
    {data: 'ISO', title: 'ISO', visible: false},
    {data: 'HT_Waermeabgabe_W', title: 'Wärmeabgabe[W]', visible: false, case: ""},
    {data: 'HT_Raumtemp Sommer °C', title: 'Raumtemp Sommer °C', visible: false, case: "num"},
    {data: 'HT_Raumtemp Winter °C', title: 'Raumtemp Winter °C', visible: false, case: "num"},
    {data: 'HT_Spuele_Stk', title: 'Spüle [Stk]', visible: false, case: "num"},
    {data: 'HT_Kühlwasser', title: 'Kühlwasser', visible: false, case: "num"},
    {data: 'HT_Notdusche', title: 'Notdusche', visible: false, case: "num"},
    {data: 'HT_Tempgradient_Ch', title: 'Tempgradient C/h', visible: false, case: "num"},

    //ET
    {data: 'Anwendungsgruppe', title: 'RG', visible: false},
    {data: 'Fussboden OENORM B5220', title: 'B5220', visible: false},
    {data: 'AV', title: 'AV', visible: false, defaultContent: '-', case: "bit"},
    {data: 'SV', title: 'SV', visible: false, defaultContent: '-', case: "bit"},
    {data: 'ZSV', title: 'ZSV', visible: false, defaultContent: '-', case: "bit"},
    {data: 'USV', title: 'USV', visible: false, defaultContent: '-', case: "bit"},
    {data: 'EL_AV Steckdosen Stk', defaultContent: '-', title: 'AV #SSD', visible: false, case: "num"},
    {data: 'EL_SV Steckdosen Stk', defaultContent: '-', title: 'SV #SSD', visible: false, case: "num"},
    {data: 'EL_ZSV Steckdosen Stk', defaultContent: '-', title: 'ZSV #SSD', visible: false, case: "num"},
    {data: 'EL_USV Steckdosen Stk', defaultContent: '-', title: 'USV #SSD', visible: false, case: "num"},
    {data: 'EL_Roentgen 16A CEE Stk', title: 'CEE16A Röntgen', visible: false, case: "num"},
    {data: 'EL_Laser 16A CEE Stk', defaultContent: '-', title: 'CEE16A Laser', visible: false, case: "num"},
    {data: 'ET_Anschlussleistung_W', defaultContent: '-', title: 'Anschlussleistung Summe[W]', visible: false, case: "num"},
    {data: 'ET_Anschlussleistung_AV_W', defaultContent: '-', title: 'Anschlussleistung AV[W]', visible: false, case: "num"},
    {data: 'ET_Anschlussleistung_SV_W', defaultContent: '-', title: 'Anschlussleistung SV[W]', visible: false, case: "num"},
    {data: 'ET_Anschlussleistung_ZSV_W', defaultContent: '-', title: 'Anschlussleistung ZSV[W]', visible: false, case: "num"},
    {data: 'ET_Anschlussleistung_USV_W', defaultContent: '-', title: 'Anschlussleistung USV[W]', visible: false, case: "num"},
    {data: 'IT Anbindung', title: 'IT', visible: false, case: "bit"},
    {data: 'ET_RJ45-Ports', title: 'RJ45-Ports', visible: false, case: "num"},
    {data: 'Laserklasse', title: 'Laserklasse', visible: false},
    {data: 'ET_EMV_ja-nein', title: 'ET EMV', visible: false, case: "bit"},

    //AR 
    {data: 'AR_AP_permanent', title: 'AR AP permanent', name: 'AR AP permanent ', visible: false, case: "bit", render: function (data) {
            return data === '1' ? 'permanenter AP' : 'kein perma AP';
        }},
    {data: 'AR_Statik_relevant', title: 'AR Statik relevant', name: 'AR Statik relevant', visible: false, case: "bit", render: function (data) {
            return data === '1' ? 'relevant' : 'nicht rel.';
        }},

    {data: 'AR_Empf_Breite_cm', defaultContent: '-', title: 'Empf. Breite [cm]', visible: false, case: "num"},
    {data: 'AR_Empf_Tiefe_cm', defaultContent: '-', title: 'Empf. Tiefe [cm]', visible: false, case: "num"},
    {data: 'AR_Empf_Hoehe_cm', defaultContent: '-', title: 'Empf. Höhe [cm]', visible: false, case: "num"},
    {data: 'AR_Flaechenlast_kgcm2', defaultContent: '-', title: 'Flaechenlast [kg/cm2]', visible: false, case: "num"},

    //MEDGASE
    {data: '1 Kreis O2', title: '1_K O2', visible: false, case: "bit"},
    {data: '2 Kreis O2', title: '2_K O2', visible: false, case: "bit"},
    {data: 'CO2', title: 'CO2', visible: false, case: "bit"},
    {data: '1 Kreis Va', title: '1_K Va', visible: false, case: "bit"},
    {data: '2 Kreis Va', title: '2_K Va', visible: false, case: "bit"},
    {data: '1 Kreis DL-5', title: '1_K DL5', visible: false, case: "bit"},
    {data: '2 Kreis DL-5', title: '2_K DL5', visible: false, case: "bit"},
    {data: 'DL-10', title: 'DL-10', visible: false, case: "bit"},
    {data: 'DL-tech', title: 'DL-tech', visible: false, case: "bit"},
    {data: 'NGA', title: 'NGA', visible: false, case: "bit"},
    {data: 'N2O', title: 'N2O', visible: false, case: "bit"},
    {data: 'VEXAT_Zone', title: 'VEXAT Zone', visible: false, case: "bit"},

    //LABORZ
    {data: 'O2', title: 'O2', visible: false, case: "bit"},
    {data: 'O2 l/min', title: 'O2_l/min', visible: false, case: "num"},
    {data: 'O2 Reinheit', title: 'O2 Reinheit', visible: false, case: ""},

    {data: 'CO2 l/min', title: 'CO2_l/min', visible: false},
    {data: 'CO2 Reinheit', title: 'CO2 Reinheit', visible: false, case: ""},

    {data: 'VA', title: 'VA', visible: false, case: "bit"},
    {data: 'VA l/min', title: 'VA_l/min', visible: false, case: "num"},

    {data: 'H2', title: 'H2', visible: false, case: "bit"},
    {data: 'H2 Reinheit', title: 'H2 Reinheit', visible: false, case: ""},
    {data: 'H2 l/min', title: 'H2_l/min', visible: false, case: "num"},

    {data: 'He', title: 'He', visible: false, case: "bit"},
    {data: 'He Reinheit', title: 'He Reinheit', visible: false, case: ""},
    {data: 'He l/min', title: 'He_l/min', visible: false, case: "num"},
    {data: 'He-RF', title: 'He-RF', visible: false, case: "bit"},
    {data: 'LHe', title: 'LHe', visible: false, case: "bit"},

    {data: 'Ar', title: 'Ar', visible: false, case: "bit"},
    {data: 'Ar Reinheit', title: 'Ar Reinheit', visible: false, case: ""},
    {data: 'Ar l/min', title: 'Ar_l/min', visible: false, case: "num"},

    {data: 'LN', title: 'LN', visible: false, case: "bit"},
    {data: 'LN l/Tag', title: 'LN l/Tag', visible: false, case: "num"},

    {data: 'N2', title: 'N2', visible: false, case: "bit"},
    {data: 'N2 Reinheit', title: 'N2 Reinheit', visible: false, case: ""},
    {data: 'N2 l/min', title: 'N2_l/min', visible: false, case: "num"},

    {data: 'DL-5', title: 'DL-5', visible: false, case: "bit"},
    {data: 'DL ISO 8573', title: 'DL_ISO 8573', visible: false, case: "bit"},
    {data: 'DL l/min', title: 'DL_l/min', visible: false, case: "num"},

    {data: 'Kr', title: 'Kr', visible: false, case: 'bit'},
    {data: 'Ne', title: 'Ne', visible: false, case: 'bit'},
    {data: 'NH3', title: 'NH3', visible: false, case: 'bit'},
    {data: 'C2H2', title: 'C2H2', visible: false, case: 'bit'},
    {data: 'Propan_Butan', title: 'Propan_Butan', visible: false, case: 'num'},
    {data: 'N2H2', title: 'N2H2', visible: false, case: 'num'},
    {data: 'Inertgas', title: 'Inertgas', visible: false, case: 'num'},
    {data: 'Ar_CO2_Mix', title: 'AR CO2 Mix', visible: false, case: 'num'},
    {data: 'ArCal15', title: 'ArCal15', visible: false, case: 'num'},

    {data: 'O2_Mangel', title: 'O2_Mangel', visible: false, case: 'num'},
    {data: 'CO2_Melder', title: 'CO2_Melder', visible: false, case: 'num'},
    {data: 'NH3_Sensor', title: 'NH3_Sensor', visible: false, case: 'num'},
    {data: 'H2_Sensor', title: 'H2_Sensor', visible: false, case: 'num'},

    {data: 'O2_Sensor', title: 'O2_Sensor', visible: false, case: 'num'},
    {data: 'Acetylen_Melder', title: 'Acetylen_Melder', visible: false, case: 'num'},

    {data: 'Blitzleuchte', title: 'Blitzleuchte', visible: false, case: 'num'},

    {data: 'ET_PA_Stk', title: 'ET PA Stk', visible: false, case: "num"},
    {data: 'ET_16A_3Phasig_Einzelanschluss', title: '16A 3Ph Einzelanschluss', visible: false, case: "num"},
    {data: 'ET_32A_3Phasig_Einzelanschluss', title: '32A 3Ph Einzelanschluss', visible: false, case: "num"},
    {data: 'ET_64A_3Phasig_Einzelanschluss', title: '64A 3Ph Einzelanschls', visible: false, case: "num"},
    {data: 'ET_Digestorium_MSR_230V_SV_Stk', title: 'Digestorium_MSR 230V_SV_Stk', visible: false, case: "num"},
    {data: 'ET_5x10mm2_Digestorium_Stk', title: '5x10mm2 Digestorium_Stk', visible: false, case: "num"},
    {data: 'ET_5x10mm2_USV_Stk', title: '5x10mm2 USV_Stk', visible: false, case: "num"},
    {data: 'ET_5x10mm2_SV_Stk', title: '5x10mm2 SV_Stk', visible: false, case: "num"},
    {data: 'ET_5x10mm2_AV_Stk', title: '5x10mm2 AV_Stk', visible: false, case: "num"},
    {data: 'EL_Not_Aus_Funktion', title: 'Not Aus Funktion', visible: false, case: ""},
    {data: 'EL_Not_Aus', title: 'Not Aus Stk', visible: false, case: 'num'},

    {data: 'HT_Luftwechsel 1/h', title: 'Luftwechsel l/h', visible: false, case: "num"},

    {data: 'HT_Abluft_Vakuumpumpe', title: 'Abluft Vakuumpumpe', visible: false, case: "num"},
    {data: 'HT_Abluft_Sicherheitsschrank_Stk', title: 'Abluft Sicherheitsschrank Stk', visible: false, case: "num"},
    {data: 'HT_Abluft_Schweissabsaugung_Stk', title: 'Abluft Schweissabsaugung_Stk', visible: false, case: "num"},
    {data: 'HT_Abluft_Esse_Stk', title: 'Abluft Esse_Stk', visible: false, case: "num"},
    {data: 'HT_Abluft_Rauchgasabzug_Stk', title: 'Abluft Rauchgasabzug_Stk', visible: false, case: "num"},
    {data: 'HT_Abluft_Digestorium_Stk', title: 'Abluft Digestorium_Stk', visible: false, case: "num"},
    {data: 'HT_Punktabsaugung_Stk', title: 'Punktabsaugung_Stk', visible: false, case: "num"},
    {data: 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk', title: 'Abluft Sicherheitsschrank_Unterbau_Stk', visible: false, case: "num"},

    {data: 'HT_Abwasser_Stk', title: 'Abwasser Stk', visible: false, case: "num"},

    {data: 'HT_Abluft_Geraete', title: 'Abluft Geräte', visible: false, case: "num"},

    {data: 'VE_Wasser', title: 'VE_Wasser', visible: false, case: 'num'},
    {data: 'HT_Warmwasser', title: 'Warmwasser', visible: false, case: "num"},
    {data: 'HT_Kaltwasser', title: 'Kaltwasser', visible: false, case: "num"},
    {data: 'Wasser Qual 1 l/Tag', title: 'H20_Q1 l/Tag', visible: false, case: "num"},
    {data: 'Wasser Qual 2 l/Tag', title: 'H20_Q2 l/Tag', visible: false, case: "num"},
    {data: 'Wasser Qual 3 l/min', title: 'H2O_Q3 l/min', visible: false, case: "num"},
    {data: 'Wasser Qual 3', title: 'H20 Q3', visible: false, case: "bit"},
    {data: 'Wasser Qual 2', title: 'H20 Q2', visible: false, case: "bit"},
    {data: 'Wasser Qual 1', title: 'H20 Q1', visible: false, case: "bit"}

];



