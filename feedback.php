<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - LiveStrike</title>
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
            margin-top: 20px;
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
        
        .feedback-form {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        label {
            font-size: 17px;
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
        
        .rating {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .star {
            font-size: 28px;
            color: #ddd;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .star:hover, .star.active {
            color: var(--primary-light);
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
        
        .thank-you {
            text-align: center;
            padding: 40px 20px;
            display: none;
        }
        
        .thank-you h2 {
            color: var(--primary-dark);
            margin-bottom: 15px;
        }
        
        .thank-you p {
            font-size: 16px;
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
                <h1 style="text-align: center; font-size: 1.8rem;">Your Feedback Matters</h1>
                <div style="text-align: center; font-size: 1.1rem;">Help us improve LiveStrike</div>
            </div>
        </div>
        <div class="container2">
            <p>We're committed to making <b>LiveStrike</b> the best sports scoring platform available. Your feedback helps us understand what's working well and where we can improve. Please take a moment to share your thoughts with us.</p>

            <div class="feedback-form" id="feedbackForm">
                <div class="form-group">
                    <label for="name">Your Name (Optional)</label>
                    <input type="text" id="name" placeholder="Enter your name">
                </div>
                
                <div class="form-group">
                    <label for="email">Email (Optional)</label>
                    <input type="email" id="email" placeholder="Enter your email">
                </div>
                
                <div class="form-group">
                    <label for="feedback-type">Type of Feedback</label>
                    <select id="feedback-type">
                        <option value="">Select feedback type</option>
                        <option value="suggestion">Feature Suggestion</option>
                        <option value="bug">Bug Report</option>
                        <option value="praise">Compliment</option>
                        <option value="general">General Feedback</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>How would you rate your experience?</label>
                    <div class="rating">
                        <span class="star" data-rating="1">★</span>
                        <span class="star" data-rating="2">★</span>
                        <span class="star" data-rating="3">★</span>
                        <span class="star" data-rating="4">★</span>
                        <span class="star" data-rating="5">★</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="message">Your Feedback</label>
                    <textarea id="message" placeholder="Tell us what you think about LiveStrike..." required></textarea>
                </div>
                
                <button type="button" class="submit-btn" onclick="submitFeedback()">Submit Feedback</button>
            </div>
            
            <div class="thank-you" id="thankYou">
                <h2>Thank You for Your Feedback!</h2>
                <p>We appreciate you taking the time to help us improve LiveStrike. Our team will review your comments carefully.</p>
                <p style="margin-top: 15px;">Want to help even more? <a href="#" style="color: var(--primary-dark); font-weight: 600;">Join our beta testing program</a></p>
            </div>
        </div>
    </div>
    
    <script>
        function goBack() {
            window.history.back();
        }
        
        // Star rating functionality
        const stars = document.querySelectorAll('.star');
        let currentRating = 0;
        
        stars.forEach(star => {
            star.addEventListener('click', () => {
                const rating = parseInt(star.getAttribute('data-rating'));
                currentRating = rating;
                
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
            
            star.addEventListener('mouseover', () => {
                const rating = parseInt(star.getAttribute('data-rating'));
                
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.style.color = 'var(--primary-light)';
                    }
                });
            });
            
            star.addEventListener('mouseout', () => {
                stars.forEach((s, index) => {
                    if (index >= currentRating) {
                        s.style.color = '#ddd';
                    }
                });
            });
        });
        
        function submitFeedback() {
        const message = document.getElementById('message').value;
        
        if (!message) {
            alert('Please enter your feedback before submitting.');
            return;
        }
        
        // Get form data
        const feedbackData = {
            name: document.getElementById('name').value || 'Anonymous',
            email: document.getElementById('email').value || 'No email provided',
            type: document.getElementById('feedback-type').value || 'General Feedback',
            rating: currentRating || 'Not rated',
            message: message
        };

        // Show loading state
        const submitBtn = document.querySelector('.submit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';

        // Send to FormSubmit.co
        fetch('https://formsubmit.co/ajax/livestrike.in@gmail.com', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                "Feedback Type": feedbackData.type,
                "Rating": feedbackData.rating + ' ★',
                "From": feedbackData.name + ' (' + feedbackData.email + ')',
                "Message": feedbackData.message,
                "_subject": "New Feedback Submission - " + feedbackData.type
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Feedback email sent:', data);
            // Show thank you message
            document.getElementById('feedbackForm').style.display = 'none';
            document.getElementById('thankYou').style.display = 'block';
            document.getElementById('thankYou').scrollIntoView({ behavior: 'smooth' });
        })
        .catch(error => {
            console.error('Error sending feedback:', error);
            alert('Failed to submit feedback. Please try again later.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Feedback';
        });
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
    </script>
</body>
</html>