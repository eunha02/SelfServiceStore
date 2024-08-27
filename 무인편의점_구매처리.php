<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>구매 처리</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
        }

        .button {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }

        .button:hover {
            background-color: #0066cc;
        }
    </style>
</head>
<body>
    <h1>구매 처리</h1>
    <p>멤버십 회원이신가요?</p>
    <div>
        <form action="무인편의점_멤버십확인.php" method="post">
            <input type="submit" class="button" value="네, 회원입니다">
        </form>
        <form action="무인편의점_그냥_구매완료.php" method="post">
            <input type="submit" class="button" value="아니요, 회원이 아닙니다">
        </form>
    </div>
</body>
</html>
