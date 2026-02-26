<div class="modal fade" id="InfoModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="helpModalLabel">Hilfe – Bauangaben Check</h5>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">x</button>
            </div>

            <div class="modal-body">
                <p>
                    <strong>Feedback: Bei Problemen, Unstimmigkeiten oder Erweiterungswünschen wenden Sie sich bitte an
                        das Support-Team.</strong>
                </p>
                <p>
                    Es werden ausschließlich die im aktuellen Aufruf ausgewählten Räume geprüft.
                    Die Prüfungen vergleichen <strong>Raumparameter</strong> mit den im Raum verbauten
                    <strong>Elementen</strong>
                    (inkl. Leistungen, Netzarten, Abwärme und Medizingasen) und liefern bei Unstimmigkeiten
                    Fehlermeldungen. Diese verallgemeinerten Checks sind keine zwingenden Bedingungen für alle Räume.
                </p>

                <h6>Allgemeines Verhalten</h6>
                <ul>
                    <li>Die Liste zeigt je Zeile ein Problem zu einem Raum (Raum + Kategorie + Beschreibung).</li>
                    <li>Die Checkboxen in der ersten Spalte sind rein kosmetisch, dienen der Abarbeitungskontrolle und
                        werden im Browser gespeichert (localStorage), bis die Seite neu geladen oder „Markierungen
                        löschen“ gedrückt wird.
                    </li>
                    <li>Mit den Dropdowns „Problem wählen…“ und „Kategorie wählen…“ können alle Zeilen mit einem
                        bestimmten Problem bzw. einer Kategorie auf einmal abgehakt werden.
                    </li>
                    <li>Farbcodierung:
                        ET (Elektro) hellblau, HT (Heizung/Lüftung/Klima/Sanitär) hellviolett, MED-GAS hellgrün,
                        Laser/Röntgen/CEE/sonstige Raumparameter hellgelb usw.
                    </li>
                </ul>

                <h6>Grundlagen der Berechnungen</h6>
                <ul>
                    <li>Gleichzeitigkeit (GLZ): Ist kein Wert angegeben, wird standardmäßig GLZ = 1,0 angenommen.</li>
                    <li>Leistungen werden mit und ohne Gleichzeitigkeit geprüft (INKL. GLZ / EXKL. GLZ) und jeweils mit
                        den Raumparametern verglichen.
                    </li>
                    <li>Wenn ein Element mehrere Netzarten (AV/SV/ZSV/USV) im Parameter „Netzart“ hat, wird die
                        Elementleistung gleichmäßig auf diese Netzarten aufgeteilt (z. B. „SV ZSV“ → 50/50).
                    </li>
                </ul>

                <hr>

                <h5>Prüfungen nach Kategorien</h5>

                <h6>ET – Elektro-Technik</h6>
                <table class="table table-sm table-hover table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>BAUANGABEN-Parameter (Raum / Element)</th>
                        <th>Prüfung</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th>Raumgruppe 1</th>
                        <td>SV muss 1 sein.</td>
                    </tr>
                    <tr>
                        <th>Raumgruppe 2</th>
                        <td>SV 1, ZSV 1, Fußboden Klasse 1 nach B5220; ggf. Hinweis, wenn RG ≠ 2 aber Boden Klasse 1
                            oder umgekehrt.
                        </td>
                    </tr>
                    <tr>
                        <th>RG – SV / ZSV / Fußboden</th>
                        <td>Wenn Anwendungsgruppe (RG) 1 ist, muss SV = 1. Wenn RG = 2, muss ZSV = 1 und Fußboden Klasse
                            1 sein.
                        </td>
                    </tr>
                    <tr>
                        <th>IT Anbindung / ETRJ45-Ports</th>
                        <td>IT-Anbindung und RJ45-Ports dürfen nicht widersprüchlich sein (z. B. IT=1 aber RJ45=0 bzw.
                            umgekehrt).
                        </td>
                    </tr>
                    <tr>
                        <th>ET Anschlussleistung AV / SV / ZSV / USV</th>
                        <td>
                            Summe der Leistungen je Netzart aus den Elementen darf die Raumangabe nicht überschreiten.
                            Zusätzlich Hinweis, falls P je Netzart 8 kW übersteigt (insb. ZSV → Trafodimensionierung
                            beachten).
                            AV+SV+ZSV+USV (Raumparameter) müssen mit der Gesamt-Anschlussleistung des Raumes
                            übereinstimmen.
                        </td>
                    </tr>
                    <tr>
                        <th>Netzarten im Raum (AV/SV/ZSV/USV)</th>
                        <td>
                            Wenn ein Element eine Netzart hat, muss der entsprechende Raumparameter =1 sein.
                            Umgekehrt: Wenn ein Raum Netzart-Leistung hat, müssen Elemente diese Netzart auch nutzen.
                        </td>
                    </tr>
                    <tr>
                        <th>Leistung Elemente im Raum (INKL./EXKL. GLZ)</th>
                        <td>
                            Für jede Netzart wird die Summe der Elementleistungen (mit bzw. ohne GLZ) mit den
                            Raumparametern
                            verglichen, inkl. Plausibilitätscheck: „hat Leistung aber keine Netzart“ bzw. „PElemente =
                            0, aber Raumparameter = 1“.
                        </td>
                    </tr>
                    <tr>
                        <th>CEE-Anschlüsse</th>
                        <td>
                            Wenn bestimmte mobile Röntgen-/C-Bogen-Elemente im Raum vorkommen, muss der Raum einen
                            passenden CEE-Anschluss-Parameter haben (z. B. „ELRoentgen 16A CEE Stk“).
                        </td>
                    </tr>
                    </tbody>
                </table>

                <h6>HT – Heizung, Lüftung, Klima, Sanitär</h6>
                <table class="table table-sm table-hover table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>BAUANGABEN-Parameter (Raum / Element)</th>
                        <th>Prüfung</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th>Abwärme (Raum, INKL./EXKL. GLZ)</th>
                        <td>
                            Summe der Abwärmeleistungen aller Elemente (mit/ohne GLZ) darf die im Raum hinterlegte
                            Abwärmeangabe nicht überschreiten.
                        </td>
                    </tr>
                    <tr>
                        <th>Digestorium / Sicherheitsschrank im Raum</th>
                        <td>
                            Wenn ein Digestorium oder Gefahrstoffsicherheitsschrank-Element im Raum ist,
                            müssen passende Abluft-Parameter am Raum vorhanden sein (z. B. HTAbluftDigestoriumStk,
                            HTAbluftSicherheitsschrankStk).
                        </td>
                    </tr>
                    <tr>
                        <th>Abluft Digestorium / Sicherheitsschrank</th>
                        <td>
                            Spezielle Digestorium- bzw. Sicherheitsschrank-Elemente (verschiedene Varianten) werden
                            geprüft, ob der Abzugs-/Abluftparameter im Raum korrekt hinterlegt ist.
                        </td>
                    </tr>
                    </tbody>
                </table>

                <h6>MED-GAS – Medizingase</h6>
                <table class="table table-sm table-hover table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>BAUANGABEN-Parameter (Raum / Element)</th>
                        <th>Prüfung</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th>1 Kreis O2 / DL-5 / Va / DL-10 / NGA / N2O / CO2 …</th>
                        <td>
                            Wenn entsprechende Gas-Elemente im Raum sind (z. B. Wandentnahmestelle, in DVE/MVE,
                            Digestorium, Labormedienzelle), muss der Raumparameter (z. B. „1 Kreis O2“) gesetzt sein.
                        </td>
                    </tr>
                    <tr>
                        <th>2-Kreis-Parameter</th>
                        <td>
                            Für Paare wie „1 Kreis O2 / 2 Kreis O2“, „1 Kreis Va / 2 Kreis Va“, „1 Kreis DL-5 / 2 Kreis
                            DL-5“
                            wird geprüft, dass die Abhängigkeit stimmt (z. B. kein 2. Kreis ohne 1. Kreis).
                        </td>
                    </tr>
                    <tr>
                        <th>Gasanschluss / Vorabsperrkasten am Stativ</th>
                        <td>
                            Stative mit Gasanschluss brauchen einen hinterlegten Raumparameter für Gasanschluss und
                            ggf. Vorabsperrkasten; außerdem wird geprüft, dass Stative mit Druckluft auch die
                            entsprechenden Druckluft-Parameter im Raum haben.
                        </td>
                    </tr>
                    </tbody>
                </table>

                <h6>Laser / Röntgen / Abdunkelbarkeit / Wandverstärkung</h6>
                <table class="table table-sm table-hover table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>BAUANGABEN-Parameter (Raum / Element)</th>
                        <th>Prüfung</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th>Laser-Elemente im Raum</th>
                        <td>
                            Wenn eines der Laser-Elemente (OP-Laser, Therapie-Laser etc.) im Raum vorhanden ist,
                            muss der Raumparameter „Laseranwendung“ gesetzt sein.
                        </td>
                    </tr>
                    <tr>
                        <th>Röntgen-/Strahlenanwendungs-Elemente</th>
                        <td>
                            Bei Vorhandensein bestimmter Röntgen-/Strahlen-Elemente (Deckenstativ, CT, SPECT, C-Bogen,
                            Linearbeschleuniger usw.) muss der Raumparameter „Strahlenanwendung“ gesetzt sein.
                        </td>
                    </tr>
                    <tr>
                        <th>Abdunkelbarkeit</th>
                        <td>
                            Diverse Ultraschall-, Endoskopie- und Mikroskopie-Elemente erfordern den Raumparameter
                            „Abdunkelbarkeit“ (z. B. kardiologischer Ultraschall, Fluoreszenzmikroskop usw.).
                        </td>
                    </tr>
                    <tr>
                        <th>Wandverstärkung</th>
                        <td>
                            Bestimmte Elemente (z. B. Wandschienen, Konsolen, Halterungen) benötigen den
                            Elementparameter „Wandverstärkung“. Wird für solche Elemente keine Wandverstärkung
                            gefunden, wird ein Fehler ausgegeben.
                        </td>
                    </tr>
                    </tbody>
                </table>

                <hr>

                <p>
                    <strong>Hinweis:</strong> Wenn keine Fehler gefunden werden, erscheint die Meldung
                    „INFO – Keine Fehler gefunden“ für die ausgewählten Räume.
                </p>


            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>
