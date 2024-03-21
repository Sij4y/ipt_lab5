<?php

// Start the session to manage user data
session_start();

// Include the database connection file
include "db_conn.php";

// Redirect to the login page if the username or password is not provided
if (!isset($_POST['uname']) || !isset($_POST['password'])) {
    header("Location: login.php");
    exit();
}

// Function to validate user input
function validate($data){
    $data = trim($data);        // Remove whitespace from the beginning and end of the input
    $data = stripslashes($data);   // Remove backslashes (\) from the input
    $data = htmlspecialchars($data);   // Convert special characters to HTML entities
    return $data;
}

// Validate the provided username and password
$uname = validate($_POST['uname']);
$pass = validate($_POST['password']);

// Check if the username or password is empty
if (empty($uname) || empty($pass)) {
    header("Location: login.php?error=Username and password are required");
    exit();
}

// SQL query to select user data based on the provided username
$sql = "SELECT * FROM user_profile WHERE username='$uname'";
$result = mysqli_query($conn, $sql);

// Check if the query returned exactly one row (user found)
if (mysqli_num_rows($result) === 1) {
    // Get the user data as an associative array
    $row = mysqli_fetch_assoc($result);
    // Check if the user's email is verified
    if ($row['is_verified'] == 1) {
        // Check if the provided password matches the password in the database
        if ($pass === $row['password']) {
            // Store user data in the session
            $_SESSION["username"] = $row['username'];
            $_SESSION["name"] = $row['name'];
            $_SESSION["id"] = $row['id'];
            // Redirect to the home page
            header("Location: home.php");
            exit();
        } else {
            // Redirect with an error message if the password is incorrect
            header("Location: login.php?error=Incorrect username or password");
            exit();
        }
    } else {
        // Redirect with an error message if the email is not verified
        header("Location: login.php?error=Check your email to verify your account");
        exit();
    }
} else {
    // Redirect with an error message if the user is not found
    header("Location: login.php?error=Incorrect username or password");
    exit();
}
?>
