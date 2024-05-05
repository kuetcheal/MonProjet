<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <script>
    function compteOccu {
        const compteur = {}
        const caract = chaine.split('');

        caract.forEach(caractere => {
            compteur[caractere] = (compteur[caratere]) + 1
        });
        return compteur;

    }

    console.log('compteur');

    compteOccu();
    </script>
</body>

</html>