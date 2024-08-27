<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>매출 상세 조회</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            text-align: center;
        }
        h1 {
            color: #444;
        }
        table {
            margin-left: auto;
            margin-right: auto;
            border-collapse: collapse;
            width: 80%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #8c8c8c;
            color: white;
        }
        tr:nth-child(even){background-color: #f2f2f2;}
        tr:hover {background-color: #ddd;}
        a {
            text-decoration: none;
            color: #5a5a5a;
        }
        a:hover {
            color: #000;
        }
        .back-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>매출 상세 조회</h1>

    <?php
    // 데이터베이스 연결 설정
    $servername = "localhost";
    $username = "root";
    $password = "1234";
    $dbname = "cvDB";
    $owner_id = $_GET['ownerID'] ?? '';
    $year = $_GET['year'] ?? '';
    $month = $_GET['month'] ?? '';

    // 데이터베이스 연결
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 연결 확인
    if ($conn->connect_error) {
        die("MySQL 접속 실패: " . $conn->connect_error);
    }

    // 매출 상세 조회 쿼리
    $sql = "SELECT p.product_name, p.price, SUM(b.amount) as sold_amount, DATE(b.buy_date) as buy_date
            FROM buy b
            JOIN product p ON b.productID = p.productID
            WHERE YEAR(b.buy_date) = ? AND MONTH(b.buy_date) = ? AND b.branchID IN (SELECT branchID FROM branch WHERE ownerID = ?)
            GROUP BY p.product_name, p.price, DATE(b.buy_date)
            ORDER BY DATE(b.buy_date)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iis", $year, $month, $owner_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // 결과 출력
        echo "<table>";
        echo "<tr><th>상품명</th><th>판매 가격</th><th>판매 수량</th><th>판매 날짜</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
            echo "<td>" . htmlspecialchars(number_format($row['price'])) . "원</td>";
            echo "<td>" . htmlspecialchars($row['sold_amount']) . "개</td>";
            echo "<td>" . htmlspecialchars($row['buy_date']) . "</td>";
            echo "</tr>";
        }

        echo "</table>";
        $stmt->close();
    } else {
        echo "SQL 쿼리 준비 오류: " . $conn->error;
    }
    echo "<button onclick='goBack()'>뒤로 가기</button>";
    echo "<script>
        function goBack() {
            window.history.back();
        }
    </script>";

    $conn->close();
    ?>



</body>
</html>
