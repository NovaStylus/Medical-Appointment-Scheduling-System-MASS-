<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - MASS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background-color: #007bff;
    color: white;
    display: flex;
    justify-content: space-between;
    padding: 5px 10px;
    align-items: center;
    z-index: 1000; /* stays above other content */
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}


        .navbar .logo {
            font-size: 20px;
            font-weight: bold;
        }

        .navbar .nav-links a {
            color: white;
            margin-left: 15px;
            text-decoration: none;
        }

        .navbar .nav-links a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 500;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            margin-top: 5px;
            border: 1px solid #ccc;
            background-color: #fff;
        }

        textarea {
            resize: vertical;
        }

        button {
            margin-top: 15px;
            margin-bottom:10px;
            width: 100%;
            padding: 15px;
            background-color: #007bff;
            color: white;
            border: none;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .success-message, .error-message {
            margin: 20px auto;
            max-width: 500px;
            padding: 15px;
            border-radius: 5px;
            font-size: 15px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }

        .success-message a {
            display: inline-block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }

        .success-message a:hover {
            text-decoration: underline;
        }

        @media (max-width: 500px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
        }
                        /* Dark mode styling */



    </style>
</head>
<body>
    <div style="height: 70px;"></div>

  <nav class="navbar navbar-expand-lg bg-body-primary py-3">
  <div class="container-fluid">
    <div class="me-5">
     <img src="img/jk.png" alt="Bootstrap" width="65" height="34">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    </div>
    
  </div>
</nav>

    <h2 class="my-4">Contact Us</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="success-message">
            Your message has been sent successfully.<br>
            <a href="index.php">Return to Homepage</a>
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="error-message">
            <?php
            switch ($_GET['error']) {
                case 'invalid_email':
                    echo "Please provide a valid email address.";
                    break;
                case 'database_error':
                    echo "There was a problem saving your message. Please try again.";
                    break;
                case 'mail_failed':
                    echo "Message could not be sent. Please try again later.";
                    break;
                default:
                    echo "An error occurred. Please try again.";
            }
            ?>
        </div>
    <?php endif; ?>

    <div class="container">
        <form action="send_message.php" method="POST">
            <label for="name">Your Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Your Email</label>
            <input type="email" id="email" name="email" required>

            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button type="submit">Send Message</button>
        </form>
    </div>
  
</body>
</html>
