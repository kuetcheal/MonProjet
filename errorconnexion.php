<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    


<div class="container">
      <h1>Connexion</h1>
      <form method="post" action="#">
         <label for="username">Nom d'utilisateur :</label>
         <input type="text" name="username" id="username" required>
         <label for="password">Mot de passe :</label>
         <input type="password" name="password" id="password" required><br>
         <button type="submit">se connecter</button>
         <p class="para">Attention !!! nom d'utilisateur et mot de passe incompatibles. <a href="#">Mot de passe oublié ?</a> </p>
         <p class="para">Si vous n'avez pas de compte. <a href="inscription.php">s'inscrire</a> </p>
        
      </form>
    </div>


    <style>
   
    body{
        background-color: aliceblue;
    }

    .container {
    background-color: white;
    border-radius: 5px;
   
    margin: 50px auto;
    max-width: 500px;
    padding: 20px;
    margin-top: 100px;
    border-color:  red;
    border: 2px solid red;
   
  }
 
  h1 {
    margin-bottom: 20px;
    text-align: center;
    /* color: white; */
  }
  
  form {
    display: flex;
    flex-direction: column;
  }
  
  label {
    margin-bottom: 10px;
  }
  
  input[type="text"],
  input[type="password"] {
    border: black;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 20px;
    border-color: red;
    border: 2px solid red;
  } 
  
   button[type="submit"] {
    background-color: white;
    border: none;
    border-radius: 5px;
    color: white;
    padding: 10px;
    font-size: 16px;
    cursor: pointer;

  } 
   button[type="submit"] {
    padding: 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
   
  .liste ul{
  display: flex;
  font-size: 15px;
  list-style-type: none;
}
  
  button:hover {
    background-color: #3e8e41;
  }
  .para{
    font-size: 16px;
  }
  hr{
    color: aqua;
    
  }
  #ligne{
    margin-top: 50px;
    color: aqua;
    height: 2px;
  }
  #ligne_bas{
    margin-bottom: 70px;
    height: 2px;
    color: aqua;
  }
  .social-icons {
    display: flex;
    justify-content: center;
    margin: '25px';
    flex-basis: 30%;
      margin-bottom: 20px;
      margin-left: 50px;
  } 
   
   .social-icons ul{
    display: flex;
    margin-top: 10px;
    margin-bottom: 10px;
    list-style-type: none;
  }
  
  .social-icons a {
    display: flex;
    
    margin: 0 10px;
  }
  
  .social-icons li {
  /* Ajoute un espace de 10 pixels à gauche et à droite de chaque élément de la liste */
  padding-left: 15px;
  padding-right: 15px;
}


  .social-icons i {
    font-size: 24px;
    color: black;
  } 

  .social-icons li.liste {
  font-size: 20px;
}
.social-icons li a i {
  font-size: 32px;
}



  a {
    color: blue;
  }

  /* ECHEC AUTHENTIFICATION */
   .para{
    text-align: center;
    color: red;
    font-size: 20px;
  }
 
</style>
</body>
</html>