<?php
session_start();
include '무인편의점_데이터삽입_구매처리.php';

// 데이터베이스 설정
$servername = "localhost";
$username = "root";
$password = "1234"; // 데이터베이스 비밀번호
$dbname = "cvDB";

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("연결 실패: " . $conn->connect_error);
}

// 사용자가 멤버십 회원인지 확인
$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : 'unknown';

// 장바구니에서 상품 정보 가져오기
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

foreach ($cartItems as $productID => $item) {
    // 현재 날짜와 시간
    $currentDateTime = date('Y-m-d');

    // buy 테이블에 구매 정보 추가
    $insertSql = "INSERT INTO buy (userID, productID, amount, branchID, buy_date) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("ssiss", $userID, $productID, $item['quantity'], $item['branchID'], $currentDateTime);
    $stmt->execute();

    // warehouse 테이블에서 수량 업데이트
    $updateSql = "UPDATE warehouse SET amount = amount - ? WHERE productID = ? AND branchID = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("iss", $item['quantity'], $productID, $item['branchID']);
    $updateStmt->execute();
}

// 장바구니 비우기
$_SESSION['cart'] = [];

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>구매 완료</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f8f8;
            color: #333;
            text-align: center;
            margin: 0;
            padding: 40px;
        }

        h1 {
            color: #4CAF50;
        }

        p {
            font-size: 18px;
            margin-top: 20px;
        }

        a {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 30px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>멤버십 구매 완료</h1>
    <?php if (!empty($userName)): ?>
        <p><?php echo htmlspecialchars($userName); ?>님, 구매가 완료되었습니다. 감사합니다!</p>
    <?php else: ?>
        <p>구매가 완료되었습니다. 감사합니다!</p>
    <?php endif; ?>
    <a href="무인편의점_main.php">메인 페이지로 돌아가기</a>
</body>
</html>
