<!DOCTYPE html>
<html lang="de">
<head>
    <title>Queries</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="../Logo/iphone_favicon.png"/>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
          rel="stylesheet">
</head>
<body>
<div class="container-fluid bg-light">

    <div id="limet-navbar"></div>
    <?php require_once "../utils/_utils.php";
    init_page_serversides(); ?>

    <div class="card">
        <div class="card-header">
        </div>
        <div class="card-body">
        </div>
    </div>
</body>
</html>


<div class="card">
    <div class="card-body">
        UPDATE `tabelle_räume` r
        JOIN (
        SELECT '234' AS Raumnr, '' AS spez, 'Abwasser wird Hämatoxylin, Eosin, verdünnte Pikrinsäure, Alkohole
        enthalten - histologischer Färbeabfall' AS kommentar UNION ALL
        SELECT '424', 'Neutralisationsanlage', 'Gebinde mit Restsand und Beizstaub werden gewaschen' UNION ALL
        SELECT '564', '', 'Methanolabfälle; AcN Abfälle' UNION ALL
        SELECT '306', '', 'Neutralisation nach Sedimentation, große Wassermenge!!' UNION ALL
        SELECT '316', '', 'Neutralisation und Schwermetallbehandlung (Barium)' UNION ALL
        SELECT '368', 'Neutralisationsanlage', 'Option das Abwasser in einem Kanister zu sammeln, damit es separat
        behandelt werden kann (z.B. wegen Beizmittelrückständen)' UNION ALL
        SELECT '374O','Neutralisationsanlage', 'Option das Abwasser in einem Kanister zu sammeln, damit es separat
        behandelt werden kann (z.B. wegen Beizstaub)' UNION ALL
        SELECT '367', 'Neutralisationsanlage', 'Option das Abwasser in einem Kanister zu sammeln, damit es separat
        behandelt werden kann (z.B. wegen QSO-Pilzen)' UNION ALL
        SELECT '379', 'Sedimentationsanlage', 'Option das Abwasser normal oder über Sedimentationsbecken abzuführen'
        UNION ALL
        SELECT '370', 'Sedimentationsanlage', 'Option das Abwasser normal oder über Sedimentationsbecken abzuführen'
        UNION ALL
        SELECT '372', 'Sedimentationsanlage', 'Option das Abwasser normal oder über Sedimentationsbecken abzuführen'
        UNION ALL
        SELECT '511', 'Neutralisationsanlage', 'Sammlung radioaktiver Abfall derzeit mit einem Kanister unter dem
        Decontbecken. Es braucht also zwei Waschbecken, eines für die Neutralisationsanlage und eines für den
        radioaktiven Abfall' UNION ALL
        SELECT '259', '', 'Zwischenlagerung in Tank, chemische Desinfektion' UNION ALL
        SELECT '592', '', 'Abwasser Geschirrwäsche'
        ) d ON d.Raumnr = r.`Raumnr`
        SET
        r.`HT_Spezialabwasser` = CASE WHEN d.spez = '' THEN COALESCE(r.`HT_Spezialabwasser`, '') ELSE d.spez END,
        r.`Anmerkung HKLS` = CONCAT(
        COALESCE(r.`Anmerkung HKLS`, ''),
        CASE
        WHEN COALESCE(r.`Anmerkung HKLS`, '') = '' THEN ''
        WHEN RIGHT(TRIM(r.`Anmerkung HKLS`), 1) = '.' THEN ''
        ELSE '.'
        END,
        ' ',
        d.kommentar,
        CASE WHEN RIGHT(d.kommentar, 1) = '.' THEN '' ELSE '.' END
        )
        WHERE r.`tabelle_projekte_idTABELLE_Projekte` = 95;


        UPDATE `tabelle_räume`
        SET `VA` = 1
        WHERE `tabelle_projekte_idTABELLE_Projekte` = 95
        AND `Raumnr` IN ('618','495','584','585','299','563','485','484','574',
        '571','570','577','569','568','561','567','550','216','217');

        UPDATE `tabelle_räume`
        SET `N2` = 1
        WHERE `tabelle_projekte_idTABELLE_Projekte` = 95
        AND `Raumnr` IN ('572','341');

        UPDATE `tabelle_räume`
        SET `DL-5` = 1
        WHERE `tabelle_projekte_idTABELLE_Projekte` = 95
        AND `Raumnr` IN ('368','370','626');


        UPDATE `tabelle_räume` r
        JOIN (
        SELECT '234' AS Raumnr, 'Abwasser wird Hämatoxylin, Eosin, verdünnte Pikrinsäure, Alkohole enthalten -
        histologischer Färbeabfall' AS kommentar UNION ALL
        SELECT '424', 'Gebinde mit Restsand und Beizstaub werden gewaschen' UNION ALL
        SELECT '564', 'Methanolabfälle; AcN Abfälle' UNION ALL
        SELECT '306', 'Neutralisation nach Sedimentation, große Wassermenge!!' UNION ALL
        SELECT '316', 'Neutralisation und Schwermetallbehandlung (Barium)' UNION ALL
        SELECT '368', 'Option das Abwasser in einem Kanister zu sammeln, damit es separat behandelt werden kann
        (z.B. wegen Beizmittelrückständen)' UNION ALL
        SELECT '374O','Option das Abwasser in einem Kanister zu sammeln, damit es separat behandelt werden kann
        (z.B. wegen Beizstaub)' UNION ALL
        SELECT '367', 'Option das Abwasser in einem Kanister zu sammeln, damit es separat behandelt werden kann
        (z.B. wegen QSO-Pilzen)' UNION ALL
        SELECT '379', 'Option das Abwasser normal oder über Sedimentationsbecken abzuführen' UNION ALL
        SELECT '370', 'Option das Abwasser normal oder über Sedimentationsbecken abzuführen' UNION ALL
        SELECT '372', 'Option das Abwasser normal oder über Sedimentationsbecken abzuführen' UNION ALL
        SELECT '511', 'Sammlung radioaktiver Abfall derzeit mit einem Kanister unter dem Decontbecken. Es braucht
        also zwei Waschbecken, eines für die Neutralisationsanlage und eines für den radioaktiven Abfall' UNION ALL
        SELECT '259', 'Zwischenlagerung in Tank, chemische Desinfektion' UNION ALL
        SELECT '592', 'Abwasser Geschirrwäsche'
        ) d ON d.Raumnr = r.`Raumnr`
        SET r.`Anmerkung HKLS` = REPLACE(
        r.`Anmerkung HKLS`,
        CONCAT(d.kommentar, '. ', d.kommentar, '.'),
        CONCAT(d.kommentar, '.')
        )
        WHERE r.`tabelle_projekte_idTABELLE_Projekte` = 95
        AND LOCATE(CONCAT(d.kommentar, '. ', d.kommentar, '.'), r.`Anmerkung HKLS`) > 0;

        SELECT r.`Raumnr`,
        r.`Anmerkung HKLS`,
        (LOCATE(CONCAT(d.kommentar, '. ', d.kommentar, '.'), r.`Anmerkung HKLS`) > 0) AS doppelt
        FROM `tabelle_räume` r
        JOIN (
        SELECT '234' AS Raumnr, 'Abwasser wird Hämatoxylin, Eosin, verdünnte Pikrinsäure, Alkohole enthalten -
        histologischer Färbeabfall' AS kommentar UNION ALL
        SELECT '424', 'Gebinde mit Restsand und Beizstaub werden gewaschen' UNION ALL
        SELECT '564', 'Methanolabfälle; AcN Abfälle' UNION ALL
        SELECT '306', 'Neutralisation nach Sedimentation, große Wassermenge!!' UNION ALL
        SELECT '316', 'Neutralisation und Schwermetallbehandlung (Barium)' UNION ALL
        SELECT '368', 'Option das Abwasser in einem Kanister zu sammeln, damit es separat behandelt werden kann
        (z.B. wegen Beizmittelrückständen)' UNION ALL
        SELECT '374O','Option das Abwasser in einem Kanister zu sammeln, damit es separat behandelt werden kann
        (z.B. wegen Beizstaub)' UNION ALL
        SELECT '367', 'Option das Abwasser in einem Kanister zu sammeln, damit es separat behandelt werden kann
        (z.B. wegen QSO-Pilzen)' UNION ALL
        SELECT '379', 'Option das Abwasser normal oder über Sedimentationsbecken abzuführen' UNION ALL
        SELECT '370', 'Option das Abwasser normal oder über Sedimentationsbecken abzuführen' UNION ALL
        SELECT '372', 'Option das Abwasser normal oder über Sedimentationsbecken abzuführen' UNION ALL
        SELECT '511', 'Sammlung radioaktiver Abfall derzeit mit einem Kanister unter dem Decontbecken. Es braucht
        also zwei Waschbecken, eines für die Neutralisationsanlage und eines für den radioaktiven Abfall' UNION ALL
        SELECT '259', 'Zwischenlagerung in Tank, chemische Desinfektion' UNION ALL
        SELECT '592', 'Abwasser Geschirrwäsche'
        ) d ON d.Raumnr = r.`Raumnr`
        WHERE r.`tabelle_projekte_idTABELLE_Projekte` = 95;

        UPDATE `tabelle_räume`
        SET `HT_Kühlwasser` = CASE
        WHEN `Raumnr` IN ('299','571','569','568','561','216','217','565','389A') THEN 1
        ELSE 0
        END
        WHERE `tabelle_projekte_idTABELLE_Projekte` = 95;

        UPDATE `tabelle_räume` r
        JOIN (
        SELECT '556' AS Raumnr, '24+-2' AS kommentar UNION ALL
        SELECT '213A', '15°C - 25°C' UNION ALL
        SELECT '563', '22+-2' UNION ALL
        SELECT '561', '22+-2' UNION ALL
        SELECT '567', '22+-2' UNION ALL
        SELECT '216', '20°C-25°C' UNION ALL
        SELECT '217', '21°C-24°C - Begründung: gesetzliche / normative Vorgaben' UNION ALL
        SELECT '234', '20-23 °C +/-2 °C' UNION ALL
        SELECT '316', '22°C +/- 2°C, Notwendigkeit stabiler!! Temperaturen bei der Extraktion' UNION ALL
        SELECT '427', 'Solltemperatur 15°C - 30°C Spektrum' UNION ALL
        SELECT '300', 'ICP-Messraum - stabile Temperatur notwendig' UNION ALL
        SELECT '624', '21 - 23°C +/-1' UNION ALL
        SELECT '623', '18 - 22°C +/-1' UNION ALL
        SELECT '534', 'Solltemperatur 22°C +/- 2°C' UNION ALL
        SELECT '496', '20°C +/- 1 (bei Bedarf) Qualifizierung Titratoren, Maßlösungen,...' UNION ALL
        SELECT '620', '21 - 23°C +/-1' UNION ALL
        SELECT '326', 'Trockenraum max 40°C' UNION ALL
        SELECT '612', '10 °C' UNION ALL
        SELECT '518', '18 - 20 Grad' UNION ALL
        SELECT '518A', '20 - 22 Grad ohne Temperaturschwankungen' UNION ALL
        SELECT '518B', '20 - 22 Grad ohne Temperaturschwankungen' UNION ALL
        SELECT '565', '22+-2' UNION ALL
        SELECT '374Z', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374Y', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374X', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374W', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374V', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374U', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374T', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374S', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374AA', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374M', 'Steuerstellwert 5 - 24 °C' UNION ALL
        SELECT '233', '20-23 °C +/-2 °C' UNION ALL
        SELECT '237A', '20-23 °C +/-2 °C' UNION ALL
        SELECT '215', '20°C-25°C' UNION ALL
        SELECT '213', '15°C - 25°C' UNION ALL
        SELECT '208', '20°C-25°C' UNION ALL
        SELECT '537A', '18 °C - 22°C (+/- 1 °C) lt. ISO 8655'
        ) d ON d.Raumnr = r.`Raumnr`
        SET r.`Anmerkung HKLS` = CONCAT(
        COALESCE(r.`Anmerkung HKLS`, ''),
        CASE
        WHEN COALESCE(r.`Anmerkung HKLS`, '') = '' THEN ''
        WHEN RIGHT(TRIM(r.`Anmerkung HKLS`), 1) = '.' THEN ''
        ELSE '.'
        END,
        ' ',
        d.kommentar,
        CASE WHEN RIGHT(d.kommentar, 1) = '.' THEN '' ELSE '.' END
        )
        WHERE r.`tabelle_projekte_idTABELLE_Projekte` = 95;

        UPDATE `tabelle_räume` r
        JOIN (
        SELECT '391' AS Raumnr, '<40% rH' AS kommentar UNION ALL
        SELECT '213A', '<75%' UNION ALL
        SELECT '563', '40%relF' UNION ALL
        SELECT '561', '40%relF' UNION ALL
        SELECT '567', '40%relF' UNION ALL
        SELECT '216', '<75%' UNION ALL
        SELECT '217', '<75%' UNION ALL
        SELECT '234', 'zwischen 40 % und 60 %' UNION ALL
        SELECT '427', '40 - 60% +/- 5%' UNION ALL
        SELECT '534', '60% rel. Luftfeuchtigkeit +/- 5%' UNION ALL
        SELECT '496', '30-45% (wegen KF) bei Bedarf' UNION ALL
        SELECT '326', 'Entstehende Feuchte Luft sollte abgeführt werden' UNION ALL
        SELECT '612', '0.5' UNION ALL
        SELECT '565', 'mind 40% rel.Feuchte' UNION ALL
        SELECT '374Z', 'Steuerstellwert 20-90% rH' UNION ALL
        SELECT '374Y', 'Steuerstellwert 40-90% rH' UNION ALL
        SELECT '374X', 'Steuerstellwert 40-90% rH' UNION ALL
        SELECT '374W', 'Steuerstellwert 40-90% rH' UNION ALL
        SELECT '374V', 'Steuerstellwert 40-90% rH' UNION ALL
        SELECT '374U', 'Steuerstellwert 40-90% rH' UNION ALL
        SELECT '374T', 'Steuerstellwert 40-90% rH' UNION ALL
        SELECT '374S', 'Steuerstellwert 40-90% rH' UNION ALL
        SELECT '374AA', 'Steuerstellwert 40-99% rH' UNION ALL
        SELECT '374M', 'Steuerstellwert 40-99% rH' UNION ALL
        SELECT '215', '<75%' UNION ALL
        SELECT '213', '<75%' UNION ALL
        SELECT '208', '<75%' UNION ALL
        SELECT '537A', '50-70% ± 5%, lt. ISO 8655'
        ) d ON d.Raumnr = r.`Raumnr`
        SET r.`Anmerkung HKLS` = CONCAT(
        COALESCE(r.`Anmerkung HKLS`, ''),
        CASE
        WHEN COALESCE(r.`Anmerkung HKLS`, '') = '' THEN ''
        WHEN RIGHT(TRIM(r.`Anmerkung HKLS`), 1) = '.' THEN ''
        ELSE '.'
        END,
        ' ',
        d.kommentar,
        CASE WHEN RIGHT(d.kommentar, 1) = '.' THEN '' ELSE '.' END
        )
        WHERE r.`tabelle_projekte_idTABELLE_Projekte` = 95;


        -- Praefix vor Raumtemp-Kommentaren
        UPDATE `tabelle_räume` r
        JOIN (
        SELECT '556' AS Raumnr, '24+-2' AS kommentar UNION ALL
        SELECT '213A', '15°C - 25°C' UNION ALL
        SELECT '563', '22+-2' UNION ALL
        SELECT '561', '22+-2' UNION ALL
        SELECT '567', '22+-2' UNION ALL
        SELECT '216', '20°C-25°C' UNION ALL
        SELECT '217', '21°C-24°C - Begründung: gesetzliche / normative Vorgaben' UNION ALL
        SELECT '234', '20-23 °C +/-2 °C' UNION ALL
        SELECT '316', '22°C +/- 2°C, Notwendigkeit stabiler!! Temperaturen bei der Extraktion' UNION ALL
        SELECT '427', 'Solltemperatur 15°C - 30°C Spektrum' UNION ALL
        SELECT '300', 'ICP-Messraum - stabile Temperatur notwendig' UNION ALL
        SELECT '624', '21 - 23°C +/-1' UNION ALL
        SELECT '623', '18 - 22°C +/-1' UNION ALL
        SELECT '534', 'Solltemperatur 22°C +/- 2°C' UNION ALL
        SELECT '496', '20°C +/- 1 (bei Bedarf) Qualifizierung Titratoren, Maßlösungen,...' UNION ALL
        SELECT '620', '21 - 23°C +/-1' UNION ALL
        SELECT '326', 'Trockenraum max 40°C' UNION ALL
        SELECT '612', '10 °C' UNION ALL
        SELECT '518', '18 - 20 Grad' UNION ALL
        SELECT '518A', '20 - 22 Grad ohne Temperaturschwankungen' UNION ALL
        SELECT '518B', '20 - 22 Grad ohne Temperaturschwankungen' UNION ALL
        SELECT '565', '22+-2' UNION ALL
        SELECT '374Z', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374Y', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374X', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374W', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374V', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374U', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374T', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374S', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374AA', 'Steuerstellwert 5 - 40 °C' UNION ALL
        SELECT '374M', 'Steuerstellwert 5 - 24 °C' UNION ALL
        SELECT '233', '20-23 °C +/-2 °C' UNION ALL
        SELECT '237A', '20-23 °C +/-2 °C' UNION ALL
        SELECT '215', '20°C-25°C' UNION ALL
        SELECT '213', '15°C - 25°C' UNION ALL
        SELECT '208', '20°C-25°C' UNION ALL
        SELECT '537A', '18 °C - 22°C (+/- 1 °C) lt. ISO 8655'tabelle_projekte
        ) d ON d.Raumnr = r.`Raumnr`
        SET r.`Anmerkung HKLS` = REPLACE(
        r.`Anmerkung HKLS`,
        CONCAT(' ', d.kommentar),
        CONCAT(' ', 'Nutzerangabe Sonderfragekathalog: ', d.kommentar)
        )
        WHERE r.`tabelle_projekte_idTABELLE_Projekte` = 95
        AND LOCATE(CONCAT(' ', d.kommentar), r.`Anmerkung HKLS`) > 0
        AND LOCATE(CONCAT(' ', 'Nutzerangabe Sonderfragekathalog: ', d.kommentar), r.`Anmerkung HKLS`) = 0;

        -- Praefix vor Luftfeuchte-Kommentaren
        UPDATE `tabelle_räume` r
        JOIN (
        SELECT '391' AS Raumnr, '<40% rH' AS kommentar UNION ALL
        SELECT '213A', '<75%' UNION ALL
        SELECT '563', '40%relF' UNION ALL
        SELECT '561', '40%relF' UNION ALL
        SELECT '567', '40%relF' UNION ALL
        SELECT '216', '<75%' UNION ALL
        SELECT '217', '<75%' UNION ALL
        SELECT '234', 'zwischen 40 % und 60 %' UNION ALL
        SELECT '427', '40 - 60% +/- 5%' UNION ALL
        SELECT '534', '60% rel. Luftfeuchtigkeit +/- 5%' UNION ALL
        SELECT '496', '30-45% (wegen KF) bei Bedarf' UNION ALL
        SELECT '326', 'Entstehende Feuchte Luft sollte abgeführt werden' UNION ALL
        SELECT '612', '0.5' UNION ALL
        SELECT '565', 'mind 40% rel.Feuchte' UNION ALL
        SELECT '374Z', 'Steuerstellwert 20-90% rH' UNION ALL
        SELECT '374Y', 'Steuerstellwert 40-90% rH' UNION ALL
        SELECT '374X', 'Steuerstellwert 40-90% rH' UNION ALL
        SELECT '374W', 'Steuerstellwert 40-90% rH' UNION ALL
        SELECT '374V', 'Steuerstellwert 40-90% rH' UNION ALL
        SELECT '374U', 'Steuerstellwert 40-90% rH' UNION ALL
        SELECT '374T', 'Steuerstellwert 40-90% rH' UNION ALL
        SELECT '374S', 'Steuerstellwert 40-90% rH' UNION ALL
        SELECT '374AA', 'Steuerstellwert 40-99% rH' UNION ALL
        SELECT '374M', 'Steuerstellwert 40-99% rH' UNION ALL
        SELECT '215', '<75%' UNION ALL
        SELECT '213', '<75%' UNION ALL
        SELECT '208', '<75%' UNION ALL
        SELECT '537A', '50-70% ± 5%, lt. ISO 8655'
        ) d ON d.Raumnr = r.`Raumnr`
        SET r.`Anmerkung HKLS` = REPLACE(
        r.`Anmerkung HKLS`,
        CONCAT(' ', d.kommentar),
        CONCAT(' ', 'Nutzerangabe Sonderfragekathalog: ', d.kommentar)
        )
        WHERE r.`tabelle_projekte_idTABELLE_Projekte` = 95
        AND LOCATE(CONCAT(' ', d.kommentar), r.`Anmerkung HKLS`) > 0
        AND LOCATE(CONCAT(' ', 'Nutzerangabe Sonderfragekathalog: ', d.kommentar), r.`Anmerkung HKLS`) = 0;

        UPDATE `tabelle_räume` r
        JOIN (
        SELECT '391' AS Raumnr UNION ALL SELECT '213A' UNION ALL SELECT '563' UNION ALL SELECT '561' UNION ALL
        SELECT '567' UNION ALL
        SELECT '216' UNION ALL SELECT '217' UNION ALL SELECT '234' UNION ALL SELECT '427' UNION ALL SELECT '534'
        UNION ALL
        SELECT '496' UNION ALL SELECT '326' UNION ALL SELECT '612' UNION ALL SELECT '565' UNION ALL SELECT '374Z'
        UNION ALL
        SELECT '374Y' UNION ALL SELECT '374X' UNION ALL SELECT '374W' UNION ALL SELECT '374V' UNION ALL SELECT
        '374U' UNION ALL
        SELECT '374T' UNION ALL SELECT '374S' UNION ALL SELECT '374AA'UNION ALL SELECT '374M' UNION ALL SELECT '215'
        UNION ALL
        SELECT '213' UNION ALL SELECT '208' UNION ALL SELECT '537A'
        ) d ON d.Raumnr = r.`Raumnr`
        SET r.`Anmerkung HKLS` = REPLACE(
        r.`Anmerkung HKLS`,
        'Nutzerangabe Sonderfragekatalog: ',
        'Nutzerangabe Sonderfragekatalog Luftfeuchtigkeit: '
        )
        WHERE r.`tabelle_projekte_idTABELLE_Projekte` = 95
        AND r.`Anmerkung HKLS` LIKE '%Nutzerangabe Sonderfragekatalog: %';

        UPDATE `tabelle_räume` r
        JOIN (
        SELECT '556' AS Raumnr UNION ALL SELECT '213A' UNION ALL SELECT '563' UNION ALL SELECT '561' UNION ALL
        SELECT '567' UNION ALL
        SELECT '216' UNION ALL SELECT '217' UNION ALL SELECT '234' UNION ALL SELECT '316' UNION ALL SELECT '427'
        UNION ALL
        SELECT '300' UNION ALL SELECT '624' UNION ALL SELECT '623' UNION ALL SELECT '534' UNION ALL SELECT '496'
        UNION ALL
        SELECT '620' UNION ALL SELECT '326' UNION ALL SELECT '612' UNION ALL SELECT '518' UNION ALL SELECT '518A'
        UNION ALL
        SELECT '518B' UNION ALL SELECT '565' UNION ALL SELECT '374Z' UNION ALL SELECT '374Y' UNION ALL SELECT '374X'
        UNION ALL
        SELECT '374W' UNION ALL SELECT '374V' UNION ALL SELECT '374U' UNION ALL SELECT '374T' UNION ALL SELECT
        '374S' UNION ALL
        SELECT '374AA'UNION ALL SELECT '374M' UNION ALL SELECT '233' UNION ALL SELECT '237A' UNION ALL SELECT '215'
        UNION ALL
        SELECT '213' UNION ALL SELECT '208' UNION ALL SELECT '537A'
        ) d ON d.Raumnr = r.`Raumnr`
        SET r.`Anmerkung HKLS` = REPLACE(
        r.`Anmerkung HKLS`,
        'Nutzerangabe Sonderfragekatalog: ',
        'Nutzerangabe Sonderfragekatalog Temperatur: '
        )
        WHERE r.`tabelle_projekte_idTABELLE_Projekte` = 95
        AND r.`Anmerkung HKLS` LIKE '%Nutzerangabe Sonderfragekatalog: %';

        select `Anmerkung HKLS`, Raumnr from tabelle_räume where
        tabelle_projekte_idTABELLE_Projekte = 95 and `Anmerkung HKLS` is not null;


        SELECT
        a.idtabelle_raeume_aenderungen,
        a.Timestamp,
        a.`user`,
        r.`Raumnr`,
        r.`raumbezeichnung`,
        a.`Anmerkung HKLS_copy1` AS Anmerkung_HKLS_neu,
        a.`Anmerkung HKLS` AS Anmerkung_HKLS_alt
        FROM tabelle_raeume_aenderungen a
        JOIN tabelle_räume r
        ON a.raum_id = r.idTABELLE_Räume -- ggf. an deine PK-Spalte anpassen
        WHERE r.`tabelle_projekte_idTABELLE_Projekte` = 95
        AND DATE(a.Timestamp) = DATE(NOW()) -- nur Änderungen von heute
        AND a.`Anmerkung HKLS_copy1` <> '0' -- neue Anmerkung ist "0"
        ORDER BY a.Timestamp, r.`Raumnr`;


        SELECT `Anmerkung HKLS`, Raumnr from tabelle_räume where
        `Anmerkung HKLS` is not null and tabelle_projekte_idTABELLE_Projekte =95;

        UPDATE `tabelle_räume` r
        JOIN (
        SELECT '208' AS Raumnr, 'Nutzerangabe Sonderfragekatalog Temperatur: 20°C-25°C. Nutzerangabe
        Sonderfragekatalog Luftfeuchtigkeit: <75%.' AS Anmerkung_HKLS_neu UNION ALL
        SELECT '213', 'Nutzerangabe Sonderfragekatalog Temperatur: 15°C - 25°C. Nutzerangabe Sonderfragekatalog
        Luftfeuchtigkeit: <75%.' UNION ALL
        SELECT '213A', 'Nutzerangabe Sonderfragekatalog Temperatur: 15°C - 25°C. Nutzerangabe Sonderfragekatalog
        Luftfeuchtigkeit: <75%.' UNION ALL
        SELECT '215', 'Nutzerangabe Sonderfragekatalog Temperatur: 20°C-25°C. Nutzerangabe Sonderfragekatalog
        Luftfeuchtigkeit: <75%.' UNION ALL
        SELECT '233', 'Nutzerangabe Sonderfragekatalog Temperatur: 20-23 °C +/-2 °C.' UNION ALL
        SELECT '237A', 'Nutzerangabe Sonderfragekatalog Temperatur: 20-23 °C +/-2 °C.' UNION ALL
        SELECT '300', 'Nutzerangabe Sonderfragekatalog Temperatur: ICP-Messraum - stabile Temperatur notwendig.'
        UNION ALL
        SELECT '326', 'Nutzerangabe Sonderfragekatalog Temperatur: Trockenraum max 40°C. Nutzerangabe
        Sonderfragekatalog Luftfeuchtigkeit: Entstehende Feuchte Luft sollte abgeführt werden.' UNION ALL
        SELECT '374AA','Nutzerangabe Sonderfragekatalog Temperatur: Steuerstellwert 5 - 40 °C. Nutzerangabe
        Sonderfragekatalog Luftfeuchtigkeit: Steuerstellwert 40-99% rH.' UNION ALL
        SELECT '374M', 'Nutzerangabe Sonderfragekatalog Temperatur: Steuerstellwert 5 - 24 °C. Nutzerangabe
        Sonderfragekatalog Luftfeuchtigkeit: Steuerstellwert 40-99% rH.' UNION ALL
        SELECT '374S', 'Nutzerangabe Sonderfragekatalog Temperatur: Steuerstellwert 5 - 40 °C. Nutzerangabe
        Sonderfragekatalog Luftfeuchtigkeit: Steuerstellwert 40-90% rH.' UNION ALL
        SELECT '374T', 'Nutzerangabe Sonderfragekatalog Temperatur: Steuerstellwert 5 - 40 °C. Nutzerangabe
        Sonderfragekatalog Luftfeuchtigkeit: Steuerstellwert 40-90% rH.' UNION ALL
        SELECT '374U', 'Nutzerangabe Sonderfragekatalog Temperatur: Steuerstellwert 5 - 40 °C. Nutzerangabe
        Sonderfragekatalog Luftfeuchtigkeit: Steuerstellwert 40-90% rH.' UNION ALL
        SELECT '374V', 'Nutzerangabe Sonderfragekatalog Temperatur: Steuerstellwert 5 - 40 °C. Nutzerangabe
        Sonderfragekatalog Luftfeuchtigkeit: Steuerstellwert 40-90% rH.' UNION ALL
        SELECT '374W', 'Nutzerangabe Sonderfragekatalog Temperatur: Steuerstellwert 5 - 40 °C. Nutzerangabe
        Sonderfragekatalog Luftfeuchtigkeit: Steuerstellwert 40-90% rH.' UNION ALL
        SELECT '374X', 'Nutzerangabe Sonderfragekatalog Temperatur: Steuerstellwert 5 - 40 °C. Nutzerangabe
        Sonderfragekatalog Luftfeuchtigkeit: Steuerstellwert 40-90% rH.' UNION ALL
        SELECT '374Y', 'Nutzerangabe Sonderfragekatalog Temperatur: Steuerstellwert 5 - 40 °C. Nutzerangabe
        Sonderfragekatalog Luftfeuchtigkeit: Steuerstellwert 40-90% rH.' UNION ALL
        SELECT '374Z', 'Nutzerangabe Sonderfragekatalog Temperatur: Steuerstellwert 5 - 40 °C. Nutzerangabe
        Sonderfragekatalog Luftfeuchtigkeit: Steuerstellwert 20-90% rH.' UNION ALL
        SELECT '391', 'Nutzerangabe Sonderfragekatalog Luftfeuchtigkeit: <40% rH.' UNION ALL
        SELECT '427', 'Nutzerangabe Sonderfragekatalog Temperatur: Solltemperatur 15°C - 30°C Spektrum. Nutzerangabe
        Sonderfragekatalog Luftfeuchtigkeit: 40 - 60% +/- 5%.' UNION ALL
        SELECT '518', 'Nutzerangabe Sonderfragekatalog Temperatur: 18 - 20 Grad.' UNION ALL
        SELECT '537A', 'Nutzerangabe Sonderfragekatalog Temperatur: 18 °C - 22°C (+/- 1 °C) lt. ISO 8655.
        Nutzerangabe Sonderfragekatalog Luftfeuchtigkeit: 50-70% ± 5%, lt. ISO 8655.' UNION ALL
        SELECT '556', 'Nutzerangabe Sonderfragekatalog Temperatur: 24+-2.' UNION ALL
        SELECT '563', 'Nutzerangabe Sonderfragekatalog Temperatur: 22+-2. Nutzerangabe Sonderfragekatalog
        Luftfeuchtigkeit: 40%relF.' UNION ALL
        SELECT '612', 'Nutzerangabe Sonderfragekatalog Temperatur: 10 °C. Nutzerangabe Sonderfragekatalog
        Luftfeuchtigkeit: 0.5.' UNION ALL
        SELECT '620', 'Nutzerangabe Sonderfragekatalog Temperatur: 21 - 23°C +/-1.'
        ) d ON d.Raumnr = r.`Raumnr`
        SET r.`Anmerkung HKLS` = d.Anmerkung_HKLS_neu
        WHERE r.`tabelle_projekte_idTABELLE_Projekte` = 95;
    </div>
</div>
</div>

