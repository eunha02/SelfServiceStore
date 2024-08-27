<!DOCTYPE html>
<html lang="ko">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>무인 편의점 - 발주</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            text-align: center;
            padding: 20px;
            margin: 0;
        }

        h1 {
            color: #008cba;
            margin-bottom: 20px;
        }

        .menu-container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            margin: 20px auto;
            text-align: left;
            width: 80%;
            max-width: 800px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #008cba; 
            color: white;
        }

        form {
            text-align: center;
        }

        input[type="number"] {
            width: 50px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        input[type="submit"] {
            background-color: #00a0b0; /* 밝은 파란색 배경 적용 */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #00b0ff; /* 마우스 호버 시 밝은 파란색으로 변경 */
        }

        /* 제목 행(물품 아이디, 물품 명, 현재 재고 수량, 추천 발주 수량, 발주 수량 입력)의 색상 변경 */
        th:nth-child(1), th:nth-child(2), th:nth-child(3), th:nth-child(4), th:nth-child(5) {
            background-color: #00a0b0; /* 밝은 파란색 배경 적용 */
            color: white; /* 흰색 텍스트 적용 */
        }

    </style>
</head>
<body>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "1234";
    $dbname = "cvDB";

    $con = mysqli_connect($servername, $username, $password, $dbname);

    if (!$con) {
        die("MySQL 접속 실패: " . mysqli_connect_error());
    }

    if (isset($_GET['ownerID'])) {
        $userBranchID = $_GET['ownerID'];
    } else {
        die("ownerID가 제공되지 않았습니다.");
    }

    $sql = "SELECT p.productID, p.product_name, p.price, w.amount, b.ownerID
            FROM product p
            JOIN warehouse w ON p.productID = w.productID
            JOIN branch b ON b.branchID = w.branchID
            WHERE b.ownerID = ?";

    $stmt = $con->prepare($sql);
    if (!$stmt) {
        die('Error in statement preparation: ' . $con->error);
    }

    $stmt->bind_param("i", $userBranchID);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <h1>무인 편의점 - 물품 발주</h1>
    <div class="menu-container">
        <form action='무인편의점_발주완료.php' method='post'>
            <table>
                <tr>
                    <th>물품 아이디</th>
                    <th>물품 명</th>
                    <th>현재 재고 수량</th>
                    <th>추천 발주 수량</th>
                    <th>발주 수량 입력</th>
                </tr>
                <?php
                while ($row = $result->fetch_assoc()) {
                    $productID = $row['productID'];
                    $productName = $row['product_name'];
                    $currentAmount = $row['amount'];

                    // 물품 추천 수량 계산
                    $recommendedOrderAmount = customRecommendedOrderAmount($currentAmount, $productID);

                    // 구매 빈도에 따라 추천 수량 조정
                    $purchaseFrequency = getPurchaseFrequency($productID, $con);
                    if ($purchaseFrequency > 5) {
                        $recommendedOrderAmount += 5; // 높은 구매 빈도일 때 추가 발주
                    } else {
                        $recommendedOrderAmount -= 3; // 낮은 구매 빈도일 때 감소 발주
                    }

                    // 추천 수량이 음수가 되지 않도록 조건 수정
                    $recommendedOrderAmount = max(0, $recommendedOrderAmount);

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($productID) . "</td>";
                    echo "<td>" . htmlspecialchars($productName) . "</td>";
                    echo "<td>" . htmlspecialchars($currentAmount) . "</td>";
                    echo "<td>" . htmlspecialchars($recommendedOrderAmount) . "</td>";
                    echo "<td><input type='number' name='order_amount[" . htmlspecialchars($productID) . "]' value='0'></td>";
                    echo "</tr>";
                }
                ?>
            </table>
            <input type='submit' value='발주'>
        </form>
    </div>

    <?php
    // 새로운 물품 추천 발주 수량 계산 함수
function customRecommendedOrderAmount($currentAmount, $productID) {
    $lastCharacter = substr($productID, -1);
    switch ($lastCharacter) {
        case 'A':
            return max(0, 15 - $currentAmount); // A(과자)로 끝나는 경우 추천 발주 수량 15개
        case 'B':
            return max(0, 10 - $currentAmount); // B(라면)로 끝나는 경우 추천 발주 수량 10개
        case 'C':
            return max(0, 10 - $currentAmount); // C(탄산/커피/차/숙취해소음료)로 끝나는 경우 추천 발주 수량 10개
        case 'D':
            return max(0, 5 - $currentAmount); // D(생수)로 끝나는 경우 추천 발주 수량 5개
        case 'E':
            return max(0, 10 - $currentAmount); // E(빵/디저트)로 끝나는 경우 추천 발주 수량 10개
        case 'F':
            return max(0, 10 - $currentAmount); // F(유제품)로 끝나는 경우 추천 발주 수량 10개
        case 'G':
            return max(0, 5 - $currentAmount); // G(도시락)로 끝나는 경우 추천 발주 수량 5개
        case 'H':
            return max(0, 5 - $currentAmount); // H(김밥/주먹밥)로 끝나는 경우 추천 발주 수량 5개
        case 'I':
            return max(0, 5 - $currentAmount); // I(샌드위치/버거)로 끝나는 경우 추천 발주 수량 5개
        case 'J':
            return max(0, 10 - $currentAmount); // J(빵/떡)로 끝나는 경우 추천 발주 수량 10개
        case 'K':
            return max(0, 10 - $currentAmount); // K(디저트)로 끝나는 경우 추천 발주 수량 10개
        case 'L':
            return max(0, 5 - $currentAmount); // L(샐러드)로 끝나는 경우 추천 발주 수량 5개
        case 'M':
            return max(0, 10 - $currentAmount); // M(냉동간편식)로 끝나는 경우 추천 발주 수량 10개
        case 'N':
            return max(0, 5 - $currentAmount); // N(아이스크림)로 끝나는 경우 추천 발주 수량 5개
        case 'O':
            return max(0, 5 - $currentAmount); // O(초콜릿)로 끝나는 경우 추천 발주 수량 5개
        case 'P':
            return max(0, 5 - $currentAmount); // P(캔디/젤리/껌)로 끝나는 경우 추천 발주 수량 5개
        case 'Q':
            return max(0, 10 - $currentAmount); // Q(쿠키)로 끝나는 경우 추천 발주 수량 10개
        case 'R':
            return max(0, 10 - $currentAmount); // R(안주)로 끝나는 경우 추천 발주 수량 5개
        case 'S':
            return max(0, 10 - $currentAmount); // S(취미/완구)로 끝나는 경우 추천 발주 수량 10개
        case 'T':
            return max(0, 5 - $currentAmount); // T(위생/미용용품)로 끝나는 경우 추천 발주 수량 5개
        case 'U':
            return max(0, 5 - $currentAmount); // U(의약/건강)로 끝나는 경우 추천 발주 수량 5개
        case 'V':
            return max(0, 5 - $currentAmount);  // V(반려용품)로 끝나는 경우 추천 발주 수량 5개
        case 'W':
            return max(0, 5 - $currentAmount);  // W(생활잡화)로 끝나는 경우 추천 발주 수량 5개
        default:
            return max(0, 5 - $currentAmount); // 기본 값 설정
    }
}
// 구매 기록을 `buy` 테이블에 추가
$stmt = $con->prepare("INSERT INTO buy (userID, productID, branchID, amount, buy_date) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssis", $userID, $productID, $branchID, $purchaseAmount, $buyDate);
$stmt->execute();

// `warehouse` 테이블에서 상품의 재고 수량 감소
$stmt = $con->prepare("UPDATE warehouse SET amount = amount - ? WHERE productID = ?");
$stmt->bind_param("is", $purchaseAmount, $productID);
$stmt->execute();
// 구매 빈도 계산 함수
function getPurchaseFrequency($productID, $con) {
    $query = "SELECT COUNT(*) as frequency FROM buy WHERE productID = ?";
    $stmt = $con->prepare($query);
    if (!$stmt) {
        die('Error in statement preparation: ' . $con->error);
    }
    $stmt->bind_param("s", $productID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['frequency'];
}

// 데이터베이스 연결 종료
mysqli_close($con);
?>
</body>
</html>