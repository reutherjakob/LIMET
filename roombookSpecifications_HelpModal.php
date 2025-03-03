<!-- Modal zum Ändern des Raumes -->
<div class='modal fade' id='HelpModal' role='dialog'>
    <div class='modal-dialog modal-xl'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title'>Dokumentation</h5>
                <button type='button' class='btn-close' data-BS-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body' id='mbody'>
                <table class='table table-bordered'>
                    <tbody>
                    <tr>
                        <td><strong>EDIT</strong></td>
                        <td>Checkbox (oben-links) aktivieren, um die Bauangaben direkt in der Tabelle zu bearbeiten.
                            Zum Speichern eines neuen Eintrags die <strong>ENTER</strong> Taste drücken.
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Bei erfolgreicher Aktualisierung des Eintrags erscheint in der rechten oberen Ecke ein
                            grüner <strong>Popup</strong>.
                            Roter Popup signalisiert, dass nicht gespeichert wurde (bsp. nicht mit ENTER bestätigt oder
                            unpassender Datentyp.
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Bestimmte Raumparameter haben spezifische <strong>Datentypen</strong> definiert. Diese
                            Spalten
                            erlauben bzw. speichern nur adequate Eingaben:
                            <ul>
                                <li><strong>Text</strong>: Erlaubt freien Input. Beispielsweise Raumname oder
                                    Raumbereich. (max. 45 Zeichen)
                                </li>
                                <li><strong>Nummern</strong>: Nimmt nur Zahlen und Kommas. Bsp. Leistungen oder
                                    Stückzahlen.
                                </li>
                                <li><strong>Ganzzahlige</strong>: Nimmt nur Zahlen, keine Kommas. Bsp. Flächen oder
                                    Höhen.
                                </li>
                                <li><strong>Vordefinierte/Bit</strong>: In Feldern, für welche entweder 1/0 oder nur
                                    bestimmte vordefinierte Antwortmöglichkeiten infrage kommen, sind diese in einem
                                    Dropdowner abrufbar.
                                </li>
                            </ul>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>FILTER & SPALTEN SICHTBARKEIT </strong></td>
                        <td>
                            <ul>
                                <li>MT-Relevant/Entfallen Filter, Suchleiste und Search Builder erörtern sich selbst.
                                </li>
                                <li> Mithilfe des "vis" buttons können gewünschte Spalten Ein-/ausgeblendet werden.
                                </li>
                                <li>Die grünen/roten Buttons schalten die diesen Kategorien zugeordneten Spalten auf
                                    sichtbar/unsichtbar.
                                </li>
                            </ul>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>AUSWAHL</strong></td>
                        <td>Buttons ermöglichen die Auswahl aller/sichtbarer/keiner Räume. STRG + Klick → Raum zur
                            Auswahl hinzufügen.
                        </td>
                    </tr>

                    <tr>
                        <td><strong>NEU & OUTPUT</strong></td>
                        <td>
                            <i class="fas fa-plus-square"></i> ermöglicht das Anlegen eines neuen Raumes, dessen weitere
                            Parameter über die Tabelle zu befüllen sind. <br>
                            <i class="fas fa-window-restore"></i> Button erzeugt eine Kopie inkl. aller Parameter exkl.
                            Bauangaben-Textfelder eines angelegten Raumes.<br>
                            <i class="fa fa-download"></i> Downloaded die aktuell sichtbaren Spalten der ausgewählten
                            Räume als Excel. <br>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>CHECK</strong></td>
                        <td>Überprüft die markierten Räume auf Plausibilität ihrer Bauangaben. Details finden sich auf
                            der Check-Seite.
                        </td>
                    </tr>

                    <tr>
                        <td><strong>EINSTELLUNGEN </strong></td>
                        <td>
                            Ist "Tabellenzustand speichern" aktiviert (für dieses oder alle Projekte),
                            so speichert der Browser die aktuelle Konfiguration der Darstellung (welche Spaten sichtbar
                            sind, wie viele Zeilen, Filter.
                            (Allerdings muss dies, um zu funktionieren, beim Laden der Tabelle bereits aktiviert sein.
                            Dementsprechend der Aufruf die Seite neu zu laden).
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Weiteres</strong></td>
                        <td>
                            <ul>
                                <li>Löschen von Räumen nicht möglich! Raumparameter durch " - " ersetzbar.</li>
                                <li>Neuer Raumparameter: Entfällt [1 oder 0]</li>
                            </ul>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>