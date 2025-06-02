$("button[value='saveParameter']").click(function () {
    let id = this.id;
    let wertElement = $("#Wert_" + id);  // Changed to underscore syntax
    let einheitElement = $("#Einheit_" + id);

    // Proper select detection and value retrieval
    let wert = wertElement.is("select") ? wertElement.val() : wertElement.val();
    let einheit = einheitElement.is("select") ? einheitElement.val() : einheitElement.val();

    console.log("Values:", wert, einheit, id); // Debugging
    let variantenID = $('#variante').val();

    if (id !== "") {
        $.ajax({
            url: "updateParameter.php",
            data: {"parameterID": id, "wert": wert, "einheit": einheit, "variantenID": variantenID},
            type: "GET",
            success: function (data) {
                makeToaster(data.trim(), true);
            }
        });
    }
});

$("button[value='saveAllParameter']").click(function () {
    const deleteBtns = document.querySelectorAll('#tableElementParameters tbody button[value="deleteParameter"]');
    const ids = Array.from(deleteBtns).map(btn => btn.id);
    let variantenID = $('#variante').val();

    ids.forEach(function(id) {
        let wertElement = $("#Wert_" + id);
        let einheitElement = $("#Einheit_" + id);

        // Proper select detection and value retrieval
        let wert = wertElement.val();
        let einheit = einheitElement.val();

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

