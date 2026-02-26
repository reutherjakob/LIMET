<!-- Help Modal -->
<div class='modal fade' id='helpModal' role='dialog' tabindex="-1">
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            <div class='modal-header bg-primary text-white'>
                <h4 class='modal-title'><i class="fas fa-info-circle me-2"></i>Hilfe - Lose Übersicht</h4>
                <button type='button' class='btn-close btn-close-white' data-bs-dismiss='modal'></button>
            </div>
            <div class='modal-body'>
                <h5><i class="fas fa-list-ul me-2"></i>Übersicht</h5>
                <p>Diese Seite zeigt alle Lose (Ausschreibungen) der verschiedenen Projekte in einer tabellarischen Übersicht.
                    Sie können Lose filtern, durchsuchen und deren Status verwalten.</p>

                <hr>

                <h5 class="mt-4"><i class="fas fa-sliders-h me-2"></i>Filter und Suche</h5>
                <ul class="list-unstyled ms-3">
                    <li class="mb-2"><i class="fas fa-calendar-alt text-primary me-2"></i><strong>Datumsfilter:</strong>
                        Rechts oben können Sie einstellen, ab welchem Versanddatum Lose angezeigt werden sollen</li>
                    <li class="mb-2"><i class="fas fa-search text-primary me-2"></i><strong>Suche:</strong>
                        Schnellsuche über alle Spalten</li>
                    <li class="mb-2"><i class="fas fa-eye text-primary me-2"></i><strong>Spalten ein/ausblenden:</strong>
                        Passen Sie die Ansicht an Ihre Bedürfnisse an</li>
                    <li class="mb-2"><i class="fas fa-file-excel text-success me-2"></i><strong>Excel-Export:</strong>
                        Exportieren Sie die gefilterten Daten</li>
                    <li class="mb-2"><i class="fas fa-filter text-primary me-2"></i><strong>Erweiterte Filter:</strong>
                        Erstellen Sie komplexe Filterregeln</li>
                </ul>

                <hr>

                <h5 class="mt-4"><i class="fas fa-columns me-2"></i>Wichtige Spalten</h5>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled ms-3">
                            <li class="mb-2">
                                <button class="btn btn-sm btn-outline-secondary" disabled><i class="fas fa-code-branch"></i></button>
                                <strong class="ms-2">Workflow:</strong><br>
                                <small class="ms-4 text-muted">Zeigt den Bearbeitungsverlauf des Loses</small>
                            </li>
                            <li class="mb-2">
                                <button class="btn btn-sm btn-outline-secondary" disabled><i class="fas fa-briefcase-medical"></i></button>
                                <strong class="ms-2">Elemente:</strong><br>
                                <small class="ms-4 text-muted">Übersicht aller Elemente im Los</small>
                            </li>
                            <li class="mb-2">
                                <button class="btn btn-sm btn-warning position-relative" disabled>
                                    <i class="fas fa-tasks"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                                </button>
                                <strong class="ms-2">ToDos:</strong><br>
                                <small class="ms-4 text-muted">Zeigt Anzahl offener Aufgaben (Badge mit Zahl = Anzahl ToDos)</small>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled ms-3">
                            <li class="mb-2">
                                <label class='form-check form-switch form-switch-sm'>
                                    <input class='form-check-input' type='checkbox' disabled>
                                </label>
                                <strong class="ms-2">Preis in DB:</strong><br>
                                <small class="ms-4 text-muted">Checkbox zum Markieren: "Angebotspreise sind eingetragen"</small>
                            </li>
                            <li class="mb-2">
                                <button class="btn btn-sm btn-outline-dark" disabled><i class="fas fa-check"></i></button>
                                <strong class="ms-2">Kontrolle:</strong><br>
                                <small class="ms-4 text-muted">Button zur Preiskontrolle (erscheint erst nach Checkbox-Aktivierung)</small>
                            </li>
                        </ul>
                    </div>
                </div>

                <hr>

                <h5 class="mt-4"><i class="fas fa-check-double me-2"></i>Kontroll-Mechanik für Preise</h5>
                <div class="alert alert-info">
                    <strong>Workflow in 2 Schritten:</strong>
                    <ol class="mt-2 mb-0">
                        <li class="mb-2">
                            <strong>Schritt 1 - Preise eintragen:</strong><br>
                            Wenn das Los abgeschlossen ist (Status <span class='badge bg-success'>Fertig</span>),
                            erscheint eine <strong>Checkbox</strong> <i class="fas fa-dollar-sign"></i>.<br>
                            → Aktivieren Sie diese, sobald alle Angebotspreise in der Datenbank eingetragen wurden.
                        </li>
                        <li class="mb-2">
                            <strong>Schritt 2 - Preise kontrollieren:</strong><br>
                            Nach Aktivierung der Checkbox erscheint ein <strong>Button</strong> <i class="fas fa-user-check"></i>.<br>
                            → Ein anderer Benutzer klickt diesen Button, um die Preise zu kontrollieren und freizugeben.<br>
                            → Nach Klick wird der Username gespeichert und der Button zeigt die ersten 3 Buchstaben des Kontrolleurs an.<br>
                            → Der Button wird dann <span class="badge bg-success">grün</span> und deaktiviert (= kontrolliert).
                        </li>
                    </ol>
                </div>

                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Hinweis:</strong> Die Kontroll-Buttons erscheinen nur bei abgeschlossenen Losen
                    (Status <span class='badge bg-success'>Fertig</span>) und erst nachdem die Checkbox aktiviert wurde.
                </div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-primary btn-sm' data-bs-dismiss='modal'>
                    <i class="fas fa-check me-1"></i>Verstanden
                </button>
            </div>
        </div>
    </div>
</div>