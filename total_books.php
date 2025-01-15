<?php
include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM `book_info` WHERE bid = :bid");
    $stmt->execute(['bid' => $delete_id]);
    header('location:total_books.php');
}

if (isset($_POST['update_product'])) {
    $update_p_id = $_POST['update_p_id'];
    $update_name = $_POST['update_name'];
    $update_title = $_POST['update_title'];
    $update_description = $_POST['update_description'];
    $update_price = $_POST['update_price'];
    $update_category = $_POST['update_category'];

    $stmt = $conn->prepare("UPDATE `book_info` SET name = :name, title = :title, description = :description, price = :price, category = :category WHERE bid = :bid");
    $stmt->execute([
        'name' => $update_name,
        'title' => $update_title,
        'description' => $update_description,
        'price' => $update_price,
        'category' => $update_category,
        'bid' => $update_p_id
    ]);

    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_folder = './added_books/' . $update_image;
    $update_old_image = $_POST['update_old_image'];

    if (!empty($update_image)) {
        if ($update_image_size > 2000000) {
            $message[] = 'image file size is too large';
        } else {
            $stmt = $conn->prepare("UPDATE `book_info` SET image = :image WHERE bid = :bid");
            $stmt->execute(['image' => $update_image, 'bid' => $update_p_id]);
            move_uploaded_file($update_image_tmp_name, $update_folder);
            unlink('uploaded_img/' . $update_old_image);
        }
    }

    header('location:total_books.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/register.css">
    <title>Add Books</title>
</head>

<body>
    <?php
    include './admin_header.php'
    ?>
    <?php
    if (isset($message)) {
        foreach ($message as $message) {
            echo '
        <div class="message"><span>' . $message . '</span><i onclick="this.parentElement.remove();">Close</i>
        </div>
        ';
        }
    }
    ?>
    <a class="update_btn" style="position: fixed ; z-index:100;" href="add_books.php">Add More Books</a>

    <section class="edit-product-form">
        <?php
        if (isset($_GET['update'])) {
            $update_id = $_GET['update'];
            $stmt = $conn->prepare("SELECT * FROM `book_info` WHERE bid = :bid");
            $stmt->execute(['bid' => $update_id]);
            if ($stmt->rowCount() > 0) {
                while ($fetch_update = $stmt->fetch(PDO::FETCH_ASSOC)) {
        ?>
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['bid']; ?>">
                        <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['image']; ?>">
                        <img src="./added_books/<?php echo $fetch_update['image']; ?>" alt="">
                        <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="Enter Book Name">
                        <input type="text" name="update_title" value="<?php echo $fetch_update['title']; ?>" class="box" required placeholder="Enter Author Name">
                        <select name="update_category" value="<?php echo $fetch_update['category']; ?>" required class="text_field">
                            <option value="Adventure">Adventure</option>
                            <option value="Magic">Magic</option>
                            <option value="knowledge">knowledge</option>
                        </select>
                        <input type="text" name="update_description" value="<?php echo $fetch_update['description']; ?>" class="box" required placeholder="enter product description">
                        <input type="number" name="update_price" value="<?php echo $fetch_update['price']; ?>" min="0" class="box" required placeholder="enter product price">
                        <input type="file" class="box" name="update_image" accept="image/jpg, image/jpeg, image/png">
                        <input type="submit" value="update" name="update_product" class="delete_btn">
                        <input type="reset" value="cancel" id="close-update" class="update_btn">
                    </form>
        <?php
                }
            }
        } else {
            echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
        }
        ?>

    </section>

    <section class="show-products">
        <div class="box-container">

            <?php
            $stmt = $conn->query("SELECT * FROM `book_info` ORDER BY date DESC");
            if ($stmt->rowCount() > 0) {
                while ($fetch_book = $stmt->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <div class="box">
                        <img class="books_images" src="added_books/<?php echo $fetch_book['image']; ?>" alt="">
                        <div class="name">Author: <?php echo $fetch_book['title']; ?></div>
                        <div class="name">Name: <?php echo $fetch_book['name']; ?></div>
                        <div class="price">Price: ₹ <?php echo $fetch_book['price']; ?>/-</div>
                        <a href="total_books.php?update=<?php echo $fetch_book['bid']; ?>" class="update_btn">update</a>
                        <a href="total_books.php?delete=<?php echo $fetch_book['bid']; ?>" class="delete_btn" onclick="return confirm('delete this product?');">delete</a>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">no products added yet!</p>';
            }
            ?>
        </div>

    </section>

    <script src="./js/admin.js"></script>
</body>

</html>