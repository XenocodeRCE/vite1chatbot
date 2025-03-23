<?php
// Initialisation
session_start();
$chatbotData = null;
$error = null;

// Définir la même clé de chiffrement que dans creer.php
define('ENCRYPTION_KEY', hash('sha256', 'VOTRE_CLEF_SECRETE_PERSO_POUR_CHIFFRER' . $_SERVER['HTTP_HOST']));

// Dossier de stockage des chatbots
$dataDir = 'data/chatbots/';
if (!file_exists($dataDir)) {
    mkdir($dataDir, 0755, true);
}

// Fonction pour déchiffrer les données
function decryptData($data, $key) {
    $data = base64_decode($data);
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($data, 0, $ivLength);
    $encrypted = substr($data, $ivLength);
    return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
}

// Chargement d'un chatbot existant
if (isset($_GET['id'])) {
    $chatbotId = trim($_GET['id']);
    $chatbotFile = $dataDir . $chatbotId . '.json';
    
    if (file_exists($chatbotFile)) {
        $chatbotData = json_decode(file_get_contents($chatbotFile), true);
        $_SESSION['current_chatbot'] = $chatbotId;
    } else {
        $error = "Ce chatbot n'existe pas ou a été supprimé.";
    }
} 
// Utilisation du chatbot en session
elseif (isset($_SESSION['current_chatbot'])) {
    $chatbotFile = $dataDir . $_SESSION['current_chatbot'] . '.json';
    
    if (file_exists($chatbotFile)) {
        $chatbotData = json_decode(file_get_contents($chatbotFile), true);
    } else {
        $error = "Ce chatbot n'existe pas ou a été supprimé.";
        unset($_SESSION['current_chatbot']);
    }
} else {
    $error = "Aucun chatbot n'a été spécifié.";
}

// Fonction pour appeler l'API GROQ
function callGroqAPI($messages, $encryptedApiKey) {
    global $error;
    $endpoint = 'https://api.groq.com/openai/v1/chat/completions';
    
    // Déchiffrer la clé API
    $apiKey = decryptData($encryptedApiKey, ENCRYPTION_KEY);
    
    if (!$apiKey) {
        return "Erreur: Impossible de déchiffrer la clé API.";
    }
    
    $data = [
        'model' => 'llama-3.3-70b-versatile',
        'messages' => $messages,
        'temperature' => 0.7,
        'max_tokens' => 2048,
    ];
    
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ];
    
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? "Désolé, je n'ai pas pu générer une réponse.";
    } else {
        $error = json_decode($response, true);
        return "Erreur lors de la communication avec l'API GROQ: " . ($error['error']['message'] ?? "Code d'erreur: $httpCode");
    }
}

// Traitement d'une question via Ajax
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_message']) && isset($_POST['chatbot_id'])) {
    $userMessage = trim($_POST['user_message']);
    $chatbotId = trim($_POST['chatbot_id']);
    $chatbotFile = $dataDir . $chatbotId . '.json';
    
    if (file_exists($chatbotFile)) {
        $chatbotData = json_decode(file_get_contents($chatbotFile), true);
        
        // Ajouter le message de l'utilisateur
        $chatbotData['messages'][] = [
            'role' => 'user',
            'content' => $userMessage
        ];
        
        // Appeler l'API GROQ avec la clé chiffrée (qui sera déchiffrée dans la fonction)
        $response = callGroqAPI($chatbotData['messages'], $chatbotData['api_key']);
        
        // Ajouter la réponse de l'assistant
        $chatbotData['messages'][] = [
            'role' => 'assistant',
            'content' => $response
        ];
        
        // Sauvegarder la conversation mise à jour
        file_put_contents($chatbotFile, json_encode($chatbotData, JSON_PRETTY_PRINT));
        
        // Retourner la réponse en JSON
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'response' => $response
        ]);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => "Ce chatbot n'existe pas ou a été supprimé."
        ]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $chatbotData ? htmlspecialchars($chatbotData['name']) : 'Assistant pédagogique' ?></title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <style>
        :root {
            --primary: #3B82F6;
            --primary-dark: #2563EB;
            --primary-light: #DBEAFE;
            --secondary: #10B981;
            --secondary-dark: #059669;
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-400: #9CA3AF;
            --gray-500: #6B7280;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-800: #1F2937;
            --gray-900: #111827;
            --radius: 6px;
            --font-sans: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            --font-mono: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-sans);
            background-color: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.5;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .chat-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
            height: 100%;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .chat-header {
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            background-color: white;
        }
        
        .chatbot-logo {
            margin-right: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            background: var(--primary-light);
            color: var(--primary-dark);
        }
        
        .chat-info {
            flex: 1;
        }
        
        .chat-info h1 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.125rem;
        }
        
        .chat-info p {
            font-size: 0.875rem;
            color: var(--gray-600);
        }
        
        .chat-actions {
            display: flex;
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            background-color: white;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .message {
            display: flex;
            gap: 1rem;
            max-width: 80%;
        }
        
        .message-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            background-color: var(--primary-light);
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--primary-dark);
            flex-shrink: 0;
        }
        
        .user-message {
            align-self: flex-end;
            flex-direction: row-reverse;
        }
        
        .user-message .message-avatar {
            background-color: var(--gray-200);
            color: var(--gray-700);
        }
        
        .message-content {
            background-color: var(--gray-100);
            padding: 1rem;
            border-radius: var(--radius);
            color: var(--gray-800);
            position: relative;
        }
        
        .user-message .message-content {
            background-color: var(--primary-light);
            color: var(--gray-900);
        }
        
        .message-content::before {
            content: '';
            position: absolute;
            left: -0.5rem;
            top: 1rem;
            width: 0.5rem;
            height: 0.5rem;
            background-color: var(--gray-100);
            transform: rotate(45deg);
        }
        
        .user-message .message-content::before {
            left: auto;
            right: -0.5rem;
            background-color: var(--primary-light);
        }
        
        .message-content p {
            margin-bottom: 0.75rem;
        }
        
        .message-content p:last-child {
            margin-bottom: 0;
        }
        
        .chat-input-wrapper {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--gray-200);
            background-color: white;
        }
        
        .chat-input-container {
            display: flex;
            align-items: center;
            border-radius: var(--radius);
            border: 1px solid var(--gray-300);
            background-color: white;
            overflow: hidden;
            padding-left: 1rem;
        }
        
        .chat-input-container:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        #user-input {
            flex: 1;
            border: none;
            padding: 0.75rem 0;
            font-size: 0.95rem;
            outline: none;
            resize: none;
            overflow-y: auto;
            max-height: 150px;
            min-height: 20px;
            line-height: 1.5;
            font-family: var(--font-sans);
        }
        
        .chat-input-actions {
            display: flex;
            align-items: center;
            border-left: 1px solid var(--gray-200);
            padding: 0.5rem;
        }
        
        #send-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        #send-btn:hover {
            background-color: var(--primary-dark);
        }
        
        #send-btn:disabled {
            background-color: var(--gray-400);
            cursor: not-allowed;
        }
        
        .typing-indicator {
            display: flex;
            gap: 0.25rem;
            padding: 0.5rem 1rem;
            align-items: center;
        }
        
        .typing-dot {
            width: 0.5rem;
            height: 0.5rem;
            background-color: var(--gray-400);
            border-radius: 50%;
            animation: typingAnimation 1.4s infinite ease-in-out;
            opacity: 0.7;
        }
        
        .typing-dot:nth-child(1) { animation-delay: 0s; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        
        @keyframes typingAnimation {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-0.25rem);
            }
        }
        
        .error-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 2rem;
            text-align: center;
        }
        
        .error-container h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--gray-900);
        }
        
        .error-container p {
            max-width: 600px;
            margin-bottom: 2rem;
            color: var(--gray-600);
        }
        
        .error-actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius);
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
        
        .clear-button {
            background: none;
            border: none;
            color: var(--gray-500);
            font-size: 0.875rem;
            cursor: pointer;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            transition: color 0.2s;
        }
        
        .clear-button:hover {
            color: var(--gray-700);
        }
        
        code {
            font-family: var(--font-mono);
            background-color: var(--gray-100);
            padding: 0.125rem 0.25rem;
            border-radius: 3px;
            font-size: 0.9em;
        }
        
        pre {
            font-family: var(--font-mono);
            background-color: var(--gray-800);
            color: var(--gray-100);
            padding: 1rem;
            border-radius: var(--radius);
            overflow-x: auto;
            margin: 0.75rem 0;
        }
        
        .message-content ul, .message-content ol {
            margin: 0.75rem 0;
            padding-left: 1.5rem;
        }
        
        .message-content a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .message-content a:hover {
            text-decoration: underline;
        }
        
        /* MathJax stylings */
        .mjx-chtml {
            margin: 0 !important;
            display: inline-block !important;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .message {
                max-width: 85%;
            }
            
            .chat-header {
                padding: 0.75rem 1rem;
            }
            
            .chat-messages {
                padding: 1rem;
            }
            
            .chat-input-wrapper {
                padding: 0.75rem 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .message {
                max-width: 90%;
            }
            
            .chatbot-logo {
                display: none;
            }
        }
    </style>
</head>
<body>
    <?php if ($error): ?>
        <div class="error-container">
            <h2>Oups, quelque chose s'est mal passé</h2>
            <p><?= htmlspecialchars($error) ?></p>
            <div class="error-actions">
                <a href="javascript:history.back()" class="btn btn-primary">Retour</a>
            </div>
        </div>
    <?php elseif ($chatbotData): ?>
        <div class="chat-container">
            <div class="chat-header">
                <div class="chatbot-logo">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 7.5L12 10.5L15 7.5M12 10.5V16.5M7 2H17L22 7V17L17 22H7L2 17V7L7 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="chat-info">
                    <h1><?= htmlspecialchars($chatbotData['name']) ?></h1>
                    <p>Assistant pédagogique IA</p>
                </div>
                <div class="chat-actions">
                    <button id="clear-btn" class="clear-button">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 6H5M5 6H21M5 6V20C5 20.5523 5.44772 21 6 21H18C18.5523 21 19 20.5523 19 20V6M8 6V4C8 3.44772 8.44772 3 9 3H15C15.5523 3 16 3.44772 16 4V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Effacer
                    </button>
                </div>
            </div>
            
            <div class="chat-messages" id="chat-messages">
                <?php 
                // Afficher les messages existants
                $displayMessages = array_filter($chatbotData['messages'], function($msg) {
                    return $msg['role'] != 'system';
                });
                
                foreach ($displayMessages as $message): 
                    if ($message['role'] == 'assistant'): 
                ?>
                    <div class="message bot-message">
                        <div class="message-avatar">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 7.5L12 10.5L15 7.5M12 10.5V16.5M7 2H17L22 7V17L17 22H7L2 17V7L7 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="message-content">
                            <?= processMessageContent($message['content']) ?>
                        </div>
                    </div>
                <?php elseif ($message['role'] == 'user'): ?>
                    <div class="message user-message">
                        <div class="message-avatar">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="message-content">
                            <p><?= nl2br(htmlspecialchars($message['content'])) ?></p>
                        </div>
                    </div>
                <?php endif; endforeach; ?>
            </div>
            
            <div class="chat-input-wrapper">
                <form id="chat-form">
                    <div class="chat-input-container">
                        <textarea 
                            id="user-input" 
                            placeholder="Posez votre question..." 
                            rows="1" 
                            autocomplete="off"
                        ></textarea>
                        <div class="chat-input-actions">
                            <button type="submit" id="send-btn" disabled>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22 2L11 13M22 2L15 22L11 13M11 13L2 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <script>
        // Variables pour stocker les informations du chatbot
        const chatbotData = {
            id: '<?= $chatbotData ? $chatbotData['id'] : '' ?>',
            name: '<?= $chatbotData ? addslashes($chatbotData['name']) : '' ?>'
        };
        
        // Référence aux éléments du DOM
        const chatForm = document.getElementById('chat-form');
        const userInput = document.getElementById('user-input');
        const messagesContainer = document.getElementById('chat-messages');
        const sendButton = document.getElementById('send-btn');
        
        // Auto-resize le textarea quand l'utilisateur tape
        userInput.addEventListener('input', function() {
            // Reset height - important to shrink on delete
            this.style.height = 'auto';
            
            // Set new height
            this.style.height = (this.scrollHeight) + 'px';
            
            // Enable/disable button based on content
            sendButton.disabled = this.value.trim() === '';
        });
        
        // Gérer l'envoi de messages
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = userInput.value.trim();
            
            if (message) {
                // Désactiver le champ de saisie et le bouton pendant le traitement
                userInput.disabled = true;
                sendButton.disabled = true;
                
                // Ajouter le message de l'utilisateur
                addMessage(message, 'user');
                
                // Vider le champ de saisie
                userInput.value = '';
                userInput.style.height = 'auto';
                
                // Montrer l'indicateur de chargement
                showTypingIndicator();
                
                // Appeler l'API du serveur
                fetch('chat.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `user_message=${encodeURIComponent(message)}&chatbot_id=${encodeURIComponent(chatbotData.id)}`
                })
                .then(response => response.json())
                .then(data => {
                    removeTypingIndicator();
                    
                    if (data.success) {
                        addMessage(data.response, 'bot');
                    } else {
                        addMessage("Désolé, une erreur s'est produite: " + data.error, 'bot');
                    }
                })
                .catch(error => {
                    removeTypingIndicator();
                    addMessage("Désolé, une erreur de communication s'est produite. Veuillez réessayer.", 'bot');
                    console.error('Error:', error);
                })
                .finally(() => {
                    // Réactiver le champ de saisie et le bouton
                    userInput.disabled = false;
                    userInput.focus();
                });
            }
        });
        
        // Fonction pour formater le texte avec Markdown simple
        function formatMessage(text) {
            // Échapper les caractères HTML
            text = text.replace(/&/g, '&amp;')
                       .replace(/</g, '&lt;')
                       .replace(/>/g, '&gt;');
            
            // Formatage du code
            text = text.replace(/```([^`]+)```/g, '<pre>$1</pre>');
            text = text.replace(/`([^`]+)`/g, '<code>$1</code>');
            
            // Formatage des listes
            text = text.replace(/^\s*[\-\*]\s+(.+)$/gm, '<li>$1</li>');
            text = text.replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>');
            
            // Formatage simple
            text = text.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
            text = text.replace(/\*([^*]+)\*/g, '<em>$1</em>');
            
            // Convertir les retours à la ligne
            text = text.replace(/\n/g, '<br>');
            
            return text;
        }
        
        // Ajouter un message à la conversation
        function addMessage(content, sender) {
            const messageElement = document.createElement('div');
            messageElement.classList.add('message');
            
            if (sender === 'user') {
                messageElement.classList.add('user-message');
                messageElement.innerHTML = `
                    <div class="message-avatar">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="message-content">
                        <p>${content.replace(/\n/g, '<br>')}</p>
                    </div>
                `;
            } else {
                messageElement.classList.add('bot-message');
                messageElement.innerHTML = `
                    <div class="message-avatar">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 7.5L12 10.5L15 7.5M12 10.5V16.5M7 2H17L22 7V17L17 22H7L2 17V7L7 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="message-content">
                        ${formatMessage(content)}
                    </div>
                `;
            }
            
            messagesContainer.appendChild(messageElement);
            
            // Scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        // Afficher l'indicateur de frappe
        function showTypingIndicator() {
            const typingElement = document.createElement('div');
            typingElement.classList.add('message', 'bot-message', 'typing-indicator');
            typingElement.innerHTML = `
                <div class="message-avatar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 7.5L12 10.5L15 7.5M12 10.5V16.5M7 2H17L22 7V17L17 22H7L2 17V7L7 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="message-content">
                    <div class="typing-indicator">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            `;
            
            messagesContainer.appendChild(typingElement);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        // Retirer l'indicateur de frappe
        function removeTypingIndicator() {
            const typingIndicator = document.querySelector('.typing-indicator');
            if (typingIndicator) {
                typingIndicator.closest('.message').remove();
            }
        }
        
        // Effacer la conversation
        document.getElementById('clear-btn').addEventListener('click', function() {
            if (confirm('Voulez-vous effacer cette conversation et recommencer ?')) {
                fetch('clear.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `chatbot_id=${encodeURIComponent(chatbotData.id)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Recharger la page pour afficher la conversation réinitialisée
                        window.location.reload();
                    } else {
                        alert("Erreur lors de la réinitialisation de la conversation");
                    }
                })
                .catch(error => {
                    alert("Une erreur s'est produite lors de la réinitialisation de la conversation");
                    console.error('Error:', error);
                });
            }
        });
        
        // Scroll vers le bas au chargement
        window.addEventListener('load', function() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            userInput.focus();
        });
        
        // Gérer le raccourci clavier Entrée/Shift+Entrée
        userInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (!sendButton.disabled) {
                    chatForm.dispatchEvent(new Event('submit'));
                }
            }
        });
    </script>
</body>
</html>

<?php
// Fonction pour traiter le contenu des messages avec un formatage basique de Markdown
function processMessageContent($content) {
    // Échapper les caractères HTML
    $content = htmlspecialchars($content);
    
    // Formater le code
    $content = preg_replace('/```(.*?)```/s', '<pre>$1</pre>', $content);
    $content = preg_replace('/`([^`]+)`/', '<code>$1</code>', $content);
    
    // Formater les listes
    $content = preg_replace('/^\s*[\-\*]\s+(.+)$/m', '<li>$1</li>', $content);
    $content = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $content);
    
    // Formatage simple
    $content = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $content);
    $content = preg_replace('/\*([^*]+)\*/', '<em>$1</em>', $content);
    
    // Convertir les retours à la ligne
    $content = nl2br($content);
    
    // Retourner le contenu formaté
    return $content;
}
?>