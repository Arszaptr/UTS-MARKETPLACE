<?php require 'common.php';
if($_SERVER['REQUEST_METHOD']==='POST' && csrf_ok()){ $_SESSION=[]; session_destroy(); }
header('Location: /index.php');