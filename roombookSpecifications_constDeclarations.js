

function data2title(columnsDefinition, dataIdentifier) {
    const column = columnsDefinition.find(col => col.data === dataIdentifier);
    return column ? column.title : dataIdentifier;
}

function title2data(columnsDefinition, title) {
    const column = columnsDefinition.find(col => col.title === title);
    return column ? column.data : title;
}

const buttonRanges = [
    {name: 'Alle', start: 7, end: 131 + 3},
    {name: 'RAUM', start: 7, end: 23},
    {name: 'HKLS', start: 24, end: 30 + 2},
    {name: 'ELEK', start: 31 + 2, end: 50 + 2},
    {name: 'AR', start: 51 + 2, end: 52 + 2},
    {name: 'MEDGAS', start: 53 + 2, end: 64 + 2},
    {name: 'LAB', start: 65 + 2, end: 131},
    {name: 'L-GAS', start: 65 + 2, end: 65 + 41},
    {name: 'L-ET', start: 65 + 44, end: 65 + 41 + 12},
    {name: 'L-HT', start: 117 + 2, end: 124 + 2},
    {name: 'L-H2O', start: 125 + 3, end: 131 + 3}
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
    {data: "Bezeichnung", title: "Funktionsstelle", case: "none-edit"}, //#7
    {data: 'Funktionelle Raum Nr', title: 'Funkt.R.Nr'},
    {data: "Nummer", title: "DIN13080", case: "none-edit"},
    {data: 'Raumnummer_Nutzer', title: 'Raumnr Nutzer'},
    {data: 'Raumbereich Nutzer', title: 'Raumbereich'},
    {data: 'Geschoss', title: 'Geschoss'},
    {data: 'Bauetappe', title: 'Bauetappe'},
    {data: 'Bauabschnitt', title: 'Bauabschnitt'},
    {data: 'Nutzfläche', title: 'Nutzfläche', case: "num"},
    {data: 'Abdunkelbarkeit', title: 'Abdunkelbar', case: "bit"},
    {data: 'Strahlenanwendung', title: 'Strahlenanw.', case: "bit"},
    {data: 'Laseranwendung', title: 'Laseranw.', case: "bit"},
    {data: 'Allgemeine Hygieneklasse', title: 'Allg. Hygieneklasse'},
    {data: 'Raumhoehe', title: 'Raumhoehe', case: "num"},
    {data: 'Raumhoehe 2', title: 'Raumhoehe2', case: "num"},
    {data: 'Belichtungsfläche', title: 'Belichtungsfläche', case: "num"},
    {data: 'Umfang', title: 'Umfang', case: "num"},
    {data: 'Volumen', title: 'Volumen', case: "num"},
    //HKLS
    {data: 'H6020', title: 'H6020'},
    {data: 'GMP', title: 'GMP'},
    {data: 'ISO', title: 'ISO'},
    {data: 'HT_Waermeabgabe_W', title: 'Wärmeabgabe[W]', case: ""},
    {data: 'HT_Raumtemp Sommer °C', title: 'Raumtemp Sommer °C', case: "num"},
    {data: 'HT_Raumtemp Winter °C', title: 'Raumtemp Winter °C', case: "num"},
    {data: 'HT_Spuele_Stk', title: 'Spüle [Stk]', case: "num"},
    {data: 'HT_Kühlwasser', title: 'Kühlwasser', case: "num"},
    {data: 'HT_Notdusche', title: 'Notdusche', case: "bit"},
    //ET
    {data: 'Anwendungsgruppe', title: 'AWG'},
    {data: 'Fussboden OENORM B5220', title: 'B5220'},
    {data: 'AV', title: 'AV', case: "bit"},
    {data: 'SV', title: 'SV', case: "bit"},
    {data: 'ZSV', title: 'ZSV', case: "bit"},
    {data: 'USV', title: 'USV', case: "bit"},
    {data: 'EL_AV Steckdosen Stk', defaultContent: '-', title: 'AV #SSD', case: "num"},
    {data: 'EL_SV Steckdosen Stk', defaultContent: '-', title: 'SV #SSD', case: "num"},
    {data: 'EL_ZSV Steckdosen Stk', defaultContent: '-', title: 'ZSV #SSD', case: "num"},
    {data: 'EL_USV Steckdosen Stk', defaultContent: '-', title: 'USV #SSD', case: "num"},
    {data: 'EL_Roentgen 16A CEE Stk', title: 'CEE16A Röntgen', case: "num"},
    {data: 'EL_Laser 16A CEE Stk', defaultContent: '-', title: 'CEE16A Laser', case: "num"},
    {data: 'ET_Anschlussleistung_W', defaultContent: '-', title: 'Anschlussleistung Summe[W]', case: "num"},
    {data: 'ET_Anschlussleistung_AV_W', defaultContent: '-', title: 'Anschlussleistung AV[W]', case: "num"},
    {data: 'ET_Anschlussleistung_SV_W', defaultContent: '-', title: 'Anschlussleistung SV[W]', case: "num"},
    {data: 'ET_Anschlussleistung_ZSV_W', defaultContent: '-', title: 'Anschlussleistung ZSV[W]', case: "num"},
    {data: 'ET_Anschlussleistung_USV_W', defaultContent: '-', title: 'Anschlussleistung USV[W]', case: "num"},
    {data: 'IT Anbindung', title: 'IT', case: "bit"},
    {data: 'ET_RJ45-Ports', title: 'RJ45-Ports', case: "num"},
    {data: 'Laserklasse', title: 'Laserklasse'},

    //AR 
    {data: 'AR_AP_permanent', title: 'AR AP permanent', name: 'AR AP permanent ', case: "bit", render: function (data) {
            return data === '1' ? 'permanenter AP' : 'kein perma AP';
        }},
    {data: 'AR_Statik_relevant', title: 'AR Statik relevant', name: 'AR Statik relevant', case: "bit", render: function (data) {
            return data === '1' ? 'relevant' : 'nicht rel.';
        }},

    //MEDGASE
    {data: '1 Kreis O2', title: '1_K O2', case: "bit"},
    {data: '2 Kreis O2', title: '2_K O2', case: "bit"},
    {data: 'CO2', title: 'CO2', case: "bit"},
    {data: '1 Kreis Va', title: '1_K Va', case: "bit"},
    {data: '2 Kreis Va', title: '2_K Va', case: "bit"},
    {data: '1 Kreis DL-5', title: '1_K DL5', case: "bit"},
    {data: '2 Kreis DL-5', title: '2_K DL5', case: "bit"},
    {data: 'DL-10', title: 'DL-10', case: "bit"},
    {data: 'DL-tech', title: 'DL-tech', case: "bit"},
    {data: 'NGA', title: 'NGA', case: "bit"},
    {data: 'N2O', title: 'N2O', case: "bit"},
    {data: 'VEXAT_Zone', title: 'VEXAT Zone', case: "bit"},

    //LABORZ
    {data: 'O2', title: 'O2', case: "bit"},
    {data: 'O2 l/min', title: 'O2_l/min', case: "num"},
    {data: 'O2 Reinheit', title: 'O2 Reinheit', case: ""},

    {data: 'CO2 l/min', title: 'CO2_l/min'},
    {data: 'CO2 Reinheit', title: 'CO2 Reinheit', case: ""},

    {data: 'VA', title: 'VA', case: "bit"},
    {data: 'VA l/min', title: 'VA_l/min', case: "num"},

    {data: 'H2', title: 'H2', case: "bit"},
    {data: 'H2 Reinheit', title: 'H2 Reinheit', case: ""},
    {data: 'H2 l/min', title: 'H2_l/min', case: "num"},

    {data: 'He', title: 'He', case: "bit"},
    {data: 'He Reinheit', title: 'He Reinheit', case: ""},
    {data: 'He l/min', title: 'He_l/min', case: "num"},
    {data: 'He-RF', title: 'He-RF', case: "bit"},
    {data: 'LHe', title: 'LHe', case: "bit"},

    {data: 'Ar', title: 'Ar', case: "bit"},
    {data: 'Ar Reinheit', title: 'Ar Reinheit', case: ""},
    {data: 'Ar l/min', title: 'Ar_l/min', case: "num"},

    {data: 'LN', title: 'LN', case: "bit"},
    {data: 'LN l/Tag', title: 'LN l/Tag', case: "num"},

    {data: 'N2', title: 'N2', case: "bit"},
    {data: 'N2 Reinheit', title: 'N2 Reinheit', case: ""},
    {data: 'N2 l/min', title: 'N2_l/min', case: "num"},

    {data: 'DL-5', title: 'DL-5', case: "bit"},
    {data: 'DL ISO 8573', title: 'DL_ISO 8573', case: "bit"},
    {data: 'DL l/min', title: 'DL_l/min', case: "num"},

    {data: 'Kr', title: 'Kr', case: 'bit'},
    {data: 'Ne', title: 'Ne', case: 'bit'},
    {data: 'NH3', title: 'NH3', case: 'bit'},
    {data: 'C2H2', title: 'C2H2', case: 'bit'},
    {data: 'Propan_Butan', title: 'Propan_Butan', case: 'num'},
    {data: 'N2H2', title: 'N2H2', case: 'num'},
    {data: 'Inertgas', title: 'Inertgas', case: 'num'},
    {data: 'Ar_CO2_Mix', title: 'AR CO2 Mix', case: 'num'},
    {data: 'ArCal15', title: 'ArCal15', case: 'num'},

    {data: 'O2_Mangel', title: 'O2_Mangel', case: 'num'},
    {data: 'CO2_Melder', title: 'CO2_Melder', case: 'num'},
    {data: 'NH3_Sensor', title: 'NH3_Sensor', case: 'num'},
    {data: 'H2_Sensor', title: 'H2_Sensor', case: 'num'},
    {data: 'Blitzleuchte', title: 'Blitzleuchte', case: 'num'},
    {data: 'O2_Sensor', title: 'O2_Sensor', case: 'num'},
    {data: 'Acetylen_Melder', title: 'Acetylen_Melder', case: 'num'},
        
        
    {data: 'ET_PA_Stk', title: 'ET PA Stk', case: "num"},
    {data: 'ET_16A_3Phasig_Einzelanschluss', title: '16A 3Ph Einzelanschluss', case: "num"},
    {data: 'ET_32A_3Phasig_Einzelanschluss', title: '32A 3Ph Einzelanschluss', case: "num"},
    {data: 'ET_64A_3Phasig_Einzelanschluss', title: '64A 3Ph Einzelanschls', case: "num"},
    {data: 'ET_Digestorium_MSR_230V_SV_Stk', title: 'Digestorium_MSR 230V_SV_Stk', case: "num"},
    {data: 'ET_5x10mm2_Digestorium_Stk', title: '5x10mm2 Digestorium_Stk', case: "num"},
    {data: 'ET_5x10mm2_USV_Stk', title: '5x10mm2 USV_Stk', case: "num"},
    {data: 'ET_5x10mm2_SV_Stk', title: '5x10mm2 SV_Stk', case: "num"},
    {data: 'ET_5x10mm2_AV_Stk', title: '5x10mm2 AV_Stk', case: "num"},
    {data: 'EL_Not_Aus_Funktion', title: 'Not Aus Funktion', case: ""},
    {data: 'EL_Not_Aus', title: 'Not Aus Stk', case: 'num'},
        
    {data: 'HT_Kaltwasser', title: 'Kaltwasser', case: "num"},
    {data: 'HT_Luftwechsel 1/h', title: 'Luftwechsel l/h', case: "num"},
    {data: 'HT_Abluft_Vakuumpumpe', title: 'Abluft Vakuumpumpe', case: "num"},
    {data: 'HT_Abluft_Schweissabsaugung_Stk', title: 'Abluft Schweissabsaugung_Stk', case: "num"},
    {data: 'HT_Abluft_Esse_Stk', title: 'Abluft Esse_Stk', case: "num"},
    {data: 'HT_Abluft_Rauchgasabzug_Stk', title: 'Abluft Rauchgasabzug_Stk', case: "num"},
    {data: 'HT_Abluft_Digestorium_Stk', title: 'Abluft Digestorium_Stk', case: "num"},
    {data: 'HT_Punktabsaugung_Stk', title: 'Punktabsaugung_Stk', case: "num"},
    {data: 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk', title: 'Abluft Sicherheitsschrank_Unterbau_Stk', case: "num"},

    {data: 'Wasser Qual 1 l/Tag', title: 'H20_Q1 l/Tag', case: "num"},
    {data: 'Wasser Qual 2 l/Tag', title: 'H20_Q2 l/Tag', case: "num"},
    {data: 'Wasser Qual 3 l/min', title: 'H2O_Q3 l/min', case: "num"},
    {data: 'Wasser Qual 3', title: 'H20 Q3', case: "bit"},
    {data: 'Wasser Qual 2', title: 'H20 Q2', case: "bit"},
    {data: 'Wasser Qual 1', title: 'H20 Q1', case: "bit"},
    {data: 'VE_Wasser', title: 'VE_Wasser', case: 'bit'}
];