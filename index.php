<?php
require_once('login/autentication.php');
		$main = trim($_SESSION['sess_mainpage']) > '' ? $_SESSION['sess_mainpage'] : 'praticheStatus.php';
header('Location:' . $main);