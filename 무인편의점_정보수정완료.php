<!DOCTYPE html>
<html lang="ko">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>회원 정보 수정 결과</title>
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
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: inline-block;
            text-align: left; 
        }

        .success {
            color: black;
            font-weight: bold;
            font-size: 18px;
        }

        .failure {
            color: red;
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
            padding: 8px 10px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            display: inline-block;
            transition: background-color 0.3s, color 0.3s
        }
        
        .back-button a:hover {
            background-color: #66b2ff;
            color: white;
        }
        .input-container label {
            color: #005f73;
        }

        input[type="text"] {
            flex: 2;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #005f73; 
            box-sizing: border-box;
            transition: border-color 0.3s;
            background-color: white; 
            color: #005f73; 
            font-size: 16px;
        }
        
    </style>
</head>
<body>
    <h1>회원 정보 수정 결과</h1>
    <div class="result">
        <?php
        $con = mysqli_connect("localhost", "root", "1234", "cvDB") or die("MySQL 접속 실패");

        $userID = $_POST["userID"];
        $password = $_POST['password'];
        $user_name = $_POST['user_name'];
        $mobile = $_POST['mobile'];
        $birthyear = $_POST['birthyear'];
        $gender = $_POST['gender'];
        $role = $_POST['role'];

        $sql = "UPDATE userTBL SET user_name = '".$user_name."', mobile = '".$mobile."', birthyear = '".$birthyear."' WHERE password = '".$password."'";

        $ret = mysqli_query($con, $sql);

        if ($ret)
        {
            echo "<p class='success'>고객님의 정보가 수정되었습니다.</p>";
        }
        else
        {
            echo "<p class='failure'>데이터 수정 실패<br>실패 원인 : ".mysqli_error($con)."</p>";
        }

        mysqli_close($con);
        ?>
        <div class="back-button">
            <a href="무인편의점_main.php">돌아가기</a>
        </div>
    </div>
</body>
</html>
