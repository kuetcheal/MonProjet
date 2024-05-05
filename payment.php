<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
try
{
$bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
 $idvoyage=$_POST['idVoyage'];
 $_SESSION['idVoyage']= $idvoyage;
 $requette ="select * From voyage where idVoyage='$idvoyage'";
 $resultat = $bdd->query($requette);
 while($donne=$resultat->fetch()){
  $price=$donne["prix"];
  $_SESSION["prix"]=$price;
  // echo($_SESSION["prix"]);
}
}
catch (Exception $e)
{
echo("echec de connexion");
}




?>

    <?php
function generateReservationNumber($length = 8) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$reservationNumber = generateReservationNumber();
?>



    <main>
        <form method="post" action="finalisation.php">
            <h3> 1) Informations du passager</h3>
            <div class="info-personnel">

                <div class="titre">
                    <p>1</p>
                    <h3 class="mode"> Passagers</h3>
                </div>
                <div class="contenaire1">
                    <div class="fisrtName">
                        <label htmlFor="inputText">Prénom </label><br><br>
                        <input type="text" name="prenom">
                    </div>
                    <div class="name">
                        <label htmlFor="inputText">Nom </label><br><br>
                        <input type="text" name="nom">
                    </div>
                </div>
            </div>
            <br><br>

            <div class="info-personnel">
                <div class="titre">
                    <p>2</p>
                    <h3 class="mode"> Contact</h3>
                </div>
                <div class="contenaire1">
                    <div class="fisrtName">
                        <label htmlFor="inputText">Email</label><br>
                        <input type="text" name="email">
                    </div>
                    <div class="name">
                        <label htmlFor="inputText">Numéro téléphone (facultatif) </label><br>
                        <input type="text" name="telephone" placeholder="+237">
                    </div>
                    <input type="hidden" name="reservationNumber" value="<?php echo $reservationNumber; ?>">
                </div>
            </div>
            <div class="titre">
                <h2>Total à payer: <span><?php echo($_SESSION["prix"]); ?> FCFA</span></h2>
            </div>
            <br>
            <h3>2). Mode de paiement</h3>
            <div> <input type="submit" name="submit" value="Payer à l'agence"> </div>

        </form>
        <br>
        <div class="col-2">
            <div id="paypal-button-container"></div>
        </div>



    </main>
    <!-- Replace "test" with your own sandbox Business account app client ID -->
    <script
        src="https://www.paypal.com/sdk/js?client-id=AWx5xzND7ZwEaNCZV0ySlgdj4AEUFEF1WwWFl8M8c3UUTrfZ_buif_zdrbEyPL7odxvEoN68o6_gPGjL&currency=EUR">
    </script>
    <script>
    paypal.Buttons({
        style: {
            layout: 'vertical',
            color: 'blue',
            shape: 'rect',
            label: 'paypal'
        },
        createOrder: (data, actions) => {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?php echo($_SESSION["prix"]); ?>'
                    }
                }]
            });
        },
        onApprove(data, actions) {
            return actions.order.capture(), then(function(orderData) {
                // Successful capture! For dev/demo purposes:
                console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                //const transaction = orderData.purchase_units[0].payments.captures[0];
                //alert(`Transaction ${transaction.status}: ${transaction.id}\n\nSee console for all available details`);
                window.location.replace('https://localhost/Paypal/Sucess.php')
            });
        },
        onCancel(data) {
            window.location.replace('https://localhost/Paypal/Cancel.php')
        }
    }).render('#paypal-button-container');
    </script>




    <style>
    body {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .main {
        display: flex;
        flex-direction: column;
    }


    .info-personnel {
        background-color: aliceblue;
        width: 720px;
        height: 130px;
        display: flex;
        justify-content: space-between;
        padding: 15px;
        border: 1px solid black;
    }

    .info-paiment {
        background-color: aliceblue;
        width: 800px;
        height: 380px;
        display: flex;
        margin: 20px;
        padding: 20px;
        border: 1px solid black;
        flex-direction: column;
    }

    .contenaire1 {
        display: flex;
        justify-content: space-between;
        padding: 15px;

    }

    .name {
        margin-left: 90px;
    }

    h3 {
        margin-left: 9px;
    }

    input[type="text"] {
        padding: 5px;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 200px;
    }


    input[type="submit"] {
        padding: 5px;
        font-size: 19px;
        border: 1px solid #ccc;
        color: white;
        border-radius: 5px;
        width: 750px;
        background-color: green;
        height: 60px;
        cursor: pointer;
    }

    .custom-radio {
        transform: scale(1.5);
        /* Ajustez la valeur selon la taille souhaitée */
        border: 1px solid #ccc;
    }

    span {
        background-color: green;
        height: 10px;
        width: 20px;
    }

    table {
        width: 650px;
        height: 130px;
        border-collapse: collapse;
    }

    td {
        padding: 10px;
        border: 1px solid #ccc;
        text-align: center;
    }

    button {
        display: block;
        margin-bottom: 5px;
        background-color: #f1f1f1;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
    }

    input[type="radio"] {
        margin-right: 5px;
    }

    p {
        margin: 0;
    }

    .adresse {

        font-weight: bold;
        font-size: 18px;
        margin-left: 10px;
    }

    .mode {

        font-weight: bold;
        font-size: 18px;
        margin-left: 15px;
    }

    .credit {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 20px;
    }

    .paypal {
        margin-left: 10px;
    }

    .option {
        display: flex;
        align-items: center;
    }

    .titre {
        display: flex;
        align-items: center;
    }
    </style>



</body>

</html>