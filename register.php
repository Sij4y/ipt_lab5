<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Registration Page</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition register-page">
<div class="register-box">
  <div class="register-logo">
    <a href="index2.php"><b>Admin</b>LTE</a>
  </div>

  <div class="card">
    <div class="card-body register-card-body">
      <p class="login-box-msg">Register a new account</p>

      <?php
        if (isset($_POST["submit"])) {
           $fullName = $_POST["fullname"];
           $email = $_POST["email"];
           $password = $_POST["password"];
           $phoneNumber = $_POST["phonenumber"];
           $address = $_POST["address"];

           $passwordHash = password_hash($password, PASSWORD_DEFAULT);

           $errors = array();
           
           if (empty($fullName) OR empty($email) OR empty($password) OR empty($phoneNumber) OR empty($address)) {
            array_push($errors,"All fields are required");
           }
           if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Email is not valid");
           }
           if (strlen($password)<8) {
            array_push($errors,"Password must be at least 8 characters long");
           }
           if (!preg_match("/^\d{11}$/", $phoneNumber)) { 
            array_push($errors, "Phone number must be 11 digits");
           }
        
           require_once "db_conn.php";
           $sql = "SELECT * FROM user_profile WHERE email = ?";
           $stmt = mysqli_stmt_init($conn);
           mysqli_stmt_prepare($stmt, $sql);
           mysqli_stmt_bind_param($stmt, "s", $email);
           mysqli_stmt_execute($stmt);
           $result = mysqli_stmt_get_result($stmt);
           $rowCount = mysqli_num_rows($result);
           if ($rowCount>0) {
            array_push($errors,"Email already exists!");
           }
           if (count($errors)>0) {
            foreach ($errors as  $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
           }  else {
            $sql = "INSERT INTO user_profile (full_name, email, password, phone_number, address) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);
            mysqli_stmt_prepare($stmt, $sql);
            mysqli_stmt_bind_param($stmt, "sssss", $fullName, $email, $passwordHash, $phoneNumber, $address);
            mysqli_stmt_execute($stmt);
            echo "<div class='alert alert-success'>You are registered successfully.</div>";

            // Send verification email
            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'cjmarquez6000@gmail.com';
                $mail->Password   = 'wjaw cacm gjpy tjhq';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
            
                // Recipients
                $mail->setFrom('cjmarquez6000@gmail.com', 'MarquezLAB_5');
                $mail->addAddress($email);
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Email Verification';
                $mail->Body    = "Thanks for registration! Click the following link to verify your email address:<br><a href='http://localhost/ipt_lab4/login.php?email=$email&v_code=$v_code'>Verify Email</a>";
                
                // Send the email
                $mail->send();
            } catch (Exception $e) {
                // Handle SMTP errors
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

            // Redirect to the login page after successful registration
            header("Location: login.php");
            exit(); // Ensure that the script stops executing after redirection
        }
        
        }
      ?>

      <form action="register.php" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="fullname" placeholder="Full Name" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="email" class="form-control" name="email" placeholder="Email" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" placeholder="Password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="phonenumber" placeholder="Phone Number" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-phone"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="address" placeholder="Address" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-map-marker-alt"></span>
            </div>
          </div>
          <div class="row">
    <div class="col-8">
        <div class="icheck-primary">
            <input type="checkbox" id="agreeTerms" name="terms" value="agree">
            <label for="agreeTerms">
                I agree to the terms and policy
            </label>
        </div>
    </div>
    <div class="col-4 text-center">
        <button type="submit" class="btn btn-primary btn-block" name="submit">Register</button>
    </div>
</div>
<div class="social-auth-links text-center">
        <p>- OR -</p>
        <a href="#" class="btn btn-block btn-primary">
          <i class="fab fa-facebook mr-2"></i>
          Sign up using Facebook
        </a>
        <a href="#" class="btn btn-block btn-danger">
          <i class="fab fa-google-plus mr-2"></i>
          Sign up using Gmail
        </a>
      </div>

      <a href="login.php" class="text-center">I already have a account</a>