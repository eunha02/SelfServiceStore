<?php
session_start(); // 세션 시작

// 장바구니가 비어있으면 무인편의점_고객구매.php 페이지로 리다이렉트
if (empty($_SESSION['cart'])) {
    header("Location: 무인편의점_고객구매.php");
    exit;
}

// 장바구니 목록 가져오기
$cartItems = $_SESSION['cart'];
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>장바구니 확인</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f8f8;
        }

        .total {
            font-size: 1.2em;
            font-weight: bold;
            text-align: right;
            padding: 10px;
        }

        .purchase-button {
            width: 100%;
            text-align: center;
            margin-top: 30px; /* 버튼과 테이블 사이의 간격을 조금 더 넓혀줍니다. */
        }

        .purchase-button input[type="submit"] {
            background-color: #007BFF; /* 밝은 파란색으로 변경 */
            color: white;
            padding: 15px 30px; /* 버튼의 패딩을 늘려 크기를 키웁니다. */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2em; /* 폰트 크기를 늘려줍니다. */
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16); /* 그림자 효과 추가 */
            transition: background-color 0.3s; /* 색상 변경 애니메이션 추가 */
        }

        .purchase-button input[type="submit"]:hover {
            background-color: #0056b3; /* 호버 시 더 어두운 색상으로 변경 */
        }
    </style>
</head>
<body>
    <h1>장바구니 확인</h1>
    <table>
        <tr>
            <th>물품 이름</th>
            <th>수량</th>
            <th>가격</th>
        </tr>
        <?php
$totalPrice = 0;
foreach($_SESSION['cart'] as $id => $item) {
    $itemTotal = $item['price'] * $item['quantity']; // 각 항목의 총 금액 계산
    $totalPrice += $itemTotal; // 총 금액에 더하기

    echo "<tr>";
    echo "<td>" . htmlspecialchars($item['name']) . "</td>";
    echo "<td>" . htmlspecialchars($item['quantity']) . "</td>";
    echo "<td>" . htmlspecialchars(number_format($itemTotal)) . "원</td>"; // 각 항목의 총 금액 표시
    echo "</tr>";
}
?>
<tr>
    <td colspan="2" class="total">총 금액</td>
    <td class="total"><?php echo number_format($totalPrice); ?>원</td>
</tr>

    </table>

    <div class="purchase-button">
        <form action="무인편의점_구매처리.php" method="post">
            <!-- 장바구니에 있는 상품 정보를 숨겨진 필드로 전송 -->
            <?php foreach($_SESSION['cart'] as $id => $item): ?>
                <input type="hidden" name="cart[<?php echo $id; ?>][quantity]" value="<?php echo $item['quantity']; ?>">
                <input type="hidden" name="cart[<?php echo $id; ?>][branchID]" value="<?php echo $item['branchID']; ?>">
            <?php endforeach; ?>
            <input type="submit" value="구매 확인">
        </form>
    </div>
    </body>
</html>
