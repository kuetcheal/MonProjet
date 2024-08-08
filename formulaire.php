<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Document</title>
</head>

<body>
    <!-- Modal Container -->
    <div id="reservationForm" class="modalisation">
        <div class="modalité-content">
            <div class="infos">
                <h1>Gérez votre reservation</h1>
                <span class="close-button">×</span>
            </div>
            <form id="reservationForm" method="post">
                <label for="exampleInputEmail1">Votre adresse mail</label>
                <input type="email" id="exampleInputEmail1" name="mail" placeholder="alex99@gmail.com" required>
                <br>
                <label for="exampleFormControlInput1">Numéro réservation</label>
                <input type="text" id="exampleFormControlInput1" name="Numero_reservation" placeholder="8I4P5SPD"
                    required>
                <h5>c'est un numéro de 8 caractères contenant des valeurs alpha-numériques.</h5> <br>
                <button type="submit">Vérifier</button>
            </form>
        </div>
    </div>

    <style>
    .modalisation {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.7);
        /* Fond avec opacité */
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modalité-content {
        background-color: #fefefe;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #888;
        width: 40%;
        max-width: 500px;
        margin: 0 auto;
        /* Centre la modale */
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
    .close-button {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }



    .close-button:hover,
    .close-button:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    h1 {
        color: green;
        font-size: 24px;
    }

    /* Style pour le titre et le bouton de fermeture */
    .infos {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    h1 {
        color: green;
        margin-left: 25px;
    }

    .close-button {
        font-size: 1.4em;
        color: #aaa;
        cursor: pointer;
    }

    .close-button:hover {
        color: green;
    }

    /* Styles des formulaires pour améliorer la lisibilité et l'interaction */
    form {
        margin-top: 20px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        color: black;
        font-size: 14px;
    }

    input[type="email"],
    input[type="text"] {
        width: 100%;
        padding: 12px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
    }

    input[type="email"]:focus,
    input[type="text"]:focus {
        border-color: #0056b3;
        outline: none;
    }

    button {
        background-color: green;
        color: white;
        border: none;
        padding: 12px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s;
        width: 100%;
    }

    button:hover {
        background-color: #006400;
    }
    </style>

</body>

</html>