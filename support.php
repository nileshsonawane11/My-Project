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
        -webkit-user-drag: none;
        user-select: none;
        scrollbar-width: none;
    }

    :root {
        --primary-color: rgba(209, 34, 31, 1);
        --primary-light: rgba(209, 34, 31, 0.8);
        --primary-dark: rgba(160, 25, 23, 1);
        --primary-transparent: rgba(209, 34, 31, 0.1);
        --background: #ffffff;
        --text-color: #000000;
        --light-bg: #f8f9fa;
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        --border-radius: 12px;
        --transition: all 0.3s ease;
        --border-color: #dddddd;
    }

    [data-theme="dark"] {
        --background: #121212;
        --text-color: #ffffff;
        --light-bg: #1e1e1e;
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        --border-color: #333333;
    }

    body {
        min-height: 100vh;
        background-color: var(--light-bg);
        color: var(--text-color);
        display: flex;
        justify-content: center;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    
    .container {
        width: 100%;
        max-width: 800px;
        background: var(--background);
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--card-shadow);
        transition: background 0.3s ease, box-shadow 0.3s ease;
    }

    .header {
        background: var(--primary-color);
        padding: 20px;
        color: white;
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
        opacity: 0.8;
    }

    .header-txt {
        display: flex;
        gap: 25px;
        flex-direction: column;
    }
    
    .container2 {
        margin-top: 20px;
        padding: 20px;
        line-height: 27px;
        font-size: 18px;
        color: var(--text-color);
        transition: color 0.3s ease;
    }
    
    .para {
        position: relative;
    }

    .info {
        margin-top: 20px;
    }

    h3 {
        margin-bottom: 10px;
        color: var(--text-color);
        transition: color 0.3s ease;
    }
    
    .support-option {
        background-color: var(--primary-transparent);
        border-radius: var(--border-radius);
        padding: 20px;
        margin-bottom: 15px;
        transition: var(--transition);
        border-left: 4px solid var(--primary-color);
    }
    
    .support-option:hover {
        transform: translateY(-3px);
        box-shadow: var(--card-shadow);
    }
    
    .support-option h4 {
        color: var(--primary-color);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: color 0.3s ease;
    }
    
    .support-option p {
        margin-bottom: 15px;
        color: var(--text-color);
        transition: color 0.3s ease;
    }
    
    .support-btn {
        background: var(--primary-color);
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
        color: white;
    }
    
    .faq-item {
        margin-bottom: 20px;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 15px;
        transition: border-color 0.3s ease;
    }
    
    .faq-question {
        font-weight: 600;
        color: var(--primary-color);
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: color 0.3s ease;
    }
    
    .faq-answer {
        margin-top: 10px;
        display: none;
        color: var(--text-color);
        transition: color 0.3s ease;
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
        color: var(--primary-color);
        transition: color 0.3s ease;
    }
    
    input, textarea, select {
        padding: 12px 15px;
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        font-size: 16px;
        transition: var(--transition);
        background-color: var(--background);
        color: var(--text-color);
    }
    
    input:focus, textarea:focus, select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px var(--primary-transparent);
    }
    
    textarea {
        min-height: 150px;
        resize: vertical;
    }
    
    .submit-btn {
        background: var(--primary-color);
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
        box-shadow: 0 4px 10px rgba(209, 34, 31, 0.3);
        background: var(--primary-dark);
    }
    
    .icon {
        font-size: 20px;
        color: var(--primary-color);
        transition: color 0.3s ease;
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
        background: var(--background);
        padding: 30px;
        border-radius: var(--border-radius);
        max-width: 400px;
        width: 90%;
        text-align: center;
        box-shadow: var(--card-shadow);
        transition: background 0.3s ease;
    }
    
    .dialog-content h3 {
        color: var(--primary-color);
        margin-bottom: 20px;
        transition: color 0.3s ease;
    }
    
    .dialog-content p {
        color: var(--text-color);
        transition: color 0.3s ease;
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
        background: var(--primary-color);
        color: white;
    }
    
    .confirm-btn:hover {
        background: var(--primary-dark);
    }
    
    .cancel-btn {
        background: var(--light-bg);
        color: var(--text-color);
    }
    
    .cancel-btn:hover {
        background: var(--border-color);
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
    fetch('https://formsubmit.co/ajax/livestrike.in@gmail.com', {
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
            background: var(--border-color);
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
  // Disable right-click
  document.addEventListener('contextmenu', event => event.preventDefault());

  // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
  document.onkeydown = function(e) {
    if(e.keyCode == 123) return false; // F12
    if(e.ctrlKey && e.shiftKey && (e.keyCode == 'I'.charCodeAt(0))) return false;
    if(e.ctrlKey && e.shiftKey && (e.keyCode == 'J'.charCodeAt(0))) return false;
    if(e.ctrlKey && (e.keyCode == 'U'.charCodeAt(0))) return false;
  }

  // Theme management for this page
    function initializeTheme() {
        // Check for saved theme preference or use system preference
        const currentTheme = localStorage.getItem('theme') || 
                            (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        
        // Set the initial theme
        if (currentTheme === 'dark') {
            document.body.setAttribute('data-theme', 'dark');
        } else {
            document.body.removeAttribute('data-theme');
        }
        
        // Listen for theme changes from other tabs/pages
        window.addEventListener('storage', function(e) {
            if (e.key === 'theme') {
                if (e.newValue === 'dark') {
                    document.body.setAttribute('data-theme', 'dark');
                } else {
                    document.body.removeAttribute('data-theme');
                }
            }
        });
        
        // Listen for custom events if your dashboard dispatches them
        window.addEventListener('themeChanged', function(e) {
            if (e.detail === 'dark') {
                document.body.setAttribute('data-theme', 'dark');
            } else {
                document.body.removeAttribute('data-theme');
            }
        });
    }

    // Initialize theme when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeTheme();
        
        // FAQ toggle functionality
        const faqQuestions = document.querySelectorAll('.faq-question');
        faqQuestions.forEach(question => {
            question.addEventListener('click', function() {
                const answer = this.nextElementSibling;
                answer.classList.toggle('show');
            });
        });
        
        // Form submission handling
        const contactForm = document.querySelector('.contact-form');
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const confirmationDialog = document.querySelector('.confirmation-dialog');
                if (confirmationDialog) {
                    confirmationDialog.style.display = 'flex';
                }
            });
        }
        
        // Dialog button functionality
        const confirmBtn = document.querySelector('.confirm-btn');
        const cancelBtn = document.querySelector('.cancel-btn');
        const confirmationDialog = document.querySelector('.confirmation-dialog');
        
        if (confirmBtn && confirmationDialog) {
            confirmBtn.addEventListener('click', function() {
                confirmationDialog.style.display = 'none';
                // Here you would typically submit the form data
                alert('Support request submitted successfully!');
            });
        }
        
        if (cancelBtn && confirmationDialog) {
            cancelBtn.addEventListener('click', function() {
                confirmationDialog.style.display = 'none';
            });
        }
    });

    // Function to programmatically change theme if needed
    function setTheme(theme) {
        if (theme === 'dark') {
            document.body.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.body.removeAttribute('data-theme');
            localStorage.setItem('theme', 'light');
        }
    }

    // Function to get current theme
    function getCurrentTheme() {
        return document.body.getAttribute('data-theme') || 'light';
    }

    </script>
</body>
</html>