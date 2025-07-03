$(document).ready(function () {
    $("button[value='saveParameter']").click(function () {
        let id = this.id;
        let wertElement = $("#Wert_" + id);  // Changed to underscore syntax
        let einheitElement = $("#Einheit_" + id);
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


});