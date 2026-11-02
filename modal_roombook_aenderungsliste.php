<!-- InfoModal: Hilfe zur Änderungsliste -->
<div class="modal fade" id="InfoModal" tabindex="-1" aria-labelledby="InfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="InfoModalLabel">
                    <i class="fas fa-info-circle me-2 text-info"></i>Änderungsliste – kurz erklärt
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
            </div>

            <div class="modal-body">

                <p class="mb-3">
                    Vergleicht das Raumbuch zu <strong>zwei Zeitpunkten</strong> (Start / Ende) und zeigt je Raum,
                    Element und Variante, was sich bei Stück, Budget und Preis geändert hat.
                </p>

                <div class="accordion" id="infoAccordion">

                    <!-- ============ Bedienung ============ -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="infoHeadSteps">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#infoSteps" aria-expanded="true" aria-controls="infoSteps">
                                <i class="fas fa-list-ol me-2"></i>Bedienung
                            </button>
                        </h2>
                        <div id="infoSteps" class="accordion-collapse collapse show" aria-labelledby="infoHeadSteps"
                             data-bs-parent="#infoAccordion">
                            <div class="accordion-body">
                                <ol class="mb-3">
                                    <li><strong>Zeitraum</strong> und <strong>Arbeitspakete</strong> wählen.</li>
                                    <li><span class="badge text-bg-success"><i class="fas fa-sync-alt me-1"></i>Daten
                                            laden</span> klicken.
                                    </li>
                                    <li>Mit <strong>Budgets</strong> und <strong>Budget-Status</strong> festlegen, was
                                        mitzählt.
                                    </li>
                                    <li>Mit den <strong>Anzeige-Filtern</strong> eingrenzen und per
                                        <span class="badge text-bg-success"><i class="fas fa-file-excel me-1"></i>Excel</span>
                                        exportieren.
                                    </li>
                                </ol>
                                <table class="table table-sm table-bordered small mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th class="w-50">Einstellung</th>
                                        <th>wirkt</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Zeitraum, Arbeitspakete, Standort, Bestand</td>
                                        <td>nach <strong>Daten laden</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Budgets ausblenden, Budget-Status</td>
                                        <td>lädt automatisch neu</td>
                                    </tr>
                                    <tr>
                                        <td>Änderungsarten, Preis-Schwellwert, Preisanpassung</td>
                                        <td>sofort</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ============ Laden ============ -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="infoHeadLaden">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#infoLaden" aria-expanded="false" aria-controls="infoLaden">
                                <i class="fas fa-database me-2"></i>Laden &amp; Budgets
                            </button>
                        </h2>
                        <div id="infoLaden" class="accordion-collapse collapse" aria-labelledby="infoHeadLaden"
                             data-bs-parent="#infoAccordion">
                            <div class="accordion-body">
                                <table class="table table-sm align-middle small mb-2">
                                    <tbody>
                                    <tr>
                                        <th class="w-25">Zeitraum</th>
                                        <td>Standard: 20.12.2025 bis heute. Stichzeit jeweils <strong>01:00
                                                Uhr</strong> – Von- und Bis-Tag sind enthalten.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Arbeitspakete</th>
                                        <td>Aus <em>Anmerkung allgemein</em> des Raums. „Alle“ wählt sämtliche.</td>
                                    </tr>
                                    <tr>
                                        <th>Standort / Bestand</th>
                                        <td>Nur Zeilen mit <code>Standort = 1</code> bzw. nur Neu-Elemente.</td>
                                    </tr>
                                    <tr>
                                        <th>Budgets ausblenden</th>
                                        <td>Alle Elemente, deren <strong>Endbudget</strong> hier gewählt ist, fallen
                                            komplett weg – Zusätze, Entfälle, Mengen-, Budget- und Preisänderungen.
                                            Standard: alle <strong>AEV…/AEG…</strong>; „Alle“ stellt das wieder her.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Budget-Status</th>
                                        <td>Je Status ein Schalter „… anzeigen“. Ausgeschaltet = alle Elemente, deren
                                            Endbudget diesen Status hat, werden ausgeblendet.
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                <span class="badge text-bg-warning"><i class="fas fa-minus me-1"></i>Offen</span>
                                                <span class="badge text-bg-success"><i class="fas fa-check me-1"></i>Freigegeben</span>
                                                <span class="badge text-bg-danger"><i class="fas fa-times me-1"></i>Abgelehnt</span>
                                                <span class="badge text-bg-info"><i class="fas fa-map me-1"></i>Planfortschreibung</span>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="alert alert-info small py-2 mb-0">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Endbudget</strong> = Budget der Raumbuch-Zeile zum Endzeitpunkt (bei
                                    gelöschten Zeilen das letzte bekannte). Geprüft wird je Raumbuch-Zeile; der
                                    Hinweis über dem Dropdown zeigt, wie viele ausgeblendet wurden.
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
                                <table class="table table-sm align-middle small mb-2">
                                    <tbody>
                                    <tr>
                                        <th class="w-25">Änderungsarten</th>
                                        <td>Ein-/ausblenden je Art; Zahl im Button = Anzahl. „unverändert“ ist
                                            standardmäßig aus.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Preis-Schwellwert</th>
                                        <td>Reine Preisänderungen unter diesem EP-Betrag gelten als „unverändert“.</td>
                                    </tr>
                                    <tr>
                                        <th>Preisanpassung</th>
                                        <td>Alle EP zum Start/Ende um x % ändern (z. B. Indexierung). Aktiv erkennbar an
                                            <span class="badge text-bg-warning">Preise angepasst</span>.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Buttons</th>
                                        <td>
                                            <span class="badge text-bg-success">Excel</span> = gefilterte Zeilen inkl.
                                            Blatt „Info“ (optional mit Ergebniszeilen),
                                            <span class="badge text-bg-light border">Filter</span> = eigene Bedingungen,
                                            <span class="badge text-bg-light border">Spalten</span> = ein/aus.
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <p class="small text-muted mb-0">Filter und Anpassungen ändern nichts in der Datenbank.</p>
                            </div>
                        </div>
                    </div>

                    <!-- ============ Tabelle lesen ============ -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="infoHeadTabelle">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#infoTabelle" aria-expanded="false" aria-controls="infoTabelle">
                                <i class="fas fa-table me-2"></i>Tabelle lesen
                            </button>
                        </h2>
                        <div id="infoTabelle" class="accordion-collapse collapse" aria-labelledby="infoHeadTabelle"
                             data-bs-parent="#infoAccordion">
                            <div class="accordion-body">
                                <p class="small mb-1"><strong>Änderungsart</strong> (geprüft in dieser Reihenfolge):</p>
                                <ul class="small mb-3">
                                    <li><span class="badge text-bg-success">Zusatz</span> Stk Start = 0, Ende &gt; 0</li>
                                    <li><span class="badge text-bg-danger">Entfall</span> Stk Start &gt; 0, Ende = 0</li>
                                    <li><span class="badge text-bg-warning">Mengenänderung</span> Stückzahl geändert</li>
                                    <li><span class="badge text-bg-info">Budgetänderung</span> gleiche Stückzahl,
                                        anderes Budget
                                    </li>
                                    <li><span class="badge text-bg-secondary">Preisänderung</span> nur der Preis
                                        (Mauszeiger zeigt alten/neuen EP)
                                    </li>
                                    <li><span class="badge text-bg-light border">unverändert</span> keine relevante
                                        Änderung
                                    </li>
                                </ul>
                                <table class="table table-sm align-middle small mb-0">
                                    <tbody>
                                    <tr>
                                        <th class="w-25">Budget Ende</th>
                                        <td><strong class="text-primary">Blau</strong> = anders als zum Start.</td>
                                    </tr>
                                    <tr>
                                        <th>Status Ende</th>
                                        <td>Aktueller Status des Endbudgets (bei Entfall leer).</td>
                                    </tr>
                                    <tr>
                                        <th>Stk / EP</th>
                                        <td>Geänderte Werte <strong>fett</strong>;
                                            <i class="fas fa-exclamation-triangle text-warning"></i> = kein Preis
                                            hinterlegt.
                                        </td>
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

                    <!-- ============ Gut zu wissen ============ -->
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
                                <ul class="small mb-0">
                                    <li>Gleiche Elemente/Varianten in einem Raum werden zu <strong>einer
                                            Position</strong> zusammengefasst.
                                    </li>
                                    <li>Ein <strong>Variantenwechsel</strong> erscheint als Entfall + Zusatz.</li>
                                    <li>Wird ein Element <strong>aus</strong> einer ausgeblendeten AEV in ein anderes
                                        Budget verschoben, bleibt es sichtbar (das Endbudget zählt).
                                    </li>
                                    <li>Der Budget-Status ist immer der <strong>aktuelle</strong>, nicht der historische.
                                    </li>
                                    <li>Summen (Tabellenfuß, Zusammenfassung, Excel) beziehen sich auf die gefilterten
                                        Zeilen.
                                    </li>
                                    <li><i class="fas fa-caret-left"></i> / <i class="fas fa-caret-right"></i> blendet
                                        die Filterspalte aus/ein.
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