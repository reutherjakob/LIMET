<?php
require_once "_utils.php";
init_page_serversides("x");

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Icon Legende</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen"/>
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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
</head>

<div id="limet-navbar"></div>
<div class="container-fluid">
    <div class="row">
        <div class="col-3"></div>
        <div class="col-6">
            <div class="card">
                <div class="card-header d-flex align-items-center">

                    <h6 class="mb-0"> Icon-Legende (work in Progress) </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                            <tr>
                                <th>Icon</th>
                                <th>Bedeutung</th>
                            </tr>
                            </thead>
                            <tbody>


                            <tr>
                                <td><i class="fas fa-history"></i></td>
                                <td> Zeitlicher Verlauf/ Änderungen</td>
                            </tr>

                            <tr>
                                <td><i class="far fa-sticky-note "></i></td>
                                <td>Notiz</td>
                            </tr>

                            <tr>
                                <td><i class="fas fa-code-branch "></i></td>
                                <td>Workflow</td>
                            </tr>

                            <tr>
                                <td><i class="fas fa-fingerprint "></i></td>
                                <td>ID</td>
                            </tr>

                            <tr>
                                <td><i class="fab fa-periscope "></i></td>
                                <td>Standort</td>
                            </tr>

                            <tr>
                                <td><i class="fas fa-euro-sign"></i></td>
                                <td>Kosten</td>
                            </tr>

                            <tr>
                                <td><i class="far fa-comments"></i>
                                    <i class="far fa-comment"></i></td>

                                <td>Kommentar</td>
                            </tr>

                            <tr>
                                <td><i class="far fa-calendar-alt "></i></td>
                                <td>Termin/-kalender</td>
                            </tr>

                            <tr>
                                <td><i class="fas fa-cog "></i></td>
                                <td>Einstellungen</td>
                            </tr>

                            <tr>
                                <td><i class="fas fa-sync-alt"></i></td>
                                <td>Aktualisieren</td>
                            </tr>


                            <tr>
                                <td><i class="fas fa-file-excel"></i></td>
                                <td>Download als Excel</td>
                            </tr>



                            <tr>
                                <td>
                                    <i class='far fa-file-pdf'></i>
                                </td>
                                <td>Download als PDF</td>
                            </tr>



                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>