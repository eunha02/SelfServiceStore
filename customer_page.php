<!DOCTYPE html>
<html lang="ko">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script>
        function addUserIDToLinks(userID) {
            var links = document.querySelectorAll('.user-link');
            links.forEach(function(link) {
                link.href += '?userID=' + encodeURIComponent(userID);
            });
        }

        window.onload = function() {
            // URL에서 userID 매개변수 가져오기
            var urlParams = new URLSearchParams(window.location.search);
            var userID = urlParams.get('userID');

            if (userID) {
                addUserIDToLinks(userID);
            }
        };
    </script>
    <title>무인 편의점 관리 시스템</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
            text-align: center;
            padding: 20px;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
        }

        .welcome-message {
            color: #008cba;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .navigation a {
            display: inline-block;
            background-color: #008cba;
            color: white;
            padding: 15px 30px;
            margin: 10px;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .navigation a:hover {
            background-color: #00b0ff;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }

        footer {
            margin-top: 30px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>

<body>
    <h1>무인 편의점 관리 시스템</h1>
    <div class="welcome-message">고객님, 환영합니다!</div>
    <div class="navigation">
        <a href='무인편의점_구매목록.php' class='user-link'>구매목록</a><br><br>
        <a href='무인편의점_정보수정.php' class='user-link'>정보 수정</a><br><br>
    </div>
    <footer>
        © 2023 무인 편의점. 
    </footer>
</body>
</html>
