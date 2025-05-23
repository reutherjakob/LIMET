<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BauangabenTexteFürAlleRäumeMitElement</title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- boostrap  --->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <!--- charts --->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
    <!-- select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
</head>
<body>
<div id="limet-navbar"></div>
<div class="container-fluid">
    <?php
    if (!function_exists('utils_connect_sql')) {
        include "_utils.php";
    }
    init_page_serversides("x", "x");
    ?>
    <div class="row">

        <div class="col-4">
            <div class="card mb-3">
                <div class="card-header">
                    <label for="elementSelect" class="form-label mb-0">Element auswählen</label>
                </div>
                <div class="card-body">
                    <select id="elementSelect" class="form-select select2" style="width: 100%;">

                        <?php
                        $mysqli = utils_connect_sql();
                        $result = $mysqli->query("SELECT idTABELLE_Elemente, Bezeichnung, ElementID FROM tabelle_elemente ORDER BY Bezeichnung");
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['idTABELLE_Elemente']) . '">' .
                                htmlspecialchars($row['ElementID']) . " " . htmlspecialchars($row['Bezeichnung']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

            </div>

            <div class="card mb-3">
                <div class="card-header">Elemente mit Standartisiertem Text</div>
                <div class="card-body">

                    Tabelle mit jenen Elementen, welche einen Bauangaben text für alle Räume generiern sollten
                    <!-- TODO -->

                </div>
            </div>


        </div>

        <div class="col-8">
            <div class="card mb-3">
                <div class="card-header">Anmerkungen</div>
                <div class="card-body">
                    <form>

                        <div class="mb-3">
                            <label for="anmerkungET" class="form-label">Anmerkung ET</label>
                            <textarea class="form-control" id="anmerkungET" name="anmerkungET" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="anmerkungHT" class="form-label">Anmerkung HT</label>
                            <textarea class="form-control" id="anmerkungHT" name="anmerkungHT" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="anmerkungStatik" class="form-label">Anmerkung Statik</label>
                            <textarea class="form-control" id="anmerkungStatik" name="anmerkungStatik"
                                      rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="anmerkungMedGas" class="form-label">Anmerkung MedGas</label>
                            <textarea class="form-control" id="anmerkungMedGas" name="anmerkungMedGas"
                                      rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success text-nowrap w-100">Anmerkungen für alle Räume im
                            Projekt mit dem Element übernehmen
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card 3: Placeholder -->

    </div>
</div>
</body>
<script>
    $(document).ready(function () {
        $('#elementSelect').select2({
            placeholder: 'Element auswählen'
        });
    });
</script>

