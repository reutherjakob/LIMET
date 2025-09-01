// Besprechung.js
class Besprechung {
    constructor({id, action, name, datum, startzeit, endzeit, ort, verfasser, art, projektID = null}) {
        this.id = id; // id der Vermerkgruppe
        this.action = action;
        this.name = name;           // string: Name der Besprechung
        this.datum = datum;         // string (YYYY-MM-DD)
        this.startzeit = startzeit; // string (HH:MM)
        this.endzeit = endzeit;     // string (HH:MM)
        this.ort = ort;             // string
        this.verfasser = verfasser; // string
        this.art = art;             // string (z.B. "Protokoll Besprechung")
        // this.projektID = projektID; // int or null
        // this.trhteID =0;            // ID der letzten Änderung (tabelle_räume_has_elemente) in der Besprechung

        this.roomVermerkMap = {}; // key: roomID, value: array of vermerkIDs
    }

    // Serialize object to a plain payload object for AJAX submission
    toPayload() {
        const payload = {};
        Object.keys(this).forEach(key => {
            if (this[key] !== null && this[key] !== undefined && this[key] !== '') {
                payload[key] = this[key];
            }
        });
        return payload;
    }

    create(formSelector, modalSelector, pdfSelector, toasterFn, updateFilterFn) {
        const self = this;
        $(formSelector).on('submit', function (e) {
            e.preventDefault();

            // Set/update properties from form fields
            self.action = "new";
            self.name = $("#meetingName").val();
            self.datum = $("#meetingDatum").val();
            self.startzeit = $("#meetingUhrzeitStart").val();
            self.endzeit = $("#meetingUhrzeitEnde").val();
            self.ort = $("#meetingOrt").val();
            self.verfasser = $("#meetingVerfasser").val();
            self.art = "Protokoll Besprechung";

            if (self.name && self.verfasser && self.datum && self.startzeit) {
                $.ajax({
                    url: "../controllers/BesprechungController.php",
                    type: "POST",
                    data: self.toPayload(),
                    success: function (response) {
                        if (response.success) {
                            self.id = response.insertId;
                            $(modalSelector).modal('hide');
                            $(formSelector).reset();
                            $(pdfSelector).attr('src', '../../PDFs/pdf_createVermerkGroupPDF.php?gruppenID=' + self.id);
                            if (typeof toasterFn === "function") toasterFn("Besprechung erfolgreich angelegt! - ID:" + self.id, true);
                            if (typeof updateFilterFn === "function") updateFilterFn();
                        } else {
                            if (typeof toasterFn === "function") toasterFn("Fehler: " + (response.message || "Unbekannter Fehler"), false);
                        }
                    },
                    error: function (xhr) {
                        const errorMsg = xhr.responseJSON?.errors?.join(', ') || xhr.responseText || "Fehler beim Anlegen";
                        alert(errorMsg);
                    }
                });
            } else {
                if (typeof toasterFn === "function") toasterFn("Bitte alle Pflichtfelder ausfüllen!", false);
            }
        });
    }


    consolidateMultipleElementsperRoom(selectedRaumbereiche) {
        if (!selectedRaumbereiche || selectedRaumbereiche.length === 0) {
            alert("Bitte mindestens einen Raumbereich wählen.");
            return;
        }

        $.ajax({
            url: '../controllers/consolidateMultipleElementsperRoomsperRoomarea.php',
            method: 'POST',
            data: {
                raumbereiche: selectedRaumbereiche
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    console.log("Konsolidierung erfolgreich:", response.message);
                } else {
                    alert("Fehler bei Konsolidierung: " + (response.message || "Unbekannter Fehler"));
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX-Fehler bei Konsolidierung:", status, error);
                alert("Serverfehler bei der Konsolidierung der Elemente.");
            }
        });
    }

    bindModalShowHandler(modalSelector, tableSelector, toasterFn, updateFilterFn, loadRaumbereicheFn) {
        const self = this;

        $(modalSelector).on('shown.bs.modal', function () {
            if ($.fn.DataTable.isDataTable(tableSelector)) {
                $(tableSelector).DataTable().destroy();
            }
            $(tableSelector).DataTable({
                ajax: {
                    url: '../controllers/BesprechungController.php',
                    type: 'POST',
                    data: {action: 'getProtokollBesprechungen'},
                    dataSrc: function (json) {
                        if (!json.success) {
                            $('#besprechungLoading').text('Fehler: ' + json.message);
                            return [];
                        }
                        $('#besprechungLoading').text('');
                        return json.data;
                    },
                    error: function (xhr, error, thrown) {
                        $('#besprechungLoading').text('Serverfehler: ' + thrown);
                    }
                },
                columns: [
                    {data: 'idtabelle_Vermerkgruppe', title: "id", visible: false},
                    {data: 'Gruppenname', title: "Name"},
                    {data: 'Gruppenart', title: "Art"},
                    {data: 'Ort', title: "Ort"},
                    {data: 'Verfasser', title: "Verfasser"},
                    {data: 'Startzeit', title: "Startzeit"},
                    {data: 'Endzeit', title: "Endzeit"},
                    {data: 'Datum', title: "Datum"}
                ],
                searching: true,
                paging: true,
                info: false,
                lengthChange: false,
                language: {url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/de-DE.json'},
                rowId: 'id',
                createdRow: function (row, data) {
                    $(row).off('click').on('click', function () {
                        $('#besprechungTable tbody tr').removeClass('selected');
                        $(this).addClass('selected');

                        self.id = data.idtabelle_Vermerkgruppe;
                        self.action = "opened";
                        self.name = data.Gruppenname;
                        self.datum = data.Datum;
                        self.startzeit = data.Startzeit;
                        self.endzeit = data.Endzeit;
                        self.ort = data.Ort;
                        self.verfasser = data.Verfasser;
                        self.art = "Protokoll Besprechung";
                        self.projektID = data.tabelle_projekte_idTABELLE_Projekte;

                        console.log("Raw: ", data);
                        console.log(self.toPayload());

                        $('#pdfPreview').attr('src', '../../PDFs/pdf_createVermerkGroupPDF.php?gruppenID=' + data.idtabelle_Vermerkgruppe);

                        setTimeout(() => {
                            if (typeof updateFilterFn === "function") updateFilterFn();
                            $(modalSelector).modal("hide");
                            if ($.fn.DataTable.isDataTable(tableSelector)) {
                                $(tableSelector).DataTable().destroy();
                            }
                            if (typeof toasterFn === "function") toasterFn("Besprechung geöffnet " + self.id, true);
                            if (typeof loadRaumbereicheFn === "function") loadRaumbereicheFn(self.id);
                        }, 100);
                    });
                }
            });
        });
    }


}
