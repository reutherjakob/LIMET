<!-- Modal zum Ändern des Raumes -->
<div class='modal fade' id='HelpModal' role='dialog' tabindex="-1">
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
                            Zum Speichern eines neuen Eintrags die <strong>ENTER</strong> oder <strong>TAB</strong>
                            Taste drücken.<br>
                            Bei erfolgreicher Aktualisierung des Eintrags erscheint in der rechten oberen Ecke ein
                            grüner <strong>Popup</strong>.
                            Roter Popup signalisiert, dass nicht gespeichert wurde (bsp. nicht mit ENTER bestätigt oder
                            unpassender Datentyp.<br>
                            Raumparameter haben spezifische <strong>Datentypen</strong> definiert. Diese
                            Spalten
                            erlauben bzw. speichern nur adequate Eingaben:
                            <ul>
                                <li><strong>Text</strong>: Erlaubt max. 45 Zeichen.
                                </li>
                                <li><strong>Nummern</strong>: Nimmt Zahlen & Symbole. (Dezimalkomma: . oder ,).
                                </li>
                                <li><strong>Vordefinierte/Bit</strong>: In Feldern, für welche entweder 1/0 oder nur
                                    bestimmte vordefinierte Antwortmöglichkeiten infrage kommen.
                                </li>
                            </ul>
                        </td>

                    </tr>

                    <tr>
                        <td></td>
                        <td>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>FILTER & SPALTEN SICHTBARKEIT </strong></td>
                        <td>
                            <ul>
                                <li>MT-Relevant/Entfallen Filter, Suchleiste und Search Builder filtern die Tabelle.
                                </li>
                                <li> Mithilfe des "vis" buttons können gewünschte Spalten Ein-/ausgeblendet werden.
                                </li>
                                <li>Die grünen/roten Buttons schalten die diesen Kategorien zugeordneten Spalten auf
                                    sichtbar/unsichtbar. (ALLE / Raumangaben/ HeizungKühlungLüftungSanitär/ Elektrotechnik / Architektur / MedGas/ Laborparameter)
                                </li>
                            </ul>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>AUSWAHL</strong></td>
                        <td>
                            <ul>
                                <li>
                                    <button class="btn border-secondary btn-light fas fa-check"></button>
                                    <strong>Alle auswählen:</strong> Markiert alle Räume in der Tabelle.
                                </li>
                                <li>
                                    <button class="btn btn-light border-secondary fas fa-eye"></button>
                                    <strong>Sichtbare auswählen:</strong> Markiert nur die aktuell sichtbaren Räume.
                                </li>
                                <li>
                                    <button class="btn btn-light border-secondary fas fa-times"></button>
                                    <strong>Keine auswählen:</strong> Hebt die Auswahl aller Räume auf.
                                </li>
                                <li>
                                    STRG + Klick → Raum zur Auswahl hinzufügen.
                                </li>
                            </ul>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>NEU & OUTPUT</strong></td>
                        <td>
                            <ul>
                                <li>
                                    <i class="fas fa-plus-square"></i>
                                    <strong>Neuen Raum anlegen:</strong> Öffnet das Modal zum Erstellen eines neuen
                                    Raums.
                                </li>
                                <li>
                                    <i class="fas fa-window-restore"></i>
                                    <strong>Ausgewählten Raum kopieren:</strong> Erstellt eine Kopie der aktuell
                                    ausgewählten Zeile (ohne Bauangaben-Textfelder).
                                </li>
                                <li>
                                    <i class="fa fa-download"></i>
                                    <strong>Download als Excel:</strong> Exportiert die aktuell sichtbaren Spalten der
                                    ausgewählten Räume als Excel-Datei.
                                </li>
                            </ul>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>FILTER & SICHTBARKEIT</strong></td>
                        <td>
                            <ul>
                                <li>
                                    <span class="btn btn-light border-secondary">Vis</span>
                                    <strong>Spalten ein-/ausblenden:</strong> Mit dem <b>Vis</b>-Button können
                                    gewünschte Spalten sichtbar oder unsichtbar geschaltet werden.
                                </li>
                                <li>
                                    <i class="fa fa-paper-plane"></i> <b>R</b>: Schaltet die für Berichte relevanten
                                    Spalten sichtbar.
                                </li>
                                <li>
                                    Die grünen/roten Buttons schalten die diesen Kategorien zugeordneten Spalten auf
                                    sichtbar/unsichtbar.
                                </li>
                            </ul>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Bauangaben CHECK</strong></td>
                        <td>
                            <i class="fas fa-vote-yea"></i>
                             Überprüft die markierten Räume auf Plausibilität ihrer Bauangaben.
                        </td>
                    </tr>

                    <tr>
                    <tr>
                        <td><strong>EINSTELLUNGEN</strong></td>
                        <td>
                            <i class="fas fa-cogs"></i>
                            Öffnet das Einstellungs-Modal für weitere Optionen.<br>
                            <ul>
                                <li>
                                    <b>Tabellenzustand speichern (f. alle Projekte)</b>:
                                    Merkt sich die aktuelle Tabellenkonfiguration (sichtbare Spalten, Filter, Sortierung, Zeilenanzahl) für <u>alle</u> Projekte im Browser.
                                </li>
                                <li>
                                    <b>Tabellenzustand speichern (f. aktuelles Projekt)</b>:
                                    Merkt sich die aktuelle Tabellenkonfiguration <u>nur</u> für das aktuell geöffnete Projekt.
                                </li>
                                <li>
                                    <b>Tabelle bearbeitbar initiieren</b>:
                                    Aktiviert die Bearbeitungsfunktion der Tabelle direkt beim Laden (Checkbox oben links ist dann aktiv).
                                </li>
                                <li>
                                    <b>Labels über den Buttons anzeigen</b>:
                                    Zeigt kurze Beschriftungen über den Button-Gruppen an (z.B. "Auswahl", "Neu & Output").
                                </li>
                                <li>
                                    <b>Button Texte anzeigen</b>:
                                    Zeigt statt nur Icons auch Textbeschriftungen auf den Buttons an.
                                </li>
                            </ul>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Weiteres</strong></td>
                        <td>
                            <ul>
                                <li>Löschen von Räumen nicht möglich! Neuer Raumparameter zum Filtern: Entfällt  </li>
                                <li>Raumparameter durch " - " ersetzbar.</li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Parameter Details </strong></td>
                        <td>
                            <ul>
                                <li>Abdunkelbarkeit: Für KH nur "abdunkelbar" [1 und 2] und "kein Anspruch" [0]. GCP hat "kein anspruch" =0, "vollverdunkelbar" = 1 und "abdunkelbar" = 2.</li>
                            </ul>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>