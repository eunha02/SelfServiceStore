<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>물품 검색</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
        }

        form {
            background-color: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: center;
        }

        label, input[type="text"] {
            display: block;
            margin: 0 auto;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 4px;
            font-weight: bold;
        }

        input[type="text"] {
            width: calc(100% - 22px);
            padding: 6px;
            margin-bottom: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            display: inline-block;
        }

        input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: inline-block;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        button {
            background-color: #3498db;
            color: #fff;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
<?php
    $servername = "localhost";
    $username = "root";
    $password = "1234";
    $dbname = "cvDB";

    if (!isset($_GET['ownerID'])) {
        die("ownerID URL 매개변수가 설정되지 않았습니다.");
    }
    $ownerID = $_GET['ownerID'];

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $branchSql = "SELECT branchID FROM branch WHERE ownerID = ?";
    $branchStmt = $conn->prepare($branchSql);
    $branchStmt->bind_param("s", $ownerID);
    $branchStmt->execute();
    $branchResult = $branchStmt->get_result();

    echo "<h1>지점 재고 조회</h1>";
    function getCategory($productId) {
        $categories = [
            'A' => '과자', 'B' => '라면', 'C' => '탄산/커피/차/숙취해소음료', 
            'D' => '생수', 'E' => '빵/디저트', 'F' => '유제품', 
            'G' => '도시락', 'H' => '김밥/주먹밥', 'I' => '샌드위치/버거',
            'J' => '빵/떡', 'K' => '디저트', 'L' => '샐러드', 
            'M' => '냉동간편식', 'N' => '아이스크림', 'O' => '초콜릿', 
            'P' => '캔디/젤리/껌', 'Q' => '쿠키', 'R' => '안주', 
            'S' => '취미/완구', 'T' => '위생/미용용품', 'U' => '의약/건강', 
            'V' => '반려용품', 'W' => '생활잡화'
        ];
        $lastChar = strtoupper(substr($productId, -1));
        return $categories[$lastChar] ?? '기타';
    }
    
    if ($branchResult->num_rows > 0) {
        $branchRow = $branchResult->fetch_assoc();
        $branchID = $branchRow['branchID'];

        // 물품 이름 검색 폼
        echo "<form action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' method='get'>";
        echo "<input type='hidden' name='ownerID' value='" . htmlspecialchars($ownerID) . "'>";
        echo "<label for='product_name'>물품이름:</label>";
        echo "<input type='text' name='product_name' id='product_name' value='" . htmlspecialchars(isset($_GET['product_name']) ? $_GET['product_name'] : "") . "'>";
        echo "<input type='submit' value='검색'>";
        echo "</form>";

        // 카테고리 선택 폼
        echo "<form action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' method='get'>";
        echo "<input type='hidden' name='ownerID' value='" . htmlspecialchars($ownerID) . "'>";
        echo "<label for='category'>카테고리 선택:</label>";
        echo "<select name='category' id='category'>";
        echo "<option value=''>모든 카테고리</option>";
        echo "<option value='A'>과자</option>";
        echo "<option value='B'>라면</option>";
        echo "<option value='C'>탄산/커피/차/숙취해소음료</option>";
        echo "<option value='D'>생수</option>";
        echo "<option value='E'>빵/디저트</option>";
        echo "<option value='F'>유제품</option>";
        echo "<option value='G'>도시락</option>";
        echo "<option value='H'>김밥/주먹밥</option>";
        echo "<option value='I'>샌드위치/버거</option>";
        echo "<option value='J'>빵/떡</option>";
        echo "<option value='K'>디저트</option>";
        echo "<option value='L'>샐러드</option>";
        echo "<option value='M'>냉동간편식</option>";
        echo "<option value='N'>아이스크림</option>";
        echo "<option value='O'>초콜릿</option>";
        echo "<option value='P'>캔디/젤리/껌</option>";
        echo "<option value='Q'>쿠키</option>";
        echo "<option value='R'>안주</option>";
        echo "<option value='S'>취미/완구</option>";
        echo "<option value='T'>위생/미용용품</option>";
        echo "<option value='U'>의약/건강</option>";
        echo "<option value='V'>반려용품</option>";
        echo "<option value='W'>생활잡화</option>";

        echo "</select>";
        echo "<input type='submit' value='검색'>";
        echo "</form>";

        $selectedProductName = isset($_GET['product_name']) ? $_GET['product_name'] : null;
        $selectedCategory = isset($_GET['category']) ? $_GET['category'] : null;

        // 정렬 매개변수 설정
        $sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'w.amount';
        $sortDirection = isset($_GET['sort']) ? $_GET['sort'] : 'asc';

        // SQL 쿼리 수정
        $sql = "SELECT p.productID, p.product_name, w.amount, 
        CASE 
            WHEN RIGHT(p.productID, 1) = 'A' THEN '과자'
            WHEN RIGHT(p.productID, 1) = 'B' THEN '라면'
            WHEN RIGHT(p.productID, 1) = 'C' THEN '탄산/커피/차/숙취해소음료'
            WHEN RIGHT(p.productID, 1) = 'D' THEN '생수'
            WHEN RIGHT(p.productID, 1) = 'E' THEN '빵/디저트'
            WHEN RIGHT(p.productID, 1) = 'F' THEN '유제품'
            WHEN RIGHT(p.productID, 1) = 'G' THEN '도시락'
            WHEN RIGHT(p.productID, 1) = 'H' THEN '김밥/주먹밥'
            WHEN RIGHT(p.productID, 1) = 'I' THEN '샌드위치/버거'
            WHEN RIGHT(p.productID, 1) = 'J' THEN '빵/떡'
            WHEN RIGHT(p.productID, 1) = 'K' THEN '디저트'
            WHEN RIGHT(p.productID, 1) = 'L' THEN '샐러드'
            WHEN RIGHT(p.productID, 1) = 'M' THEN '냉동간편식'
            WHEN RIGHT(p.productID, 1) = 'N' THEN '아이스크림'
            WHEN RIGHT(p.productID, 1) = 'O' THEN '초콜릿'
            WHEN RIGHT(p.productID, 1) = 'P' THEN '캔디/젤리/껌'
            WHEN RIGHT(p.productID, 1) = 'Q' THEN '쿠키'
            WHEN RIGHT(p.productID, 1) = 'R' THEN '안주'
            WHEN RIGHT(p.productID, 1) = 'S' THEN '취미/완구'
            WHEN RIGHT(p.productID, 1) = 'T' THEN '위생/미용용품'
            WHEN RIGHT(p.productID, 1) = 'U' THEN '의약/건강'
            WHEN RIGHT(p.productID, 1) = 'V' THEN '반려용품'
            WHEN RIGHT(p.productID, 1) = 'W' THEN '생활잡화'
            ELSE '기타'
        END AS category
        FROM product p 
        JOIN warehouse w ON p.productID = w.productID
        WHERE w.branchID = ?";


        $queryParams = [$branchID];
        if ($selectedProductName !== null) {
            $sql .= " AND p.product_name LIKE ?";
            $queryParams[] = "%$selectedProductName%";
        }

        if (!empty($selectedCategory)) {
            $sql .= " AND RIGHT(p.productID, 1) = ?";
            $queryParams[] = $selectedCategory;
        }

        // 정렬 방향에 따라 ORDER BY 추가
        $sql .= " ORDER BY " . ($sortColumn === 'category' ? 'category' : $sortColumn) . " " . ($sortDirection === 'asc' ? 'ASC' : 'DESC');

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(str_repeat("s", count($queryParams)), ...$queryParams);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<table>";
        echo "<tr>
                <th><a href='?ownerID=$ownerID&sortColumn=category&sort=" . ($sortDirection === 'asc' ? 'desc' : 'asc') . "'>카테고리</a></th>
                <th><a href='?ownerID=$ownerID&sortColumn=productID&sort=" . ($sortDirection === 'asc' ? 'desc' : 'asc') . "'>물품 ID</a></th>
                <th><a href='?ownerID=$ownerID&sortColumn=product_name&sort=" . ($sortDirection === 'asc' ? 'desc' : 'asc') . "'>물품 이름</a></th>
                <th><a href='?ownerID=$ownerID&sortColumn=amount&sort=" . ($sortDirection === 'asc' ? 'desc' : 'asc') . "'>수량</a></th>
              </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . getCategory($row['productID']) . "</td>";
            echo "<td>" . $row['productID'] . "</td>";
            echo "<td>" . $row['product_name'] . "</td>";
            echo "<td>" . $row['amount'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "지점에 재고 데이터가 없습니다.";
    }

    echo "<button onclick='goBack()'>뒤로 가기</button>";
    echo "<script>
        function goBack() {
            window.history.back();
        }
    </script>";

    $branchStmt->close();
    $conn->close();
    ?>
    <script>
    // 테이블 정렬 함수
    function sortTable(tableId, column, order) {
        var table, rows, switching, i, x, y, shouldSwitch;
        table = document.getElementById(tableId);
        switching = true;
        
        while (switching) {
            switching = false;
            rows = table.rows;

            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                x = rows[i].getElementsByTagName("td")[column];
                y = rows[i + 1].getElementsByTagName("td")[column];

                // 정렬 방향에 따라 비교
                if (order === 'asc') {
                    if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                } else {
                    if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }

            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
            }
        }
    }
</script>
</body>
</html>