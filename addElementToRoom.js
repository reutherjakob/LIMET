$(document).ready(function () {
    $("button[value='addElement']").click(function () {
        let elementID = this.id;
        if (elementID !== "") {
            $.ajax({
                url: "getElementToElementID.php",
                data: {"elementID": elementID},
                type: "GET",
                success: function (data) {
                    $("#elID").html(data);
                    $.ajax({
                        url: 'getSessionRoomData.php',
                        dataType: 'json',
                        success: function (data) {
                            if (!data.error) {
                                $('#roomID').text(
                                    data.Raumbezeichnung + ' ' +
                                    data.Raumnr + ' ' +
                                    data.RaumbereichNutzer + ' ' +
                                    data.Geschoss
                                );
                            } else {
                                $('#roomID').text(data.error);
                            }
                        }
                    });
                }
            });
        }
    });

    $("#addElementToRoom").click(function () {
        $.ajax({
            url: "addElementToRoom.php",
            type: "GET",
            success: function (data) {
                makeToaster(data, true);
                setTimeout(function () {
                    $.ajax({
                        url: "getRoomElementsDetailed1.php",
                        type: "GET",
                        success: function (data) {
                            $("#roomElements").html(data);
                        }
                    });
                }, 100);
            }
        });
    });

    $("#saveElement").click(function () {
        let bezeichnung = $("#bezeichnung").val();
        let kurzbeschreibung = $("#kurzbeschreibungModal").val();
        if (bezeichnung !== "" && kurzbeschreibung !== "") {
            $.ajax({
                url: "saveElement.php",
                data: {"bezeichnung": bezeichnung, "kurzbeschreibung": kurzbeschreibung},
                type: "GET",
                success: function (data) {
                    $('#changeElementModal').modal('hide');
                    alert(data);
                    $.ajax({
                        url: "getElementsInDB.php",
                        type: "GET",
                        success: function (data) {
                            $("#elementsInDB").html(data);
                        }
                    });
                }
            });
        } else {
            alert("Bitte alle Felder ausfüllen!");
        }
    });
});
