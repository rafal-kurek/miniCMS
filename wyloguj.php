<html>

<head>
    <title>Wylogowywanie</title>
    <meta charset="utf-8" />
    <style>
        body {
            width: 960px;
            margin: 0 auto;
            font-size: 20px;
            padding-top: 10%;
            text-align: center;
            color: white;
            background-color: #616366;
        }

    </style>
</head>

<body>
    <?php
session_start();
    //zerwanie sesji, by móc rozpocząć nową
session_destroy();
    //komunikat i odnośnik do strony logowania
echo "Zostałeś wylogowany. <a href='logowanie.php'>Kliknij tutaj</a>, aby zalogować się ponownie";
?>
</body>

</html>
