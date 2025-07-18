<?php
session_name("receptionist_session");
session_start();
if (!isset($_SESSION['receptionist_id'])) {
    // Not logged in, redirect to login page or show error
    header("Location: rec_login.php");
    exit();
}
$initial = '?';
if (isset($_SESSION['email'])) {
    $initial = strtoupper(substr($_SESSION['email'], 0, 1));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reception Dashboard</title>
  <link rel="stylesheet" href="styles.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <!-- Add this to your <head> section -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Xy3oZbLvA3sM9om0gSTX1rBt4ZKXXFkDGo0YoOvLgNO5yxC1uk+PxK2yLRQZsVvDCj2/Fb7nLDbuv6hwLDosNQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f7fa;
    }

    .navbar {
      background-color: #007bff;
      color: white;
      display: flex;
      justify-content: space-between;
      padding: 3px 2px;
      align-items: center;
    }

    .navbar .logo {
      font-size: 22px;
      font-weight: bold;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
    }

    .nav-links a:hover {
      text-decoration: underline;
    }

    .profile-circle {
      width: 29px;
      height: 33px;
      background-color: rgb(255, 255, 255) ;;
      color: #007bff;
      font-weight: bold;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      cursor: pointer;
      text-decoration: none;
      margin-left: auto;
      border: 2px solid #007bff;
      user-select: none;
      transition: background-color 0.3s, color 0.3s;
    }

    .profile-circle:hover {
      background-color: #007bff;
      color: white;
    }

    h2 {
      text-align: center;
      margin: 30px 0 10px;
      color: #333;
    }

    .dashboard-cards {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      max-width: 1000px;
      margin: 0 auto;
      gap: 20px;
      padding: 20px;
    }

    .card-link {
      flex: 1 1 calc(33% - 20px);
      text-decoration: none;
    }

    .card {
      background-color: white;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      text-align: center;
      transition: transform 0.2s;
      border: 1px solid #e0e0e0;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    }

    .card strong {
      display: block;
      margin-top: 8px;
      font-size: 18px;
      color: #333;
    }

    .card-logo {
      font-size: 36px;
    }

    @media (max-width: 768px) {
      .card-link {
        flex: 1 1 100%;
      }
    }

    /* Dark mode support */
    body.dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }

    body.dark-mode .navbar {
      background-color: #1e1e1e;
    }

    body.dark-mode .card {
      background-color: #1f1f1f;
      border: 1px solid #444;
    }

    body.dark-mode .profile-circle {
  background-color: white !important; /* keep white background */
  color: #007bff !important;           /* keep blue text */
  border-color: #007bff !important;    /* keep blue border */
}

body.dark-mode .profile-circle:hover {
  background-color: #007bff !important; /* invert colors on hover */
  color: white !important;
  border-color: #007bff !important;
}



    body.dark-mode .card strong,
   body.dark-mode h2 {
  color: #f0f0f0;
}


    body.dark-mode .nav-links a,
    body.dark-mode .profile-circle,
    body.dark-mode #modeLabel {
      color: #f0f0f0;
    }
body.dark-mode .bg-light {
  background-color: #1e1e1e !important;
}

    .switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 26px;
      margin-left: 15px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      background-color: #ccc;
      border-radius: 34px;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      transition: .4s;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 20px;
      width: 20px;
      border-radius: 50%;
      background-color: white;
      left: 3px;
      bottom: 3px;
      transition: .4s;
    }

    input:checked + .slider {
      background-color: #2196F3;
    }

    input:checked + .slider:before {
      transform: translateX(24px);
    }
    .profile-dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    right: 0; /* align to right edge of profile */
    background-color: white;
    min-width: 140px;
    box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
    border-radius: 6px;
    z-index: 1000;
    margin-top: 8px;
}

.dropdown-content a {
    color: #333;
    padding: 5px 10px;
    text-decoration: none;
    display: block;
    font-size: 14px;
    border-bottom: 1px solid #e0e0e0;
    cursor: pointer;
}

.dropdown-content a:last-child {
    border-bottom: none;
}

.dropdown-content a:hover {
    background-color: #007bff;
 
}

/* Show dropdown when active */
.profile-dropdown.show .dropdown-content {
    display: block;
}
footer {
      text-align: center;
      padding: 49px 10px;
      font-size: 14px;
      color: white;
      border-top: 1px solid #ddd;
     background-color: #007bff;
      margin-top: 0px;
      
      
    }

    body.dark-mode footer {
      color: #bbb;
      border-top: 1px solid #444;
      background-color: #1e1e1e;
    }

    .social-icons i {
  margin: 0 10px;
  cursor: pointer;
  font-size: 18px;
  color: #ffffff;
}

.social-icons i:hover {
  color: #00aced;
}


    body.dark-mode footer {
      color: #bbb;
      border-top: 1px solid #444;
      background-color: #1e1e1e;
    }
    
    body.dark-mode .card {
  background-color: #2c2c2c !important;
  color: #fff !important;
}

body.dark-mode .card .card-title,
body.dark-mode .card .card-text {
  color: #fff !important;
}

body.dark-mode .text-secondary {
  color: #ccc !important;
}
/* Footer dark mode styling */
body.dark-mode footer {
  background-color: #1a1a1a !important;
  color: #f8f9fa !important;
}

body.dark-mode footer a {
  color: #f8f9fa !important;
}

body.dark-mode footer a:hover {
  color: #ffc107 !important; /* Optional highlight on hover */
}

body.dark-mode .btn-primary {
  background-color: #0d6efd; /* Bootstrap primary blue */
  color: white;
  border-color: #0d6efd;
  /* optionally add box shadow or hover */
}

body.dark-mode .btn-primary:hover,
body.dark-mode .btn-primary:focus {
  background-color: #0b5ed7; /* slightly darker on hover */
  border-color: #0a58ca;
  color: white;
}

  </style>
</head>
<body>
 <nav class="navbar bg-primary py-0" data-bs-theme="dark">
  <div class="container-fluid d-flex align-items-center justify-content-between">

    <!-- Left: Logo -->
    <span class="navbar-brand mb-0 h1 d-flex align-items-center ps-4">
      <img src="img/jk.png" alt="MASS Logo"
       class="me-2 img-fluid" 
       style="width: 80px; height: 40px;" />
    </span>
    <!-- Right: Notification + Dark Mode + Profile -->
    <div class="d-flex align-items-center gap-3 pe-3">

      <!-- Notification Bell -->
      <a href="recnotifications.php" title="Notifications" style="position: relative; text-decoration: none;">
        <i class="bi bi-bell-fill fs-4 text-warning position-relative"></i>
        <span id="notificationBadge"
              style="position: absolute; top: -5px; right: -5px; background: red; color: white; 
                     border-radius: 50%; padding: 2px 6px; font-size: 12px; display: none;">
        </span>
      </a>

      <!-- Dark Mode Toggle -->
      <label class="switch m-0" title="Toggle Dark Mode">
        <input type="checkbox" id="darkModeToggle">
        <span class="slider"></span>
      </label>
      <span id="modeLabel" style="color: white; font-size: 14px;">Light Mode</span>

      <!-- Profile -->
      <div class="profile-dropdown" style="position: relative;">
        <a href="javascript:void(0);" class="profile-circle" id="profileBtn" title=""
           style="color: dark blue; text-decoration: none; font-weight: bold; font-size: 1.25rem;">
          <?= $initial ?>
        </a>
        <div class="dropdown-content" id="profileMenu" style="position: absolute; right: 0;">
          <a href="views_rec.php">Your Details</a>
          <a href="javascript:void(0);" onclick="confirmLogout()">Logout</a>
        </div>
      </div>

    </div>
  </div>
</nav>
<script>
document.addEventListener("DOMContentLoaded", function () {
  fetch('get_unread_recnotifications.php')
    .then(response => response.json())
    .then(data => {
      const badge = document.getElementById('notificationBadge');
      if (data.count > 0) {
        badge.innerText = data.count;
        badge.style.display = 'inline-block';
      } else {
        badge.style.display = 'none';
      }
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    fetch("get_unread_recnotifications.php")
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById("notificationBadge");
            if (data.count > 0) {
                badge.innerText = data.count;
                badge.style.display = "inline-block";
            } else {
                badge.style.display = "none";
            }
        })
        .catch(error => console.error("Error loading notifications:", error));
});
</script>
  <section class="py-3 bg-light">
  <div class="container">
    <h2 class="fw-bold text-center mb-5">Welcome, Receptionist</h2>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-2 g-4">

      <!-- Manage Appointments -->
      <div class="col">
        <a href="receptionist.php" class="text-decoration-none">
          <div class="card text-center shadow-sm h-100 p-4 border-0 hover-shadow">
            <div class="fs-1 mb-3 text-primary">
              <i class="bi bi-calendar-check-fill "></i>
            </div>
            <h5 class="card-title text-dark">Manage Appointments</h5>
          </div>
        </a>
      </div>

      <!-- Walk-In Patients -->
      <div class="col">
        <a href="add_walkin.php" class="text-decoration-none">
          <div class="card text-center shadow-sm h-100 p-4 border-0 hover-shadow">
            <div class="fs-1 text-success mb-3 ">
              <i class="bi bi-person-walking"></i>
            </div>
            <h5 class="card-title text-dark">Walk-In Patients</h5>
          </div>
        </a>
      </div>

      <!-- Booking Payments -->
      <div class="col">
        <a href="users_payment.php" class="text-decoration-none" >
          <div class="card text-center shadow-sm h-100 p-4 border-0 hover-shadow">
            <div class="fs-1 mb-3 text-warning">
              <i class="bi bi-cash-coin"></i>
            </div>
            <h5 class="card-title text-dark">Booking Payments</h5>
          </div>
        </a>
      </div>

      <!-- Settings -->
      <div class="col">
        <a href="update_rec.php" class="text-decoration-none">
          <div class="card text-center shadow-sm h-100 p-4 border-0 hover-shadow">
            <div class="fs-1 mb-3 text-secondary">
              <i class="bi bi-gear-fill"></i>
            </div>
            <h5 class="card-title text-dark">Settings</h5>
          </div>
        </a>
      </div>

      <!-- Manage Walk-In Patients -->
     <div class="col-12 col-md-6 col-lg-4 mx-auto">
  <a href="manage_walkins.php" class="text-decoration-none">
    <div class="card text-center shadow-sm h-100 p-4 border-0 hover-shadow">
      <div class="fs-1 mb-3 text-info">
        <i class="bi bi-people-fill"></i>
      </div>
      <h5 class="card-title text-dark">Manage Walk-In Patients</h5>
    </div>
  </a>
</div>

    </div>
  </div>
</section>

<!-- confirmLogout()function -->
  <script>
    function confirmLogout() {
      if (confirm("Are you sure you want to logout?")) {
        window.location.href = "logout_rec.php";
      }
    }

    const toggle = document.getElementById('darkModeToggle');
    const label = document.getElementById('modeLabel');

    document.addEventListener('DOMContentLoaded', function () {
      if (localStorage.getItem('darkMode') === 'true') {
        document.body.classList.add('dark-mode');
        toggle.checked = true;
        label.textContent = 'Dark Mode';
      }
    });

    toggle.addEventListener('change', function () {
      document.body.classList.toggle('dark-mode');
      const mode = document.body.classList.contains('dark-mode');
      localStorage.setItem('darkMode', mode);
      label.textContent = mode ? 'Dark Mode' : 'Light Mode';
    });
  </script>

</script>
<script>document.addEventListener('DOMContentLoaded', function() {
    const profileBtn = document.getElementById('profileBtn');
    const profileMenu = document.getElementById('profileMenu');
    const profileDropdown = profileBtn.parentElement;

    profileBtn.addEventListener('click', function(e) {
        e.preventDefault();
        profileDropdown.classList.toggle('show');
    });

    // Close dropdown if clicking outside
    window.addEventListener('click', function(e) {
        if (!profileDropdown.contains(e.target)) {
            profileDropdown.classList.remove('show');
        }
    });
});
</script>

<!-- Footer -->
<footer class="bg-primary text-white py-5 ">
  <div class="container">
    <div class="row">

      <!-- JK Hospital Info -->
      <div class="col-12 col-md-4 mb-4 mb-md-0">
        <h4 class="fw-bold mb-3">JK Hospital</h4>
        <p><i class="bi bi-geo-alt-fill me-2"></i>Mikadi, Kigamboni, Dar es Salaam, Tanzania</p>
        <p><i class="bi bi-telephone-fill me-2"></i>0678101010</p>
        <p><i class="bi bi-globe me-2"></i>www.jkhospital.or.tz</p>
      </div>

      <!-- Quick Links -->
      <div class="col-6 col-md-2 mb-4 mb-md-0">
        <h5 class="fw-semibold mb-3">Quick Links</h5>
        <ul class="list-unstyled">
          <li><a href="about_us.php" class="text-white text-decoration-none">About Us</a></li>
          
          <li><a href="faq.php" class="text-white text-decoration-none">FAQs</a></li>
        </ul>
      </div>

      <!-- Hours -->
      <div class="col-6 col-md-3 mb-4 mb-md-0">
        <h5 class="fw-semibold mb-3">Hours <i class="fa fa-clock-o ms-1"></i></h5>
        <p><strong>Open 24/7</strong><br>Including weekends and public holidays</p>
      </div>

    <!-- Social Media -->
<div class="col-12 col-md-3">
  <h5 class="fw-semibold mb-3">Follow Us</h5>
  <div class="d-flex gap-3">

    <a href="https://www.facebook.com/yourpage" target="_blank" 
       class=" d-flex align-items-center justify-content-center p-2"
       style="width: 40px; height: 40px;" title="Facebook">
      <i class="bi bi-facebook text-white fs-5"></i>
    </a>

    <a href="https://www.twitter.com/yourhandle" target="_blank" 
       class="d-flex align-items-center justify-content-center p-2"
       style="width: 40px; height: 40px;" title="Twitter">
      <i class="bi bi-twitter text-white fs-5"></i>
    </a>

    <a href="https://www.instagram.com/j.khospitaltz?igsh=ajN2cGVucmIxMTRk" target="_blank" 
       class="d-flex align-items-center justify-content-center p-2"
       style="width: 40px; height: 40px;" title="Instagram">
      <i class="bi bi-instagram text-white fs-5"></i>
    </a>

    <a href="https://www.linkedin.com/company/yourcompany" target="_blank" 
       class="d-flex align-items-center justify-content-center p-2"
       style="width: 40px; height: 40px;" title="LinkedIn">
      <i class="bi bi-linkedin text-white fs-5"></i>
    </a>

  </div>
</div>


    </div>

    <hr class="border-light my-4" />

    <p class="text-center small mb-0">&copy; 2025 JK Hospital. All rights reserved.</p>
  </div>
</footer>

<!-- End of Footer -->

</body>
</html>
