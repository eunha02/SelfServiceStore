<!DOCTYPE html>
<html lang="ko">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>회원 정보 수정</title>
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
            text-align: left; /* 입력란 텍스트 왼쪽 정렬 */
        }

        .input-container {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .input-container label {
            flex: 1;
            margin-right: 10px;
        }

        input[type="text"] {
            flex: 2;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            box-sizing: border-box;
            transition: border-color 0.3s;
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
    <h1>회원 정보 수정</h1>
    <?php
    $con = mysqli_connect("localhost", "root", "1234", "cvDB") or die("MySQL 접속 실패");

    $sql = "SELECT * FROM userTBL WHERE password ='".$_GET['password']."'";

    $ret = mysqli_query($con, $sql);
    if ($ret)
    {
        $count = mysqli_num_rows($ret);

        if ($count == 0)
        {
            echo $_GET['password']." 비밀번호를 다시 입력해주세요 !"."<br>";
            echo "<br> <a href = '무인편의점_main.php'> <-- 초기 화면</a>";
            exit();
        }
    }

    else
    {
        echo "데이터 조회 실패 !"."<br>";
        echo "실패 원인 : ".mysqli_error($con);
        echo "<br> <a href = 'main.php'> <-- 초기 화면 </a>";
        exit();
    }

    $row = mysqli_fetch_array($ret);
    $userID = $row['userID'];
    $password = $row['password'];
    $user_name = $row['user_name'];
    $mobile = $row['mobile'];
    $birthyear = $row['birthyear'];
    $gender = $row['gender'];
    $role = $row['role'];
    ?>

    <form method="post" action="무인편의점_정보수정완료.php">
        <div class="input-container">
            <label for="userID">아이디:</label>
            <input type="text" id="userID" name="userID" value="<?php echo $userID ?>" readonly>
        </div>
        <div class="input-container">
            <label for="password">비밀번호:</label>
            <input type="text" id="password" name="password" value="<?php echo $password ?>" readonly>
        </div>
        <div class="input-container">
            <label for="user_name">이름:</label>
            <input type="text" id="user_name" name="user_name" value="<?php echo $user_name ?>">
        </div>
        <div class="input-container">
            <label for="mobile">전화번호:</label>
            <input type="text" id="mobile" name="mobile" value="<?php echo $mobile ?>">
        </div>
        <div class="input-container">
            <label for="birthyear">출생년도:</label>
            <input type="text" id="birthyear" name="birthyear" value="<?php echo $birthyear ?>">
        </div>
        <div class="input-container">
            <label for="gender">성별:</label>
            <input type="text" id="gender" name="gender" value="<?php echo $gender ?>" readonly>
        </div>
        <div class="input-container">
            <label for="role">역할:</label>
            <input type="text" id="role" name="role" value="<?php echo $role ?>" readonly>
        </div>
        <br><br>
        <input type="submit" value="수정 완료">
    </form>
</body>
</html>
