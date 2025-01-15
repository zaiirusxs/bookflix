<?php
    include 'config.php';
    if(isset($_POST['submit'])) {
        $name = $_POST['Name'];
        $Sname = $_POST['Sname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        $user_type = $_POST['user_type'];

        $stmt = $conn->prepare("SELECT * FROM users_info WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if($stmt->rowCount() != 0){
            $message[] = 'User Already exists!';
        } else {
            if($password != $cpassword){
                $message[] = 'Confirm password not matched.';
            } else {
                $stmt = $conn->prepare("INSERT INTO users_info(`name`, `surname`, `email`, `password`, `user_type`) VALUES(:name, :surname, :email, :password, :user_type)");
                $stmt->execute([
                    'name' => $name,
                    'surname' => $Sname,
                    'email' => $email,
                    'password' => $password,
                    'user_type' => $user_type
                ]);
                $message[] = 'Registration Done Successfully';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/register.css" />

    <title>Register</title>
    <style>
      .container2 {
        display: flex;
        justify-content: center;
        background-image: linear-gradient(45deg,
          rgba(0, 0, 3, 0.1),
          rgba(0, 0, 0, 0.5)), url(../bgimg/2.jpg);
        background-repeat: no-repeat;
        background-position: center;
        background-size: cover;
        height: 98vh;
      }
    </style>
    <style>
       .container form .link{
            text-decoration: none; color:white;  border-radius: 17px; padding: 8px 18px; margin: 0px 10px; background: rgb(0, 0, 0); font-size: 20px;
        }
        .container form .link:hover{
            background: rgb(0, 167, 245);
        }
    </style>
  </head>
  <body>
  <?php
    if(isset($message)){
      foreach($message as $message){
        echo '
        <div class="message" id= "messages"><span>'.$message.'</span>
        </div>
        ';
      }
    }
    ?>
    <div class="container">
      <form action="" method="post">
         <h3 style="color:white">Register to Use <a href="index.php"><span>Bookflix &  </span><span>Chill</span></a></h3>
         <input type="text" name="Name" placeholder="Enter Name" required class="text_field ">
         <input type="text" name="Sname" placeholder="Enter Surname" required class="text_field">
         <input type="email" name="email" placeholder="Enter Email Id" required class="text_field">
         <input type="password" name="password" placeholder="Enter password" required class="text_field">
         <input type="password" name="cpassword" placeholder="Confirm password" required class="text_field">
         <select name="user_type" id="" required class="text_field">
            <option value="User">User</option>
            <option value="Admin">Admin</option>
         </select>
         <input type="submit" value="Register" name="submit" class="btn text_field">
         <p>Already have a Account? <br> <a class="link" href="login.php">Login</a><a class="link" href="index.php">Back</a></p>
      </form>
    </div>

    <script>
setTimeout(() => {
  const box = document.getElementById('messages');

  // 👇️ hides element (still takes up space on page)
  box.style.display = 'none';
}, 8000);
</script>
  </body>
</html>