const form = document.querySelector('form');
const usernameInput = document.querySelector('#username');
const passwordInput = document.querySelector('#password');

form.addEventListener('submit', (e) => {
  e.preventDefault();
  const username = usernameInput.value.trim();
  const password = passwordInput.value.trim();

  // Vérification du nom d'utilisateur et du mot de passe
  if (username === 'monNomUtilisateur' && password === 'monMotDePasse') {
    // Connexion réussie
    alert('Connexion réussie !');
    // Redirection vers la page d'accueil de l'application
    window.location.href = 'accueil.html';
  } else {
  
