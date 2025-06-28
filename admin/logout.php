<?php
// Initialize the session.
// session_start() is called in conn.php but we call it here to be safe
// as this is a standalone script.
session_start();

// Unset all of the session variables.
$_SESSION = array();

// Destroy the session.
session_destroy();

// Redirect to login page
header("location: login.php");
exit;
?>