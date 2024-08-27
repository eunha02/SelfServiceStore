<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>매출 조회</title>
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
        .average-sales {
            margin-top: 20px;
            font-size: 20px;
            color: #444;
        }
        canvas {
            margin: 20px auto;
            display: block;
            max-width: 80%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        canvas {
            /* 그래프 크기 조정 */
            max-width: 600px; /* 예시로 600px로 설정 */
            height: 400px; /* 높이 설정 */
            margin: 20px auto;
            display: block;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>매출 조회</h1>

    <?php
    // 데이터베이스 연결 설정
    $servername = "localhost";
    $username = "root";
    $password = "1234";
    $dbname = "cvDB";

    // 데이터베이스 연결
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 연결 확인
    if ($conn->connect_error) {
        die("MySQL 접속 실패: " . $conn->connect_error);
    }

    // URL에서 owner_id 가져오기
    $ownerID = $_GET['userID'] ?? '';

    // 매출 조회 쿼리
    $sql = "SELECT YEAR(buy_date) as year, MONTH(buy_date) as month, SUM(p.price * b.amount) as total_sales
            FROM buy b
            JOIN product p ON b.productID = p.productID
            WHERE b.branchID = (SELECT branchID FROM branch WHERE ownerID = ?)
            GROUP BY YEAR(buy_date), MONTH(buy_date)
            ORDER BY year DESC, month DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ownerID);
    $stmt->execute();
    $result = $stmt->get_result();

    // 최근 3개월 평균 매출 계산
    $currentYear = date("Y");
    $currentMonth = date("m");
    $totalSalesLastThreeMonths = 0;
    $monthsCounted = 0;

    while ($row = $result->fetch_assoc()) {
        if ($row['year'] == $currentYear && $row['month'] == $currentMonth) {
            continue; // 현재 달은 제외
        }

        if ($monthsCounted < 3) {
            $totalSalesLastThreeMonths += $row['total_sales'];
            $monthsCounted++;
        }
    }

    $averageSales = ($monthsCounted > 0) ? $totalSalesLastThreeMonths / $monthsCounted : 0;

    // 결과 출력
    echo "<table>";
    echo "<tr><th>년도</th><th>월</th><th>총 매출</th><th> </th></tr>";

    mysqli_data_seek($result, 0); // 결과 포인터를 처음으로 되돌림

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['year']) . "</td>";
        echo "<td>" . htmlspecialchars($row['month']) . "</td>";
        echo "<td>" . htmlspecialchars(number_format($row['total_sales'])) . "원</td>";
        echo "<td><a href='무인편의점_매출_자세히.php?ownerID=" . urlencode($ownerID) . "&year=" . htmlspecialchars($row['year']) . "&month=" . htmlspecialchars($row['month']) . "'>상세 보기</a></td>";
        echo "</tr>";
    }

    echo "</table>";

    $currentMonth = date('n');
    // 월 이름 배열 정의
    $monthNames = array(
        1 => '1월',
        2 => '2월',
        3 => '3월',
        4 => '4월',
        5 => '5월',
        6 => '6월',
        7 => '7월',
        8 => '8월',
        9 => '9월',
        10 => '10월',
        11 => '11월',
        12 => '12월'
    );
    // 현재 월에 해당하는 월 이름 가져오기
    $currentMonthName = $monthNames[$currentMonth];

    echo "<div class='average-sales'>$currentMonthName 매출은 " . number_format($averageSales) . "원으로 예상됩니다.</div>";

    ?>

<?php
    // 그래프 데이터 및 라벨 배열 초기화
    $graphData = [];
    $monthLabels = [];

    // 결과 포인터 초기화
    mysqli_data_seek($result, 0);

    // 그래프 데이터 및 라벨 채우기
    while ($row = $result->fetch_assoc()) {
        $graphData[] = $row['total_sales'];
        $monthLabels[] = $monthNames[$row['month']] . ' ' . $row['year'];
    }

    //최신 정보가 오른쪽에 오도록 그래프 뒤집기
    $graphData = array_reverse($graphData);
    $monthLabels = array_reverse($monthLabels);
    // 그래프 데이터 JSON 인코딩
    $graphData = json_encode($graphData);
    $monthLabels = json_encode($monthLabels);


    // 나이대별 매출 조회 쿼리
    $ageSql = "SELECT FLOOR((YEAR(CURDATE()) - u.birthyear) / 10) * 10 AS age_group, 
               SUM(b.amount * p.price) AS total_sales
               FROM buy b
               INNER JOIN usertbl u ON b.userID = u.userID
               INNER JOIN product p ON b.productID = p.productID
               INNER JOIN branch br ON b.branchID = br.branchID
               WHERE br.ownerID = ?
               GROUP BY age_group
               ORDER BY age_group";
               
    $ageStmt = $conn->prepare($ageSql);
    $ageStmt->bind_param("s", $ownerID);
    $ageStmt->execute();
    $ageResult = $ageStmt->get_result();

    $ageData = [];
    $ageLabels = [];
    while ($row = $ageResult->fetch_assoc()) {
        $ageLabels[] = $row['age_group'] . '대';
        $ageData[] = $row['total_sales'];
    }
    $ageStmt->close();


    // 카테고리 데이터를 가져오는 쿼리
    // product_id에서 숫자를 제외한 알파벳 부분만 추출합니다.
    $categorySql = "SELECT SUBSTRING(p.productID, -1) AS categoryID, SUM(b.amount * p.price) AS total_sales
                    FROM buy b
                    INNER JOIN product p ON b.productID = p.productID
                    INNER JOIN branch br ON b.branchID = br.branchID
                    WHERE br.ownerID = ?
                    GROUP BY categoryID
                    ORDER BY total_sales DESC"; // 매출이 높은 순으로 정렬

        $categoryStmt = $conn->prepare($categorySql);
        $categoryStmt->bind_param("s", $ownerID);
        $categoryStmt->execute();
        $categoryResult = $categoryStmt->get_result();

        $categories = [
            'A' => '과자', 'B' => '라면', 'C' => '탄산/커피/차/숙취해소음료', 'D' => '생수',
            'E' => '빵/디저트', 'F' => '유제품', 'G' => '도시락', 'H' => '김밥/주먹밥',
            'I' => '샌드위치/버거', 'J' => '빵/떡', 'K' => '디저트', 'L' => '샐러드',
            'M' => '냉동간편식', 'N' => '아이스크림', 'O' => '초콜릿', 'P' => '캔디/젤리/껌',
            'Q' => '쿠키', 'R' => '안주', 'S' => '취미/완구', 'T' => '위생/미용용품',
            'U' => '의약/건강', 'V' => '반려용품', 'W' => '생활잡화'
        ];

        
    // 카테고리별 매출 데이터를 처리하는 부분
    // 카테고리별 매출 데이터를 처리하는 부분을 수정합니다.
    $categorySales = [];
    while ($row = $categoryResult->fetch_assoc()) {
        $categoryChar = $row['categoryID']; // 카테고리 ID (알파벳)
        $categoryName = $categories[$categoryChar] ?? '기타'; // 카테고리명 매칭

        if (!array_key_exists($categoryName, $categorySales)) {
            $categorySales[$categoryName] = 0; // 카테고리가 존재하지 않으면 초기화
        }
        $categorySales[$categoryName] += $row['total_sales']; // 매출 추가
    }

    // 상위 8개 카테고리 데이터와 '기타' 카테고리 데이터를 준비합니다.
    arsort($categorySales); // 매출액 기준 내림차순 정렬
    $topCategories = array_slice($categorySales, 0, 8, true); // 상위 8개 카테고리 추출
    $otherTotal = array_sum(array_slice($categorySales, 8)); // 나머지 카테고리 매출 합계

    $categoryData = array_values($topCategories); // 상위 8개 카테고리의 매출 데이터
    $categoryLabels = array_keys($topCategories); // 상위 8개 카테고리의 라벨
    $categoryData[] = $otherTotal; // '기타' 카테고리 매출 추가
    $categoryLabels[] = '기타'; // '기타' 라벨 추가


    $conn->close();
?>

    <!-- 매출 그래프 -->
    <canvas id="salesChart"></canvas>
    <!-- 나이대별 매출 그래프 -->
    <canvas id="ageSalesChart"></canvas>
     <!-- 카테고리별 매출 그래프 -->
    <canvas id="categorySalesChart"></canvas>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script>
        var ctx = document.getElementById('salesChart').getContext('2d');//총매출
        var salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo $monthLabels; ?>,
                datasets: [{
                    label: '매출',
                    data: <?php echo $graphData; ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    lineTension: 0, // 선을 직선으로 만듦
                    pointRadius: 3 // 점의 크기 조정
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
        
        var ageCtx = document.getElementById('ageSalesChart').getContext('2d');//나이대별매출
        var ageSalesChart = new Chart(ageCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($ageLabels); ?>,
                datasets: [{
                    label: '나이대별 매출',
                    data: <?php echo json_encode($ageData); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
        
// 카테고리별 매출 그래프 생성 코드
var categoryCtx = document.getElementById('categorySalesChart').getContext('2d');
var categorySalesChart = new Chart(categoryCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($categoryLabels); ?>,
        datasets: [{
            label: '카테고리별 매출',
            data: <?php echo json_encode($categoryData); ?>,
            backgroundColor: 'rgba(255, 206, 86, 0.2)',
            borderColor: 'rgba(255, 206, 86, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});
        </script>
        <div style="text-align: right;">
    <button onclick="goBack()">뒤로 가기</button>
</div>
<script>
    function goBack() {
        window.history.back();
    }
</script>
</body>
