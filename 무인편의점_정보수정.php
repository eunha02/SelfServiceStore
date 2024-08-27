<!DOCTYPE html>
<html lang="ko">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>정보 수정하기</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
            text-align: center;
            padding: 40px;
        }

        h1 {
            color: #005f73;
            margin-bottom: 30px;
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: inline-block;
        }

        input[type="text"], input[type="submit"] {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ddd;
            box-sizing: border-box;
            transition: border-color 0.3s, background-color 0.3s;
        }

        input[type="text"]:hover {
            background-color: #DAE8FC; /* 호버 시 밝은 파란색 배경 */
            border-color: #B6D4FE; /* 호버 시 밝은 파란색 테두리 */
        }

       /* Placeholder Styling */
       ::placeholder {
            color: #aaa;
        }

        input[type="submit"] {
            background-color: #008cba;
            color: white;
            border: none;
            padding: 8px 20px;
            margin-top: 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 16px;
            font-weight: bold;
        }

        input[type="submit"]:hover {
            background-color: #66b2ff; 
        }
    </style>
</head>
<body>
    <h1>정보 수정</h1>
    <form method="get" action="무인편의점_정보수정_process.php">
        비밀번호를 입력해주세요. : <input type="text" name="password"><br>
        <input type="submit" value="입력">
    </form>
</body>
</html>
