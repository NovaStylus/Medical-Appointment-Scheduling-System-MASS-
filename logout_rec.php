<?php
session_start();
session_unset();
session_destroy();
header("Location: rec_login.php");
exit();
