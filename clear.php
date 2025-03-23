<?php
// Initialisation
session_start();

// Vérifier l'existence du chatbot
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chatbot_id'])) {
    $chatbotId = trim($_POST['chatbot_id']);
    $dataDir = 'data/chatbots/';
    $chatbotFile = $dataDir . $chatbotId . '.json';
    
    if (file_exists($chatbotFile)) {
        $chatbotData = json_decode(file_get_contents($chatbotFile), true);
        
        // Conserver uniquement les messages système et le message d'accueil
        $systemPrompt = null;
        $courseContent = null;
        
        foreach ($chatbotData['messages'] as $message) {
            if ($message['role'] === 'system') {
                $systemPrompt = $message['content'];
                break;
            }
        }
        
        // Réinitialiser les messages
        $chatbotData['messages'] = [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ],
            [
                'role' => 'assistant',
                'content' => "Bonjour ! Je suis votre assistant pour ce cours. Comment puis-je vous aider aujourd'hui ?"
            ]
        ];
        
        // Sauvegarder les modifications
        file_put_contents($chatbotFile, json_encode($chatbotData, JSON_PRETTY_PRINT));
        
        // Réponse
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => "Ce chatbot n'existe pas ou a été supprimé."
        ]);
        exit;
    }
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => "Requête invalide."
    ]);
    exit;
}
?>