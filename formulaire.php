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
                <label for="exampleInputEmail1">Votre adresse mail <strong>*</strong> </label>
                <input type="email" id="exampleInputEmail1" name="mail" placeholder="alex99@gmail.com" required>
                <br>
                <label for="exampleFormControlInput1">Numéro réservation <strong>*</strong></label>
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
    overflow-y: auto;
    background-color: rgba(0, 0, 0, 0.7);
    justify-content: center;
    align-items: center;
    padding: 20px;
    box-sizing: border-box;
}

.modalité-content {
    background-color: #fefefe;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #888;
    width: 40%;
    max-width: 500px;
    margin: 0 auto;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    animation: fadeIn 0.3s ease-in-out;
    box-sizing: border-box;
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

/* Bouton fermeture */
.close-button {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
}

.close-button:hover,
.close-button:focus {
    color: green;
    text-decoration: none;
}

h1 {
    color: green;
    font-size: 24px;
    margin-left: 25px;
    margin-top: 0;
    margin-bottom: 0;
    line-height: 1.3;
}

/* Header popup */
.infos {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
}

/* Formulaire */
form {
    margin-top: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
    color: black;
    font-size: 14px;
    line-height: 1.4;
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

h5 {
    margin: 0;
    font-size: 14px;
    font-weight: normal;
    line-height: 1.6;
    color: #333;
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

/* Tablette */
@media screen and (max-width: 992px) {
    .modalité-content {
        width: 60%;
    }
}

/* Mobile */
@media screen and (max-width: 768px) {
    .modalisation {
        padding: 16px;
        align-items: center;
    }

    .modalité-content {
        width: 100%;
        max-width: 360px;
        padding: 18px 16px;
        border-radius: 10px;
    }

    .infos {
        align-items: flex-start;
    }

    h1 {
        font-size: 20px;
        margin-left: 0;
        max-width: 85%;
        line-height: 1.35;
    }

    .close-button {
        font-size: 24px;
        margin-top: 2px;
    }

    form {
        margin-top: 16px;
    }

    label {
        font-size: 14px;
    }

    input[type="email"],
    input[type="text"] {
        padding: 11px;
        font-size: 15px;
        margin-bottom: 16px;
    }

    h5 {
        font-size: 13px;
        line-height: 1.5;
    }

    button {
        font-size: 15px;
        padding: 12px;
        margin-top: 8px;
    }
}

/* Petit mobile */
@media screen and (max-width: 420px) {
    .modalisation {
        padding: 12px;
    }

    .modalité-content {
        max-width: 100%;
        padding: 16px 14px;
    }

    h1 {
        font-size: 18px;
        max-width: 82%;
    }

    .close-button {
        font-size: 22px;
    }

    input[type="email"],
    input[type="text"] {
        font-size: 14px;
    }

    h5 {
        font-size: 12.5px;
    }
}
</style>