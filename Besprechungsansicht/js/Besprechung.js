// Besprechung.js
class Besprechung {
    constructor({id, action, name, datum, startzeit, endzeit, ort, verfasser, art, projektID = null }) {
        this.id = id; // id der Vermerkgruppe
        this.action = action;
        this.name = name;           // string: Name der Besprechung
        this.datum = datum;         // string (YYYY-MM-DD)
        this.startzeit = startzeit; // string (HH:MM)
        this.endzeit = endzeit;     // string (HH:MM)
        this.ort = ort;             // string
        this.verfasser = verfasser; // string
        this.art = art;             // string (z.B. "Protokoll Besprechung")
        this.projektID = projektID; // int or null
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
}
