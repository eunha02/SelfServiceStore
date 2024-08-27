<?php
session_start();

// 데이터베이스 연결 설정
$servername = "localhost";
$username = "root";
$password = "1234"; // 실제 데이터베이스 비밀번호를 입력하세요.
$dbname = "cvDB";

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 데이터베이스 연결 확인
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => '데이터베이스 연결 실패: ' . $conn->connect_error]);
    exit;
}

$productID = $_POST['productID'];
$quantity = $_POST['quantity'];
$product_name = $_POST['product_name'];
$price = $_POST['price']; // 클라이언트로부터 받은 할인된 가격
$branchID = $_POST['branch']; // 지점 ID

// 재고 확인
$stockCheckSql = "SELECT amount FROM warehouse WHERE productID = ? AND branchID = ?";
$stockCheckStmt = $conn->prepare($stockCheckSql);
$stockCheckStmt->bind_param("ss", $productID, $branchID);
$stockCheckStmt->execute();
$stockResult = $stockCheckStmt->get_result();

if ($stockRow = $stockResult->fetch_assoc()) {
    $stockAmount = $stockRow['amount'];

    // 수량이 재고를 초과하는지 확인
    if ($quantity > $stockAmount) {
        echo json_encode(['status' => 'error', 'message' => '재고 수량을 초과하는 주문은 불가능합니다.']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => '상품 재고 정보를 찾을 수 없습니다.']);
    exit;
}

// 할인된 가격이 0보다 큰지 확인
if ($price <= 0) {
    echo json_encode(['status' => 'error', 'message' => '유효하지 않은 가격입니다.']);
    exit;
}

// 장바구니에 상품 추가 로직
if (isset($_SESSION['cart'][$productID])) {
    // 이미 장바구니에 상품이 있는 경우 수량과 가격 업데이트
    $_SESSION['cart'][$productID]['quantity'] += $quantity;
    $_SESSION['cart'][$productID]['price'] = $price; // 할인된 가격 업데이트
} else {
    // 새 상품을 장바구니에 추가
    $_SESSION['cart'][$productID] = [
        'name' => $product_name,
        'quantity' => $quantity,
        'price' => $price, // 할인된 가격 저장
        'branchID' => $branchID
    ];
}

echo json_encode(['status' => 'success', 'message' => "{$product_name}이(가) 장바구니에 추가되었습니다.", 'cart' => $_SESSION['cart']]);

// 데이터베이스 연결 종료
$conn->close();
?>