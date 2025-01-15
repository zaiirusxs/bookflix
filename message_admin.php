<?php
include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}

if (isset($_GET['delete_msg'])) {
    $msg_id = $_GET['delete_msg'];
    $stmt = $conn->prepare("DELETE FROM `msg` WHERE id = :id");
    $stmt->execute(['id' => $msg_id]);
    header('location:message_admin.php');
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
    <title>Messages</title>
</head>

<body>
    <?php
    include 'admin_header.php';
    ?>
    <section class="show-products">
        <div class="box-container">

            <?php
            $stmt = $conn->query("SELECT id, name, email, number, msg, date FROM msg");
            if ($stmt->rowCount() > 0) {
                while ($fetch_user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <div class="box">
                        <div class="name">Message ID: <?php echo $fetch_user['id']; ?></div>
                        <div class="name">Name: <?php echo $fetch_user['name']; ?></div>
                        <div class="name">Email ID: <?php echo $fetch_user['email']; ?></div>
                        <div class="password">Number: <?php echo $fetch_user['number']; ?></div>
                        <div class="price">Message: <?php echo wordwrap($fetch_user['msg'], 30, "<br>\n", TRUE); ?></div>
                        <div class="price">Date: <?php echo $fetch_user['date']; ?></div>
                        <a href="message_admin.php?delete_msg=<?php echo $fetch_user['id']; ?>" onclick="return confirm('delete this message?');">Delete</a>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">No any message received yet!</p>';
            }
            ?>
        </div>
    </section>
</body>

</html>