<?php
session_name('patient_session'); 
session_start();


$_SESSION = [];


if (session_id() != '') {
    session_destroy(); 
}

header("Location: ../index.php");
exit(); 
?>
