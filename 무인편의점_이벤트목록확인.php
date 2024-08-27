<?php
// 데이터베이스 연결
$con = mysqli_connect("localhost", "root", "1234", "cvDB") or die("MySQL 접속 실패");

// GET 파라미터 처리
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'event_listID';
$order = isset($_GET['order']) && $_GET['order'] === 'DESC' ? 'DESC' : 'ASC';
$branch = isset($_GET['branch']) ? $_GET['branch'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$eventType = isset($_GET['event_type']) ? $_GET['event_type'] : '';

// 기본 SQL 쿼리
$sql = "SELECT e.event_listID, e.eventlist_info, e.product_name, e.startDate, e.endDate, b.branch_name, p.productID 
        FROM event_list e
        INNER JOIN branch b ON e.branchID = b.branchID
        INNER JOIN product p ON e.product_name = p.product_name
        WHERE 1";

// 지점 필터링
if (!empty($branch)) {
    $sql .= " AND b.branch_name = '" . mysqli_real_escape_string($con, $branch) . "'";
}

// 카테고리 필터링
if (!empty($category)) {
    $categoryCondition = " AND p.productID LIKE '%" . mysqli_real_escape_string($con, $category) . "'";
    $sql .= $categoryCondition;
}

// 이벤트 타입 필터링
if (!empty($eventType)) {
    $sql .= " AND e.eventlist_info LIKE '%" . mysqli_real_escape_string($con, $eventType) . "%'";
}

$sql .= " ORDER BY $sort $order";

$ret = mysqli_query($con, $sql);
if (!$ret) {
    die("쿼리 실행 실패: " . mysqli_error($con));
}

function generateSortLink($currentSort, $currentOrder, $newSortField) {
    $newOrder = ($currentSort == $newSortField && $currentOrder == 'ASC') ? 'DESC' : 'ASC';
    $queryString = http_build_query(array_merge($_GET, ['sort' => $newSortField, 'order' => $newOrder]));
    return $_SERVER['PHP_SELF'] . "?" . $queryString;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>이벤트 목록</title>
    <style>
        body { font-family: 'Noto Sans KR', sans-serif; background-color: #f4f4f4; }
        h1 { color: #333; text-align: center; }
        .container { max-width: 1200px; margin: auto; padding: 20px; }
        .search-bar { background-color: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin-bottom: 20px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; }
        .search-bar label { flex: 1; margin-right: 10px; }
        .search-bar select { flex: 3; padding: 10px; border-radius: 5px; border: 1px solid #ddd; width: 100%; }
        .search-bar button { flex: 1; padding: 10px; background-color: #3498db; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        .search-bar button:hover { background-color: #2980b9; }
        .select-button { background-color: #8ea4d2; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; cursor: pointer; }
        th a, .product-name { color: #3498db; text-decoration: none; }
        th a:hover, .product-name:hover { text-decoration: underline; }
        .product { background-color: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin-bottom: 10px; display: flex; align-items: center; }
        .product img { width: 120px; height: auto; margin-right: 15px; }
        .product-info { flex: 1; }
        .product-price { color: #e74c3c; font-weight: bold; }
        .new-label { background-color: #2ecc71; color: #fff; padding: 5px 10px; border-radius: 5px; font-size: 0.75em; }
        .footer { text-align: center; padding: 20px 0; color: #777; }
        .category-buttons { display: flex; flex-wrap: wrap; margin-top: 10px; }
        .category-button { flex: 1; margin: 5px; padding: 10px; background-color: #3498db; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        .category-button:hover { background-color: #2980b9; }
    </style>
</head>
<body>
    <div class="container">
        <h1>이벤트 목록 조회</h1>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
            <div class="search-bar">
                <label for="branch">지점:</label>
                <select name="branch" id="branch">
                    <option value="">모든 지점</option>
                    <?php
                    $branchQuery = "SELECT DISTINCT branch_name FROM branch";
                    $branchResult = mysqli_query($con, $branchQuery);
                    while ($branchRow = mysqli_fetch_array($branchResult)) {
                        echo "<option value='" . mysqli_real_escape_string($con, $branchRow['branch_name']) . "'";
                        if ($branchRow['branch_name'] === $branch) echo " selected";
                        echo ">" . $branchRow['branch_name'] . "</option>";
                    }
                    ?>
                </select>
                <button type="submit" id="branch-button" name="filter">지점 선택</button>
            </div>

            <div class="search-bar">
                <label for="category">카테고리:</label>
                <select name="category" id="category">
                    <option value="">전체 카테고리</option>
                    <option value="A">과자</option>
                    <option value="B">라면</option>
                    <option value="C">탄산/커피/차/숙취해소음료</option>
                    <option value="D">생수</option>
                    <option value="E">빵/디저트</option>
                    <option value="F">유제품</option>
                    <option value="G">도시락</option>
                    <option value="H">김밥/주먹밥</option>
                    <option value="I">샌드위치/버거</option>
                    <option value="J">떡</option>
                    <option value="K">디저트</option>
                    <option value="L">샐러드</option>
                    <option value="M">냉동간편식</option>
                    <option value="N">아이스크림</option>
                    <option value="O">초콜릿</option>
                    <option value="P">캔디/젤리/껌</option>
                    <option value="Q">쿠키</option>
                    <option value="R">안주</option>
                    <option value="S">취미/완구</option>
                    <option value="T">위생/미용용품</option>
                    <option value="U">의약/건강</option>
                    <option value="V">반려용품</option>
                    <option value="W">생활잡화</option>
                </select>

                <button type="submit" id="category-button" name="filter">카테고리 선택</button>
            </div>

            <div class="category-buttons">
                <button type="submit" class="category-button" name="event_type" value="1+1">1+1 이벤트</button>
                <button type="submit" class="category-button" name="event_type" value="2+1">2+1 이벤트</button>
                <button type="submit" class="category-button" name="event_type" value="">전체</button>
            </div>

            
        </form>
        <?php if ($ret): ?>
        <table>
        <tr>
        <th><a href="<?php echo generateSortLink($sort, $order, 'event_listID'); ?>">이벤트 번호</a></th>
        <th><a href="<?php echo generateSortLink($sort, $order, 'eventlist_info'); ?>">이벤트 내용</a></th>
        <th><a href="<?php echo generateSortLink($sort, $order, 'product_name'); ?>">상품명</a></th>
        <th><a href="<?php echo generateSortLink($sort, $order, 'startDate'); ?>">시작 날짜</a></th>
        <th><a href="<?php echo generateSortLink($sort, $order, 'endDate'); ?>">마감 날짜</a></th>
        <th><a href="<?php echo generateSortLink($sort, $order, 'branch_name'); ?>">지점명</a></th>
        </tr>
            
            <?php while ($row = mysqli_fetch_assoc($ret)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['event_listID']); ?></td>
                <td><?php echo htmlspecialchars($row['eventlist_info']); ?></td>
                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                <td><?php echo htmlspecialchars($row['startDate']); ?></td>
                <td><?php echo htmlspecialchars($row['endDate']); ?></td>
                <td><?php echo htmlspecialchars($row['branch_name']); ?></td>
            </tr>
            <?php endwhile; ?>
            
        </table>
        <?php else: ?>
        <p>이벤트가 없습니다.</p>
        <?php endif; ?>

        <a href="무인편의점_main.php">메인으로</a><br><br>
    </div>

</body>
</html>