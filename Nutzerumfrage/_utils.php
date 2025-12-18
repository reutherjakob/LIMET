<?php

global $role;// = get_user_role();
include '../Nutzerumfrage/footer_Nutzerumfrage.html';
?>

<script>
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    let userRole = "<?php echo $role; ?>";
    $.get("../Nutzerumfrage/navbar.html", function (data) {
        $("#limet-navbar").html(data);
        let Username = "<?php echo $_SESSION['user_name']?>";
        $("#navbar-username").text(capitalizeFirstLetter(Username));
        if(userRole === "internal_rb_user" || userRole === "spargefeld_admin"){
            $("#limet-navbar ul.navbar-nav.col-8").append(
                '<li class="nav-item">' +
                '<a class="py-0 px-2 nav-link" href="../Nutzerumfrage/adminpanel.php" role="button"><i class="fa fa-cogs"></i> Admin Panel</a>' +
                '</li>'
            );
        }
    });
</script>