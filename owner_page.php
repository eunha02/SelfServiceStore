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
            font-size: 24px;
            font-weight: bold;
        }

        .navigation {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .navigation a {
            display: block;
            background-color: #008cba;
            color: white;
            padding: 15px;
            margin: 10px;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s, color 0.3s;
            width: 200px; /* 버튼의 너비를 일정하게 조정 */
            text-align: center; /* 버튼 내의 텍스트를 가운데 정렬 */
        }

        .navigation a:hover {
            background-color: #00b0ff;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .menu-container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            padding: 20px;
            margin: 20px auto;
            text-align: left;
            width: 80%;
            max-width: 600px;
        }

        .menu {
            list-style: none;
            padding: 0;
        }

        .menu-item {
            margin-bottom: 10px;
        }

        footer {
            margin-top: 30px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <?php
    $con = mysqli_connect("localhost", "root", "1234", "cvDB");
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $userID = isset($_GET['userID']) ? $_GET['userID'] : '';

    if (empty($userID)) {
        die("사용자 ID가 제공되지 않았습니다. URL을 확인해주세요.");
    }

    $sql = "SELECT u.user_name, b.branch_name 
            FROM usertbl u 
            LEFT JOIN branch b ON u.userID = b.ownerID
            WHERE u.userID = ? AND u.role = 'owner'";
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $con->error);
    }

    $stmt->bind_param("s", $userID);
    if (!$stmt->execute()) {
        die("쿼리 실행 실패: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result === false) {
        die("결과 집합을 가져오는 데 실패: " . $stmt->error);
    }

    if ($row = $result->fetch_assoc()) {
        $userName = $row['user_name'];
        $branchName = $row['branch_name'];
    } else {
        die("사장님 정보를 찾을 수 없습니다.");
    }

    $stmt->close();
    ?>

    <h1><?php echo htmlspecialchars($userName) . " 사장님(" . htmlspecialchars($branchName) . ") 환영합니다."; ?></h1>
    <div class="navigation">
        <a href='무인편의점_물품재고조회.php?ownerID=<?php echo urlencode($userID); ?>' class="menu-button">물품 재고 조회</a>
        <a href='무인편의점_발주.php?ownerID=<?php echo urlencode($userID); ?>' class="menu-button">발주</a>
        <a href='무인편의점_매출.php?userID=<?php echo urlencode($userID); ?>' class="menu-button">매출 정보</a>
        <a href='무인편의점_이벤트추천.php?ownerID=<?php echo urlencode($userID); ?>' class="menu-button">이벤트 추천</a>
        <a href='무인편의점_정보수정.php?userID=<?php echo urlencode($userID); ?>' class="menu-button">정보 수정</a>
    </div>
</body>
</html>
