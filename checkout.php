<?php include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
  header('location:login.php');
}

if (isset($_POST['checkout'])) {

  $name = $_POST['firstname'];
  $number = $_POST['number'];
  $email = $_POST['email'];
  $method = $_POST['method'];
  $address = $_POST['address'];
  $city = $_POST['city'];
  $state = $_POST['state'];
  $country = $_POST['country'];
  $pincode = $_POST['pincode'];
  $full_address = $_POST['address'] . ', ' . $_POST['city'] . ', ' . $_POST['state'] . ', ' . $_POST['country'] . ' - ' . $_POST['pincode'];
  $placed_on = date('d-M-Y');

  $cart_total = 0;
  $cart_products = [];
  if (empty($name)) {
    $message[] = 'Please Enter Your Name';
  } elseif (empty($email)) {
    $message[] = 'Please Enter Email Id';
  } elseif (empty($number)) {
    $message[] = 'Please Enter Mobile Number';
  } elseif (empty($address)) {
    $message[] = 'Please Enter Address';
  } elseif (empty($city)) {
    $message[] = 'Please Enter city';
  } elseif (empty($state)) {
    $message[] = 'Please Enter state';
  } elseif (empty($country)) {
    $message[] = 'Please Enter country';
  } elseif (empty($pincode)) {
    $message[] = 'Please Enter your area pincode';
  } else {

    $stmt = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
    $stmt->execute([$user_id]);
    if ($stmt->rowCount() > 0) {
      while ($cart_item = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cart_products[] = $cart_item['name'] . ' #' . $cart_item['book_id'] . ',(' . $cart_item['quantity'] . ') ';
        $quantity = $cart_item['quantity'];
        $unit_price = $cart_item['price'];
        $cart_books = $cart_item['name'];
        $sub_total = ($cart_item['price'] * $cart_item['quantity']);
        $cart_total += $sub_total;
      }
    }

    $total_books = implode(' ', $cart_products);

    $stmt = $conn->prepare("SELECT * FROM `confirm_order` WHERE name = ? AND number = ? AND email = ? AND payment_method = ? AND address = ? AND total_books = ? AND total_price = ?");
    $stmt->execute([$name, $number, $email, $method, $address, $total_books, $cart_total]);

    if ($stmt->rowCount() > 0) {
      $message[] = 'order already placed!';
    } else {
      $stmt = $conn->prepare("INSERT INTO `confirm_order`(user_id, name, number, email, payment_method, address, total_books, total_price, order_date) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->execute([$user_id, $name, $number, $email, $method, $full_address, $total_books, $cart_total, $placed_on]);

      $conn_oid = $conn->lastInsertId();
      $_SESSION['id'] = $conn_oid;

      $stmt = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $stmt->execute([$user_id]);
      if ($stmt->rowCount() > 0) {
        while ($cart_item = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $cart_products[] = $cart_item['name'] . ' #' . $cart_item['book_id'] . ',(' . $cart_item['quantity'] . ') ';
          $quantity = $cart_item['quantity'];
          $unit_price = $cart_item['price'];
          $cart_books = $cart_item['name'];
          $sub_total = ($cart_item['price'] * $cart_item['quantity']);
          $cart_total += $sub_total;

          $stmt = $conn->prepare("INSERT INTO `orders`(user_id, id, address, city, state, country, pincode, book, quantity, unit_price, sub_total) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
          $stmt->execute([$user_id, $conn_oid, $address, $city, $state, $country, $pincode, $cart_books, $quantity, $unit_price, $sub_total]);
        }
      }

      $message[] = 'order placed successfully!';
      $stmt = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $stmt->execute([$user_id]);
    }
  }
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Checkout</title>
  <style>
    body {
      font-family: Arial;
      font-size: 17px;
      padding: 8px;
      overflow-x: hidden;
    }

    * {
      box-sizing: border-box;
    }

    .row {
      display: -ms-flexbox;
      /* IE10 */
      display: flex;
      -ms-flex-wrap: wrap;
      /* IE10 */
      flex-wrap: wrap;
      margin: 0 -16px;
      padding: 30px;
    }

    .col-25 {
      -ms-flex: 25%;
      /* IE10 */
      flex: 25%;
    }

    .col-50 {
      -ms-flex: 50%;
      /* IE10 */
      flex: 50%;
    }

    .col-75 {
      -ms-flex: 75%;
      /* IE10 */
      flex: 75%;
    }

    .col-25,
    .col-50,
    .col-75 {
      padding: 0 16px;
    }

    .container {
      background-color: #f2f2f2;
      padding: 5px 20px 15px 20px;
      border: 1px solid lightgrey;
      border-radius: 3px;
    }

    input[type=text],
    select {
      width: 100%;
      margin-bottom: 20px;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 3px;
    }

    label {
      margin-bottom: 10px;
      display: block;
      color: black;
    }

    .icon-container {
      margin-bottom: 20px;
      padding: 7px 0;
      font-size: 24px;
    }

    .btn {
      background-color: rgb(28 146 197);
      color: white;
      padding: 12px;
      margin: 10px 0;
      border: none;
      width: 100%;
      border-radius: 3px;
      cursor: pointer;
      font-size: 17px;
    }

    .btn:hover {
      background-color: rgb(6 157 21);
      letter-spacing: 1px;
      font-weight: 600;
    }

    a {
      color: #rgb(28 146 197);
    }

    hr {
      border: 1px solid lightgrey;
    }

    span.price {
      float: right;
      color: grey;
    }

    @media (max-width: 800px) {
      .row {
        flex-direction: column-reverse;
        padding: 0;
      }

      .col-25 {
        margin-bottom: 20px;
      }
    }
    .message {
  position: sticky;
  top: 0;
  margin: 0 auto;
  width: 61%;
  background-color: #fff;
  padding: 6px 9px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  z-index: 100;
  gap: 0px;
  border: 2px solid rgb(68, 203, 236);
  border-top-right-radius: 8px;
  border-bottom-left-radius: 8px;
}
.message span {
  font-size: 22px;
  color: rgb(240, 18, 18);
  font-weight: 400;
}
.message i {
  cursor: pointer;
  color: red;
  font-size: 15px;
}

  </style>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://kit.fontawesome.com/493af71c35.js" crossorigin="anonymous"></script>
  
</head>

<body>
  <?php include 'index_header.php'; ?>

  <?php
  if (isset($message)) {
    foreach ($message as $message) {
      echo '
        <div class="message" id= "messages"><span>' . $message . '</span>
        </div>
        ';
    }
  }
  ?>

  <h1 style="text-align: center; margin-top:15px;  color:rgb(9, 152, 248);">Place Your Order Here</h1>
  <p style="text-align: center; ">Just One Step away from getting your books</p>
  <div class="row">
    <div class="col-75">
      <div class="container">
        <form action="" method="POST">

          <div class="row">
            <div class="col-50">
              <h3>Billing Address</h3>
              <label for="fname"><i class="fa fa-user"></i> Full Name</label>
              <input type="text" id="fname" name="firstname" placeholder="Pawan Mishra">
              <label for="email"><i class="fa fa-envelope"></i> Email</label>
              <input type="text" id="email" name="email" placeholder="example@gmail.com">
              <label for="email"><i class="fa fa-envelope"></i> Number</label>
              <input type="text" id="email" name="number" placeholder="+91987456123">
              <label for="adr"><i class="fa fa-address-card-o"></i> Address</label>
              <input type="text" id="adr" name="address" placeholder="Mumbai 60">
              <label for="city"><i class="fa fa-institution"></i> City</label>
              <input type="text" id="city" name="city" placeholder="Mumbai">
              <label for="city"><i class="fa fa-institution"></i> State</label>
              <input type="text" id="city" name="state" placeholder="Maharashtra">

              <div style="padding: 0px;" class="row">
                <div class="col-50">
                  <label for="state">Country</label>
                  <input type="text" id="state" name="country" placeholder="india">
                </div>
                <div class="col-50">
                  <label for="zip">Pincode</label>
                  <input type="text" id="zip" name="pincode" placeholder="400060">
                </div>
              </div>
            </div>

            <div class="col-50">
              <div class="col-25">
                <div class="container">
                  <h4>Books In Cart</h4>
                  <?php
                  $grand_total = 0;
                  $stmt = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                  $stmt->execute([$user_id]);
                  if ($stmt->rowCount() > 0) {
                    while ($fetch_cart = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
                      $grand_total += $total_price;
                  ?>
                      <p> <a href="book_details.php?details=<?php echo $fetch_cart['book_id']; ?>"><?php echo $fetch_cart['name']; ?></a><span class="price">(<?php echo '₹ ' . $fetch_cart['price'] . '/-' . ' x ' . $fetch_cart['quantity']; ?>)</span> </p>
                  <?php
                    }
                  } else {
                    echo '<p class="empty">your cart is empty</p>';
                  }
                  ?>

                  <hr>
                  <p>Grand total : <span class="price" style="color:black">₹ <b><?php echo $grand_total; ?>/-</b></span></p>
                </div>
              </div>
              <div style="margin: 20px;">
                <h3>Payment </h3>
                <label for="fname">Accepted Payment Gateways</label>
                <div class="icon-container">
                  <i class="fa fa-cc-visa" style="color:navy;"></i>
                  <i class="fa-brands fa-cc-amazon-pay"></i>
                  <i class="fa-brands fa-google-pay" style="color:red;"></i>
                  <i class="fa fa-cc-paypal" style="color:#3b7bbf;"></i>
                </div>
                <div class="inputBox">
                  <label for="method">Choose Payment Method :</label>
                  <select name="method" id="method">
                    <option value="cash on delivery">Cash on delivery</option>
                    <option value="Debit card">Debit card</option>
                    <option value="Amazon Pay">Amazon Pay</option>
                    <option value="Paypal">Paypal</option>
                    <option value="Google Pay">Google Pay</option>
                  </select>
                </div>
              </div>
            </div>

          </div>
          <label>
            <input type="checkbox" checked="checked" name="sameadr"> Shipping address same as billing
          </label>
          <input type="submit" name="checkout" value="Continue to checkout" class="btn">
        </form>
      </div>
    </div>
  </div>
  <?php include 'index_footer.php'; ?>
  <script>
    setTimeout(() => {
      const box = document.getElementById('messages');

      // 👇️ hides element (still takes up space on page)
      box.style.display = 'none';
    }, 5000);
  </script>
</body>

</html>