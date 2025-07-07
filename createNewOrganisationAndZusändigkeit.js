
$(document).ready(function () {

    $("#addZustaendigkeitBtn").click(function () {
        if (confirm("Wurde vorab genau geprüft, ob es diese Zuständigkeit schon gibt?")) {
            $("#newZustaendigkeitName").val('');
            $("#addZustaendigkeitModal").modal('show');
        }
    });

    $('#zustaendigkeit').select2({
        placeholder: 'Zuständigkeit wählen.',
        dropdownCssClass: 'select2-dropdown-long'
    });

    $("#saveZustaendigkeitBtn").click(function () {
        let Name = $("#newZustaendigkeitName").val().trim();
        if (Name === "") {
            alert("Bitte geben Sie einene Zustaendigkeit ein.");
            return;
        }
        $.ajax({
            url: "saveZustaendigkeit.php",
            type: "POST",
            data: {name: Name},
            success: function (response) {
                console.log(response);
                try {
                    let data = JSON.parse(response);
                    if (data.success) {
                        let newOption = $("<option>")
                            .val(data.id)
                            .text(data.name)
                            .prop("selected", true);
                        $("#zustaendigkeit").append(newOption);
                        $("#addZustaendigkeitModal").modal('hide');
                        makeToaster(response,true);
                    } else {
                        alert(data.error || "Fehler beim Hinzufügen der Organisation.");
                    }
                } catch (e) {
                    alert("Fehler beim Verarbeiten der Antwort.");
                }
            },
            error: function () {
                alert("Fehler beim Speichern");
            }
        });
    });

    $("#addOrganisationBtn").click(function () {
        if (confirm("Wurde vorab genau geprüft, ob es diese Organisation schon gibt?")) {
            $("#newOrganisationName").val('');
            $("#addOrganisationModal").modal('show');
        }
    });

    $("#saveOrganisationBtn").click(function () {
        let orgName = $("#newOrganisationName").val().trim();
        if (orgName === "") {
            alert("Bitte geben Sie einen Organisationsnamen ein.");
            return;
        }
        $.ajax({
            url: "saveOrganisation.php",
            type: "POST",
            data: {name: orgName},
            success: function (response) {
                try {
                    let data = JSON.parse(response);
                    if (data.success) {
                        // Add new option to select and select it
                        let newOption = $("<option>")
                            .val(data.id)
                            .text(data.name)
                            .prop("selected", true);
                        $("#organisation").append(newOption);
                        $("#addOrganisationModal").modal('hide');
                        makeToaster(response,true);
                    } else {
                        alert(data.error || "Fehler beim Hinzufügen der Organisation.");
                    }
                } catch (e) {
                    console.log(e);
                    alert("Fehler beim Verarbeiten der Antwort.");
                }
            },
            error: function () {
                alert("Fehler beim Speichern.");
            }
        });
    });

    $('#organisation').select2({
        placeholder: 'Organistation wählen',
        dropdownCssClass: 'select2-dropdown-long'
    });

});