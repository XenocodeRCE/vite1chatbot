<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vite1Chatbot - Créez et partagez des chatbots pédagogiques</title>
    <link rel="stylesheet" href="styles.css">
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
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        
        header {
            background-color: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo a {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            text-decoration: none;
        }
        
        nav ul {
            display: flex;
            list-style: none;
            gap: 1.5rem;
            align-items: center;
        }
        
        nav a {
            color: var(--gray-600);
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.2s;
        }
        
        nav a:hover {
            color: var(--gray-900);
        }
        
        nav a.active {
            color: var(--primary);
            font-weight: 500;
        }
        
        .nav-button a {
            background-color: var(--primary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .nav-button a:hover {
            background-color: var(--primary-dark);
            color: white;
        }
        
        .hero {
            padding: 4rem 0;
        }
        
        .hero .container {
            display: flex;
            align-items: center;
            gap: 3rem;
        }
        
        .hero-content {
            flex: 1;
        }
        
        .hero h1 {
            font-size: 2.5rem;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: var(--gray-900);
        }
        
        .hero p {
            font-size: 1.125rem;
            color: var(--gray-600);
            margin-bottom: 2rem;
            max-width: 600px;
        }
        
        .hero-buttons {
            display: flex;
            gap: 1rem;
        }
        
        .hero-image {
            flex: 1;
            display: flex;
            justify-content: center;
        }
        
        .hero-image img {
            max-width: 100%;
            height: auto;
            border-radius: 0.75rem;
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
        
        .btn-secondary {
            background-color: white;
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
        }
        
        .btn-secondary:hover {
            background-color: var(--gray-50);
        }
        
        .features {
            padding: 4rem 0;
            background-color: white;
        }
        
        .features h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 3rem;
            color: var(--gray-900);
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .feature-card {
            background-color: white;
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .feature-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .feature-card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            color: var(--gray-900);
        }
        
        .feature-card p {
            color: var(--gray-600);
            line-height: 1.6;
        }
        
        .about {
            padding: 4rem 0;
            background-color: var(--gray-50);
        }
        
        .about h2 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: var(--gray-900);
        }
        
        .about p {
            color: var(--gray-700);
            margin-bottom: 1rem;
            max-width: 800px;
            line-height: 1.6;
        }
        
        .load-chatbot {
            padding: 4rem 0;
            background-color: white;
        }
        
        .load-card {
            max-width: 500px;
            margin: 0 auto;
            background-color: white;
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .load-card h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--gray-900);
        }
        
        .load-card p {
            color: var(--gray-600);
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--gray-700);
        }
        
        input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            font-size: 0.95rem;
            transition: border-color 0.2s;
        }
        
        input[type="text"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        footer {
            padding: 2rem 0;
            background-color: var(--gray-800);
            color: var(--gray-300);
            text-align: center;
        }
        
        /* Styles spécifiques pour le tutoriel */
        .tutorial-section {
            background-color: white;
            padding: 3rem 0;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .tutorial-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .tutorial-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .tutorial-header h2 {
            font-size: 2rem;
            color: var(--gray-900);
            margin-bottom: 1rem;
        }
        
        .tutorial-header p {
            font-size: 1.125rem;
            color: var(--gray-600);
            max-width: 700px;
            margin: 0 auto;
        }
        
        .tutorial-steps {
            display: flex;
            flex-direction: column;
            gap: 3rem;
        }
        
        .tutorial-step {
            display: flex;
            gap: 2rem;
            align-items: flex-start;
        }
        
        .step-number {
            background-color: var(--primary-light);
            color: var(--primary);
            font-weight: 700;
            font-size: 1.5rem;
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-shrink: 0;
        }
        
        .step-content {
            flex: 1;
        }
        
        .step-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.75rem;
        }
        
        .step-description {
            color: var(--gray-700);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .step-image-container {
            position: relative;
            margin-bottom: 1.5rem;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
        }
        
        .step-image-container:hover {
            transform: translateY(-5px);
        }
        
        .step-image {
            width: 100%;
            display: block;
        }
        
        .highlight-area {
            position: absolute;
            background-color: rgba(59, 130, 246, 0.2);
            border: 2px solid var(--primary);
            border-radius: 5px;
            pointer-events: none;
            animation: pulse 2s infinite;
            z-index: 10;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 0.7;
            }
            50% {
                opacity: 0.3;
            }
        }
        
        .step-note {
            background-color: var(--primary-light);
            padding: 1rem;
            border-radius: 0.5rem;
            color: var(--primary-dark);
            font-size: 0.9rem;
            margin-top: 1rem;
        }
        
        .step-note strong {
            font-weight: 600;
        }
        
        .step-note svg {
            vertical-align: middle;
            margin-right: 0.25rem;
        }
        
        .step-actions {
            margin-top: 1rem;
        }
        
        .tutorial-conclusion {
            text-align: center;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid var(--gray-200);
        }
        
        .tutorial-conclusion h3 {
            font-size: 1.5rem;
            color: var(--gray-900);
            margin-bottom: 1rem;
        }
        
        .tutorial-conclusion p {
            color: var(--gray-600);
            margin-bottom: 1.5rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        @media (max-width: 768px) {
            .tutorial-step {
                flex-direction: column;
            }
            
            .step-number {
                margin-bottom: 1rem;
            }
            
            .hero .container {
                flex-direction: column-reverse;
                gap: 2rem;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="index.php">Vite1Chatbot</a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php" class="active">Accueil</a></li>
                    <li><a href="#tutoriel">Tutoriel</a></li>
                    <li><a href="#apropos">À propos</a></li>
                    <li class="nav-button"><a href="creer.php">Créer un chatbot</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Créez et partagez des chatbots pédagogiques</h1>
                <p>Transformez votre contenu de cours en assistants IA personnalisés pour mieux accompagner vos étudiants, en quelques minutes et sans compétences techniques.</p>
                <div class="hero-buttons">
                    <a href="creer.php" class="btn btn-primary">Créer un chatbot</a>
                    <a href="#tutoriel" class="btn btn-secondary">Voir le tutoriel</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxjaXJjbGUgY3g9IjEyIiBjeT0iMTIiIHI9IjEwIiBzdHJva2U9IiMzQjgyRjYiIHN0cm9rZS13aWR0aD0iMiIgZmlsbD0iI0RCRUFGRSIvPjxwYXRoIGQ9Ik05IDExLjVDOSAxMC4xMiAxMC4xMiA5IDExLjUgOUgxMi41QzEzLjg4IDkgMTUgMTAuMTIgMTUgMTEuNVYxMS41QzE1IDEyLjg4IDEzLjg4IDE0IDEyLjUgMTRIMTEuNSIgc3Ryb2tlPSIjMkU1OUMyIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPjxwYXRoIGQ9Ik0xMSAxN0gxMi4wMSIgc3Ryb2tlPSIjMkU1OUMyIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPjwvc3ZnPg==" alt="Vite1Chatbot illustration">
            </div>
        </div>
    </section>

        <section class="features">
            <div class="container">
                <h2>Pourquoi utiliser Vite1Chatbot ?</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🚀</div>
                        <h3>Simple et rapide</h3>
                        <p>Créez un chatbot personnalisé en quelques minutes, sans connaissances techniques.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🧠</div>
                        <h3>IA avancée</h3>
                        <p>Utilise l'API groQ et le modèle Llama 3.1 8B Instant pour des réponses précises et rapides.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🔗</div>
                        <h3>Partage facile</h3>
                        <p>Partagez votre chatbot avec vos étudiants via un simple lien, sans inscription nécessaire.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="tutoriel" class="tutorial-section">
            <div class="tutorial-container">
                <div class="tutorial-header">
                    <h2>Tutoriel pas à pas</h2>
                    <p>Suivez ces étapes simples pour créer votre premier chatbot pédagogique en quelques minutes</p>
                </div>
                
                <div class="tutorial-steps">
                    <!-- Étape 1 : Créer un compte GroQ -->
                    <div class="tutorial-step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3 class="step-title">Créer un compte sur groQ</h3>
                            <p class="step-description">
                                groQ est une plateforme d'IA qui fournit l'intelligence derrière votre chatbot. Commencez par créer un compte gratuit sur leur site.
                            </p>
                            
                            <div class="step-image-container">
                                <img src="./tuto_1.png"  alt="Page de connexion groQ" class="step-image">
                            </div>
                            
                            <ol class="step-description">
                                <li>Visitez <a href="https://console.groq.com/login" target="_blank">console.groq.com/login</a></li>
                                <li>Entrez votre adresse e-mail ou créez un compte avec GitHub ou Google</li>
                                <li>Suivez les instructions pour finaliser votre inscription</li>
                                <li>Une fois connecté, vous accédez au tableau de bord groQ</li>
                            </ol>
                            
                            <div class="step-note">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 8V12M12 16H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <strong>Bon à savoir :</strong> La création d'un compte groQ est gratuite et vous donne accès à leur API.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Étape 2 : Créer une clé API -->
                    <div class="tutorial-step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3 class="step-title">Générer votre clé API sur groQ</h3>
                            <p class="step-description">
                                Une fois connecté à votre compte groQ, vous devez créer une clé API qui permettra à votre chatbot de communiquer avec l'intelligence artificielle.
                            </p>
                            
                            <div class="step-image-container">
                                <img src="./tuto_2.png"  alt="Section API Keys sur groQ" class="step-image">
                                <div class="highlight-area" style="top: 15px; right: 130px; width: 130px; height: 30px;"></div>
                            </div>
                            
                            <ol class="step-description">
                                <li>Dans votre compte groQ, accédez à la section "API Keys" (<a href="https://console.groq.com/keys" target="_blank">console.groq.com/keys</a>)</li>
                                <li>Cliquez sur le bouton <strong>"Create API Key"</strong> en haut à droite</li>
                                <li>Dans la fenêtre qui s'ouvre, donnez un nom à votre clé (par exemple "Vite1Chatbot")</li>
                                <li>Cliquez sur "Submit" pour créer la clé</li>
                            </ol>
                            
                            <div class="step-image-container">
                                <img src="./tuto_2_1.png"  alt="Création d'une clé API sur groQ" class="step-image">
                                <div class="highlight-area" style="top: 95px; left: 36px; width: 428px; height: 40px;"></div>
                            </div>
                            
                            <div class="step-note">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 8V12M12 16H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <strong>IMPORTANT :</strong> Copiez immédiatement votre clé API et conservez-la en lieu sûr. Elle ne sera affichée qu'une seule fois et ne pourra plus être récupérée ensuite !
                            </div>
                        </div>
                    </div>
                    
                    <!-- Étape 3 : Créer votre chatbot -->
                    <div class="tutorial-step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3 class="step-title">Créer votre chatbot sur Vite1Chatbot</h3>
                            <p class="step-description">
                                Maintenant que vous avez votre clé API, revenez sur Vite1Chatbot pour créer votre assistant pédagogique personnalisé.
                            </p>
                            
                            <div class="step-image-container">
                                <img src="./tuto_3.png"  alt="Formulaire de création de chatbot" class="step-image">
                            </div>
                            
                            <ol class="step-description">
                                <li>Cliquez sur le bouton <strong>"Créer un chatbot"</strong> sur notre site</li>
                                <li>Dans le formulaire, collez votre clé API groQ</li>
                                <li>Donnez un nom à votre chatbot (par exemple "Assistant Mathématiques Terminale")</li>
                                <li>Copiez-collez le contenu de votre cours dans le champ prévu à cet effet</li>
                                <li>Personnalisez éventuellement les instructions système (ou conservez celles par défaut)</li>
                                <li>Cliquez sur <strong>"Créer mon chatbot"</strong></li>
                            </ol>
                            
                            <p class="step-description">
                                Pour le contenu du cours, vous pouvez copier-coller des documents Word, des PDF, des présentations PowerPoint ou tout autre texte contenant les informations que votre chatbot devra connaître.
                            </p>
                        </div>
                    </div>
                    
                    <!-- Étape 4 : Tester et partager -->
                    <div class="tutorial-step">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3 class="step-title">Tester et partager votre chatbot</h3>
                            <p class="step-description">
                                Une fois créé, vous serez automatiquement redirigé vers votre chatbot pour le tester. Assurez-vous qu'il répond correctement aux questions sur votre cours.
                            </p>
                            
                            <div class="step-image-container">
                                <img src="./tuto_4.png" alt="Interface du chatbot" class="step-image">
                            </div>
                            
                            <p class="step-description">
                                Pour partager votre chatbot avec vos étudiants, notez ou copiez son identifiant unique. Vous pourrez ensuite envoyer le lien à vos étudiants par e-mail, l'intégrer dans votre ENT ou votre plateforme de cours en ligne.
                            </p>
                            <div class="step-actions">
                                <a href="creer.php" class="btn btn-primary">Créer mon premier chatbot</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="tutorial-conclusion">
                    <h3>Prêt à créer votre assistant pédagogique ?</h3>
                    <p>En quelques minutes seulement, vous pouvez mettre à disposition de vos étudiants un assistant IA personnalisé qui les aidera à mieux comprendre votre cours, à réviser et à répondre à leurs questions, 24h/24 et 7j/7.</p>
                    <a href="creer.php" class="btn btn-primary">Commencer maintenant</a>
                </div>
            </div>
        </section>

        <section id="apropos" class="about">
            <div class="container">
                <h2>À propos de Vite1Chatbot</h2>
                <p>Vite1Chatbot est un outil conçu pour les enseignants qui souhaitent offrir à leurs étudiants un soutien pédagogique accessible 24/7. En transformant le contenu de vos cours en assistants IA personnalisés, vous permettez à vos étudiants de poser des questions et d'obtenir des réponses précises à tout moment.</p>
                <p>Notre solution utilise l'API groQ et le modèle Llama 3.1 8B Instant pour offrir des performances optimales avec des temps de réponse rapides.</p>
            </div>
        </section>

        <section class="load-chatbot">
            <div class="container">
                <div class="load-card">
                    <h2>Charger un chatbot existant</h2>
                    <p>Entrez l'ID de votre chatbot pour y accéder :</p>
                    <form id="loadForm" action="chat.php" method="get">
                        <div class="form-group">
                            <label for="chatbot-id">ID du chatbot</label>
                            <input type="text" id="chatbot-id" name="id" placeholder="Ex: math-2025-03-24" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Charger le chatbot</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Vite1Chatbot - Créez et partagez des assistants pédagogiques en quelques clics</p>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>