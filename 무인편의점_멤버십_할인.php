<?php
session_start();

// 데이터베이스 설정
$servername = "localhost";
$username = "root";
$password = "1234"; // 실제 데이터베이스 비밀번호를 입력하세요.
$dbname = "cvDB";

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("연결 실패: " . $conn->connect_error);
}

// 사용자 아이디 세션에서 가져오기
$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;

// 누적 구매액을 가져오는 쿼리
$totalPurchaseQuery = "SELECT SUM(b.amount * p.price) as recent_total_price
FROM buy b
INNER JOIN product p ON b.productID = p.productID
WHERE b.userID = ? AND b.buy_date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
$totalStmt = $conn->prepare($totalPurchaseQuery);
$totalStmt->bind_param("s", $userID);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalSpent = 0;

// 누적 구매액을 가져오는 쿼리 결과 처리
if ($totalRow = $totalResult->fetch_assoc()) {
    $totalSpent = $totalRow['recent_total_price']; // 'total_spent' 대신 'recent_total_price'를 사용
}
$totalStmt->close();


// 누적 구매액에 따른 멤버십 등급과 할인율 결정
$membershipGrade = 'GREEN'; // 기본 등급
$discountRate = 0.0; // 기본 할인율

if ($totalSpent > 300000) {
    $membershipGrade = 'VIP';
    $discountRate = 0.15; // 15% 할인
} elseif ($totalSpent > 200000) {
    $membershipGrade = 'GOLD';
    $discountRate = 0.10; // 10% 할인
} elseif ($totalSpent > 100000) {
    $membershipGrade = 'SILVER';
    $discountRate = 0.05; // 5% 할인
}

// 장바구니 세션에서 총 금액을 계산합니다.
$cartItems = $_SESSION['cart'];
$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}

// 할인율을 적용한 총 금액 계산
$discountAmount = $totalPrice * $discountRate;
$finalPrice = $totalPrice - $discountAmount;

// 결과를 세션에 저장하거나 출력
$_SESSION['membership_grade'] = $membershipGrade;
$_SESSION['discount_rate'] = $discountRate;
$_SESSION['final_price'] = $finalPrice;
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>멤버십 할인</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h1 {
            color: #444;
            text-align: center;
        }

        .summary {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin: 30px auto;
            width: 80%;
            max-width: 500px;
            background: #fff;
        }

        .summary p {
            font-size: 1.2em;
            line-height: 1.6;
            margin: 10px 0;
        }

        .summary p span {
            font-weight: bold;
        }

        .action-button {
            display: block;
            width: 80%;
            max-width: 300px;
            margin: 20px auto;
            padding: 10px;
            font-size: 1.2em;
            text-align: center;
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .action-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>멤버십 할인 확인</h1>
    <div class="summary">
        <p>원래 금액: <span><?php echo number_format($totalPrice); ?>원</span></p>
        <p>할인 금액: <span>-<?php echo number_format($discountAmount); ?>원</span></p>
        <p>최종 금액: <span><?php echo number_format($finalPrice); ?>원</span></p>
    </div>

    <a href="무인편의점_멤버십_구매완료.php" class="action-button">구매하기</a>
</body>
</html>
