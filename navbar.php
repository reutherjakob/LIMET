<nav class="navbar navbar-expand-lg bg-light navbar-light">	
    <a class="py-0 navbar-brand" href="#"><img src="LIMET_logo.png" alt="LIMETLOGO" height="40"/></a>
    <ul class="navbar-nav">
        <ul class='navbar-nav'>
            <li class='nav-item'><a class='py-0 nav-link' href='dashboard.php'><i class='fa fa-tachometer-alt'></i> Dashboard</a></li>
        </ul>
        <li class='py-0 nav-item dropdown'>
            <a class='py-0 nav-link dropdown-toggle' data-toggle="dropdown" href="#"><i class='fa fa-list-alt'></i> Projekte</a>              
            <ul class="dropdown-menu">
                <a class="dropdown-item" href="projects.php"><i class='fa fa-list-alt'></i> Projektauswahl</a> 
                <a class='dropdown-item' href='projectParticipants.php'><i class='fa fa-users'></i> Projektbeteiligte</a>
                <a class='dropdown-item' href='documentationV2.php'><i class='fa fa-comments'></i> Dokumentation</a>
            </ul>
        </li>
        <li class='nav-item dropdown'>
            <a class=' py-0 nav-link dropdown-toggle' data-toggle='dropdown' href='#'><i class='fa fa-book'></i> Raumbuch</a>              
            <ul class='dropdown-menu'>
                <a class='dropdown-item' href='roombookSpecifications_New.php'>Raumbuch - Bauangaben</a>
                <a class='dropdown-item' href='roombookSpecificationsLab.php'>Raumbuch - Bauangaben Labor</a>
                <a class='dropdown-item' href='roombookMeeting.php'>Raumbuch - Meeting</a>
                <a class='dropdown-item' href='roombookDetailed.php'>Raumbuch - Detail</a>
                <a class='dropdown-item' href='roombookElements.php'>Raumbuch - Räume mit Element</a>
                <a class='dropdown-item' href='roombookReports.php'>Raumbuch - Berichte</a>
                <a class='dropdown-item' href='elementsInProject.php'>Elemente im Projekt</a>
                <a class='dropdown-item' href='roombookList.php'>Raumbuch - Liste</a>
            </ul>
        </li>
        <li class='nav-item dropdown'>
            <a class='py-0 nav-link dropdown-toggle' data-toggle='dropdown' href='#'><i class='fa fa-euro-sign'></i> Kosten</a>              
            <ul class='dropdown-menu'>
                <a class='dropdown-item' href='costsOverall.php'>Kosten - Berichte</a> 
                <a class='dropdown-item' href='costsRoomArea.php'>Kosten - Raumbereich</a>
                <a class='dropdown-item' href='costChanges.php'>Kosten - Änderungen</a>
                <a class='dropdown-item' href='elementBudgets.php'>Kosten - Budgets</a>
            </ul>
        </li>              
        <li class="py-0 nav-item dropdown">
            <a class="py-0 nav-link dropdown-toggle" data-toggle="dropdown" href="#"><i class='fa fa-recycle'></i> Bestand</a>             
            <ul class="dropdown-menu">
                <a class="dropdown-item" href="roombookBestand.php">Bestand - Raumbereich</a>	
                <a class="dropdown-item" href="roombookBestandElements.php">Bestand - Gesamt</a>
            </ul>
        </li>
        <li class="py-0 nav-item dropdown">
            <a class="py-0 nav-link dropdown-toggle" data-toggle="dropdown" href="#"><i class='fa fa-tasks'></i> Ausschreibungen</a>
            <ul class="dropdown-menu">
                <a class="dropdown-item" href="tenderLots.php">Los-Verwaltung</a>
                <a class="dropdown-item" href="tenderCalendar.php">Vergabekalender</a>
                <a class='dropdown-item' href='tenderCharts.php'>Vergabe-Diagramme</a>
                <a class="dropdown-item" href="elementLots.php">Element-Verwaltung</a>
            </ul>
        </li>
        <li class="py-0 nav-item dropdown">
            <a class="py-0 nav-link dropdown-toggle" data-toggle="dropdown" href="#"><i class='fas fa-wrench'></i> Ausführung-ÖBA</a>
            <ul class="dropdown-menu">
                <a class="dropdown-item active" href="dashboardAusfuehrung.php"><i class='fas fa-tachometer-alt'></i> Dashboard</a>
                <a class="dropdown-item" href="roombookVorleistungen.php"><i class='fas fa-tasks'></i> Vorleistungen</a>
                <a class="dropdown-item" href="roombookAusfuehrung.php"><i class='fas fa-building'></i> Räume</a>
                <a class="dropdown-item" href="roombookAusfuehrungLiefertermine.php"><i class='far fa-calendar-alt'></i> Liefertermine</a>
                <a class="dropdown-item" href="roombookAbrechnung.php"><i class='fas fa-euro-sign'></i> Abrechnung</a>
            </ul>
        </li>
        <li class='py-0 nav-item dropdown'>
            <a class='py-0 nav-link dropdown-toggle' data-toggle='dropdown' href='#'><i class='fa fa-buromobelexperte '></i> Datenbank-Verwaltung</a>              
            <ul class='dropdown-menu'>
                <a class='dropdown-item' href='elementAdministration.php'>Elemente-Verwaltung</a>
                <a class='dropdown-item' href='elementeCAD.php'>Elemente-CAD</a>
            </ul>
        </li>    
        <ul class='navbar-nav'>
            <li class='nav-item'><a class='py-0 nav-link' href='firmenkontakte.php'><i class='fa fa-address-card'></i> Firmenkontakte</a></li>
        </ul>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="py-0 nav-item "><a class="py-0 nav-link text-success disabled" id="projectSelected">Aktuelles Projekt: 
                <?php
                session_start();

                if ($_SESSION["projectName"] != "") {
                    echo $_SESSION["projectName"];
                } else {
                    echo "Kein Projekt gewählt!";
                }
                ?></a></li>
        <li><a class="py-0 nav-link" href="logout.php"><i class="fa fa-sign-out-alt"></i>Logout</a></li>
    </ul>              
</nav>	

   