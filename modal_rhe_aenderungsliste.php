<!-- InfoModal: Hilfe zur Änderungsliste -->
<div class="modal fade" id="InfoModal" tabindex="-1" aria-labelledby="InfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="InfoModalLabel">
                    <i class="fas fa-info-circle me-2 text-info"></i>Änderungsliste – so funktioniert's
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
            </div>

            <div class="modal-body">

                <p class="mb-3">
                    Die Änderungsliste vergleicht den Raumbuch-Stand zu <strong>zwei Zeitpunkten</strong>
                    (Start / Ende) und zeigt je Raum, Element und Variante, was sich bei Stückzahl, Budget
                    und Preis geändert hat. Der Stand zu beiden Zeitpunkten wird aus den Änderungsprotokollen
                    des Raumbuchs und der Elementkosten zurückgerechnet.
                </p>

                <div class="accordion" id="infoAccordion">

                    <!-- ============ Bedienung ============ -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="infoHeadSteps">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#infoSteps" aria-expanded="true" aria-controls="infoSteps">
                                <i class="fas fa-list-ol me-2"></i>Bedienung in 4 Schritten
                            </button>
                        </h2>
                        <div id="infoSteps" class="accordion-collapse collapse show" aria-labelledby="infoHeadSteps"
                             data-bs-parent="#infoAccordion">
                            <div class="accordion-body">
                                <ol class="mb-0">
                                    <li class="mb-1"><strong>Zeitraum</strong> wählen (Von / Bis inkl.).</li>
                                    <li class="mb-1"><strong>Arbeitspakete</strong> und ggf. auszublendende
                                        <strong>Budgets</strong> wählen.
                                    </li>
                                    <li class="mb-1"><span class="badge text-bg-success"><i
                                                    class="fas fa-sync-alt me-1"></i>Daten laden</span> klicken.
                                    </li>
                                    <li>Ergebnis über die Anzeige-Filter eingrenzen und per
                                        <span class="badge text-bg-success"><i class="fas fa-file-excel me-1"></i>Excel</span>
                                        exportieren.
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- ============ Zeitraum & Filter ============ -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="infoHeadFilter">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#infoFilter" aria-expanded="false" aria-controls="infoFilter">
                                <i class="fas fa-sliders-h me-2"></i>Zeitraum &amp; Filter
                            </button>
                        </h2>
                        <div id="infoFilter" class="accordion-collapse collapse" aria-labelledby="infoHeadFilter"
                             data-bs-parent="#infoAccordion">
                            <div class="accordion-body">
                                <table class="table table-sm align-middle mb-2">
                                    <tbody>
                                    <tr>
                                        <th class="w-25">Zeitraum</th>
                                        <td>
                                            Stichzeit ist jeweils <strong>01:00 Uhr</strong>: Start = Von-Tag 01:00,
                                            Ende = Tag <em>nach</em> dem Bis-Tag 01:00.
                                            Änderungen am Von-Tag und am Bis-Tag sind damit enthalten.
                                            Änderungen zwischen 00:00 und 01:00 zählen zum Vortag.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Arbeitspakete</th>
                                        <td>
                                            Die AP-Nummer stammt aus dem Raumparameter <em>Anmerkung allgemein</em>.
                                            Räume ohne Eintrag erscheinen als „— ohne Arbeitspaket —“.
                                            „Alle“ wählt sämtliche Arbeitspakete.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Budgets ausblenden</th>
                                        <td>
                                            Standardmäßig sind alle Budgets mit <strong>AEV…</strong> bzw.
                                            <strong>AEG…</strong> gewählt. Positionen, die zum Endzeitpunkt
                                            <em>ausschließlich</em> diesen Budgets zugeordnet sind, werden ausgeblendet.
                                            <br><strong>Ausnahme:</strong> Entfallene Elemente bleiben immer sichtbar –
                                            auch wenn sie über eine AEV hinzugekommen sind.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Nur Standort-Elemente</th>
                                        <td>Berücksichtigt nur Zeilen mit <code>Standort = 1 = Ja</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Bestand ausblenden</th>
                                        <td>Berücksichtigt nur Neu-Elemente (<code>Neu/Bestand = 1</code>).</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="alert alert-light border small mb-0">
                                    <i class="fas fa-redo me-1"></i>
                                    Zeitraum, Arbeitspakete, Standort und Bestand wirken erst nach
                                    <strong>Daten laden</strong>. Der Budget-Filter wirkt sofort.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============ Anzeige & Export ============ -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="infoHeadAnzeige">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#infoAnzeige" aria-expanded="false" aria-controls="infoAnzeige">
                                <i class="fas fa-filter me-2"></i>Anzeige &amp; Export
                            </button>
                        </h2>
                        <div id="infoAnzeige" class="accordion-collapse collapse" aria-labelledby="infoHeadAnzeige"
                             data-bs-parent="#infoAccordion">
                            <div class="accordion-body">
                                <p class="small text-muted">Alle Einstellungen in diesem Bereich wirken sofort, ohne
                                    neu zu laden.</p>
                                <table class="table table-sm align-middle mb-2">
                                    <tbody>
                                    <tr>
                                        <th class="w-25">Änderungsarten filtern</th>
                                        <td>Blendet Positionen je Änderungsart ein/aus. Die Zahl im Button zeigt die
                                            Anzahl der Positionen. „unverändert“ ist standardmäßig aus.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Preisänderungen ignorieren bis</th>
                                        <td>
                                            Schwellwert für den <strong>Einheitspreis</strong> (Standard 0,50 €).
                                            Ändert sich nur der Preis und liegt die Abweichung darunter, gilt die
                                            Position als „unverändert“. Die Beträge in der Tabelle bleiben echt.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Einheitspreise anpassen</th>
                                        <td>
                                            Erhöht/verringert alle Einheitspreise zum Start bzw. Ende um x %
                                            (z. B. für Indexanpassungen). Wirkt auf EP, Positionspreise, Summen und
                                            Excel. Die Änderungsart und der Schwellwert beziehen sich weiterhin auf die
                                            Originalpreise.
                                            <br><span class="badge text-bg-warning">Preise angepasst</span>
                                            im Tabellenkopf zeigt, dass eine Anpassung aktiv ist.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Excel: Ergebniszeilen</th>
                                        <td>Fügt graue Summenzeilen je Raum, Arbeitspaket und Gesamt ein – wie in der
                                            bisherigen Excel-Änderungsliste.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tabellen-Buttons</th>
                                        <td>
                                            <span class="badge text-bg-success">Excel</span> exportiert genau die
                                            aktuell gefilterten Zeilen (inkl. Blatt „Info“ mit allen Einstellungen).
                                            <span class="badge text-bg-light border">Filter</span> öffnet den
                                            Such-Builder für eigene Bedingungen,
                                            <span class="badge text-bg-light border">Spalten</span> blendet Spalten
                                            ein/aus.
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="alert alert-warning small mb-0">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Schwellwert und Preisanpassung werden <strong>nicht in der Datenbank
                                        gespeichert</strong> – sie beeinflussen nur Anzeige und Export.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============ Änderungsarten ============ -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="infoHeadArten">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#infoArten" aria-expanded="false" aria-controls="infoArten">
                                <i class="fas fa-tags me-2"></i>Änderungsarten
                            </button>
                        </h2>
                        <div id="infoArten" class="accordion-collapse collapse" aria-labelledby="infoHeadArten"
                             data-bs-parent="#infoAccordion">
                            <div class="accordion-body">
                                <p class="small text-muted">Jede Position erhält genau eine Art – geprüft wird in
                                    dieser Reihenfolge:</p>
                                <table class="table table-sm align-middle mb-2">
                                    <tbody>
                                    <tr>
                                        <td class="w-25"><span class="badge text-bg-success">Zusatz</span></td>
                                        <td>Stück zum Start = 0, zum Ende &gt; 0.</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge text-bg-danger">Entfall</span></td>
                                        <td>Stück zum Start &gt; 0, zum Ende = 0.</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge text-bg-warning">Mengenänderung</span></td>
                                        <td>Stückzahl hat sich geändert.</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge text-bg-info">Budgetänderung</span></td>
                                        <td>Gleiche Stückzahl, aber anderes Budget.</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge text-bg-secondary">Preisänderung</span></td>
                                        <td>Nur der Positionspreis hat sich geändert (Schwellwert beachten).
                                            Mauszeiger auf das Badge zeigt alten und neuen EP.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge text-bg-light border">unverändert</span></td>
                                        <td>Keine relevante Änderung.</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="alert alert-light border small mb-0">
                                    <i class="fas fa-exchange-alt me-1"></i>
                                    Ein <strong>Variantenwechsel</strong> (z. B. A → B) erscheint als zwei Zeilen:
                                    Entfall der alten und Zusatz der neuen Variante.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============ Spalten ============ -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="infoHeadSpalten">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#infoSpalten" aria-expanded="false" aria-controls="infoSpalten">
                                <i class="fas fa-columns me-2"></i>Spalten der Tabelle
                            </button>
                        </h2>
                        <div id="infoSpalten" class="accordion-collapse collapse" aria-labelledby="infoHeadSpalten"
                             data-bs-parent="#infoAccordion">
                            <div class="accordion-body">
                                <table class="table table-sm align-middle mb-0">
                                    <tbody>
                                    <tr>
                                        <th class="w-25">Veränderung zum VE (Kommentar)</th>
                                        <td>Kurzbeschreibung der Raumbuch-Zeile zum Endzeitpunkt; bei Entfall der
                                            letzte Stand vor dem Löschen.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Budget Start / Ende</th>
                                        <td>Budgetnummer(n) zum jeweiligen Zeitpunkt. Geänderte Werte sind
                                            <strong class="text-primary">blau</strong> hervorgehoben.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Stk Start / Ende</th>
                                        <td>Summe der Stückzahl im Raum. Geänderte Werte sind <strong>fett</strong>.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>EP Start / Ende</th>
                                        <td>Einheitspreis der Elementvariante im Projekt zum jeweiligen Zeitpunkt.
                                            <i class="fas fa-exclamation-triangle text-warning"></i> beim Element =
                                            kein Preis hinterlegt.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>PP Start / Ende</th>
                                        <td>Positionspreis = Stück × EP.</td>
                                    </tr>
                                    <tr>
                                        <th>Δ PP</th>
                                        <td><span class="text-danger">rot = Mehrkosten</span>,
                                            <span class="text-success">grün = Einsparung</span>.
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ============ Hinweise ============ -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="infoHeadHinweise">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#infoHinweise" aria-expanded="false" aria-controls="infoHinweise">
                                <i class="fas fa-lightbulb me-2"></i>Gut zu wissen
                            </button>
                        </h2>
                        <div id="infoHinweise" class="accordion-collapse collapse" aria-labelledby="infoHeadHinweise"
                             data-bs-parent="#infoAccordion">
                            <div class="accordion-body">
                                <ul class="mb-0">
                                    <li class="mb-1">Mehrere Raumbuch-Zeilen mit gleichem Element und gleicher Variante
                                        in einem Raum werden zu <strong>einer Position</strong> zusammengefasst.
                                    </li>

                                    <li class="mb-1">Die Summen im Tabellenfuß, in der Zusammenfassung und im Excel
                                        beziehen sich immer auf die <strong>aktuell gefilterten</strong> Zeilen.
                                    </li>
                                    <li><i class="fas fa-caret-left"></i> / <i class="fas fa-caret-right"></i>
                                        blendet die Filterspalte aus bzw. ein – die Tabelle nutzt dann die volle Breite.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
            </div>

        </div>
    </div>
</div>