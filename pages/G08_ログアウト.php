<?php
session_start();
unset($_SESSION['customer']);
header('Location: login-input.php');
exit;
