<?php
session_name("user_session");
session_start();
if (!isset($_SESSION['user_id'])) {
    // Not logged in, redirect to login page or show error
    header("Location: login.php");
    exit();
}

$initial = '?';

if (isset($_SESSION['user_email'])) {
    $initial = strtoupper(substr($_SESSION['user_email'], 0, 1));
} elseif (isset($_SESSION['email'])) {
    $initial = strtoupper(substr($_SESSION['email'], 0, 1));
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    
      <script>
    function googleTranslateElementInit() {
    new google.translate.TranslateElement({
    pageLanguage: 'en',
    includedLanguages: 'en,sw',
    layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL
  }, 'google_translate_element');
}
</script>


<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f7fa;
      padding-top: 40px;
    }

    .navbar {
      
      background-color: #007bff;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #007bff;
      
      position: fixed;  /* <--- add this */
      top: 0;           /* <--- add this */
      left: 0;
      right: 0;
      z-index: 1000;    /* <--- add this */
    }
    

    .navbar .logo {
      font-size: 22px;
      font-weight: bold;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 15px;
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
      background-color: rgb(255, 255, 255) ;
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
      gap: 35px;
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
  min-height: 140px; /* <-- Add this line */
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

    body.dark-mode .card strong,
    body.dark-mode h2 {
      color: #f0f0f0;
    }

    body.dark-mode .nav-links a,
    body.dark-mode .profile-circle,
    body.dark-mode #modeLabel {
      color: #f0f0f0;
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

.footer {
  background-color:blue;
  color: #ffffff;
  font-family: 'Segoe UI', sans-serif;
  padding: 40px 20px;
}

.footer-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 30px;
}

.footer-column {
  flex: 1 1 250px;
}

.footer-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}

.logo-icon {
  background-color: #ffffff;
  color: #0f3d57;
  font-weight: bold;
  padding: 5px 10px;
  border-radius: 50%;
  font-size: 20px;
}

.footer-column h2 {
  margin: 0;
}

.footer-column h3 {
  margin-bottom: 10px;
  font-size: 18px;
  border-bottom: 1px solid #ffffff30;
  padding-bottom: 5px;
}

.footer-column p,
.footer-column ul,
.footer-column li {
  margin: 5px 0;
}

.footer-column ul {
  list-style: none;
  padding: 0;
}

.footer-column ul li a {
  color: #ffffff;
  text-decoration: none;
}

.footer-column ul li a:hover {
  text-decoration: underline;
}

.footer-bottom {
  width: 100%;
  display: block;
  text-align: center;
  /* your existing styles */
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
/* === Existing styles above === */

/* Hide large Google Translate top banner and skip translate elements */
.goog-te-banner-frame.skiptranslate,
body > .skiptranslate,
iframe.goog-te-banner-frame {
  display: none !important;
  height: 0 !important;
  visibility: hidden !important;
}

body {
  top: 0px !important;
}

/* Hide Google logo and "Powered by" text */
.goog-logo-link,
.goog-te-gadget span {
  display: none !important;
}

/* Style the language dropdown */
.goog-te-combo {
  font-size: 14px !important;
  padding: 5px 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  background-color: white;
  color: #333;
  cursor: pointer;
  margin-left: 8px;
  min-width: 90px; /* optional to keep width consistent */
}

/* Hover effect on dropdown */
.goog-te-combo:hover {
  border-color: #007bff;
}

/* Ensure the translate element container stays on top */
#google_translate_element {
  position: relative;
  z-index: 1000;
}

/* Optional: add icon spacing in navbar */
#google_translate_element_wrapper i {
  margin-right: 6px;
  color: white;
  font-size: 16px;
  user-select: none;
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

/* === Other existing styles below === */

/* Hide large Google Translate top banner and skip translate elements */
.goog-te-banner-frame.skiptranslate,
body > .skiptranslate,
iframe.goog-te-banner-frame {
  display: none !important;
  height: 0 !important;
  visibility: hidden !important;
}

body {
  top: 0px !important;
}

/* Hide Google logo and "Powered by" text */
.goog-logo-link,
.goog-te-gadget span {
  display: none !important;
}

/* Style the language dropdown */
.goog-te-combo {
  font-size: 14px !important;
  padding: 5px 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  background-color: white;
  color: #333;
  cursor: pointer;
  margin-left: 8px;
  min-width: 90px; /* optional to keep width consistent */
}

/* Hover effect on dropdown */
.goog-te-combo:hover {
  border-color: #007bff;
}

/* Ensure the translate element container stays on top */
#google_translate_element {
  position: relative;
  z-index: 1000;
}

/* Optional: add icon spacing in navbar */
#google_translate_element_wrapper i {
  margin-right: 6px;
  color: white;
  font-size: 16px;
  user-select: none;
}

/* === Other existing styles below === */

  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body >
 <nav class="navbar bg-primary py-0 " data-bs-theme="dark">
  <div class="container-fluid d-flex align-items-center justify-content-between">
    <!-- Left: Logo -->
    <span class="navbar-brand mb-0 h1 d-flex align-items-center ps-4">
      <img src="img/jk.png" alt="MASS Logo"
       class="me-2 img-fluid" 
       style="width: 80px; height: 40px;" />
    </span>
    <div id="google_translate_element" style="margin-left:15px;"></div>
    <!-- Right: Notification + Dark Mode + Profile -->
    <div class="d-flex align-items-center gap-3 pe-3">

      <!-- Notification Bell -->
      <a href="notifications.php" title="Notifications" style="position: relative; text-decoration: none;">
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
          <a href="views.php">Your Details</a>
          <a href="javascript:void(0);" onclick="confirmLogout()">Logout</a>
        </div>
      </div>

    </div>
  </div>
</nav>
<section class="bg-dark text-light p-5 text-center text-lg-start">
  <div class="container">
    <div class="row align-items-center">

      <!-- Left Text Content -->
      <div class="col-lg-6 mb-4 mb-lg-0 animate__animated animate__fadeInLeft">
        <h1 class="display-5 fw-bold">Welcome to Your Dashboard</h1>
        <p class="lead">We focus on providing the best appointment scheduling experience for patients and medical professionals.</p>
        <a href="payment_option.php" class="btn btn-primary btn-lg mt-3 animate__animated animate__pulse animate__infinite">
          Book Appointment
        </a>
      </div>

      <!-- Right Image Slideshow -->
      <div class="col-lg-6 animate__animated animate__fadeInRight">
        <div id="dashboardCarousel" class="carousel slide carousel-fade shadow rounded" data-bs-ride="carousel">

          <div class="carousel-inner">
            <div class="carousel-item active">
              <img src="img/4.jpg" class="d-block w-100 rounded" alt="Slide 1">
            </div>
            <div class="carousel-item">
              <img src="img/5.jpg" class="d-block w-100 rounded" alt="Slide 2">
            </div>
            <div class="carousel-item">
              <img src="img/3.jpg" class="d-block w-100 rounded" alt="Slide 3">
            </div>
          </div>
          <!-- Optional controls -->
          <button class="carousel-control-prev" type="button" data-bs-target="#dashboardCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#dashboardCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        </div>
      </div>
      

    </div>
  </div>
</section>
<script>

  //Notification Badge //
document.addEventListener("DOMContentLoaded", function () {
  fetch('get_unread_notifications.php')
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
    fetch("get_unread_notifications.php")
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

<section
    class="position-relative py-5"
    style="
      background-image: url('img/3.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      min-height: 700px;
    "
  >
    <!-- Overlay -->
    <div style="position: absolute; inset: 0; background-color: rgba(0, 0, 0, 0.55); z-index: 1;"></div>



  <div class="container position-relative" style="z-index: 2;">
    <div class="text-center text-light mb-5">
      <h1 class="fw-bold mb-3">Take Charge of Your Health</h1>
      <p class="lead">
        Easily book appointments, track your payments, and update your settings
        — all from one place.
      </p>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4">

      <!-- Book Appointment -->
      <div class="col">
        <a href="payment_option.php" class="text-decoration-none">
          <div
            class="card h-100 shadow-sm border-0 p-4 bg-white bg-opacity-90 text-center rounded-3 hover-shadow"
            style="transition: transform 0.3s ease, box-shadow 0.3s ease;"
          >
            <div class="text-primary fs-1 mb-3"><i class="bi bi-calendar-plus-fill"></i></div>
            <h5 class="card-title text-dark">Book Appointment</h5>
            <p class="card-text text-secondary">
              Easily schedule a new medical appointment with your preferred
              department.
            </p>
          </div>
        </a>
      </div>

      <!-- My Appointments -->
      <div class="col">
        <a href="appointments.php" class="text-decoration-none">
          <div
            class="card h-100 shadow-sm border-0 p-4 bg-white bg-opacity-90 text-center rounded-3 hover-shadow"
            style="transition: transform 0.3s ease, box-shadow 0.3s ease;"
          >
            <div class="text-success fs-1 mb-3"><i class="bi bi-journal-medical"></i></div>
            <h5 class="card-title text-dark">My Appointments</h5>
            <p class="card-text text-secondary">
              View, manage, or cancel your upcoming and past appointments.
            </p>
          </div>
        </a>
      </div>

      <!-- My Payments -->
      <div class="col">
        <a href="payments.php" class="text-decoration-none">
          <div
            class="card h-100 shadow-sm border-0 p-4 bg-white bg-opacity-90 text-center rounded-3 hover-shadow"
            style="transition: transform 0.3s ease, box-shadow 0.3s ease;"
          >
            <div class="text-warning fs-1 mb-3">
              <i class="bi bi-credit-card-2-back-fill"></i>
            </div>
            <h5 class="card-title text-dark">My Payments</h5>
            <p class="card-text text-secondary">
              Access your payment history and manage billing options.
            </p>
          </div>
        </a>
      </div>

      <!-- Settings -->
      <div class="col">
        <a href="update.php" class="text-decoration-none">
          <div
            class="card h-100 shadow-sm border-0 p-4 bg-white bg-opacity-90 text-center rounded-3 hover-shadow"
            style="transition: transform 0.3s ease, box-shadow 0.3s ease;"
          >
            <div class="text-secondary fs-1 mb-3"><i class="bi bi-gear-fill"></i></div>
            <h5 class="card-title text-dark">Settings</h5>
            <p class="card-text text-secondary">
              Update your personal details and preferences.
            </p>
          </div>
        </a>
      </div>

      <!-- Reschedule -->
      <div class="col">
        <a href="reschedule.php" class="text-decoration-none">
          <div
            class="card h-100 shadow-sm border-0 p-4 bg-white bg-opacity-90 text-center rounded-3 hover-shadow"
            style="transition: transform 0.3s ease, box-shadow 0.3s ease;"
          >
            <div class="text-info fs-1 mb-3"><i class="bi bi-arrow-repeat"></i></div>
            <h5 class="card-title text-dark">Reschedule</h5>
            <p class="card-text text-secondary">
              Change the date or time of a previously booked appointment.
            </p>
          </div>
        </a>
      </div>

      <!-- This Week -->
      <div class="col">
        <a href="view_slots.php" class="text-decoration-none">
          <div
            class="card h-100 shadow-sm border-0 p-4 bg-white bg-opacity-90 text-center rounded-3 hover-shadow"
            style="transition: transform 0.3s ease, box-shadow 0.3s ease;"
          >
            <div class="text-primary fs-1 mb-3"><i class="bi bi-clock-history"></i></div>
            <h5 class="card-title text-dark">This Week</h5>
            <p class="card-text text-secondary">
              View available appointment slots for this week.
            </p>
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
        window.location.href = "logout.php";
    }
}
</script>

    <script>
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
        <p><i class="fa fa-map-marker me-2"></i>Mikadi, Kigamboni, Dar es Salaam, Tanzania</p>
        <p><i class="fa fa-phone me-2"></i>0692895572</p>
        <p><i class="fa fa-envelope me-2"></i>www.jkhospital.or.tz</p>
      </div>

      <!-- Quick Links -->
      <div class="col-6 col-md-2 mb-4 mb-md-0">
        <h5 class="fw-semibold mb-3">Quick Links</h5>
        <ul class="list-unstyled">
          <li><a href="about_us.php" class="text-white text-decoration-none">About Us</a></li>
          <li><a href="contact.php" class="text-white text-decoration-none">Contact</a></li>
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
       class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center p-2"
       style="width: 40px; height: 40px;" title="Facebook">
      <i class="fa fa-facebook text-white fs-5"></i>
    </a>

    <a href="https://www.twitter.com/yourhandle" target="_blank" 
       class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center p-2"
       style="width: 40px; height: 40px;" title="Twitter">
      <i class="fa fa-twitter text-white fs-5"></i>
    </a>

    <a href="https://www.instagram.com/j.khospitaltz?igsh=ajN2cGVucmIxMTRk" target="_blank" 
       class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center p-2"
       style="width: 40px; height: 40px;" title="Instagram">
      <i class="fa fa-instagram text-white fs-5"></i>
    </a>

    <a href="https://www.linkedin.com/company/yourcompany" target="_blank" 
       class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center p-2"
       style="width: 40px; height: 40px;" title="LinkedIn">
      <i class="fa fa-linkedin text-white fs-5"></i>
    </a>

  </div>
</div>


    </div>

    <hr class="border-light my-4" />

    <p class="text-center small mb-0">&copy; 2025 JK Hospital. All rights reserved.</p>
  </div>
</footer>



</body>
</html>
