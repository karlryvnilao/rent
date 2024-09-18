<a href="#" class="btn btn-primary btn-block mb-2" data-bs-toggle="modal" data-bs-target="#realEstateBotModal" style="font-weight: bold; text-transform: uppercase; text-align: center;">
  <i class="fas fa-home"></i> Chatbot
</a>


<div class="modal fade" id="realEstateBotModal" tabindex="-1" aria-labelledby="realEstateBotModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-lg shadow-lg">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="realEstateBotModalLabel">
          <i class="fas fa-comments-dollar"></i> Rental Assistant
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="background-color: #f8f9fa;">
        <div id="chatWindow" class="border rounded p-3" style="height: 400px; overflow-y: auto; background-color: white;">
          <div class="bot-message bg-light rounded p-2 mb-2">
            <strong>Bot:</strong> Hello! Looking to rent a home? Let me help you find the perfect place.
          </div>
        </div>
        <div class="input-group mt-3">
          <input type="text" id="userMessage" class="form-control rounded-pill" placeholder="Ask me anything about renting..." style="padding-left: 15px;">
          <button class="btn btn-success rounded-pill" id="sendMessage" style="margin-left: 10px;">Send</button>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  #chatWindow {
    background-color: #f1f1f1;
    border-radius: 10px;
    padding: 10px;
  }
  
  .bot-message {
    background-color: #e9ecef;
    color: #333;
    border-radius: 15px;
    padding: 10px;
    margin-bottom: 10px;
  }

  .user-message {
    background-color: #007bff;
    color: white;
    border-radius: 15px;
    padding: 10px;
    margin-bottom: 10px;
    text-align: right;
  }

  .input-group input {
    font-size: 16px;
    height: 45px;
  }

  .input-group button {
    font-size: 16px;
    height: 45px;
    width: 100px;
  }
</style>

<script>
  document.getElementById('sendMessage').addEventListener('click', function() {
    const userMessage = document.getElementById('userMessage').value;
    const chatWindow = document.getElementById('chatWindow');

    if (userMessage.trim() !== '') {
      const userMessageElement = `<div class="user-message"><strong>You:</strong> ${userMessage}</div>`;
      chatWindow.innerHTML += userMessageElement;

      setTimeout(function() {
        const botMessageElement = `<div class="bot-message"><strong>Bot:</strong> I can provide information on available rentals, just let me know what you're looking for!</div>`;
        chatWindow.innerHTML += botMessageElement;
        chatWindow.scrollTop = chatWindow.scrollHeight; 
      }, 1000);

      document.getElementById('userMessage').value = ''; 
      chatWindow.scrollTop = chatWindow.scrollHeight; 
    }
  });
</script>
