<?php
$con1 = mysqli_connect("localhost", "root", "1234", "cvDB") or die("MySQL 접속 실패");

$userID = $_POST['userID'];
$password = $_POST['password'];

$sql = "SELECT role FROM usertbl WHERE userID = ? AND password = ?";
$stmt = $con1->prepare($sql);

if (!$stmt) {
    die("Prepare failed: (" . $con1->errno . ") " . $con1->error);
}

$stmt->bind_param("ss", $userID, $password);
$stmt->execute();
$result = $stmt->get_result();

$loginError = false;

if ($row = $result->fetch_assoc()) {
    if ($row['role'] == 'customer') {
        header("Location: customer_page.php?userID=" . urlencode($userID));
        exit();
    } elseif ($row['role'] == 'owner') {
        header("Location: owner_page.php?userID=" . urlencode($userID));
        exit();
    } else {
        $loginError = true;
        $errorMessage = "아이디 또는 비밀번호가 일치하지 않습니다. 다시 입력해주세요.";
    }
} else {
    $loginError = true;
    $errorMessage = "아이디 또는 비밀번호가 일치하지 않습니다. 다시 입력해주세요.";
}

$stmt->close();
$con1->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>Login Error</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 10px;
        }
        .confirm-btn {
            background-color: #008cba;
            color: white;
            border: none;
            padding: 15px 30px;
            margin-top: 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 16px;
            font-weight: bold;
        }
        .confirm-btn:hover {
            background-color: #00b0ff;
        }
        .close {
            position: absolute; /* 절대 위치 */
            top: 10px; /* 상단에서 10px */
            right: 10px; /* 우측에서 10px */
            color: #aaa;
            font-size: 25px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
    </style>
</head>
<body>
    <?php if ($loginError): ?>
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="redirectToMain()">&times;</span>
                <p><?php echo $errorMessage; ?></p>
                <button class="confirm-btn" onclick="redirect()">확인</button>
            </div>
        </div>

        <script>
            var modal = document.getElementById("myModal");
            modal.style.display = "block";

            function redirect() {
                window.location = '무인편의점_로그인화면.php'; // 로그인 페이지 URL
            }

            function redirectToMain() {
                window.location = '무인편의점_main.php'; // 메인 페이지 URL
            }
        </script>
    <?php endif; ?>
</body>
</html>
