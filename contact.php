<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact IT Support | Zeal College</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --harvard-crimson: #A51C30;
            --harvard-dark: #1E1E1E;
            --harvard-cream: #FAF9F6;
            --harvard-gray: #4A4A4A;
            --font-serif: 'Lora', Georgia, serif;
            --font-sans: 'Inter', Helvetica, Arial, sans-serif;
            --zeal-blue: #0A3663;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: var(--font-sans);
            background-color: var(--harvard-cream);
            color: var(--harvard-dark);
            line-height: 1.8;
            font-size: 1.05rem;
        }
        
        header {
            background-color: var(--harvard-dark);
            color: #fff;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 5px solid var(--harvard-crimson);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-brand {
            font-family: var(--font-serif);
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .header-brand a {
            color: #fff;
            text-decoration: none;
        }
        
        .back-link {
            color: #fff;
            text-decoration: none;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .back-link:hover {
            color: var(--harvard-crimson);
        }
        
        .hero {
            padding: 120px 40px;
            text-align: center;
            background: linear-gradient(rgba(10,54,99,0.85), rgba(10,54,99,0.85)), url('https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;
            color: #fff;
            border-bottom: 8px solid var(--harvard-crimson);
        }
        
        .hero-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        .hero h1 {
            font-family: var(--font-serif);
            font-size: 4rem;
            margin: 0 0 20px 0;
            font-weight: 700;
        }
        
        .hero p {
            font-size: 1.4rem;
            font-family: var(--font-serif);
            font-style: italic;
            max-width: 800px;
            margin: 0 auto;
            color: #E0E0E0;
        }
        
        .content-container {
            max-width: 900px;
            margin: 80px auto;
            padding: 0 40px;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 80px;
        }

        .contact-info {
            background: #fff;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            border-top: 4px solid var(--zeal-blue);
        }

        .contact-info h3 {
            font-family: var(--font-serif);
            color: var(--zeal-blue);
            font-size: 1.8rem;
            margin-top: 0;
            border-bottom: 1px solid #E0E0E0;
            padding-bottom: 15px;
        }

        .contact-info p {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .contact-form {
            background: #fff;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            border-top: 4px solid var(--harvard-crimson);
        }

        .contact-form h3 {
            font-family: var(--font-serif);
            color: var(--harvard-crimson);
            font-size: 1.8rem;
            margin-top: 0;
            border-bottom: 1px solid #E0E0E0;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--harvard-dark);
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: var(--font-sans);
            font-size: 1rem;
            box-sizing: border-box;
        }

        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--zeal-blue);
            box-shadow: 0 0 0 3px rgba(10,54,99,0.1);
        }

        .btn-submit {
            background-color: var(--zeal-blue);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1rem;
            font-weight: 600;
            font-family: var(--font-sans);
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 4px;
        }

        .btn-submit:hover {
            background-color: #08294a;
        }

        /* Chatbot Widget Styles */
        .chatbot-toggle {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: var(--harvard-crimson);
            color: white;
            width: 65px;
            height: 65px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.8rem;
            cursor: pointer;
            box-shadow: 0 5px 25px rgba(165, 28, 48, 0.4);
            transition: transform 0.3s, background 0.3s;
            z-index: 1000;
        }

        .chatbot-toggle:hover {
            transform: scale(1.1);
            background-color: #8c1628;
        }

        .chatbot-window {
            position: fixed;
            bottom: 110px;
            right: 30px;
            width: 350px;
            height: 500px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transform: translateY(20px);
            transition: opacity 0.3s, transform 0.3s;
        }

        .chatbot-window.open {
            opacity: 1;
            pointer-events: all;
            transform: translateY(0);
        }

        .chat-header {
            background: var(--harvard-dark);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header h4 {
            margin: 0;
            font-family: var(--font-serif);
            font-size: 1.2rem;
            font-weight: 500;
        }
        
        .chat-close {
            cursor: pointer;
            font-size: 1.5rem;
            opacity: 0.7;
            transition: opacity 0.2s;
        }
        
        .chat-close:hover {
            opacity: 1;
        }

        .chat-body {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #F9F9F9;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .chat-message {
            max-width: 80%;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.95rem;
            line-height: 1.5;
            word-wrap: break-word;
        }

        .chat-message.bot {
            background: #fff;
            align-self: flex-start;
            border: 1px solid #E0E0E0;
            color: var(--harvard-dark);
            border-bottom-left-radius: 0;
        }

        .chat-message.user {
            background: var(--zeal-blue);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 0;
        }

        .chat-footer {
            padding: 15px;
            background: #fff;
            border-top: 1px solid #E0E0E0;
            display: flex;
            gap: 10px;
        }

        .chat-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 20px;
            font-size: 0.95rem;
            font-family: var(--font-sans);
        }
        
        .chat-input:focus {
            outline: none;
            border-color: var(--zeal-blue);
        }

        .chat-send {
            background: var(--harvard-crimson);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: background 0.3s;
        }

        .chat-send:hover {
            background: #8c1628;
        }
        
        footer {
            background: var(--harvard-dark);
            color: #fff;
            text-align: center;
            padding: 60px 40px;
            font-family: var(--font-serif);
            border-top: 5px solid var(--harvard-crimson);
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-brand"><a href="index.php">Zeal College</a></div>
        <a href="index.php" class="back-link">← Return to Home</a>
    </header>
    
    <section class="hero">
        <div class="hero-icon">🎧</div>
        <h1>IT Help Desk</h1>
        <p>"Assisting faculty and students with technical excellence 24/7."</p>
    </section>
    
    <div class="content-container">
        <div class="contact-grid">
            <div class="contact-info">
                <h3>Contact Details</h3>
                <p>📍 <strong>Address:</strong> Zeal Education Society, Narhe, Pune - 411041</p>
                <p>📞 <strong>Helpline:</strong> +91 800-123-4567</p>
                <p>📧 <strong>Email:</strong> support.zcoer@zealedu.in</p>
                <br>
                <h3>Office Hours</h3>
                <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
                <p>Saturday: 9:00 AM - 1:00 PM</p>
                <p>Sunday: Closed</p>
                <br>
                <p style="font-size: 0.9rem; color: var(--harvard-gray);">For urgent portal access issues, please use the chatbot in the bottom right corner or submit a high-priority ticket via the form.</p>
            </div>
            
            <div class="contact-form">
                <h3>Submit a Ticket</h3>
                <form id="supportForm">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" required placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label for="email">College Email ID</label>
                        <input type="email" id="email" required placeholder="john.doe@zealedu.in">
                    </div>
                    <div class="form-group">
                        <label for="department">Department</label>
                        <input type="text" id="department" required placeholder="Computer Science">
                    </div>
                    <div class="form-group">
                        <label for="message">Issue Description</label>
                        <textarea id="message" rows="5" required placeholder="Please describe your technical issue..."></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Submit Ticket</button>
                </form>
            </div>
        </div>
        
        <h2 style="font-family: var(--font-serif); font-size: 2.2rem; color: var(--harvard-crimson); border-bottom: 2px solid var(--harvard-crimson); padding-bottom: 10px;">IT Support Guidelines</h2>
        <p>Before submitting a ticket, please ensure you have checked the standard <strong>Evaluation Guidelines</strong> and <strong>Department Policies</strong>. Many common issues regarding CIE portal access, marks visibility, and assignment uploads are resolved by consulting the standard documentation.</p>
        <p>Our response time for standard tickets is 24-48 hours. Please do not submit duplicate tickets for the same issue, as this will only delay the resolution process.</p>
        <br>
    </div>
    
    <footer>
        <p>&copy; 2026 Zeal College of Engineering & Research. Academic Integrity First.</p>
    </footer>

    <!-- Chatbot Widget -->
    <div class="chatbot-toggle" id="chatToggle">
        💬
    </div>

    <div class="chatbot-window" id="chatWindow">
        <div class="chat-header">
            <h4>Zeal IT Assistant</h4>
            <div class="chat-close" id="chatClose">×</div>
        </div>
        <div class="chat-body" id="chatBody">
            <div class="chat-message bot">
                Hello! I am the automated Zeal IT Assistant. How can I help you today?
            </div>
        </div>
        <div class="chat-footer">
            <input type="text" class="chat-input" id="chatInput" placeholder="Type your message..." autocomplete="off">
            <button class="chat-send" id="chatSend">➤</button>
        </div>
    </div>

    <script>
        // Form submission simulation
        document.getElementById('supportForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Your support ticket has been submitted successfully! We will get back to you within 24 hours.');
            this.reset();
        });

        // Chatbot Logic
        const chatToggle = document.getElementById('chatToggle');
        const chatWindow = document.getElementById('chatWindow');
        const chatClose = document.getElementById('chatClose');
        const chatInput = document.getElementById('chatInput');
        const chatSend = document.getElementById('chatSend');
        const chatBody = document.getElementById('chatBody');

        chatToggle.addEventListener('click', () => {
            chatWindow.classList.add('open');
        });

        chatClose.addEventListener('click', () => {
            chatWindow.classList.remove('open');
        });

        function addMessage(text, sender) {
            const msgDiv = document.createElement('div');
            msgDiv.classList.add('chat-message', sender);
            msgDiv.innerText = text;
            chatBody.appendChild(msgDiv);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function handleSend() {
            const text = chatInput.value.trim();
            if (text === '') return;
            
            addMessage(text, 'user');
            chatInput.value = '';

            // Simulate bot typing delay
            setTimeout(() => {
                let reply = "Our support agents are currently busy. Please submit a ticket using the form on this page, or email us at support.zcoer@zealedu.in.";
                
                const lowerText = text.toLowerCase();
                if (lowerText.includes('password') || lowerText.includes('login')) {
                    reply = "For password resets, please use the 'Forgot Password' link on the main portal login page. If you are still locked out, submit a ticket with your Student ID.";
                } else if (lowerText.includes('upload') || lowerText.includes('assignment')) {
                    reply = "If you are having trouble uploading assignments, please ensure the file is under 10MB and in PDF format. Late submissions require HOD approval.";
                } else if (lowerText.includes('hi') || lowerText.includes('hello')) {
                    reply = "Hello! Please describe your technical issue in detail.";
                }
                
                addMessage(reply, 'bot');
            }, 1000);
        }

        chatSend.addEventListener('click', handleSend);
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                handleSend();
            }
        });
    </script>
</body>
</html>
