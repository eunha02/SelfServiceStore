<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productID = $_POST['productID'];
    $quantity = $_POST['quantity'];

    if (isset($_SESSION['cart'][$productID])) {
        $_SESSION['cart'][$productID]['quantity'] = $quantity;

        echo json_encode(['status' => 'success', 'cart' => $_SESSION['cart']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => '장바구니 항목을 찾을 수 없습니다.']);
    }
}
?>
