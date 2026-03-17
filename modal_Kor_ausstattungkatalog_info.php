
<!-- InfoModal -->
<div class="modal fade" id="InfoModal" tabindex="-1" aria-labelledby="InfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="InfoModalLabel">
                    <i class="fas fa-info-circle me-2 text-info"></i>
                    Elektrische Anschlussübersicht — Spalteninformation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <!-- Legende -->
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 fw-normal">
                        <i class="fas fa-database me-1"></i>Direkt — Wert direkt aus Elementparameter
                    </span>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 fw-normal">
                        <i class="fas fa-calculator me-1"></i>Berechnet — aus mehreren Parametern errechnet
                    </span>
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 fw-normal">
                        <i class="fas fa-project-diagram me-1"></i>Abgeleitet — aus Anzeigewert (inkl. Einheit)
                    </span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 fw-normal">
                        <i class="fas fa-lock me-1"></i>Fix — systemseitig gesetzt
                    </span>
                </div>

                <!-- Identifikation -->
                <h6 class="text-uppercase text-muted small fw-semibold mt-3 mb-2">Identifikation</h6>
                <div class="row g-2 mb-3">
                    <?php
                    $infoRows = [
                        ['Gewerk', 'fix', 'Immer „GPMT" — wird systemseitig gesetzt'],
                        ['Bauteilelement ID', 'direkt', 'ElementID + Variantenbuchstabe (A, B, C …)'],
                        ['Bauteilelement Bezeichnung', 'direkt', 'Freitextbezeichnung aus der Elementdatenbank'],
                        ['Bauteilelement Gruppen ID', 'direkt', 'Gewerk-Kürzel (BEW / ORT / GRO oder Gewerke_Nr)'],
                        ['Ortsveränderlich', 'calc', '„Ja" wenn Gewerk = ORT, sonst „Nein"'],
                        ['Versorgungseinheit', 'calc', '„1" bei bestimmten ElementIDs (1.61.x, 4.35.25.x, 1.35.13.2, 1.35.13.6)'],
                    ];
                    $badgeMap = [
                        'direkt' => ['bg-primary', 'Direkt'],
                        'calc' => ['bg-success', 'Berechnet'],
                        'abgeleitet' => ['bg-warning', 'Abgeleitet'],
                        'fix' => ['bg-secondary', 'Fix'],
                    ];
                    foreach ($infoRows as [$col, $type, $desc]):
                        [$bg, $label] = $badgeMap[$type];
                        ?>
                        <div class="col-md-4">
                            <div class="p-2 rounded border bg-light h-100">
                                <div class="fw-semibold small mb-1"><?= htmlspecialchars($col) ?>
                                    <span class="badge <?= $bg ?> bg-opacity-75 fw-normal mb-1"><?= $label ?></span>
                                </div>
                                <div class="text-muted" style="font-size:12px"><?= htmlspecialchars($desc) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Elektro Grunddaten -->
                <h6 class="text-uppercase text-muted small fw-semibold mt-3 mb-2">Elektro — Grunddaten</h6>
                <div class="row g-2 mb-3">
                    <?php
                    $infoRows = [
                        ['Leistung [W]', 'direkt', 'Parameter Nennleistung — Einheit im Kopf, kW → W automatisch'],
                        ['Netzart', 'direkt', 'Parameter Netzart (z. B. AV, SV, ZSV, USV; mehrere Werte möglich)'],
                        ['Spannung [V]', 'direkt', 'Parameter Spannung — Einheit im Kopf, kV → V automatisch'],
                        ['Steckdosen Anz.', 'direkt', 'Parameter Steckdosen_Anzahl'],
                        ['Direktanschluss', 'direkt', 'Parameter Direktanschluss (Ja/Nein)'],
                        ['Potentialausgleich', 'direkt', 'Parameter PA'],
                        ['IKT Anschluss', 'direkt', 'Parameter RJ45 Ports'],
                        ['24V Anschluss', 'calc', '„1" wenn Spannung = 24 V'],
                    ];
                    foreach ($infoRows as [$col, $type, $desc]):
                        [$bg, $label] = $badgeMap[$type];
                        ?>
                        <div class="col-md-4">
                            <div class="p-2 rounded border bg-light h-100">
                                <div class="fw-semibold small mb-1"><?= htmlspecialchars($col) ?>
                                    <span class="badge <?= $bg ?> bg-opacity-75 fw-normal mb-1"><?= $label ?></span>
                                </div>
                                <div class="text-muted" style="font-size:12px"><?= htmlspecialchars($desc) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- 230V/400V Spalten -->
                <h6 class="text-uppercase text-muted small fw-semibold mt-3 mb-2">Elektro — Direktanschluss &amp;
                    Steckdosen (230V / 400V × AV / SV / ZSV / USV)</h6>
                <div class="alert alert-success py-2 px-3 small mb-3">
                    <i class="fas fa-calculator me-1"></i>
                    <strong>16 berechnete Spalten.</strong>
                    Eine Zelle zeigt <strong>„1"</strong> (Direktanschluss) bzw. die <strong>Anzahl</strong>
                    (Steckdosen), wenn alle drei Bedingungen erfüllt sind:
                    <em>Netzart</em> enthält das jeweilige Kürzel (AV/SV/ZSV/USV) <strong>und</strong>
                    <em>Spannung</em> stimmt (230 V oder 400 V) <strong>und</strong>
                    Direktanschluss = Ja <em>bzw.</em> Steckdosen_Anzahl &gt; 0.
                </div>

                <!-- Tech. Druckluft -->
                <h6 class="text-uppercase text-muted small fw-semibold mt-3 mb-2">Technische Druckluft</h6>
                <div class="row g-2 mb-3">
                    <?php
                    $infoRows = [
                        ['Tech. DL 6 bar', 'calc', '„1" wenn Druckluftanschluss vorhanden und Druckluft_Druck = 6'],
                        ['Tech. DL 9 bar', 'calc', '„1" wenn Druckluftanschluss vorhanden und Druckluft_Druck = 9'],
                        ['Tech. DL 12 bar', 'calc', '„1" wenn Druckluftanschluss vorhanden und Druckluft_Druck = 12'],
                    ];
                    foreach ($infoRows as [$col, $type, $desc]):
                        [$bg, $label] = $badgeMap[$type];
                        ?>
                        <div class="col-md-4">
                            <div class="p-2 rounded border bg-light h-100">
                                <div class="fw-semibold small mb-1"><?= htmlspecialchars($col) ?> <span
                                        class="badge <?= $bg ?> bg-opacity-75 fw-normal mb-1"><?= $label ?></span>
                                </div>

                                <div class="text-muted" style="font-size:12px"><?= htmlspecialchars($desc) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Medizingas -->
                <h6 class="text-uppercase text-muted small fw-semibold mt-3 mb-2">Medizingase</h6>
                <div class="row g-2 mb-3">
                    <?php
                    $infoRows = [
                        ['O2', 'direkt', 'Parameter O2 Anschluss'],
                        ['Med. DL5', 'direkt', 'Parameter DL-5 Anschluss'],
                        ['Med. DL10', 'direkt', 'Parameter DL-10 Anschluss'],
                        ['NGA', 'direkt', 'Parameter NGA Anschluss'],
                        ['VA', 'direkt', 'Parameter VAC Anschluss'],
                        ['CO2', 'direkt', 'Parameter CO2 Anschluss'],
                    ];
                    foreach ($infoRows as [$col, $type, $desc]):
                        [$bg, $label] = $badgeMap[$type];
                        ?>
                        <div class="col-md-4">
                            <div class="p-2 rounded border bg-light h-100">
                                <div class="fw-semibold small mb-1"><?= htmlspecialchars($col) ?> <span
                                        class="badge <?= $bg ?> bg-opacity-75 fw-normal mb-1"><?= $label ?></span>
                                </div>

                                <div class="text-muted" style="font-size:12px"><?= htmlspecialchars($desc) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Kaltwasser & Warmwasser -->
                <h6 class="text-uppercase text-muted small fw-semibold mt-3 mb-2">Kaltwasser &amp; Warmwasser</h6>
                <div class="row g-2 mb-3">
                    <?php
                    $infoRows = [
                        ['KW Stadtwasser', 'calc', 'Anschluss vorhanden, Härte > 4 °DH (oder unbekannt), Leitfähigkeit nicht 80–120 μS/cm'],
                        ['KW Stadtwasser l/h', 'abgeleitet', 'Anzeigewert Kaltwasser_Strom — nur wenn KW Stadtwasser gesetzt'],
                        ['KW weich <4°DH', 'calc', 'Anschluss vorhanden, Härte ≤ 4 °DH, Leitfähigkeit NICHT 80–120 μS/cm'],
                        ['KW weich <4°DH l/h', 'abgeleitet', 'Anzeigewert Kaltwasser_Strom — nur wenn KW weich gesetzt'],
                        ['KW weich <4°DH, 80-120 μS/cm', 'calc', 'Anschluss vorhanden, Härte ≤ 4 °DH UND Leitfähigkeit 80–120 μS/cm'],
                        ['KW weich <4°DH, 80-120 μS l/h', 'abgeleitet', 'Anzeigewert Kaltwasser_Strom — nur wenn diese Kategorie gesetzt'],
                        ['KW VE <0,2 μS', 'calc', 'Aus Parameter Labor_Analysewasser_Anschluss'],
                        ['KW VE <0,2 μS l/h', 'abgeleitet', 'Anzeigewert Labor_Analysewasser_Strom'],
                        ['KW VE <15 μS/cm', 'calc', 'Aus Parameter Voll_entsalztes Wasser_Anschluss'],
                        ['KW VE <15 μS/cm l/h', 'abgeleitet', 'Anzeigewert Voll_entsalztes Wasser_Strom'],
                        ['WW Stadtwasser', 'calc', 'WW-Anschluss vorhanden, Härte > 4 °DH (oder unbekannt)'],
                        ['WW Stadtwasser l/h', 'abgeleitet', 'Anzeigewert Warmwasser_Strom'],
                        ['WW weich <4°DH', 'calc', 'WW-Anschluss vorhanden, Härte ≤ 4 °DH'],
                        ['WW weich <4°DH l/h', 'abgeleitet', 'Anzeigewert Warmwasser_Strom'],
                        ['Fließdruck', 'abgeleitet', 'KW-Fließdruck vorrangig, Fallback WW-Fließdruck (inkl. Einheit)'],
                        ['Direktanschluss Wasser', 'calc', '„Ja" wenn KW oder WW Direktanschluss = Ja'],
                        ['Anschlussdimension [DN]', 'abgeleitet', 'Nur gefüllt wenn Einheit „DN" enthält'],
                        ['Anschlusspunkt [Zoll]', 'abgeleitet', 'Nur gefüllt wenn Einheit „"" (Zoll-Zeichen) enthält'],
                        ['Rohrtrenner EN1717', 'direkt', 'Anzeigewert Trennung EN1717 (inkl. Einheit)'],
                    ];
                    foreach ($infoRows as [$col, $type, $desc]):
                        [$bg, $label] = $badgeMap[$type];
                        ?>
                        <div class="col-md-4">
                            <div class="p-2 rounded border bg-light h-100">
                                <div class="fw-semibold small mb-1"><?= htmlspecialchars($col) ?>
                                    <span class="badge <?= $bg ?> bg-opacity-75 fw-normal mb-1"><?= $label ?></span>
                                </div>
                                <div class="text-muted" style="font-size:12px"><?= htmlspecialchars($desc) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Abwasser & Lüftung -->
                <h6 class="text-uppercase text-muted small fw-semibold mt-3 mb-2">Abwasser &amp; Lüftung</h6>
                <div class="row g-2 mb-3">
                    <?php
                    $infoRows = [
                        ['Abwasser Anschluss', 'calc', '„1" wenn Abfluss_Boden oder Abfluss_Wand = Ja'],
                        ['Abwasser Strom', 'abgeleitet', 'Anzeigewert Abflussstrom (inkl. Einheit)'],
                        ['Kondensat', 'calc', '„1" wenn Kondensat_Anschluss gesetzt und ≠ 0/Nein'],
                        ['Kondensat Wert', 'abgeleitet', 'Anzeigewert Kondensat_Anschluss (inkl. Einheit)'],
                        ['Abwassertemperatur [°C]', 'direkt', 'Parameter Abflusstemperatur — Einheit unterdrückt'],
                        ['Siphon (durch GPHT)', 'calc', '„Ja" wenn Abfluss_Siphon_notwendig = Ja'],
                        ['Anschlussdimension AW', 'abgeleitet', 'Anzeigewert Abflussdurchmesser (inkl. Einheit)'],
                        ['Raumwärme sensibel [W]', 'direkt', 'Parameter Abwärme — kW → W automatisch, Einheit unterdrückt'],
                        ['Abluftmenge', 'abgeleitet', 'Anzeigewert Abluftstrom (inkl. Einheit)'],
                        ['Abluftdimension', 'abgeleitet', 'Anzeigewert Abluftdurchmesser (inkl. Einheit)'],
                        ['Direktanschluss Abluft', 'calc', '„Ja" wenn Abluft_Direktanschluss = Ja'],
                        ['Abluft Temp >50°C', 'direkt', 'Parameter Ablufttemperatur — Einheit unterdrückt'],
                    ];
                    foreach ($infoRows as [$col, $type, $desc]):
                        [$bg, $label] = $badgeMap[$type];
                        ?>
                        <div class="col-md-4">
                            <div class="p-2 rounded border bg-light h-100">
                                <div class="fw-semibold small mb-1"><?= htmlspecialchars($col) ?>
                                    <span class="badge <?= $bg ?> bg-opacity-75 fw-normal mb-1"><?= $label ?> </span>
                                </div>
                                <div class="text-muted" style="font-size:12px"><?= htmlspecialchars($desc) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- GLT, Kälte, Architektur -->
                <h6 class="text-uppercase text-muted small fw-semibold mt-3 mb-2">GLT, Kälte &amp; Architektur</h6>
                <div class="row g-2 mb-3">
                    <?php
                    $infoRows = [
                        ['GLT Datenpunkt', 'calc', '„Ja" wenn Parameter GLT = Ja'],
                        ['Kälteleistung [W]', 'direkt', 'Parameter Kühlwasser_Abwärme — Einheit unterdrückt'],
                        ['Vorlauf Temp [°C]', 'direkt', 'Parameter Kühlwasser_Temperatur — Einheit unterdrückt'],
                        ['Vorlauf Anschluss', 'abgeleitet', 'Anzeigewert Kühlwasser_Vorlauf_Anschluss (inkl. Einheit)'],
                        ['Rücklauf Anschluss', 'abgeleitet', 'Anzeigewert Kühlwasser_Rücklauf_Anschluss (inkl. Einheit)'],
                        ['Druckverlust [Pa]', 'direkt', 'Parameter Kühlwasser_Druckverlust — Einheit unterdrückt'],
                        ['Gewicht [kg]', 'direkt', 'Parameter (Eigen-)Gewicht — kg bleibt unverändert'],
                        ['Lärm [dB(A)]', 'direkt', 'Parameter Lärm — Einheit unterdrückt'],
                        ['Punktlast abgehängt [N]', 'calc', 'Aus Punktlast — nur wenn Montage_Ort „Decke" enthält'],
                        ['Punktlast Boden [N]', 'calc', 'Aus Punktlast — nur wenn Montage_Ort „Boden" enthält'],
                    ];
                    foreach ($infoRows as [$col, $type, $desc]):
                        [$bg, $label] = $badgeMap[$type];
                        ?>
                        <div class="col-md-4">
                            <div class="p-2 rounded border bg-light h-100">
                                <div class="fw-semibold small mb-1"><?= htmlspecialchars($col) ?>
                                    <span class="badge <?= $bg ?> bg-opacity-75 fw-normal mb-1"><?= $label ?></span>
                                </div>
                                <div class="text-muted" style="font-size:12px"><?= htmlspecialchars($desc) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Allgemeine Hinweise -->
                <h6 class="text-uppercase text-muted small fw-semibold mt-3 mb-2">Allgemeine Hinweise</h6>
                <ul class="small text-muted mb-0">
                    <li><strong>Einheitenumrechnung:</strong> Präfixe wie kW→W, kV→V, MW→W werden automatisch auf
                        die Basiseinheit umgerechnet. Ausnahmen: kg, km, MHz, GHz, MB, GB bleiben unverändert.
                    </li>
                    <li><strong>Einheit im Spaltenkopf:</strong> Wo der Spaltenkopf die Einheit nennt ([W], [°C],
                        [N] usw.), wird sie im Zellwert unterdrückt.
                    </li>
                    <li><strong>Leere Zellen:</strong> Nicht gesetzte oder 0-Werte werden als leere Zelle
                        dargestellt.
                    </li>
                </ul>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>
