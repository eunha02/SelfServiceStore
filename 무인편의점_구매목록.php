<?php
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "cvDB";
$userID = isset($_GET['userID']) ? $_GET['userID'] : '';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$nameSql = "SELECT user_name FROM usertbl WHERE userID = ?";
$nameStmt = $conn->prepare($nameSql);
$nameStmt->bind_param("s", $userID);
$nameStmt->execute();
$nameResult = $nameStmt->get_result();
$userName = '';
if ($nameRow = $nameResult->fetch_assoc()) {
    $userName = $nameRow['user_name'];
}
$nameStmt->close();
// 물품 검색 쿼리 추가
$productSearch = isset($_GET['productSearch']) ? $_GET['productSearch'] : '';

// 사용자가 구매한 구매 목록 가져오기
$sql = "SELECT 
    br.branch_name,
    p.product_name,
    b.amount,
    p.price as product_price,
    b.buy_date
FROM 
    buy b
INNER JOIN product p ON b.productID = p.productID
INNER JOIN branch br ON b.branchID = br.branchID
WHERE b.userID = ?";
// 날짜 내림차순으로 정렬 (가장 최근 구매가 먼저 나타남)
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userID);
$stmt->execute();
$result = $stmt->get_result();

// 지점 목록 가져오기
$branchSql = "SELECT DISTINCT branch_name FROM branch";
$branchStmt = $conn->prepare($branchSql);
$branchStmt->execute();
$branchResult = $branchStmt->get_result();

// 물품명 목록 가져오기
$productSql = "SELECT DISTINCT product_name FROM product";
$productStmt = $conn->prepare($productSql);
$productStmt->execute();
$productResult = $productStmt->get_result();


$branchSelected = isset($_GET['branch']) ? $_GET['branch'] : 'all';
$productSelected = isset($_GET['product']) ? $_GET['product'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'buy_date';
$order = isset($_GET['order']) && $_GET['order'] == 'ASC' ? 'ASC' : 'DESC';

$sql = "SELECT br.branch_name, p.product_name, b.amount, p.price as product_price, b.buy_date
        FROM buy b
        INNER JOIN product p ON b.productID = p.productID
        INNER JOIN branch br ON b.branchID = br.branchID
        WHERE b.userID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userID);
$stmt->execute();

$result = $stmt->get_result();

if ($branchSelected != 'all') {
    $sql .= " AND br.branch_name = ?";
}
if (!empty($productSearch) && $productSearch != 'all') { 
    $sql .= " AND p.product_name = ?"; 
}
if ($productSelected != 'all') {
    $sql .= " AND p.product_name = ?";
}

$sql .= " ORDER BY $sort $order";

if ($branchSelected != 'all' || (!empty($productSearch) && $productSearch != 'all')) { 
    $stmt = $conn->prepare($sql);
    if ($branchSelected != 'all' && !empty($productSearch) && $productSearch != 'all') { 
        $stmt->bind_param("sss", $userID, $branchSelected, $productSearch); 
    } elseif ($branchSelected != 'all') {
        $stmt->bind_param("ss", $userID, $branchSelected);
    } elseif (!empty($productSearch) && $productSearch != 'all') { 
        $stmt->bind_param("ss", $userID, $productSearch); 
    }
} else {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userID);
}

$stmt->execute();
$result = $stmt->get_result();

$totalSql = "SELECT SUM(b.amount * p.price) as total_price
             FROM buy b
             INNER JOIN product p ON b.productID = p.productID
             WHERE b.userID = ?";
$totalStmt = $conn->prepare($totalSql);
$totalStmt->bind_param("s", $userID);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();

// 최근 3개월 간의 구매 총액 계산
$recentTotalSql = "SELECT SUM(b.amount * p.price) as recent_total_price
                   FROM buy b
                   INNER JOIN product p ON b.productID = p.productID
                   WHERE b.userID = ? AND b.buy_date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
$recentTotalStmt = $conn->prepare($recentTotalSql);
$recentTotalStmt->bind_param("s", $userID);
$recentTotalStmt->execute();
$recentTotalResult = $recentTotalStmt->get_result();
$recentTotalRow = $recentTotalResult->fetch_assoc();
$recentTotalPrice = $recentTotalRow['recent_total_price'];
$recentTotalStmt->close();

// 고객의 전체 구매 총액 계산
$totalSql = "SELECT SUM(b.amount * p.price) as total_price
             FROM buy b
             INNER JOIN product p ON b.productID = p.productID
             WHERE b.userID = ?";
$totalStmt = $conn->prepare($totalSql);
$totalStmt->bind_param("s", $userID);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalPrice = $totalRow['total_price'];
$totalStmt->close();

$grade = "GREEN"; // 기본 등급
$color = "green"; // 기본 색상
if ($totalPrice > 300000) {
    $grade = "VIP";
    $color = "orange";
} elseif ($totalPrice > 200000) {
    $grade = "GOLD";
    $color = "yellow";
} elseif ($totalPrice > 100000) {
    $grade = "SILVER";
    $color = "grey";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>구매 목록</title>
    <style>
        body {
            font-family: 'Noto Sans KR', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* 왼쪽 정렬로 변경 */
        }

        .title {
            text-align: left;
            margin: 0;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .return-link {
            margin-top: 20px;
            text-align: left;
        }

        .search-forms-container {
            display: flex;
            flex-direction: column; /* 세로 정렬로 변경 */
            align-items: flex-start; /* 왼쪽 정렬로 변경 */
            width: 100%;
        }

        .search-forms {
            display: flex;
            flex-direction: row; /* 가로 정렬로 변경 */
            align-items: center;
            gap: 10px;
        }

        .search-form label {
            display: block;
        }

        .search-form select {
            margin-bottom: 10px;
        }

        .search-button {
            display: flex;
            align-items: center;
            margin-left: 10px;
        }

        .dashboard {
    display: grid;
    grid-template-columns: 11fr 2fr; /* Two equal columns */
    gap: 20px; /* Space between columns */
    align-items: center; /* Vertically center the items in the grid */
}

.table-container {
    overflow-x: auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: auto; 
    margin-bottom: 10px; /* 아래쪽 마진 유지 */
}

table {
    width: 100%; /* 테이블의 너비를 컨테이너 너비에 맞게 100%로 설정 */
    border-collapse: collapse;
    margin-bottom: 20px;
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

        th {
            background-color: #e3f2fd;
            color: #212529;
        }

        tr:nth-child(even) {
            background-color: #fff;
        }

        .user-info {
    background-color: #fff;
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    align-self: end; /* Align the element to the end of the grid container */
    display: flex; /* This makes the child divs stack vertically */
    flex-direction: column; /* Align children in a column */
    margin-bottom: 10px; /* Space between the table and this section */
}
        .grade, .total {
            padding: 10px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 10px;
            font-size: 20px; /* 크기 키우기 */
        }

        .total {
            font-weight: bold;
            margin-top: 10px;
        }

        .grade {
            color: green;
        }

        .grade-reset-message {
            color: red;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="title"><?php echo htmlspecialchars($userName); ?>님의 구매 목록</div>
<div class="search-forms-container">
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
        <input type="hidden" name="userID" value="<?php echo htmlspecialchars($userID); ?>">
        <!-- 지점 선택 부분 -->
        <div class="search-form">
            <label for="branch">지점 선택:</label>
            <select name="branch" id="branch">
                <option value="all">전체</option>
                <?php
                if ($branchResult) {
                    while ($branchRow = $branchResult->fetch_assoc()) {
                        $selected = ($branchSelected == $branchRow['branch_name']) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($branchRow['branch_name']) . '"' . $selected . '>';
                        echo htmlspecialchars($branchRow['branch_name']);
                        echo '</option>';
                    }
                }
                ?>
            </select>
            <!-- 선택한 지점 표시 -->
            <span><?php echo htmlspecialchars($branchSelected); ?></span>
            <!-- 지점 선택 버튼 -->
            <input type="submit" value="선택">
        </div>

        <!-- 물품명 검색 부분 -->
        <div class="search-form">
            <label for="productSearch">물품명 검색:</label>
            <input type="text" name="productSearch" id="productSearch" placeholder="물품명을 입력하세요">
            <!-- 물품명 검색 버튼 -->
            <input type="submit" value="검색">
        </div>
    </form>
</div>

<div class="grade-reset-message">
    <?php if (isset($gradeResetMessage)) echo htmlspecialchars($gradeResetMessage); ?>
</div>

<div class="dashboard">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th onclick="sortTable('branch_name')">지점</th>
                    <th onclick="sortTable('product_name')">물품명</th>
                    <th onclick="sortTable('amount')">구매개수</th>
                    <th onclick="sortTable('product_price')">금액</th>
                    <th onclick="sortTable('buy_date')">구매 날짜</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row["branch_name"]); ?></td>
                            <td><?php echo htmlspecialchars($row["product_name"]); ?></td>
                            <td><?php echo htmlspecialchars($row["amount"]); ?></td>
                            <td><?php echo htmlspecialchars($row["product_price"]); ?></td>
                            <td><?php echo htmlspecialchars($row["buy_date"]); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">구매한 데이터가 없습니다.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="user-info">
        <div class="grade">등급: <?php echo htmlspecialchars($grade); ?></div>
        <div class="total">총합: <?php echo number_format($totalPrice); ?>원</div>
        <div class="recent-total">최근 3개월 총합: <?php echo number_format($recentTotalPrice); ?>원</div>
    </div>
</div>
<div class="return-link">
    <a href='customer_page.php'>이전으로</a>
</div>
<script>
    // 정렬 기능을 위한 JavaScript
    function sortTable(column) {
        var currentOrder = '<?php echo $order; ?>';
        var newOrder = currentOrder === 'ASC' ? 'DESC' : 'ASC';
        var searchParams = new URLSearchParams(window.location.search);
        searchParams.set('sort', column);
        searchParams.set('order', newOrder);
        window.location.href = window.location.pathname + '?' + searchParams.toString();
    }
</script>
</body>
</html>
