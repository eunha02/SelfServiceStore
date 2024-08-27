<!DOCTYPE html>
<html lang="ko">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>로그인</title>
    <style>
        /* Basic Reset */
        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container Styling */
        .login-container {
            background: #ffffff;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
            width: 350px;
        }

        h1 {
            color: #005f73;
            margin-bottom: 30px;
            font-size: 24px;
        }

        /* Form Styling */
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 2px solid #ddd;
            border-radius: 10px;
            box-sizing: border-box;
        }

        /* Placeholder Styling */
        ::placeholder {
            color: #aaa;
        }

        input[type="submit"] {
            background-color: #008cba;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 16px;
            font-weight: bold;
        }

        input[type="submit"]:hover {
            background-color: #66b2ff; /* Harmonious lighter blue for hover */
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>로그인</h1>
        <form method="post" action="무인편의점_login_process.php">
            <label for="userID">아이디</label>
            <input type="text" name="userID" id="userID" placeholder="아이디 입력">

            <label for="password">비밀번호</label>
            <input type="password" name="password" id="password" placeholder="비밀번호 입력">

            <input type="submit" value="로그인">
        </form>
    </div>
</body>
</html>
