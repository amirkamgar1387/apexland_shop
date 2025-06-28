<?php
// Include database connection
require_once '../conn.php';

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "لطفا نام کاربری را وارد کنید.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "لطفا رمز عبور را وارد کنید.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, username, password, full_name, photo FROM admin_users WHERE username = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);
            $param_username = $username;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                // Check if username exists, if yes then verify password
                if ($stmt->num_rows == 1) {
                    // Bind result variables
                    $stmt->bind_result($id, $username, $db_password, $full_name, $photo);
                    if ($stmt->fetch()) {
                        // !! INSECURE !! Direct password comparison.
                        // This is only for demonstration as requested by the user.
                        if ($password === $db_password) {
                            // Password is correct, so start a new session
                            // session_start(); is already in conn.php

                            // Store data in session variables
                            $_SESSION["admin_loggedin"] = true;
                            $_SESSION["admin_id"] = $id;
                            $_SESSION["admin_username"] = $username;
                            $_SESSION["admin_full_name"] = $full_name;
                            $_SESSION["admin_photo"] = $photo;

                            // Redirect user to dashboard page
                            header("location: dashboard.php");
                            exit();
                        } else {
                            // Password is not valid, display a generic error message
                            $login_err = "نام کاربری یا رمز عبور نامعتبر است.";
                        }
                    }
                } else {
                    // Username doesn't exist, display a generic error message
                    $login_err = "نام کاربری یا رمز عبور نامعتبر است.";
                }
            } else {
                $login_err = "اوه! مشکلی پیش آمد. لطفا بعدا دوباره امتحان کنید.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // If there were any errors, set session message and redirect back to login
    if (!empty($login_err)) {
        $_SESSION['error_message'] = $login_err;
    } elseif (!empty($username_err)) {
        $_SESSION['error_message'] = $username_err;
    } elseif (!empty($password_err)) {
        $_SESSION['error_message'] = $password_err;
    }
    
    header("location: login.php");
    exit();

} else {
    // If the request is not POST, redirect to login page
    header("location: login.php");
    exit();
}
?>