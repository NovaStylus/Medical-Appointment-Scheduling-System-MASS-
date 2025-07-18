<?php
require_once "database.php";

// Fetch all FAQs
$result = $conn->query("SELECT * FROM faqs ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>FAQs</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 40px 20px;
      background: linear-gradient(to right, #f8f9fa, #e9eff5);
      color: #333;
    }
     body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fa;
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
    padding: 15px 30px;
    align-items: center;
    z-index: 1000; /* stays above other content */
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}


        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
        }
    h2 {
      text-align: center;
      color: #007bff;
      font-size: 32px;
      margin-bottom: 30px;
    }
    .faq-container {
      max-width: 800px;
      margin: 0 auto;
    }
    .faq-item {
      background: #fff;
      border-radius: 8px;
      margin-bottom: 15px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
      overflow: hidden;
      transition: all 0.3s ease;
    }
    .question {
      padding: 18px 24px;
      background: #007bff;
      color: #fff;
      cursor: pointer;
      font-weight: 600;
      font-size: 17px;
      position: relative;
    }
    .question::after {
      content: '+';
      position: absolute;
      right: 24px;
      font-size: 20px;
      transition: transform 0.3s ease;
    }
    .faq-item.active .question::after {
      content: '–';
      transform: rotate(180deg);
    }
    .answer {
      padding: 18px 24px;
      background: #f7f9fc;
      display: none;
      font-size: 15px;
      line-height: 1.6;
    }
    .faq-item.active .answer {
      display: block;
    }
    .empty {
      text-align: center;
      color: gray;
      font-style: italic;
      font-size: 18px;
    }

    @media (max-width: 600px) {
      .question {
        font-size: 16px;
      }
      .answer {
        font-size: 14px;
      }
    }
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
  </div>
</nav>
  <h2>Frequently Asked Questions</h2>
  <div class="faq-container">

    <?php
    if ($result->num_rows === 0) {
        echo "<div class='empty'>No FAQs have been added yet.</div>";
    } else {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='faq-item'>";
            echo "<div class='question'>" . htmlspecialchars($row['question']) . "</div>";
            echo "<div class='answer'>" . nl2br(htmlspecialchars($row['answer'])) . "</div>";
            echo "</div>";
        }
    }
    ?>

  </div>

  <script>
    document.querySelectorAll('.faq-item .question').forEach(q => {
      q.addEventListener('click', function () {
        const parent = this.parentElement;

        // Collapse all
        document.querySelectorAll('.faq-item').forEach(item => {
          if (item !== parent) item.classList.remove('active');
        });

        // Toggle this
        parent.classList.toggle('active');
      });
    });
  </script>

</body>
</html>
