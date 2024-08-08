<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Modal</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
        .modal {
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

        .modal-content {
            background-color: #fefefe;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #888;
            width: 40%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-button:hover {
            color: black;
        }
    </style>
</head>
<body>

<div class="button-container">
    <button class="button" id="openModalButton">Gérer ma réservation</button>
</div>

<div id="modalContainer"></div>

<script>
document.getElementById('openModalButton').addEventListener('click', function() {
    $.ajax({
        url: 'formulaire.php',
        success: function(response) {
            document.getElementById('modalContainer').innerHTML = response;
            var modal = document.querySelector('#modalContainer .modal');
            var closeButton = document.querySelector('.close-button');

            modal.style.display = "flex";

            closeButton.onclick = function() {
                modal.style.display = "none";
                modal.parentNode.removeChild(modal);
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                    modal.parentNode.removeChild(modal);
                }
            }
        }
    });
});
</script>

</body>
</html>
