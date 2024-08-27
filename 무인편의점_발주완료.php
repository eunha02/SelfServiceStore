<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta htt-equiv="content-type" content="text/html; charset=utf-8">
    <title>발주 완료</title>
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

        /* Button Styling */
        button {
            display: inline-block;
            background-color: #008cba;
            color: white;
            padding: 10px 20px;
            margin: 10px 5px;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        button:hover {
            background-color: #66b2ff;
            transform: translateY(-3px);
        }

        /* Product List Styling */
        .product-list {
            list-style: none;
            padding: 0;
        }

        .product-item {
            text-align: left;
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>발주 완료</h1>
        <ul class="product-list">
            <?php
            // 데이터베이스 연결 설정
            $servername = "localhost";
            $username = "root";
            $password = "1234";
            $dbname = "cvDB";

            $con = mysqli_connect($servername, $username, $password, $dbname);

            if (!$con) {
                die("MySQL 접속 실패: " . mysqli_connect_error());
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $orderAmounts = $_POST["order_amount"];
                $totalOrdered = 0;

                foreach ($orderAmounts as $productID => $amount) {
                    $amount = (int)$amount;
                
                    if ($amount <= 0) {
                        continue;
                    }
                
                    $sql = "UPDATE warehouse SET amount = amount + ? WHERE productID = ?";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("is", $amount, $productID);
                
                    if (!$stmt->execute()) {
                        echo "오류 발생: " . $con->error;
                        exit;
                    }
                
                    $totalOrdered += $amount;
                
                    $con->commit();
                
                    $newAmountSql = "SELECT amount FROM warehouse WHERE productID = ?";
                    $newAmountStmt = $con->prepare($newAmountSql);
                    $newAmountStmt->bind_param("i", $productID);
                    $newAmountStmt->execute();
                    $newAmountResult = $newAmountStmt->get_result();
                    if ($row = $newAmountResult->fetch_assoc()) {
                        $newAmount = $row["amount"];
                        
                        // 상품 명을 가져오는 SQL 쿼리 추가
                        $productNameSql = "SELECT product_name FROM product WHERE productID = ?";
                        $productNameStmt = $con->prepare($productNameSql);
                        $productNameStmt->bind_param("i", $productID);
                        $productNameStmt->execute();
                        $productNameResult = $productNameStmt->get_result();
                        if ($productNameRow = $productNameResult->fetch_assoc()) {
                            $productName = $productNameRow["product_name"];
                        } else {
                            $productName = "상품명을 가져올 수 없음";
                        }
                
                        echo "<li class='product-item'>";
                        echo "상품 ID: {$productID}<br>";
                        echo "상품 명: {$productName}<br>"; // 수정된 부분
                        echo "발주 수량: {$amount}개<br>";
                        echo "총 재고 수량: {$newAmount}개<br>";
                        echo "</li>";
                    }
                }

                echo "<br><p>총 {$totalOrdered}개의 상품이 발주되었습니다.</p>";
            }
            ?>
        </ul>
        <br>
        <button onclick="goBack()">뒤로 가기</button>
        <button onclick="main()">처음으로</button>
    </div>
    <script>
        function goBack() {
            window.history.back();
        }

        function main() {
            window.location.href = '무인편의점_main.php';
        }
    </script>
</body>
</html>
