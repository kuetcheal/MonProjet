<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>Document</title>
</head>

<body>
    <!-- Modal Container -->
    <div class="modalitisation" id="myModal5">
        <div class="contente-modalitisation">
            <div class="modal-header">
                <h2 class="modal-title">Contactez l'agence EasyTravel</h2>
                <span class="close1" onclick="closeModal('myModal5')" style="position: fixed; right: 415px; top: 12px;">
                    <i class="fa fa-times" aria-hidden="true" style="font-size: 25px"></i></span>
            </div>
            <br>
            <span>
                <hr>
            </span> <br>
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="mbx">
                        <label for="">Votre nom</label>
                        <input type="text" class="form-control" placeholder="Alex KUETCHE" name="name">
                    </div>
                    <div class="mbx">
                        <label for="">Numéro téléphone</label>
                        <input type="text" class="form-control" placeholder="655196254" name="telephone">
                    </div>
                    <div class="mbx">
                        <label>Email address</label>
                        <input type="email" class="form-control" id="exampleFormControlInput1" name="gmail"
                            placeholder="name@example.com">
                    </div>
                    <div class="mbz">
                        <label for="exampleFormControlTextarea1" class="form-label">Rédigez votre
                            message</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"
                            name="message"></textarea>
                    </div>
                    <div class="mbz">
                        <label for="input1">Choisir l'agence :</label>
                        <select id="input1" name="choix" style="width: 100%; height: 40px;" class="select2">
                            <?php
                                $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
                                $query = 'select * from destination order by Nom_ville ASC';
                                $response = $bdd->query($query);
                                while ($donnee = $response->fetch()) {
                                    $destination = $donnee['Nom_ville'];
                                    echo '<option value="'.htmlspecialchars($destination).'">'.htmlspecialchars($destination).'</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <br>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"
                        style="background-color: green; border-color: green;">Envoyer</button>
                </div>
            </form>
        </div>
    </div>

    <style>
    .modalitisation {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .contente-modalitisation {
        background-color: #fefefe;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #888;
        width: 40%;
        max-width: 500px;
        margin: 0 auto;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        animation: fadeIn 0.3s ease-in-out;
    }

    /* Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* The Close Button */
    .close1 {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close1:hover,
    .close1:focus {
        color: green;
        text-decoration: none;
    }

    .modal-header h2 {
        color: green;
        font-size: 24px;
        margin-left: 25px;
    }

    .modal-body label {
        display: block;
        margin-bottom: 5px;
        color: black;
        font-size: 14px;
    }

    .modal-body input[type="text"],
    .modal-body input[type="email"],
    .modal-body textarea,
    .modal-body select {
        width: 100%;
        padding: 12px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
    }

    .modal-body input[type="text"]:focus,
    .modal-body input[type="email"]:focus,
    .modal-body textarea:focus,
    .modal-body select:focus {
        border-color: #0056b3;
        outline: none;
    }

    .modal-footer button {
        background-color: green;
        color: white;
        border: none;
        padding: 12px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s;
        width: 100%;
    }

    .modal-footer button:hover {
        background-color: #006400;
    }
    </style>

</body>


</html>