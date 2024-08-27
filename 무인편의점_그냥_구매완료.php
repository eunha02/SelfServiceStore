<?php
session_start();

// 사용자 등록 함수 (예시)
function registerUser($conn, $userID) {
    $insertUserSql = "INSERT INTO usertbl (userID) VALUES (?)";
    $stmt = $conn->prepare($insertUserSql);
    if (false === $stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    $stmt->bind_param("s", $userID);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
}

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

try 
{
    // 현재 세션에서 사용자 정보 가져오기
    $userID = isset($_SESSION['user']) ? $_SESSION['user'] : 'unknown';


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
    }

    // 트랜잭션 커밋
    $conn->commit();

    // 장바구니 비우기
    $_SESSION['cart'] = [];
    
} 
catch (Exception $e) 
{
    // 오류 발생 시 롤백
    $conn->rollback();
    echo "구매 처리 중 오류 발생: " . $e->getMessage();
}
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
            text-align: center;
            padding: 50px;
        }

        h1 {
            color: #4CAF50;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            color: #333;
        }

        a {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>구매 완료</h1>
    <p class="message">구매가 완료되었습니다. 회원가입을 하시면 다양한 혜택을 누릴 수 있습니다!</p>
    <a href="무인편의점_회원가입화면.php">회원가입 하러 가기</a>
    <a href='무인편의점_main.php'>메인 페이지로 돌아가기</a>
</body>
</html>