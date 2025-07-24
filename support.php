<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - LiveStrike</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            user-select: none;
            scrollbar-width: none;
        }

        :root {
            --primary-light: #FAC01F;
            --primary-dark: #F83900;
            --primary-light-transparent: rgba(250, 192, 31, 0.1);
            --primary-dark-transparent: rgba(248, 57, 0, 0.1);
            --background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
            --text-color: #333333;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        body {
            min-height: 100vh;
            background-color: var(--light-bg);
            color: var(--text-color);
            display: flex;
            justify-content: center;
        }
        .container {
            width: 100%;
            max-width: 800px;
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .header {
            background: var(--background);
            padding: 20px;
            color: white;
            position: fixed;
            height: 220px;
            width: 100%;
            max-width: 800px;
        }

        .return {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .return svg {
            cursor: pointer;
            transition: var(--transition);
            fill: white;
        }

        .return svg:hover {
            transform: translateX(-3px);
        }

        .header-txt {
            display: flex;
            gap: 25px;
            flex-direction: column;
        }
        .container2 {
            margin-top: 230px;
            padding: 20px;
            line-height: 27px;
            font-size: 18px;
        }
        .para {
            position: relative;
        }

        .info {
            margin-top: 20px;
        }

        h3 {
            margin-bottom: 10px;
        }
        
        .support-option {
            background-color: var(--primary-light-transparent);
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 15px;
            transition: var(--transition);
            border-left: 4px solid var(--primary-dark);
        }
        
        .support-option:hover {
            transform: translateY(-3px);
            box-shadow: var(--card-shadow);
        }
        
        .support-option h4 {
            color: var(--primary-dark);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .support-option p {
            margin-bottom: 15px;
        }
        
        .support-btn {
            background: var(--primary-dark);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-block;
        }
        
        .support-btn:hover {
            background: var(--primary-light);
            color: var(--text-color);
        }
        
        .faq-item {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        
        .faq-question {
            font-weight: 600;
            color: var(--primary-dark);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .faq-answer {
            margin-top: 10px;
            display: none;
        }
        
        .faq-answer.show {
            display: block;
        }
        
        .contact-form {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        label {
            font-weight: 600;
            color: var(--primary-dark);
        }
        
        input, textarea, select {
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 16px;
            transition: var(--transition);
        }
        
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: var(--primary-dark);
            box-shadow: 0 0 0 2px var(--primary-dark-transparent);
        }
        
        textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .submit-btn {
            background: var(--background);
            color: white;
            border: none;
            padding: 15px;
            border-radius: var(--border-radius);
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(248, 57, 0, 0.3);
        }
        
        .icon {
            font-size: 20px;
        }
        
        /* Confirmation Dialog Styles */
        .confirmation-dialog {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            display: none;
        }
        
        .dialog-content {
            background: white;
            padding: 30px;
            border-radius: var(--border-radius);
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: var(--card-shadow);
        }
        
        .dialog-content h3 {
            color: var(--primary-dark);
            margin-bottom: 20px;
        }
        
        .dialog-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        
        .dialog-btn {
            padding: 10px 20px;
            border-radius: var(--border-radius);
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .confirm-btn {
            background: var(--primary-dark);
            color: white;
        }
        
        .confirm-btn:hover {
            background: var(--primary-light);
        }
        
        .cancel-btn {
            background: #f0f0f0;
            color: var(--text-color);
        }
        
        .cancel-btn:hover {
            background: #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="return">
                <svg onclick="goBack()" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z"/>
                </svg>
            </div>
            <div class="header-txt">
                <h1 style="text-align: center; font-size: 1.8rem;">LiveStrike Support</h1>
                <div style="text-align: center; font-size: 1.1rem;">We're here to help you</div>
            </div>
        </div>
        <div class="container2">
            <p>Welcome to <b>LiveStrike Support</b>. Whether you're experiencing technical issues, have questions about features, or need help with your account, we're ready to assist you. Choose from the options below to get started.</p>

            <!-- [Previous content remains the same until the contact form] -->
            
            <div class="info">
                <h3>Contact Our Support Team</h3>
                <p>Can't find what you're looking for? Send us a message and we'll get back to you as soon as possible.</p>
                
                <form class="contact-form" id="supportForm">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" placeholder="Enter your name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <select id="subject" required>
                            <option value="">Select a subject</option>
                            <option value="technical">Technical Issue</option>
                            <option value="account">Account Help</option>
                            <option value="billing">Billing Question</option>
                            <option value="feature">Feature Request</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Your Message</label>
                        <textarea id="message" placeholder="Describe your issue or question in detail..." required></textarea>
                    </div>
                    
                    <button type="button" class="submit-btn" onclick="validateForm()">Send Message</button>
                </form>
            </div>
            
            <!-- [Rest of the previous content remains the same] -->
            
        </div>
    </div>
    
    <!-- Confirmation Dialog -->
    <div class="confirmation-dialog" id="confirmationDialog">
        <div class="dialog-content">
            <h3>Confirm Submission</h3>
            <p>Are you sure you want to send this message to our support team?</p>
            <div class="dialog-buttons">
                <button class="dialog-btn cancel-btn" onclick="cancelSubmission()">Cancel</button>
                <button class="dialog-btn confirm-btn" onclick="submitSupportRequest()">Yes, Send It</button>
            </div>
        </div>
    </div>
    
    <script>
        function goBack() {
            window.history.back();
        }
        
        function toggleFAQ(element) {
            const answer = element.nextElementSibling;
            const toggle = element.querySelector('span');
            
            if (answer.classList.contains('show')) {
                answer.classList.remove('show');
                toggle.textContent = '+';
            } else {
                answer.classList.add('show');
                toggle.textContent = '-';
            }
        }
        
        function validateForm() {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;
            
            if (!name || !email || !subject || !message) {
                alert('Please fill in all required fields.');
                return;
            }
            
            // Show confirmation dialog
            document.getElementById('confirmationDialog').style.display = 'flex';
        }
        
        function cancelSubmission() {
            // Hide confirmation dialog
            document.getElementById('confirmationDialog').style.display = 'none';
        }
        
        function submitSupportRequest() {
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const subject = document.getElementById('subject').value;
    const message = document.getElementById('message').value;

    // Hide confirmation dialog
    document.getElementById('confirmationDialog').style.display = 'none';

    // Show loading state
    const submitBtn = document.querySelector('.submit-btn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Sending...';

    // Send data to FormSubmit.co
    fetch('https://formsubmit.co/ajax/patilkaustubh2811@gmail.com', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            name: name,
            email: email,
            subject: subject,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Success:', data);
        showSuccessMessage();
        document.getElementById('supportForm').reset();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to send message. Please try again later.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Send Message';
    });
}

function showSuccessMessage() {
    // Create a styled success message (better than alert)
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.innerHTML = `
        <div class="success-content">
            <h3>Thank You!</h3>
            <p>Your message has been sent successfully.</p>
            <p>We'll respond to ${document.getElementById('email').value} soon.</p>
            <button onclick="this.parentElement.parentElement.remove()">OK</button>
        </div>
    `;
    
    // Add styles
    const style = document.createElement('style');
    style.textContent = `
        .success-message {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .success-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 400px;
            text-align: center;
        }
        .success-content button {
            background: #F83900;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            margin-top: 20px;
            cursor: pointer;
        }
    `;
    document.head.appendChild(style);
    document.body.appendChild(successDiv);
}
    </script>
</body>
</html>