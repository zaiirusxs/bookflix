<?php
include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}

if (isset($_GET['delete_user'])) {
    $delete_id = $_GET['delete_user'];
    $stmt = $conn->prepare("DELETE FROM `users_info` WHERE Id = :id");
    $stmt->execute(['id' => $delete_id]);
    header('location:users_detail.php');
}

if (isset($_POST['update_user'])) {
    $update_id = $_POST['update_id'];
    $update_name = $_POST['update_name'];
    $update_sname = $_POST['update_sname'];
    $update_email = $_POST['update_email'];
    $update_password = $_POST['update_password'];
    $update_user_type = $_POST['update_user_type'];

    $stmt = $conn->prepare("UPDATE `users_info` SET name = :name, surname = :surname, email = :email, password = :password, user_type = :user_type WHERE Id = :id");
    $stmt->execute([
        'name' => $update_name,
        'surname' => $update_sname,
        'email' => $update_email,
        'password' => $update_password,
        'user_type' => $update_user_type,
        'id' => $update_id
    ]);

    header('location:users_detail.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/register.css">
    <link rel="stylesheet" href="./css/index_book.css">
    <title>User Data</title>
</head>

<body>
    <?php
    include 'admin_header.php';
    ?>

    <section class="show-products">
        <div class="box-container">

            <?php
            $stmt = $conn->query("SELECT Id, name, surname, email, password, user_type FROM users_info");
            if ($stmt->rowCount() > 0) {
                while ($fetch_user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <div class="box">
                        <div class="name">User ID: <?php echo $fetch_user['Id']; ?></div>
                        <div class="name">Name: <?php echo $fetch_user['name']; ?>&nbsp;<?php echo $fetch_user['surname']; ?></div>
                        <div class="name">Email ID: <?php echo $fetch_user['email']; ?></div>
                        <div class="password">Password: <?php echo $fetch_user['password']; ?></div>
                        <div class="price" style="color:<?php if ($fetch_user['user_type'] == 'Admin') {
                                                                echo 'red';
                                                            } else {
                                                                echo 'blue';
                                                            } ?>;">User type: <?php echo $fetch_user['user_type']; ?></div>
                        <a style="color:rgb(255, 187, 0);" href="users_detail.php?update_user=<?php echo $fetch_user['Id']; ?>">Update</a>
                        <a href="users_detail.php?delete_user=<?php echo $fetch_user['Id']; ?>" onclick="return confirm('Are you sure you want to delete this user');">Delete</a>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">no products added yet!</p>';
            }
            ?>
        </div>
    </section>

    <section class="edit_user-form">
        <div class="edit-product-form">
            <?php
            if (isset($_GET['update_user'])) {
                $update_id = $_GET['update_user'];
                $stmt = $conn->prepare("SELECT * FROM `users_info` WHERE Id = :id");
                $stmt->execute(['id' => $update_id]);
                if ($stmt->rowCount() > 0) {
                    while ($fetch_update = $stmt->fetch(PDO::FETCH_ASSOC)) {
            ?>
                        <form action="" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="update_id" value="<?php echo $fetch_update['Id']; ?>">
                            <input type="text" value="<?php echo $fetch_update['name'] ?>" name="update_name" placeholder="Enter Name" required class="box ">
                            <input type="text" value="<?php echo $fetch_update['surname'] ?>" name="update_sname" placeholder="Enter Surname" required class="box">
                            <input type="email" value="<?php echo $fetch_update['email'] ?>" name="update_email" placeholder="Enter Email Id" required class="box">
                            <input type="password" value="<?php echo $fetch_update['password'] ?>" name="update_password" placeholder="Enter password" required class="box">
                            <select name="update_user_type" id="" required class="box">
                                <option value="User">User</option>
                                <option value="Admin">Admin</option>
                            </select>
                            <input type="submit" value="Update" name="update_user" class="delete_btn">
                            <input type="reset" value="cancel" id="close-update_user" class="update_btn">
                        </form>
            <?php
                    }
                }
            } else {
                echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
            }
            ?>

        </div>
    </section>

    <script>
        document.querySelector('#close-update_user').onclick = () => {
            document.querySelector('.edit-product-form').style.display = 'none';
            window.location.href = 'users_detail.php';
        }
    </script>

</body>

</html>