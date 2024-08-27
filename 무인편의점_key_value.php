<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
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
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            display: inline-block;
            text-align: left;
            width: 400px;
            margin: 0 auto;
        }

        label {
            font-weight: bold;
        }

        .input-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .input-container input[type="text"] {
            width: 70%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button[type="button"], input[type="submit"] {
            background-color: #008cba;
            color: white;
            border: none;
            padding: 7px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        button[type="button"]:hover, input[type="submit"]:hover {
            background-color: #66b2ff;
        }

        .alert {
            font-weight: bold;
            color: red;
        }
    </style>
    <script type="text/javascript">
        function verifyKey() {
            var inputKey = document.getElementById('inputKey').value;
            if (inputKey === 'muin') {
                alert('인증완료');
                document.getElementById('verificationAlert').style.display = 'none';
                document.getElementById('nextButton').style.display = 'inline-block'; 
                document.getElementById('keyForm').action = '무인편의점_지점선택.php'; 
            } else {
                document.getElementById('verificationAlert').style.display = 'block';
                document.getElementById('nextButton').style.display = 'none'; 
            }
        }
    </script>
</head>
<body>
    <h1>인증키를 입력하세요</h1>
    <form id="keyForm" method="post">
        <div class="input-container">
            <label for="inputKey">인증키:</label>
            <input type="text" id="inputKey" name="key">
        </div>
        <input type="hidden" name="userID" value="<?php echo $_POST['userID']; ?>">
        <input type="hidden" name="password" value="<?php echo $_POST['password']; ?>">
        <input type="hidden" name="user_name" value="<?php echo $_POST['user_name']; ?>">
        <input type="hidden" name="mobile" value="<?php echo $_POST['mobile']; ?>">
        <input type="hidden" name="birthyear" value="<?php echo $_POST['birthyear']; ?>">
        <input type="hidden" name="gender" value="<?php echo $_POST['gender']; ?>">
        <input type="hidden" name="role" value="<?php echo $_POST['role']; ?>"><br>
        <button type="button" onclick="verifyKey()">확인</button>
        <span id="verificationAlert" class="alert" style="display: none;">인증키가 올바르지 않습니다.</span><br><br>
        <input id="nextButton" style="display: none;" type="submit" value="Next"> 
    </form>
</body>
</html>
