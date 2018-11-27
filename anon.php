<?php
//start anonimowej sesji
session_start();
//przesłanie w zmiennej sesji informacji, że sesja ta jest anonimowa
$_SESSION['anon']=true;
//przekierowanie na strone główną
 header("Location: index.php");
?>

?>