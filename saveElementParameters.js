$(document).ready(function () {
    $("button[value='saveParameter']").click(function () {
        let id = this.id;
        let variantenID = $('#variante').val();

        let wertSelect = $("#Wert_" + id);
        let einheitSelect = $("#Einheit_" + id);

        let wert = (wertSelect.val() === "__freetext__") ? $("#Wert_" + id + "_freetext").val() : wertSelect.val();
        let einheit = (einheitSelect.val() === "__freetext__") ? $("#Einheit_" + id + "_freetext").val() : einheitSelect.val();

        $.ajax({
            url: "updateParameter.php",
            type: "GET",
            data: {
                parameterID: id,
                wert: wert,
                einheit: einheit,
                variantenID: variantenID
            },
            success: function (data) {
                makeToaster(data.trim(), true);
            }
        });
    });

    $("button[value='saveAllParameter']").click(function () {
        const deleteBtns = document.querySelectorAll('#tableElementParameters tbody button[value="deleteParameter"]');
        const ids = Array.from(deleteBtns).map(btn => btn.id);
        let variantenID = $('#variante').val();

        ids.forEach(function (id) {
            let wertSelect = $("#Wert_" + id);
            let einheitSelect = $("#Einheit_" + id);

            // Check if 'Wert' select is set to free text option
            let wert;
            if (wertSelect.val() === "__freetext__") {
                wert = $("#Wert_" + id + "_freetext").val();
            } else {
                wert = wertSelect.val();
            }

            let einheit;
            if (einheitSelect.val() === "__freetext__") {
                einheit = $("#Einheit_" + id + "_freetext").val();
            } else {
                einheit = einheitSelect.val();
            }
            if (id !== "") {
                $.ajax({
                    url: "updateParameter.php",
                    data: {
                        "parameterID": id,
                        "wert": wert,
                        "einheit": einheit,
                        "variantenID": variantenID
                    },
                    type: "GET",
                    success: function (data) {
                        makeToaster(data.trim(), true);
                    }
                });
            }
        });
    });

    $("button[value='deleteParameter']").click(function () {
        if (confirm("Parameter wirklich löschen?")) {
            let variantenID = $('#variante').val();
            let id = this.id;
            if (id !== "") {
                $.ajax({
                    url: "deleteParameterFromVariante.php",
                    data: {"parameterID": id, "variantenID": variantenID},
                    type: "GET",
                    success: function (data) {
                        //  alert(data);
                        makeToaster(data.trim(), true);
                        $.ajax({
                            url: "getVarianteParameters.php",
                            data: {"variantenID": variantenID},
                            type: "GET",
                            success: function (data) {
                                $('#variantenParameterCh .xxx').remove();
                                $("#variantenParameter").html(data);
                                $.ajax({
                                    url: "getPossibleVarianteParameters.php",
                                    data: {"variantenID": variantenID},
                                    type: "GET",
                                    success: function (data) {
                                        $("#possibleVariantenParameter").html(data);
                                    }
                                });
                            }
                        });
                    }
                });
            }
        }
    });

});
