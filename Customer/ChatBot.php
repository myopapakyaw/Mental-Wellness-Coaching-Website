<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Popup</title>
    <style>
        /* Style for the chat popup window */
        /* Chatbot  */

        #chatbot-window {
            position: fixed;
            bottom: 120px;
            /* Increased bottom value */
            right: 20px;
            width: 300px;
            height: 400px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            display: none;
            /* Initially hidden */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 9999;
        }

        /* Style for the chatbot header */
        .chat-header {
            background-color: rgb(22, 135, 108);
            color: white;
            padding: 10px;
            font-weight: bold;
            text-align: center;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        /* Style for the chat container */
        .chat-container {
            padding: 10px;
            height: calc(100% - 60px);
            overflow-y: auto;
            /* border-radius: 5px; */
        }

        /* Style for the chat input box */
        .input-box {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-top: none;
            box-sizing: border-box;
            font-size: 14px;
            border-radius: 5px;
        }

        /* Chat icon style */
        #chat-icon {
            position: fixed;
            bottom: 20px;
            /* Increased bottom value */
            right: 20px;
            width: 60px;
            height: 60px;
            /* background-color: rgb(16, 120, 108); */
            /* background-color: rgb(10, 88, 6); */
            background-color: rgb(26, 234, 123);
            /* background-color: rgb(188, 35, 83); */
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 60px;
            font-size: 30px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: jump 0.5s infinite;
        }

        #chat-icon:hover {
            transform: scale(1.1) rotate(15deg);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        #chat-icon.pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(26, 234, 123, 0.7);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(26, 234, 123, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(26, 234, 123, 0);
            }
        }

        

        /* Style for messages */
        .message {
            padding: 5px;
            margin: 5px;
            border-radius: 5px;
        }

        .user-message {
            background-color: rgb(185, 247, 154);
        }

        .bot-message {
            background-color: #d3f9d8;
        }

        @keyframes jump {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
                /* Adjust jump height here */
            }
        }
    </style>
</head>

<body>

    <!-- Chatbot Window -->
    <div id="chatbot-window">
        <div class="chat-header">
            Chat with Velora bot!
        </div>
        <div class="chat-container" id="chat-container"></div>
        <input type="text" id="user-input" class="input-box" placeholder="Ask me anything..." />
    </div>

    <!-- Chatbot Icon -->
    <!-- <div id="chat-icon">🤖</div> -->
    <!-- <div id="chat-icon">🎁</div> -->
    <!-- <div id="chat-icon">💌</div> -->
    <!-- <div id="chat-icon">⭐</div> -->
    <!-- <div id="chat-icon">🍀</div> -->
    <!-- <div id="chat-icon">🎗</div> -->
    <div id="chat-icon">🧘🏻‍♀️</div>
    <!-- <div id="chat-icon">
        <img src="../Customer/Img/11.png" alt="Chatbot Icon">
    </div> -->

    <div></div>
    <script>
        // Initialize chat container and input
        const chatContainer = document.getElementById('chat-container');
        const userInput = document.getElementById('user-input');
        const chatbotWindow = document.getElementById('chatbot-window');
        const chatIcon = document.getElementById('chat-icon');

        // Function to display messages
        function displayMessage(message, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.textContent = message;
            messageDiv.classList.add('message');
            messageDiv.classList.add(sender === 'user' ? 'user-message' : 'bot-message');
            chatContainer.appendChild(messageDiv);
        }

        // Toggle chatbot window visibility
        chatIcon.addEventListener('click', () => {
            chatbotWindow.style.display = chatbotWindow.style.display === 'none' ? 'block' : 'none';
        });

        // Event listener for user input
        userInput.addEventListener('keydown', async (event) => {
            if (event.key === 'Enter' && userInput.value.trim() !== '') {
                const userMessage = userInput.value.trim();
                displayMessage(userMessage, 'user');
                userInput.value = '';  // Clear input field
                await getBotResponse(userMessage);
            }
        });

        // Fetch bot response from OpenAI API
        async function getBotResponse(userMessage) {
          
           getenv('OPENAI_API_KEY');
            const url = 'https://api.openai.com/v1/chat/completions';

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${apiKey}`,
                },
                body: JSON.stringify({
                    model: 'gpt-4o-mini', // Updated model
                    messages: [
                        { role: 'user', content: userMessage }
                    ],
                    max_tokens: 150,
                    temperature: 0.9,
                }),
            });

            const data = await response.json();
            const botMessage = data.choices[0].message.content.trim();
            displayMessage(botMessage, 'bot');
        }
    </script>

</body>

</html>