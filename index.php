<?php
session_start();

//zmienna, która będzie określać czy edycja jest właśnie wykonywana czy też nie
$edycja=false;

//zmienne do edycji, pod którymi będą dane, które wprowadził user
$e_tytul="";
$e_tresc="";
$e_id="";

//sprawdzenie czy mamy zalogowanego usera lub jest aktywna anonimowa sesja
if (!isset($_SESSION["login"]) && !isset($_SESSION["anon"])){
    //jeśli nie to przekierowywuje na stronę logowania i kończy skrypt
  header("Location: logowanie.php");
 exit();
} 

//łączenie się z bazą
$conn=mysqli_connect("localhost","rkurek","rkurek","rkurek");


//rozróżnianie akcji
if(isset($_POST['akcja'])){
    switch($_POST['akcja']){
            
            //Dodawanie postów
        case 'Prześlij':
            //sprawdzanie czy dane nie są pustymi ciągami
            if ($_POST['tytul']!==""&&$_POST['tresc']!=="" && $_FILES['obraz']['name'] !="" ){
                //sprawdzanie czy przesyłany plik jest odpowiedniego typu i czy jest zupload'owany
                if($_FILES['obraz']['type'] == 'image/jpeg' || $_FILES['obraz']['type'] == 'image/png'){
                    if(is_uploaded_file($_FILES['obraz']['tmp_name'])){
                        
                        
                        $autor=$_SESSION["login"];
                        $tytul=$_POST["tytul"];
                        $tresc=$_POST["tresc"];
                        //obrazek zaminiany w binarny plik
                        $obrazek=base64_encode(file_GET_contents($_FILES['obraz']['tmp_name']));
                        //przesył wszystkich danych do bazy
                        $sql="INSERT INTO tresci(autor,tytul,tresc,obrazek) VALUES('$autor','$tytul','$tresc','$obrazek')";
                        $conn->query($sql) or die('Nie mozna dodac rekordu');
                    }
                    else
                        {
                            
                            echo "Błąd! Plik nie został zapisany!";
                        }
                    
                }

    
}
            //gdyby jakieś pole zostało puste odpowiedni komunikat
    else {
        echo "Podaj tytuł i treść artykułu oraz wybierz obrazek!";
    }
            
            break;
            
            //Usuwanie postów
            case 'Usuń':
           if(isset($_POST['id'])){
               //usuwa rekord o odpowiednim id z ukrytego inputa (dalej opisany)
                    $usun="DELETE FROM tresci WHERE id=".$_POST['id'];
                    $conn->query($usun) 
                        or die('Nie mozna usunąć rekordu');
                }
            break;
            
            //edycja postów
            case 'Edytuj':
            
            if(isset($_POST['id'])){
                
                //szuka odpowiedniego rekordu o id z ukrytego inputa i wrzuca te dane pól pod edycję jednocześnie aktywując operację edycji
                $wybierz=$conn->query('SELECT * FROM tresci WHERE id='.$_POST['id'].';')
                        or die('Nie mozna pobrac rekordu');
                
                if($wybierz->num_rows>0)
                    {
                        $szukany=$wybierz->fetch_array();

                        $e_id=$szukany['id'];                       
                        $e_tytul=$szukany['tytul'];
                        $e_tresc=$szukany['tresc'];

                         $edycja=true;
                    }
                
               
                
            }
            
            
            break;
            
            //Update postów, zatwierdzenie edycji
            case 'Zapisz':
            if(isset($_POST['tytul']) && isset($_POST['tresc']) && isset($_POST['e_id']) && $_POST['tresc']!=="" && $_POST['e_id']!=="" && $_POST['tytul']!==""){
                $e_id=$_POST['e_id'];
                $e_tytul=$_POST['tytul'];
                $e_tresc=$_POST['tresc'];
                
                //aktualizuje rekord o danym id o odpowiednie dane wprowadzone przez usera
                $aktualizuj="UPDATE tresci SET tytul='$e_tytul',tresc='$e_tresc' WHERE id=".$e_id;                 
                $conn->query($aktualizuj) 
                or die('Nie mozna zaktualizować rekordu'); 

            }
            
            break;
            
            
    }
}



?>

<html>

<head>
    <title>CMS</title>
    <meta charset="utf-8" />
    <style>
        body {
            width: 960px;
            margin: 0 auto;

            text-align: center;
            color: white;
            background-color: #616366;
        }

        a {
            text-decoration: none;
            color: black;
            background-color: white;

        }

        #posty {
            text-align: center;
            margin: 0 auto;
        }

        img {
            width: 100px;
            height: 100px;
        }

    </style>
</head>

<body>
    <h1>STRONA GŁÓWNA</h1>
    <?php
   
    
    //warunek, który chowa panel dodawania postów dla anonimowych userów
  if (isset($_SESSION['login'])){  
    echo "Jesteś zalogowany jako ".$_SESSION["login"].'<br>';
    
      //przycisk wylogowywania dla zalogowanych userów
    echo ("<a href='wyloguj.php'>wyloguj</a><br>");

      
//wykrycie edycji i odpowiednio wyświetlany panel dodawania postów (jeśli edycja jest aktywowana skrypt zmienia value przycisku na "Zapisz" i wstawia dane do edycji w pola "Terść" i "Tytuł" co jest rozpoznawane we wcześniejszym switchu jako zatwierdzenie edycji, czyli update rekordów)
        if($edycja!==false){
            
            echo("<form method='POST'>
<input type='hidden' name='e_id' value='".$e_id."'/>
Tytuł: <input type='text' name='tytul' value='".$e_tytul."'/><br>
Treść: <input type='text' name='tresc' value='".$e_tresc."'/><br>
<input type='hidden' name='MAX_FILE_SIZE' value='1024000' /><br> 
Plik:<input type='file' name='obraz' />
<input type='submit' value='Zapisz' name='akcja'/>
</form>");
        }
      else {
          echo("<form method='POST' enctype='multipart/form-data'>
Tytuł: <input type='text' name='tytul'/><br>
Treść: <input type='text' name='tresc'/><br>
<input type='hidden' name='MAX_FILE_SIZE' value='1024000' /><br> 
Plik:<input type='file' name='obraz' />
<input type='submit' value='Prześlij' name='akcja'/>
</form>");
      }

        
        
      
      
    
  }
    //tutaj jest tylko mozliwosc powrotu do okna logowania dla anonimowych userów
    else {
        echo ("<a href='logowanie.php'>Zaloguj się</a>");
    }
    
    
    //tutaj wyświetlana jest tabela ze wszystkimi postami
    $wyswietl=$conn->query('SELECT * FROM tresci;')
         or die('Nie można pobrać rekordów');
         
        if($wyswietl->num_rows>0)
        {
            echo('<table border=1 id="posty">');
            
            //jeśli jest to sesja z zalogowanym userem wyświetlane są dodatkowo nagłowki Edycja i Usuń
            if(isset($_SESSION['login'])){
                echo('<tr><th>Autor</th><th>Tytuł</th><th>Treść</th><th>Grafika</th><th>Data</th><th>Edytuj</th><th>Usuń</th></tr>');
            }
            //jeżeli anonimowa to wyświetlana jest tabela tylko z danymi bez nagłowków do działań na wierszach
            else {
                echo('<tr><th>Autor</th><th>Tytuł</th><th>Treść</th><th>Grafika</th><th>Data</th></tr>');
                
            }
                
            
         
            while($rekord=$wyswietl->fetch_array())
            {
               

                echo("<form method='POST'".
                "<tr>".
                    "<td>".$rekord['autor']."</td>".
                    "<td>".$rekord['tytul']."</td>".
                    "<td>".$rekord['tresc']."</td>".
                     //obrazek z bazy dekodowany 
                    "<td><img src='data:image/jpg;base64,".$rekord['obrazek']."' alt='gfx'/></td>".
                    "<td>".$rekord['data']."</td>"
                    
                    );
                
                //warunek, który porównuje autora posta z aktualnie zalogowanym użytkownikiem i decyduje czy w tym wierszu pojawią się kontrolki edycji i usuwania
                if(isset($_SESSION['login']) && $rekord['autor']==$_SESSION['login']){ 
                     echo("<input type='hidden' name='id' value='".$rekord['id']."'/>".
                        "<td><input type='submit' name='akcja' value='Edytuj' /></td>".
                        "<td><input type='submit' name='akcja' value='Usuń' /></td>");
                    }
                
                echo("</tr>".
                    "</form>");

            }
            echo('</table>');            
        }
        $conn->close();
    
    ?>

</body>

</html>
