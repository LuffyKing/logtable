<?php
session_start();
require 'validator.php';
require 'cleanInput.php';
if(isset($_POST['email']) && isset($_POST['password'])) {
  $email = cleanInput($_POST['email']);
  $password = cleanInput($_POST['password']);
  var_dump($username, $password);
  $errors = validateInputs(array('email' => $email, 'password' => $password), array());
  if (count($errors) === 0) {
    // $servername = "localhost";
    // $username = "root";
    // $dbName = "helloDb";
    //
    // $conn = new mysqli($servername, $username,'',$dbName);
    //
    // if ($conn->connect_error) {
    //     die("Connection failed: " . $conn->connect_error);
    // }
    define('PASSWORD_HASH', '$2y$10$ipR36jyubBl26skZuCfl9uVBKZ0Fp5tZBctqxoSVTubf/BmxTPpm6');
    define('EMAILCONST', 'aderinwale17@gmail.com');

    if (cleanInput($_POST['password']) === 'damola1993' && $email === EMAILCONST) {
        $_SESSION['login'] = 'yes';
        $_SESSION['email'] = $email;
        header("Location: tableDatabaseCount.php");
    } else {
        $errors['empass'] = 'Invalid Email/Password';
    }
    // $emailConstant = "aderinwale17@gmail.com";
    // $sql = "SELECT * FROM userTable where email='$email' LIMIT 1";
    // $user = $conn->query($sql);
    // if ($user->num_rows > 0) {
    //     $userRow = $user->fetch_assoc();
    //     if (password_verify(cleanInput($_POST['password']), $userRow['password'])) {
    //         $conn->close();
    //         $_SESSION['login'] = 'yes';
    //         $_SESSION['email'] = $email;
    //         $_SESSION['id'] = $userRow['id'];
    //         $host = $_SERVER['HTTP_HOST'];
    //         header("Location: tableDatabaseCount.php");
    //     } else {
    //         $errors['empass'] = 'Invalid Email/Password';
    //         $conn->close();
    //     }
    // } else {
    //   $errors['empass'] = "Invalid Email/Password.";
    //   $conn->close();
    // }
  }
}

 ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Signin</title>
    <!-- Latest compiled and minified CSS -->
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">

   <!-- jQuery library -->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

   <!-- Popper JS -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>

   <!-- Latest compiled JavaScript -->
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
   <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
   <link rel="stylesheet" href="css/base.css">
   <link rel="stylesheet" href="css/palette.css">
  </head>
  <body>
    <?php
      include 'header.php'
     ?>
     <?php

     include 'showMessage.php';
     if (isset($errors)) {
       if (count($errors) !== 0) {
         $errMessage = 'Invalid Email/Password';

         showMessageError($errMessage);
       }
     }
     if (isset($success)) {
       $successMessage = '';
       foreach ($success as $key => $value) {
         $successMessage .= "$value ";
       }
       showMessageSuccess($successMessage);
     }
      ?>
     <div class="container box">
       <div class="row d-flex justify-content-center">
           <form class="dark-primary-color rounded p-5" method="post"  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
             <div class="form-group">
               <p class="h2 form-header db-text">BVN PORTAL</p>
             </div>
             <div class="form-group">
               <label class="db-text" for="email">Email: </label>
               <input id="email" class="form-control border-dark" type="email" name="email" required />
             </div>
             <div class="form-group">
               <label class="db-text" for="password">Password: </label>
               <input id="password" class="form-control border-dark" type="password" name="password" required/>
             </div>
             <div class="form-group d-flex justify-content-end">
               <button type="submit" class="btn btn-black ml-5" name="submit">Submit</button>
             </div>
           </form>
       </div>
     </div>
  </body>
</html>
