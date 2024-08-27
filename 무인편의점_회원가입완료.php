<!DOCTYPE html>
<html lang="ko">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>회원가입 결과</title>
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

        .result {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            display: inline-block;
            text-align: left;
            width: 400px;
        }

        .success {
            color: #005f73;
            font-weight: bold;
            font-size: 18px;
        }

        .failure {
            color: black;
            font-weight: bold;
            font-size: 18px;
        }

        .back-button {
            margin-top: 20px;
            text-align: center;
        }

        .back-button a {
            background-color: #008cba;
            color: white;
            border: none;
            padding: 12px 18px; /* 수정된 부분 */
            border-radius: 10px;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            display: inline-block;
            transition: background-color 0.3s, color 0.3s;
            margin-right: 10px; /* 버튼 사이 간격 조절을 위해 추가 */
        }

        .back-button a:hover {
            background-color: #66b2ff;
            color: white;
        }
    </style>
</head>
<body>

<div class="result">
    <?php
    $con = mysqli_connect("localhost", "root", "1234", "cvDB");

    if (!$con) {
        die("MySQL 접속 실패: " . mysqli_connect_error());
    }

    $userID = $_POST["userID"];
    $password = $_POST["password"];
    $user_name = $_POST["user_name"];
    $mobile = $_POST["mobile"];
    $birthyear = $_POST["birthyear"];
    $gender = $_POST["gender"];
    $role = $_POST["role"];
    $selectedBranch = isset($_POST["branch"]) ? $_POST["branch"] : null;

    $sqlUser = "INSERT INTO userTBL (userID, password, user_name, mobile, birthyear, gender, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtUser = $con->prepare($sqlUser);
    $stmtUser->bind_param("ssssiss", $userID, $password, $user_name, $mobile, $birthyear, $gender, $role);

    if ($stmtUser->execute()) {
        echo "<h1>회원가입 결과</h1>";
        echo "<p class='success'>" . htmlspecialchars($user_name) . "님 회원가입이 성공적으로 완료되었습니다.</p>";

        if ($role == "owner") {
            $updateSql = "UPDATE branch SET ownerID = ? WHERE branch_name = ?";
            $stmtBranch = $con->prepare($updateSql);
            $stmtBranch->bind_param("ss", $userID, $selectedBranch);

            if ($stmtBranch->execute()) {
                echo "<br><p>지점 : " . htmlspecialchars($selectedBranch) . "의 사장님으로 회원가입이 되었습니다.</p>";
            } else {
                echo "<br><p>지점 업데이트에 실패하였습니다.</p>";
            }

            $stmtBranch->close();
        }

        // 성공 시 메인화면으로 가는 버튼
        echo "<div class='back-button'>";
        echo "<a href='무인편의점_main.php'>처음으로</a><br><br>";
        echo "<a href='무인편의점_로그인화면.php'>로그인 하기</a>";
        echo "</div>";
    } else {
        echo "<h1>회원가입 결과</h1>";
        echo "<p class='failure'>이미 존재하는 아이디입니다.<br>다시 가입해주세요.";

        // 실패 시 다시 회원가입화면으로 가는 버튼
        echo "<div class='back-button'>";
        echo "<a href='무인편의점_회원가입화면.php'>다시 회원가입하기</a>";
        echo "</div>";
    }

    $stmtUser->close();
    mysqli_close($con);
    ?>
</div>
</body>
</html>
