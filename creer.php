<?php
// Initialisation
session_start();
$error = null;
$success = null;

// Définir une clé de chiffrement unique pour l'application
// Dans un vrai déploiement, cette clé devrait être stockée en dehors du code (variables d'environnement, etc.)
define('ENCRYPTION_KEY', hash('sha256', 'VOTRE_CLEF_SECRETE_PERSO_POUR_CHIFFRER' . $_SERVER['HTTP_HOST']));

// Vérifier si le dossier de données existe, sinon le créer
$dataDir = 'data/chatbots/';
if (!file_exists($dataDir)) {
    mkdir($dataDir, 0755, true);
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et valider les données
    $apiKey = isset($_POST['api_key']) ? trim($_POST['api_key']) : '';
    $botName = isset($_POST['bot_name']) ? trim($_POST['bot_name']) : '';
    $courseContent = isset($_POST['course_content']) ? trim($_POST['course_content']) : '';
    $systemPrompt = isset($_POST['system_prompt']) ? trim($_POST['system_prompt']) : '';
    
    // Validation
    if (empty($apiKey)) {
        $error = "La clé API groQ est requise.";
    } elseif (empty($botName)) {
        $error = "Le nom du chatbot est requis.";
    } elseif (empty($courseContent)) {
        $error = "Le contenu du cours est requis.";
    } elseif (empty($systemPrompt)) {
        // Utiliser le prompt système par défaut si vide
        $systemPrompt = "Tu es un assistant pédagogique amical et patient. Ta mission est d'aider les étudiants à comprendre le contenu du cours en répondant à leurs questions de manière claire et précise. Utilise uniquement les informations fournies dans le contenu du cours pour répondre. Si tu ne connais pas la réponse ou si la question sort du cadre du cours, indique-le poliment. Reste toujours encourageant et bienveillant.";
    }
    
    // Si tout est valide, créer le chatbot
    if (!$error) {
        // Génération d'un ID unique pour le chatbot
        $chatbotId = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $botName)) . '-' . date('Ymd') . '-' . substr(md5(uniqid()), 0, 6);
        
        // Vérifier la validité de la clé API groQ
        $isValidApiKey = checkGroqApiKey($apiKey);
        if (!$isValidApiKey) {
            $error = "La clé API groQ semble invalide ou a expiré. Veuillez vérifier votre clé sur https://console.groq.com/keys";
        } else {
            // Chiffrer la clé API avant de la stocker
            $encryptedApiKey = encryptData($apiKey, ENCRYPTION_KEY);
            
            // Création du chatbot
            $chatbotData = [
                'id' => $chatbotId,
                'name' => $botName,
                'description' => 'Assistant pédagogique personnalisé',
                'api_key' => $encryptedApiKey, // Clé API chiffrée
                'system_prompt' => $systemPrompt,
                'course_content' => $courseContent,
                'created_at' => date('Y-m-d H:i:s'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt . "\n\nVoici le contenu du cours que tu dois connaître :\n\n" . $courseContent
                    ],
                    [
                        'role' => 'assistant',
                        'content' => "Bonjour ! Je suis votre assistant pour ce cours. Comment puis-je vous aider aujourd'hui ?"
                    ]
                ]
            ];
            
            // Sauvegarde du chatbot dans un fichier JSON
            if (file_put_contents($dataDir . $chatbotId . '.json', json_encode($chatbotData, JSON_PRETTY_PRINT))) {
                // Rediriger vers la page de chat
                header('Location: chat.php?id=' . $chatbotId . '&new=1');
                exit;
            } else {
                $error = "Erreur lors de la sauvegarde du chatbot. Vérifiez les permissions d'écriture.";
            }
        }
    }
}

// Fonction pour vérifier la validité de la clé API groQ
function checkGroqApiKey($apiKey) {
    $endpoint = 'https://api.groq.com/openai/v1/models';
    
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode === 200;
}

// Fonction pour chiffrer les données
function encryptData($data, $key) {
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

// Fonction pour déchiffrer les données (utilisée dans chat.php)
function decryptData($data, $key) {
    $data = base64_decode($data);
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($data, 0, $ivLength);
    $encrypted = substr($data, $ivLength);
    return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un chatbot - Vite1Chatbot</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="index.php">Vite1Chatbot</a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="index.php#tutoriel">Tutoriel</a></li>
                    <li><a href="index.php#apropos">À propos</a></li>
                    <li class="nav-button"><a href="creer.php" class="active">Créer un chatbot</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="page-header">
                <h1>Créer un nouveau chatbot</h1>
                <p>Transformez votre contenu pédagogique en assistant IA interactif en quelques minutes</p>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-error">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8V12M12 16H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p><?= htmlspecialchars($error) ?></p>
            </div>
            <?php endif; ?>
            
            <div class="create-form-container">
                <form id="createForm" action="creer.php" method="post">
                    <div class="create-form">
                        <div class="form-section">
                            <h2>Configuration du chatbot</h2>
                            
                            <div class="form-group">
                                <label for="api-key">Clé API groQ <span class="required">*</span></label>
                                <input type="password" id="api-key" name="api_key" value="<?= isset($_POST['api_key']) ? htmlspecialchars($_POST['api_key']) : '' ?>" required>
                                <div class="hint">
                                    Obtenir votre clé API sur 
                                    <a href="https://console.groq.com/keys" target="_blank">console.groq.com/keys</a>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="bot-name">Nom du chatbot <span class="required">*</span></label>
                                <input type="text" id="bot-name" name="bot_name" placeholder="Ex: Assistant Mathématiques Terminale" value="<?= isset($_POST['bot_name']) ? htmlspecialchars($_POST['bot_name']) : '' ?>" required>
                                <div class="hint">Ce nom sera visible pour les utilisateurs de votre chatbot</div>
                            </div>
                            
                            <div class="divider">Contenu et comportement</div>
                            
                            <div class="form-group">
                                <label for="course-content">Contenu du cours <span class="required">*</span></label>
                                <textarea id="course-content" name="course_content" placeholder="Collez ici le contenu de votre cours..." required><?= isset($_POST['course_content']) ? htmlspecialchars($_POST['course_content']) : '' ?></textarea>
                                <div class="hint">Ce contenu sera utilisé comme base de connaissances pour votre chatbot</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="system-prompt">Personnalité du bot (prompt système)</label>
                                <textarea id="system-prompt" name="system_prompt"><?= isset($_POST['system_prompt']) ? htmlspecialchars($_POST['system_prompt']) : "Tu es un assistant pédagogique amical et patient. Ta mission est d'aider les étudiants à comprendre le contenu du cours en répondant à leurs questions de manière claire et précise. Utilise uniquement les informations fournies dans le contenu du cours pour répondre. Si tu ne connais pas la réponse ou si la question sort du cadre du cours, indique-le poliment. Reste toujours encourageant et bienveillant." ?></textarea>
                                <div class="hint">Ces instructions définissent le comportement et la personnalité de votre chatbot</div>
                            </div>
                            
                            <div class="model-info">
                                <div class="model-icon">AI</div>
                                <div class="model-details">
                                    <h3>Modèle : Llama-3.3-70b-versatile</h3>
                                    <p>Modèle optimisé pour des réponses rapides avec un excellent rapport qualité/performance</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <a href="index.php" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary" id="create-btn">Créer mon chatbot</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Vite1Chatbot - Créez et partagez des assistants pédagogiques en quelques clics</p>
        </div>
    </footer>

    <script>
        // Validation du formulaire avant soumission
        document.getElementById('createForm').addEventListener('submit', function(e) {
            const createBtn = document.getElementById('create-btn');
            const apiKey = document.getElementById('api-key').value.trim();
            const botName = document.getElementById('bot-name').value.trim();
            const courseContent = document.getElementById('course-content').value.trim();
            
            if (!apiKey || !botName || !courseContent) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
                return;
            }
            
            // Désactiver le bouton pour éviter les soumissions multiples
            createBtn.disabled = true;
            createBtn.textContent = 'Création en cours...';
            
            // Vérification complémentaire de la taille du contenu
            if (courseContent.length < 50) {
                e.preventDefault();
                alert('Le contenu du cours semble trop court. Veuillez fournir un contenu plus substantiel pour que le chatbot puisse être efficace.');
                createBtn.disabled = false;
                createBtn.textContent = 'Créer mon chatbot';
            }
        });
        
        // Ajuster automatiquement la hauteur des textareas
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const textareas = document.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                autoResize(textarea);
                textarea.addEventListener('input', function() {
                    autoResize(this);
                });
            });
        });
    </script>
</body>
</html>