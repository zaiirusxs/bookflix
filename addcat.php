<?php
include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}

if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];

    $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (:name)");
    $stmt->execute(['name' => $category_name]);

    $message[] = 'Category added successfully';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/admin.css">
    <title>Add Category</title>
    <style>
        .add-category {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .add-category .title {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .add-category .box {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .add-category .btn {
            width: 100%;
            padding: 10px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .add-category .btn:hover {
            background-color:red;
        }

        .message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #d6e9c6;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <section class="add-category">
        <h1 class="title">Add New Category</h1>

        <?php
        if (isset($message)) {
            foreach ($message as $message) {
                echo '<div class="message"><span>' . $message . '</span></div>';
            }
        }
        ?>

        <form action="" method="POST">
            <input type="text" name="category_name" placeholder="Enter category name" required class="box">
            <input type="submit" value="Add Category" name="add_category" class="btn">
        </form>
    </section>
</body>
</html>