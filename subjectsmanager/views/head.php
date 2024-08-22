<?php

	$FRONTEND_VERSION = 2;


?>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons" rel="stylesheet" type="text/css">
    <link href="css/style.css?v=<?=$FRONTEND_VERSION;?>" rel="stylesheet" type="text/css">

    <script>
        var isSuperAdmin = <?php
        $isSuperAdmin = \Publications\StaffUser::isSuperAdmin() ? true : false;
        echo $isSuperAdmin;?>

        var isEditor = <?php
        $isSuperAdmin =\Publications\StaffUser::isLoggedin() ? true : false;
        echo $isSuperAdmin;?>
    </script>

