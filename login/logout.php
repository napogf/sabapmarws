<?php
include "configsess.php";
session_start();
session_destroy();
header ("Location: ../$login");
?>
