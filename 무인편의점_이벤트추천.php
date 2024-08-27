<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>이벤트 추천</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans KR', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            height: 50px;
            background: #f8f8f8;
            color: #333;
            font-weight: 700;
            cursor: pointer;
        }
        td {
            text-align: center;
            padding: 10px;
            font-size: 14px;
            background: #ffffff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .expiring-soon {
            color: red;
            font-weight: 700;
        }
        th:hover {
            background-color: #e9e9e9;
        }
        /* 버튼 스타일 */
        input[type="submit"] {
            background-color: #3498db; /* 버튼 배경 색상 */
            color: white; /* 버튼 글자 색상 */
            border: 0;
            padding: 10px 20px;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 5px; /* 버튼 모서리를 둥글게 */
            font-size: 16px; /* 폰트 크기 조정 */
            font-weight: bold; /* 폰트 굵기 */
            border: 1px solid #3498db; /* 테두리 색상 */
        }
        input[type="submit"]:hover {
            background-color: #217dbb; /* 호버 시 색상 변경 */
        }

        /* 첫 번째 행(테이블 헤더) 글자색 스타일 */
        table tr th {
            background-color: #f4f4f4; /* 헤더 배경색을 변경할 경우 */
            color: #3498db; /* 헤더 글자 색상 */        
        }
            </style>
    </head>
<body>
<h1>이벤트 추천</h1>

<?php
// 데이터베이스 연결
$servername = "localhost";
$username = "root";
$password = "1234"; // 실제 비밀번호로 변경하세요.
$dbname = "cvDB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// URL에서 ownerID 가져오기
$ownerID = isset($_GET['ownerID']) ? $_GET['ownerID'] : '';

// ownerID를 사용하여 branchID 찾기
$branchSql = "SELECT branchID FROM branch WHERE ownerID = ?";
$branchStmt = $conn->prepare($branchSql);
if (!$branchStmt) {
    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    exit();
}
$branchStmt->bind_param("s", $ownerID);
$branchStmt->execute();
$branchResult = $branchStmt->get_result();
$branchStmt->close();

if ($branchRow = $branchResult->fetch_assoc()) {
    $branchID = $branchRow['branchID'];
} else {
    echo "해당 사장님의 지점 정보를 찾을 수 없습니다.";
    exit();
}

// 1+1 이벤트 추천 쿼리
$onePlusOneSql = "SELECT 
    p.product_name AS product_name_for_1_plus_1,
    w.amount AS amount,
    '1+1' AS event_type,
    w.utong_date AS expiration_date,
    DATEDIFF(w.utong_date, CURDATE()) AS days_remaining
FROM 
    product p
JOIN 
    warehouse w ON p.productID = w.productID
WHERE 
    w.amount >= 30 AND w.branchID = ? AND 
    w.utong_date > CURDATE()
ORDER BY RAND() 
LIMIT 1";

// 2+1 이벤트 추천 쿼리
$twoPlusOneSql = "SELECT 
    p.product_name,
    '2+1' AS event_type,
    w.amount AS current_stock,
    w.utong_date AS expiration_date,
    DATEDIFF(w.utong_date, CURDATE()) AS days_remaining
FROM 
    product p
JOIN 
    warehouse w ON p.productID = w.productID
WHERE 
    w.utong_date > CURDATE() AND w.branchID = ?
ORDER BY 
    w.amount DESC, 
    w.utong_date
LIMIT 3";

// 쿼리 실행 및 결과 처리
$onePlusOneStmt = $conn->prepare($onePlusOneSql);
if (!$onePlusOneStmt) {
    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    exit();
}
$onePlusOneStmt->bind_param("i", $branchID);
$onePlusOneStmt->execute();
$onePlusOneResult = $onePlusOneStmt->get_result();
$onePlusOneStmt->close();

$twoPlusOneStmt = $conn->prepare($twoPlusOneSql);
if (!$twoPlusOneStmt) {
    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    exit();
}
$twoPlusOneStmt->bind_param("i", $branchID);
$twoPlusOneStmt->execute();
$twoPlusOneResult = $twoPlusOneStmt->get_result();
$twoPlusOneStmt->close();

// 결과 출력
echo "<form action='무인편의점_이벤트추천완료.php' method='post'>";
echo "<table id='eventTable' border='1'>";
echo "<tr>";
echo "<th onclick='sortTable(0)'>이벤트 번호</th>";
echo "<th onclick='sortTable(1)'>이벤트 설명</th>";
echo "<th onclick='sortTable(2)'>상품명</th>";
echo "<th onclick='sortTable(3)'>남은 수량</th>";
echo "<th onclick='sortTable(4)'>유통 기한</th>";
echo "<th onclick='sortTable(5, true)'>남은 일수</th>";
echo "<th onclick='sortTable(6)'>진행 여부</th>";
echo "</tr>";

$eventCounter = 'A'; // 이벤트 번호 카운터

// 1+1 이벤트 추천 결과 출력
if ($onePlusOneResult->num_rows > 0) {
    while ($row = $onePlusOneResult->fetch_assoc()) {
        $expirationClass = ($row['days_remaining'] <= 7) ? 'expiring-soon' : '';
        echo "<tr>";
        echo "<td>" . $eventCounter++ . "</td>";
        echo "<td>1+1</td>";
        echo "<td>" . htmlspecialchars($row['product_name_for_1_plus_1']) . "</td>";
        echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
        echo "<td class='$expirationClass'>" . htmlspecialchars($row['expiration_date']) . "</td>";
        echo "<td class='$expirationClass'>D-" . htmlspecialchars($row['days_remaining']) . "</td>";
        echo "<td><input type='checkbox' name='event_selection[]' value='" . $row['product_name_for_1_plus_1'] . "_1+1'></td>";
        echo "</tr>";
    }
}

// 2+1 이벤트 추천 결과 출력
if ($twoPlusOneResult->num_rows > 0) {
    while ($row = $twoPlusOneResult->fetch_assoc()) {
        $expirationClass = ($row['days_remaining'] <= 7) ? 'expiring-soon' : '';
        echo "<tr>";
        echo "<td>" . $eventCounter++ . "</td>";
        echo "<td>2+1</td>";
        echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['current_stock']) . "</td>";
        echo "<td class='$expirationClass'>" . htmlspecialchars($row['expiration_date']) . "</td>";
        echo "<td class='$expirationClass'>D-" . htmlspecialchars($row['days_remaining']) . "</td>";
        echo "<td><input type='checkbox' name='event_selection[]' value='" . $row['product_name'] . "_2+1'></td>";
        echo "</tr>";
    }
}

// 이벤트 시작 및 종료 날짜 계산
$eventStartDate = date('Y-m-d', strtotime('+1 week'));
$eventEndDate = date('Y-m-d', strtotime('+3 weeks'));

// 이벤트 시작 및 종료 날짜를 폼에 추가
echo "<input type='hidden' name='start_date' value='" . $eventStartDate . "'>";
echo "<input type='hidden' name='end_date' value='" . $eventEndDate . "'>";
echo "<input type='hidden' name='branchID' value='" . $branchID . "'>";

echo "</table>";
echo "<br><input type='submit' value='확인'>";
echo "</form>";

mysqli_close($conn);
?>
<script>
// sortTable 함수 정의
function sortTable(column, numeric=false) {
  var table, rows, switching, i, x, y, shouldSwitch, dir = "asc", switchcount = 0;
  table = document.getElementById("eventTable");
  switching = true;
  while (switching) {
    switching = false;
    rows = table.getElementsByTagName("TR");
    for (i = 1; i < (rows.length - 1); i++) {
      shouldSwitch = false;
      x = rows[i].getElementsByTagName("TD")[column];
      y = rows[i + 1].getElementsByTagName("TD")[column];
      // Check if we are sorting numerically or as string
      if(numeric) {
        // Parse integers from "D-xx" format
        var xContent = parseInt(x.innerHTML.toLowerCase().replace('d-', ''));
        var yContent = parseInt(y.innerHTML.toLowerCase().replace('d-', ''));
      } else {
        var xContent = x.innerHTML.toLowerCase();
        var yContent = y.innerHTML.toLowerCase();
      }
      
      if (dir == "asc") {
        if (xContent > yContent) {
          shouldSwitch= true;
          break;
        }
      } else if (dir == "desc") {
        if (xContent < yContent) {
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      switchcount++;      
    } else {
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}

</script>
</body>
</html>