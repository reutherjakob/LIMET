<!-- 13.2.25: Reworked -->
<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
init_page_serversides();
 include 'roombookSpecifications_addRoomModal.php';
 include 'roombookSpecifications_HelpModal.php';
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de"></html>
<head>
    <title>RB-Bauangaben</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <link rel="icon" href="iphone_favicon.png"/>

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

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>

    <style>
        .btn_vis, .btn_invis {
            color: black;
            box-shadow: 0 2px 2px 0 rgba(100, 140, 25, 0.2), 0 2px 2px 0 rgba(100, 140, 25, 0.2);
        }
        .btn_vis {
            background-color: rgba(100, 140, 25, 0.1) !important;
            font-weight: 600;
        }
        .btn_invis {
            background-color: rgba(100, 0, 25, 0.1) !important;
            font-weight: 400;
        }


        .btn, .btn-group .btn, .dt-input {
            padding: 0.2vw 0.2vw !important;
            margin: 0 1px !important;
            height: 30px !important;
        }

        div.dt-button-collection, div.dt-button-collection.fixed.six-column {
            width: 1500px; /* Adjust this value to make the panel broader */
            left: 50% !important;
            transform: translateX(-50%);
            margin-top: 10px; /* Adjust as needed */
        }

        div.dt-button-collection.fixed.six-column > :last-child {
            padding-bottom: 5px;
            column-count: 6;
            -webkit-column-count: 6;
            -moz-column-count: 6;
        }

        .select2-container { /* so that the f-stelle dropdowner in addRoomModal isnt hidden*/
            z-index: 9999;
        }

    </style>
</head>
<body>
<div id="limet-navbar"></div>

<main class="container-fluid">

    <section class="mt-1 card">
        <header class="card-header d-flex border-light" style="height: 1vh; font-size: 1vh;" id="btnLabelz">
            <div class="col-xxl-4"><strong>Edit & Filter</strong></div>
            <div class="col-xxl-1 d-flex justify-content-end "><strong>Auswahl</strong></div>
            <div class="col-xxl-1"></div>
            <div class="col-xxl-3"><strong>Sichtbarkeit</strong></div>
            <div class="col-xxl-1 d-flex justify-content-end align-items-right "><strong>Neu & Output</strong></div>
            <div class="col-xxl-2 d-flex justify-content-end align-items-right"><strong style="float: right;">Check&Settings</strong>
            </div>
        </header>
        <div class="card-header container-fluid d-flex align-items-start border-dark">
            <div class="col-xxl-4 d-flex justify-content-left align-items-left" id='TableCardHeader'>
                <label for="checkbox_EditableTable"
                       id="edit_cbx"
                       class="form-check-label"
                       style="display: none;">
                    Edit Table
                </label>
                <input type="checkbox"
                       name="EditableTable"
                       id="checkbox_EditableTable"
                       class="form-check-input dt-input"
                       style="width: 20px; height: 30px;"
                       checked/>
            </div>
            <div class="col-xxl-1 d-flex justify-content-end align-items-center" id="TableCardHeaderX"></div>
            <div class="col-xxl-4 d-flex justify-content-center align-items-center" id="TableCardHeader2"></div>
            <div class="col-xxl-1 d-flex justify-content-end align-items-right" id='TableCardHeader3'></div>
            <div class="col-xxl-2 d-flex justify-content-end align-items-right" id='TableCardHeader4'></div>
        </div>
        <div class="card-body" id="table_container_div">
            <table class="table compact  table-responsive table-striped table-bordered border border-5 border-light table-sm sticky"

                   id="table_rooms">
                <thead>
                <tr></tr>
                </thead>
                <tbody>
                <tr>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>
    <section class='d-flex bd-highlight'>
        <div class='mt-4 me-2 mr-2 card flex-grow-1'>
            <header class="card-header fix_size"><b>Bauangaben</b></header>
            <div class="card-body" id="bauangaben"></div>
        </div>
        <div class="mt-4 card">
            <div class="card-header form-inline d-inline-flex " id="CardHEaderElemntsInRoom">
                <button type="button" class="btn btn-outline-dark " id="showRoomElements" style="width: 30px;"><i
                            class="fas fa-caret-left"></i></button>
                <div class="row" id="CardHEaderElemntsInRoom_SUB">
                    <div class="col-xxl-6" id="CardHEaderElemntsInRoom1"></div>
                    <div class="col-xxl-6 d-flex" id="CardHEaderElemntsInRoom2"></div>
                </div>
            </div>
            <div class="card-body" id="additionalInfo">
                <p id="roomElements"></p>
                <p id="elementParameters"></p>
            </div>

        </div>
    </section>
</main>

<div class="modal fade" id="einstellungModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <header class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Einstellungen</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </header>
            <div class="modal-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="settings_save_state_4all_projects">
                    <label class="form-check-label" for="settings_save_state_4all_projects">Tabellenzustand speichern
                        (f. alle Projekte)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="settings_save_state">
                    <label class="form-check-label" for="settings_save_state">Tabellenzustand speichern (f. aktuelles
                        Projekte)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="settings_save_edit_cbx">
                    <label class="form-check-label" for="settings_save_edit_cbx">Tabelle editierbar initiieren</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="settings_show_btn_grp_labels" checked>
                    <label class="form-check-label" for="settings_show_btn_grp_labels">Labels über den Buttons
                        anzeigen</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="settings_toggle_btn_texts">
                    <label class="form-check-label" for="settings_toggle_btn_texts">Button Texte anzeigen</label>
                </div>
            </div>
            <footer class="modal-footer">
                <button type="button" class="btn btn-warning" onclick="restoreDefaults()">Restore Default</button>
                &ensp;
                <button type="button" class="btn btn-secondary" data-bs-dismiss='modal'>Close</button>
                &ensp;
                <button type="button" class="btn btn-success" onclick="saveSettings()">Save changes</button>
            </footer>
        </div>
    </div>
</div>
</body>
<script src="roombookSpecifications_constDeclarations.js"></script>
<script src="_utils.js"></script>
<script src="roombookSpecifications_New.js" charset=utf-8></script>
