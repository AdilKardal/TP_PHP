<!DOCTYPE html>
<?php
include_once('src/core/security.php');
$is_connected = isConnected();
?>
<html lang="fr">
<?php
include_once('template/head.php');
?>

<body>
    <?php
    include_once('template/header.php');
    ?>
    <main>
        <?php
        include_once('src/core/router.php');
        ?>
    </main>
</body>

</html>