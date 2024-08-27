<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta htt-equiv="content-type" content="text/html; charset=utf-8">
    <title>무인 편의점 관리 시스템</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
            color: #333;
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container Styling */
        .container {
            text-align: center;
            background: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 80%;
            max-width: 500px;
        }

        /* Header Styling */
        h1 {
            color: #005f73;
            margin-bottom: 20px;
        }

        /* Navigation Link Styling */
        .navigation a {
            display: inline-block;
            background-color: #008cba;
            color: white;
            padding: 15px 30px; /* 버튼의 폭과 높이를 조정 */
            margin: 10px;
            text-decoration: none;
            border-radius: 8px;
            font-size: 13px;
            transition: background-color 0.3s, transform 0.3s;
            min-width: 150px; /* 버튼의 최소 폭을 설정하여 일정한 크기를 유지 */
            text-align: center; /* 텍스트를 가운데 정렬 */
            line-height: 1.6; /* 텍스트 줄 간격을 조절하여 가운데 정렬 */
        }

        .navigation a:hover {
            background-color: #66b2ff; /* Lighter shade of blue for a smooth transition */
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>무인 편의점 관리 시스템</h1>
        <div class="navigation">
            <a href="무인편의점_로그인화면.php">로그인</a><br><br>
            <a href="무인편의점_이벤트목록확인.php">이벤트 목록</a><br><br>
            <a href="무인편의점_고객구매.php">구매 시연</a><br><br>
            <a href="무인편의점_회원가입화면.php">회원가입</a>
        </div>
    </div>
</body>
</html>
