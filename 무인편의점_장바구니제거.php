<?php
session_start();

$response = ['status' => 'error', 'message' => '오류가 발생했습니다.'];

if (isset($_GET['remove_from_cart'])) {
    $productId = $_GET['remove_from_cart'];
    
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        $response = ['status' => 'success', 'message' => '장바구니에서 상품이 제거되었습니다.'];
    } else {
        $response['message'] = '장바구니에서 상품을 찾을 수 없습니다.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);