<!DOCTYPE html>
<html lang="ko">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>회원가입</title>
    <script type="text/javascript">
        function handleFormSubmit() 
        {
            var role = document.getElementById('role').value;
            var form = document.getElementById('registrationForm');
            if (role === 'owner') 
            {
                form.action = '무인편의점_key_value.php';
            } else 
            {
                form.action = '무인편의점_회원가입완료.php';
            }
        }
    </script>
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

        #registrationForm {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: inline-block;
            text-align: left;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: inline-block;
            margin-right: 10px;
            width: 160px;
            color: #005f73;
        }

        .form-group input, .form-group select {
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            box-sizing: border-box;
            width: 100%;
        }

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
    <h1>회원가입</h1>
    <form method="post" id="registrationForm" onsubmit="handleFormSubmit()">
        <div class="form-group">
            <label for="userID">아이디 :</label>
            <input type="text" name="userID">
        </div>
        <div class="form-group">
            <label for="password">비밀번호 :</label>
            <input type="password" name="password">
        </div>
        <div class="form-group">
            <label for="user_name">이름 :</label>
            <input type="text" name="user_name">
        </div>
        <div class="form-group">
            <label for="mobile">전화번호 :</label>
            <input type="text" name="mobile">
        </div>
        <div class="form-group">
            <label for="birthyear">출생년도 :</label>
            <input type="text" name="birthyear">
        </div>
        <div class="form-group">
            <label for="gender">성별 :</label>
            <select name="gender">
                <option value="male">남자</option>
                <option value="female">여자</option>
            </select>
        </div>
        <div class="form-group">
            <label for="role">역할 :</label>
            <select name="role" id="role">
                <option value="customer">고객</option>
                <option value="owner">사장님</option>
            </select>
        </div>
        <input type="submit" value="회원가입">
    </form>
</body>
</html>
