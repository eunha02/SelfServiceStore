<?php
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "cvDB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT branchID, branch_name FROM branch WHERE ownerID IS NULL OR ownerID = ''";
$result = $conn->query($sql);

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>지점 선택</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
            text-align: center;
            padding: 40px;
            margin: 0;
        }

        h1 {
            color: #005f73;
            margin-bottom: 20px;
        }

        form {
            background-color: #fff;
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
            color: #333;
        }

        .select-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        select {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-left: 10px;
        }

        input[type="submit"] {
            background-color: #008cba;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: #66b2ff;
        }
    </style>
</head>
<body>
    <h1>지점을 선택하세요</h1>
    <form method="post" action="무인편의점_회원가입완료.php">
        <div class="select-container">
            <label for="branch">지점선택:</label>
            <select id="branch" name="branch">
                <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['branch_name'] . "'>" . $row['branch_name'] . "</option>";
                    }
                ?>
            </select>
        </div>
        <input type="hidden" name="userID" value="<?php echo $_POST['userID']; ?>">
        <input type="hidden" name="password" value="<?php echo $_POST['password']; ?>">
        <input type="hidden" name="user_name" value="<?php echo $_POST['user_name']; ?>">
        <input type="hidden" name="mobile" value="<?php echo $_POST['mobile']; ?>">
        <input type="hidden" name="birthyear" value="<?php echo $_POST['birthyear']; ?>">
        <input type="hidden" name="gender" value="<?php echo $_POST['gender']; ?>">
        <input type="hidden" name="role" value="<?php echo $_POST['role']; ?>">
        <input type="submit" value="지점 선택 완료">
    </form>
</body>
</html>
