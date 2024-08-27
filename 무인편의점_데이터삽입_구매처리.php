<?php

// 데이터베이스 설정
$servername = "localhost";
$username = "root";
$password = "1234"; // 실제 데이터베이스 비밀번호를 입력하세요.
$dbname = "cvDB";

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("연결 실패: " . $conn->connect_error);
}

// 트랜잭션 시작
$conn->begin_transaction();

try {
    // 사용자가 멤버십 회원인지 확인
    $userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : 'unknown';

    // 장바구니에서 상품 정보 가져오기
    $cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];


    foreach ($cartItems as $productID => $item) {
        // 현재 날짜와 시간
$currentDateTime = date('Y-m-d');

// buy 테이블에 구매 정보 추가 (buy_date 포함)
$insertSql = "INSERT INTO buy (userID, productID, amount, branchID, buy_date) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insertSql);
if (false === $stmt) {
    throw new Exception("Prepare statement failed: " . $conn->error);
}
$stmt->bind_param("ssiss", $userID, $productID, $item['quantity'], $item['branchID'], $currentDateTime);
if (!$stmt->execute()) {
    throw new Exception("Execute failed: " . $stmt->error);
}
        }

        // warehouse 테이블에서 수량 업데이트
        $updateSql = "UPDATE warehouse SET amount = amount - ? WHERE productID = ? AND branchID = ?";
        $updateStmt = $conn->prepare($updateSql);
        if (false === $updateStmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $updateStmt->bind_param("iss", $item['quantity'], $productID, $item['branchID']);
        if (!$updateStmt->execute()) {
            throw new Exception("Execute failed: " . $updateStmt->error);
        }

    // 트랜잭션 커밋
    $conn->commit();

    // 장바구니 비우기
    $_SESSION['cart'] = [];
} catch (Exception $e) {
    // 오류 발생 시 롤백
    $conn->rollback();
    echo "구매 처리 중 오류 발생: " . $e->getMessage();
}

$conn->close();
?>
