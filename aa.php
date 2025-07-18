  <nav class="navbar">
        <div class="logo">+ MASS Admin</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
        </div>
    </nav>
        body { font-family: sans-serif; background: #f0f0f0; padding: 20px; 
         margin: 0;
            padding: 0;}
        
.nav-links {
    display: flex;
    align-items: center;
    gap: 15px; /* spacing between links */
}


        .navbar {
            background-color: #007bff;
            color: white;
            display: flex;
            justify-content: space-between;
            padding: 15px 30px;
            align-items: center;
        }

        .navbar .logo {
            font-size: 22px;
            font-weight: bold;
        }

        .navbar .nav-links a {
            color: white;
            margin-left: 15px;
            text-decoration: none;
        }

        .navbar .nav-links a:hover {
            text-decoration: underline;
        