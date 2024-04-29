 

function data2title(columnsDefinition, dataIdentifier) {
    const column = columnsDefinition.find(col => col.data === dataIdentifier);
    return column ? column.title : dataIdentifier;
}

function title2data(columnsDefinition, title) {
    const column = columnsDefinition.find(col => col.title === title);
    return column ? column.data : title;
}

const buttonRanges = [
    {name: 'RAUM', start: 7, end: 23},
    {name: 'HKLS', start: 24, end: 29}, 
    {name: 'ELEK', start: 30, end: 38},
    {name: 'MEDGAS', start: 39, end: 52},
    {name: 'LAB', start: 53, end: 101},
    {name: 'L-GAS', start: 55, end: 79},
    {name: 'L-ET', start: 80, end: 87},
    {name: 'L-HT', start: 88, end: 95},
    {name: 'L-H2O', start: 95, end: 101} 
];

// define cases for data formater switch: numerical, 1/0 aka bit, dropdowner; non-edit-> cell wont be editable
const columnsDefinition = [
//    { data: '', defaultContent: '', title: "Select", render:  $.fn.dataTable.render.select(), searchable: false, orderable: false }, //cool, but buggy with fix columns. 
    {data: 'tabelle_projekte_idTABELLE_Projekte', title: 'Projek ID', visible: false, searchable: false},
    {data: 'idTABELLE_Räume', title: 'Raum ID', visible: false, searchable: false},
    {data: 'TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen', title: 'Funktionsstellen ID', visible: false, searchable: false},
    {data: 'MT-relevant', title: 'MT-rel.', name: 'MT-relevant', case: "bit", render: function (data) {
            return data === '1' ? 'Ja' : 'Nein';
        }},
    //{data: 'idTABELLE_Räume', title: 'Raum ID', searchable: false}, //debugging
    {data: 'Raumbezeichnung', title: 'Raumbez.'},    
    {data: "Nummer", title: "DIN13080", case :"none-edit"},
    {data: "Bezeichnung", title: "Funktionsstelle", case :"none-edit"}, //#7
    {data: 'Raumnr', title: 'Raumnr'},
    {data: 'Funktionelle Raum Nr', title: 'Funkt.R.Nr'}, 
    {data: 'Raumnummer_Nutzer', title: 'Raumnr Nutzer'},
    {data: 'Raumbereich Nutzer', title: 'Raumbereich'},
    {data: 'Geschoss', title: 'Geschoss'},
    {data: 'Bauetappe', title: 'Bauetappe'},
    {data: 'Bauabschnitt', title: 'Bauabschnitt'},
    {data: 'Nutzfläche', title: 'Nutzfläche', case: "num"},
    {data: 'Abdunkelbarkeit', title: 'Abdunkelbar',  case: "bit"}, 
    {data: 'Strahlenanwendung', title: 'Strahlenanw.',  case: "bit"},
    {data: 'Laseranwendung', title: 'Laseranw.',  case: "bit"},
    {data: 'Allgemeine Hygieneklasse', title: 'Allg. Hygieneklasse'},
    {data: 'Raumhoehe', title: 'Raumhoehe', case: "num"},
    {data: 'Raumhoehe 2', title: 'Raumhoehe2', case: "num"},
    {data: 'Belichtungsfläche', title: 'Belichtungsfläche', case: "num"},
    {data: 'Umfang', title: 'Umfang', case: "num"},
    {data: 'Volumen', title: 'Volumen', case: "num"},
        
    {data: 'H6020', title: 'H6020'},
    {data: 'GMP', title: 'GMP'},
    {data: 'ISO', title: 'ISO'},
    {data: 'HT_Waermeabgabe_W', title: 'HT_Waermeabgabe_W', case: "num"},
    {data: 'HT_Spuele_Stk', title: 'HT_Spuele_Stk', case: "num"},
    {data: 'HT_Kühlwasser', title: 'HT_Kühlwasser',  case: "bit"},
        
    {data: 'Anwendungsgruppe', title: 'AWG'},
    {data: 'AV', title: 'AV',  case: "bit"},
    {data: 'SV', title: 'SV',  case: "bit"},
    {data: 'ZSV', title: 'ZSV',  case: "bit"},
    {data: 'USV', title: 'USV',  case: "bit"},
    {data: 'IT Anbindung', title: 'IT', case: "bit"},
    {data: 'ET_Anschlussleistung_W', title: 'ET_Anschlussleistung_W', case: "num"},
    {data: 'ET_RJ45-Ports', title: 'ET_RJ45-Ports',  case: "bit"},
    {data: 'Fussboden OENORM B5220', title: 'B5220'},
        
    {data: '1 Kreis O2', title: '1_K O2',  case: "bit"},
    {data: '2 Kreis O2', title: '2_K O2',  case: "bit"},
    {data: 'O2', title: 'O2',  case: "bit"},
    {data: '1 Kreis Va', title: '1_K Va',  case: "bit"},
    {data: '2 Kreis Va', title: '2_K Va',  case: "bit"},
    {data: 'VA', title: 'VA',  case: "bit"},
    {data: '1 Kreis DL-5', title: '1_K DL5',  case: "bit"},
    {data: '2 Kreis DL-5', title: '2_K DL5',  case: "bit"},
    {data: 'DL-5', title: 'DL-5',  case: "bit"},
    {data: 'DL-10', title: 'DL-10',  case: "bit"},
    {data: 'DL-tech', title: 'DL-tech',  case: "bit"},
    {data: 'CO2', title: 'CO2',  case: "bit"},
    {data: 'NGA', title: 'NGA',  case: "bit"},
    {data: 'N2O', title: 'N2O',  case: "bit"},
    
  
    {data: 'VEXAT_Zone', title: 'VEXAT_Zone',  case: "bit"},
    {data: 'Laserklasse', title: 'Laserklasse'},
    
    {data: 'H2', title: 'H2',  case: "bit"},
    {data: 'He', title: 'He',  case: "bit"},
    {data: 'He-RF', title: 'He-RF',  case: "bit"},
    {data: 'Ar', title: 'Ar',  case: "bit"},
    {data: 'N2', title: 'N2',  case: "bit"},
    {data: 'O2_Mangel', title: 'O2_Mangel',  case: "bit"},
    {data: 'CO2_Melder', title: 'CO2_Melder',  case: "bit"},
    {data: 'LHe', title: 'LHe',  case: "bit"},
    {data: 'LN l/Tag', title: 'LN l/Tag', case: "num"},
    {data: 'LN', title: 'LN',  case: "bit"},
    {data: 'N2 Reinheit', title: 'N2 Reinheit',  case: "bit"},
    {data: 'N2 l/min', title: 'N2 l/min', case: "num"},
    {data: 'Ar Reinheit', title: 'Ar Reinheit',  case: "bit"},
    {data: 'Ar l/min', title: 'Ar l/min', case: "num"},
    {data: 'He Reinheit', title: 'He Reinheit',  case: "bit"},
    {data: 'He l/min', title: 'He l/min', case: "num"},
    {data: 'H2 Reinheit', title: 'H2 Reinheit',  case: "bit"},
    {data: 'H2 l/min', title: 'H2 l/min', case: "num"},
    {data: 'DL ISO 8573', title: 'DL_ISO 8573',  case: "bit"},
    {data: 'DL l/min', title: 'DL l/min', case: "num"},
    {data: 'VA l/min', title: 'VA l/min', case: "num"},
    {data: 'CO2 l/min', title: 'CO2 l/min'},
    {data: 'CO2 Reinheit', title: 'CO2 Reinheit',  case: "bit"},
    {data: 'O2 l/min', title: 'O2 l/min', case: "num"},
    {data: 'O2 Reinheit', title: 'O2 Reinheit',  case: "bit"},
        
        
    {data: 'ET_64A_3Phasig_Einzelanschluss', title: '64A 3Ph Einzelanschls',  case: "bit"},
    {data: 'ET_32A_3Phasig_Einzelanschluss', title: '32A 3Ph Einzelanschluss',  case: "bit"},
    {data: 'ET_16A_3Phasig_Einzelanschluss', title: '16A 3Ph Einzelanschluss',  case: "bit", visible:false},
    {data: 'ET_Digestorium_MSR_230V_SV_Stk', title: 'Digestorium_MSR 230V_SV_Stk', case: "num"},
    {data: 'ET_5x10mm2_Digestorium_Stk', title: '5x10mm2 Digestorium_Stk', case: "num"},
    {data: 'ET_5x10mm2_USV_Stk', title: '5x10mm2 USV_Stk', case: "num"},
    {data: 'ET_5x10mm2_SV_Stk', title: '5x10mm2 SV_Stk', case: "num"},
    {data: 'ET_5x10mm2_AV_Stk', title: '5x10mm2 AV_Stk', case: "num"},
    
    {data: 'HT_Abluft_Vakuumpumpe', title: 'HT_Abluft Vakuumpumpe',  case: "bit"},
    {data: 'HT_Abluft_Schweissabsaugung_Stk', title: 'HT_Abluft Schweissabsaugung_Stk', case: "num"},
    {data: 'HT_Abluft_Esse_Stk', title: 'HT_Abluft Esse_Stk', case: "num"},
    {data: 'HT_Abluft_Rauchgasabzug_Stk', title: 'HT_Abluft Rauchgasabzug_Stk', case: "num"},
    {data: 'HT_Abluft_Digestorium_Stk', title: 'HT_Abluft Digestorium_Stk', case: "num"},
    {data: 'HT_Punktabsaugung_Stk', title: 'HT Punktabsaugung_Stk', case: "num"},
    {data: 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk', title: 'HT_Abluft Sicherheitsschrank_Unterbau_Stk', case: "num"},
    {data: 'HT_Abluft_Sicherheitsschrank_Stk', title: 'HT_Abluft Sicherheitsschrank_Stk', case: "num"},
    {data: 'Wasser Qual 3 l/min', title: 'H2O_Q3 l/min', case: "num"},
    {data: 'Wasser Qual 2 l/Tag', title: 'H20_Q2 l/Tag', case: "num"},
    {data: 'Wasser Qual 1 l/Tag', title: 'H20_Q1 l/Tag', case: "num"},
    {data: 'Wasser Qual 3', title: 'H20 Q3', case: "bit"},
    {data: 'Wasser Qual 2', title: 'H20 Q2', case: "bit"},
    {data: 'Wasser Qual 1', title: 'H20 Q1', case: "bit"}
];