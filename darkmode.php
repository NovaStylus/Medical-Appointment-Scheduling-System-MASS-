<?php
// Start session if not started (optional if done elsewhere)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Dark mode CSS -->
<style>
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
/* Add other dark mode CSS from your main style here */
body.dark-mode .container {
  background-color: #1e1e1e;
  color: #f0f0f0;
}
body.dark-mode .notification {
  background-color: #1e1e1e;
  color: #f0f0f0;
}


body.dark-mode input[type="text"],
body.dark-mode input[type="email"],
body.dark-mode input[type="tel"] {
  background-color: #2a2a2a;
  color: #f0f0f0;
  border: 1px solid #555;
}


body.dark-mode button {
  background-color: #333;
  color: #f0f0f0;
}
body.dark-mode th, td {
  background-color: #333;
  color: #f0f0f0;
}

body.dark-mode button:hover {
  background-color: #444;
}

body.dark-mode .settings-options a {
  background-color: #333;
  color: #f0f0f0;
}

body.dark-mode .settings-options a:hover {
  background-color: #444;
}

body.dark-mode .message {
  background-color: #2e2e2e;
  color: #f0f0f0;
  border: 1px solid #555;
}
body.dark-mode .about-container {
  background-color: #2e2e2e;
  color: #f0f0f0;
  border: 1px solid #555;
}
 body.dark-mode .faq-container,body.dark-mode .faq-item,body.dark-mode .question,body.dark-mode .answer  {
  background-color: #2e2e2e;
  color: #f0f0f0;
  border: 1px solid #555;
}

</style>

<!-- Dark mode toggle JS -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const toggle = document.getElementById('darkModeToggle');
  const label = document.getElementById('modeLabel');

  if (localStorage.getItem('darkMode') === 'true') {
    document.body.classList.add('dark-mode');
    if (toggle) toggle.checked = true;
    if (label) label.textContent = 'Dark Mode';
  }

  if (toggle) {
    toggle.addEventListener('change', function () {
      document.body.classList.toggle('dark-mode');
      const mode = document.body.classList.contains('dark-mode');
      localStorage.setItem('darkMode', mode);
      if (label) label.textContent = mode ? 'Dark Mode' : 'Light Mode';
    });
  }
});
</script>
