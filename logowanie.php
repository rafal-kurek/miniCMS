<?php
//łączenie się z bazą danych, która przechowuje użytkowników i wszystkie artykuły
$conn=mysqli_connect("localhost","rkurek","rkurek","rkurek");

//sprawdzenie czy zatwierdzono formularz logowania
if (isset($_POST['login']) && isset($_POST['haslo'])){

//zabezpieczenie danych przed nieodpowiednimi działaniami
$login=htmlentities($_POST['login']);
$haslo=htmlentities($_POST['haslo']);
 
    //wyszukanie usera o podanym loginie i haśle
 $rs=mysqli_query($conn,"SELECT Count(id) FROM users WHERE login='$login' AND haslo=md5('$haslo')");
   $rec=mysqli_fetch_array($rs);
   
    //jeśli liczba zwróconych rekordów jest większa od zera użytkownik taki istnieje i zostaje zalogowany (w bazie jest ustawiona unikalność loginów)
   if ($rec[0]>0){
    session_start();
       //po starcie sesji login zostaje przesłany w zmiennej sesji
	$_SESSION['login']=$_POST['login'];
       //przekierowanie do strony index.php
    header("Location: index.php");
       //zakończenie wykonywania skryptu
    exit();
       //komunikat jeśli dane są złe lub nie zostały wprowadzone
  } else
    $info="Błędny login lub hasło!";
} else
  $error = false;

?>
<html>

<head>
    <title>Logowanie</title>
    <meta charset="utf8" />
    <style>
        body{
	width:960px;
	margin: 0 auto;
	margin-top:20%;
	text-align: center;
	color:white;
	background-color:#616366;
}
    a {
text-decoration: none;
color:black;
    background-color: white;
    
}
</style>
</head>

<body>
    <h1>Podaj login i hasło</h1>
    <?php
if(isset($info)){
echo $info;
}
?>


    <form method="POST">
        Login: <input type="text" name="login"><br>
        Hasło: <input type="password" name="haslo"><br>
        <input type="submit" value="Zaloguj się">
    </form>

    <a href="anon.php">Anonimowe wejście</a>
</body>

</html>
