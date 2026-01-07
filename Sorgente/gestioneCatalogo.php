<?php
session_start();
if(isset($_POST['visibili'])) $_SESSION['NumCol']=$_POST['visibili'];
else if (isset($_POST['visibili'])) setcookie('NumCol', $_POST['visibili']);
echo "Risposta: ".$_POST['visibili']." && Sessione: ".$_SESSION['NumCol'];

?>