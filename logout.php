<?php
session_start();
session_destroy();

header("Location: cat_frontend.php");
exit();
?>
