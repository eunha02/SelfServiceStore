<?php
session_start();

// 사용자가 폼을 통해 아이디를 제출했을 경우 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userID'])) {
    // 세션에 사용자 아이디 저장
    $_SESSION['userID'] = $_POST['userID'];

    // 멤버십 구매 완료 페이지로 리다이렉트
    header("Location: 무인편의점_멤버십_할인.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>멤버십 확인</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
        }

        form {
            margin-top: 20px;
        }

        input[type="text"], input[type="submit"] {
            padding: 10px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>멤버십 확인</h1>
    <p>아이디를 입력해주세요:</p>
    <form method="post">
        <input type="text" name="userID" required placeholder="아이디 입력">
        <input type="submit" value="입력">
    </form>
</body>
</html>
