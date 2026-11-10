<?php

global $role;// = get_user_role();
// include '../roombook_nutzerumfrage_sf/footer_Nutzerumfrage.html';

function buildExcelFilename(string $documentName, array $options = []): string     // bewusst OHNE .xlsx
{
    $parts = array_filter([
        "AGES_SF", trim($_SESSION['projectPlanungsphase'] ?? ''),
        trim($options['placeholder'] ?? ''),
        trim($options['department'] ?? 'GPMT'),
        $documentName,
        trim($options['date'] ?? date('Y-m-d')),
    ], fn($p) => $p !== '');           // filtert leere Teile raus -> keine "__"

    return preg_replace('/[\/:*?"<>|]+/', '', implode('_', $parts));
}

?>

<script>
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    let userRole = "<?php echo $role; ?>";
    $.get("../roombook_nutzerumfrage_sf/navbar.html", function (data) {
        $("#limet-navbar").html(data);
        let Username = "<?php echo $_SESSION['user_name']?>";
        $("#navbar-username").text(capitalizeFirstLetter(Username));
        if (userRole === "internal_rb_user" || userRole === "spargelfeld_admin" || userRole === "spargelfeld_view") {
            $("#limet-navbar ul.navbar-nav.col-8").append(
                '<li class="nav-item">' +
                '<a class="py-0 px-2 nav-link" href="/roombook_nutzerumfrage_sf/adminpanel.php" role="button"><i class="fa fa-cogs"></i> Admin Panel</a>' +
                '</li>'
            );

        }
    });
</script>