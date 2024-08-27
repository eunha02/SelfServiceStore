<?php
// 데이터베이스 연결
$con = mysqli_connect("localhost", "root", "1234", "cvDB");
if (!$con) {
    die("MySQL 접속 실패: " . mysqli_connect_error());
}

// POST 데이터 받아오기
$selectedEvents = $_POST['event_selection'] ?? [];
$branchID = isset($_POST['branchID']) ? $_POST['branchID'] : '';

// 이벤트 시작 및 종료 날짜 계산
$eventStartDate = date('Y-m-d', strtotime('+1 week'));
$eventEndDate = date('Y-m-d', strtotime('+3 weeks', strtotime($eventStartDate)));

// 선택된 이벤트 처리
foreach ($selectedEvents as $selectedEvent) {
    // 이벤트 정보 분리
    list($productName, $eventType) = explode('_', $selectedEvent);

    // 이벤트 정보 삽입 쿼리
    // 이벤트 정보 삽입 쿼리
$sql = "INSERT INTO event_list (eventlist_info, product_name, startDate, endDate, branchID) 
VALUES (?, ?, ?, ?, ?)";
$stmt = $con->prepare($sql);
$stmt->bind_param("sssss", $eventType, $productName, $eventStartDate, $eventEndDate, $branchID);

if (!$stmt->execute()) {
echo "<p>이벤트 생성 실패: " . $stmt->error . "</p>";
} else {
echo "<p>이벤트 생성 성공: " . htmlspecialchars($productName) . " - " . htmlspecialchars($eventType) . "</p>";
}
$stmt->close();

}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>이벤트 생성 완료</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            text-align: center;
            padding: 20px;
        }
        p {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <h1>이벤트 생성 결과</h1>
    <p>이벤트가 성공적으로 생성되었습니다.</p><br>
    <a href = '무인편의점_이벤트목록확인.php'> 이벤트 목록 보기 </a> <br><br>
</body>
</html>
