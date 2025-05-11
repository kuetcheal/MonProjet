<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chatbot Mobiliis</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    #chat-container {
      max-width: 400px;
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: #f5f8fb;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      overflow: hidden;
      z-index: 9999;
    }
    #chat-header {
      background-color: #A93D87;
      color: white;
      padding: 10px;
      font-weight: bold;
    }
    #chat-body {
      max-height: 400px;
      overflow-y: auto;
      padding: 10px;
      font-family: Arial, sans-serif;
    }
    .chat-message {
      margin-bottom: 10px;
    }
    .bot-message {
      background-color: #007dc4;
      color: white;
      padding: 8px;
      border-radius: 10px 10px 10px 0;
      display: inline-block;
    }
    .user-message {
      background-color: #e0e0e0;
      color: #333;
      padding: 8px;
      border-radius: 10px 10px 0 10px;
      display: inline-block;
      float: right;
    }
    #chat-input-area {
      padding: 10px;
      background-color: white;
      border-top: 1px solid #ddd;
    }
    #chat-input {
      width: 100%;
      padding: 8px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    #close-btn {
      position: absolute;
      top: 5px;
      right: 10px;
      color: white;
      cursor: pointer;
    }
    .formulaire {
      background-color: #f9f9f9;
      border-radius: 8px;
      padding: 10px;
      margin-top: 10px;
    }
  </style>
</head>
<body>

<div id="chat-container">
  <div id="chat-header">
    Assistance Mobiliis
    <span id="close-btn"><i class="fas fa-times"></i></span>
  </div>
  <div id="chat-body">
    <div class="chat-message bot-message">Bonjour ! Que désirez-vous aujourd’hui ?</div>
  </div>
  <div id="chat-input-area">
    <input type="text" id="chat-input" placeholder="Tapez votre message...">
  </div>
</div>

<script>
  const chatBody = document.getElementById('chat-body');
  const chatInput = document.getElementById('chat-input');
  const closeBtn = document.getElementById('close-btn');

  const responses = {
    "voyage": "Quelle est votre destination ? (France, Canada, Allemagne, Belgique)",
    "france": "Quelle est la raison de votre voyage en France ? (études, tourisme, affaires, autres)",
    "canada": "Quelle est la raison de votre voyage au Canada ? (études, tourisme, immigration, résidence permanente)",
    "allemagne": "Quelle est la raison de votre voyage en Allemagne ? (études, tourisme, affaires, autres)",
    "belgique": "Quelle est la raison de votre voyage en Belgique ? (études, tourisme, affaires, autres)",
    "cours": "Quel cours souhaitez-vous suivre ? (anglais, allemand, espagnol)",
    "service": "Quel service souhaitez-vous ? (logement, assurance, caution bancaire, compte bloqué)",
    "emploi": "Merci, veuillez remplir le formulaire de contact ci-dessous pour qu’un conseiller vous recontacte.",
    "renseignement": "Merci, un conseiller vous répondra bientôt.",
    "formulaire": "Veuillez remplir le formulaire de contact ci-dessous."
  };

  chatInput.addEventListener("keypress", function(e) {
    if (e.key === "Enter") {
      const userText = chatInput.value.trim();
      if (!userText) return;
      appendMessage(userText, 'user');
      chatInput.value = '';
      handleResponse(userText.toLowerCase());
    }
  });

  function appendMessage(text, sender) {
    const msgDiv = document.createElement('div');
    msgDiv.className = 'chat-message ' + (sender === 'bot' ? 'bot-message' : 'user-message');
    msgDiv.innerText = text;
    chatBody.appendChild(msgDiv);
    chatBody.scrollTop = chatBody.scrollHeight;
  }

  function handleResponse(input) {
    if (input === 'formulaire' || input === 'emploi') {
      displayForm();
    } else {
      const response = responses[input] || "Merci pour votre message. Nous vous contacterons bientôt.";
      setTimeout(() => appendMessage(response, 'bot'), 500);
    }
  }

  function displayForm() {
    const formHtml = `
      <form class="formulaire" onsubmit="event.preventDefault(); submitForm();">
        <div class="mb-2"><input type="text" id="nom" class="form-control" placeholder="Nom" required></div>
        <div class="mb-2"><input type="text" id="telephone" class="form-control" placeholder="Téléphone" required></div>
        <div class="mb-2"><input type="email" id="email" class="form-control" placeholder="Email" required></div>
        <div class="mb-2"><textarea id="message" class="form-control" rows="3" placeholder="Message" required></textarea></div>
        <button type="submit" class="btn btn-primary w-100">Envoyer</button>
      </form>
    `;
    const container = document.createElement('div');
    container.innerHTML = formHtml;
    chatBody.appendChild(container);
    chatBody.scrollTop = chatBody.scrollHeight;
  }

  function submitForm() {
    const nom = document.getElementById('nom').value;
    const telephone = document.getElementById('telephone').value;
    const email = document.getElementById('email').value;
    const message = document.getElementById('message').value;
    console.log({ nom, telephone, email, message });
    appendMessage("Parfait, on vous recontactera dans les brefs délais.", 'bot');
  }

  closeBtn.addEventListener('click', () => {
    document.getElementById('chat-container').style.display = 'none';
  });
</script>

</body>
</html>
