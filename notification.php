<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Document</title>
</head>

<caption>
    <h2 style="margin-left: 60px">liste des messages des utilisateurs</h2>
</caption><br>

<?php

$bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');

echo "
<table class='table table-striped' style='max-width: 1200px; max-height: 500px'>
    <thead class=' table table-striped'>
        <tr>
            <th scope='col'>Id_users</th>
            <th scope='col'>Nom</th>
            <th scope='col'>Email</th>
            <th scope='col'>telephone</th>
            <th scope='col'>message</th>
            <th scope='col'>Actions</th>
        </tr>
    </thead>";

$article = $bdd->query('select * from admins order by id_writer asc');
while ($donnee = $article->fetch()) {
    $nom = $donnee['nom'];
    $mail = $donnee['email'];
    $message = $donnee['message'];
    $tel = $donnee['telephone'];
    $idvoyage = $donnee['id_writer'];

    echo "    
    <tbody class='table table-striped'>
        <tr>
            <th scope='row'> $idvoyage</th>
            <td>$nom</td>
            <td>$mail</td>
            <td>$tel</td>
            <td>$message</td>
            <td rowspan=''>
            <div class='colonne-divisee' style='display: flex;'>
                <form method='post' action='modifier.php'>
                    <input type='hidden' name='id_voyage' value='$idvoyage'>
                    <button type='submit' style='background-color: green;  cursor: pointer; color:white;'>Modifier</button>
                </form>
                <form method='post' action=''>
                    <input type='hidden' name='id_voyage' value='$idvoyage'>
                    <button type='button' onclick='openModal()' style='background-color: red; cursor: pointer;'>Supprimer</button>
                    <i class='fa fa-trash' aria-hidden='true'></i>
                </form>
            </div>
        </td> 
        </tr>
    </tbody>

    ";
}

echo '</table>';
// }catch (Exception $e) {
//     echo 'echec de connexion';
// }
// }
//     $iden=$donnee['numRef'];

//     echo "<h2>Liste des articles de notre apllication</h2>

//     <ul class='article'>

//         <?php while ($donnee = $article->fetch())
//     <li> $nom</li>
//     <li> $prix</li>
//     <li> $quantite</li>
//     <li> <img src='photos/$image' alt='Article Image'></li>
//     <form method='post' action=''>
//         <input type='hidden' name='id_article' value='$iden'>
//         <button type='submit' style='background-color: red; margin-left: 35px; cursor: pointer;'>Supprimer</button>
//     </form>
//     </ul>
//     " ;
//     }

//  $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');

// if(isset($_POST['name']) && isset($_POST['gmail']) && isset($_POST['message'])){
//     try {
//     $a=$_POST['name'];
//     $_SESSION['name']=$a;
//     $b=$_POST['gmail'];
//     $_SESSION['gmail']=$b;
//     $c=$_POST['message'];
//     $_SESSION['message']=$c;

//     $requete="insert into admins (nom, email, message) values ('$a', '$b', '$c')";
//     $bdd->exec($requete);
//     echo '<div id="myModal" class="modal1" style="display: block;">
//     <div class="modal-content1">
//         <span class="close" onclick="closeModal()">&times;</span>
//         <h2>Insertion réussie</h2>
//         <p>Votre message a été inséré avec succès.</p>
//         <button onclick="closeModal()">Fermer</button>
//     </div>
//   </div>';
//   header("refresh:5;url=Accueil.php");

// }catch (Exception $e) {
//     echo 'echec de connexion';
// }
// }
?>


<style>
.modal1 {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content1 {
    /* background-color: #fefefe; */
    background-color: aliceblue;
    /* margin: 15% auto; */
    padding: 20px;
    border: 1px solid #888;
    height: 600px;
    width: 400px;
    position: fixed;
    bottom: 41px;
    top: 1px;
    left: 930px;
}
</style>
<script>
function openModal(modalId) {
    var modal = document.getElementById(modalId);

    if (modal) {
        modal.style.display = 'block';
    }
}

// Fermer la popup
function closeModal() {
    var modal = document.getElementById('myModal');

    if (modal) {
        modal.style.display = 'none';
    }
}

// Fermer la popup si l'utilisateur clique en dehors de la popup
window.onclick = function(event) {
    var modal = document.getElementById('myModal');

    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

;
</script>

<script src=" https://code.jquery.com/jquery-3.3.1.slim.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>


</body>

</html>