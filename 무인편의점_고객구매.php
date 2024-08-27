<?php
session_start(); // 세션을 시작합니다.

// 데이터베이스 설정
$servername = "localhost";
$username = "root";
$password = "1234"; // 실제 데이터베이스 비밀번호를 입력하세요.
$dbname = "cvDB";

// MySQL 데이터베이스에 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("연결 실패: " . $conn->connect_error);
}

// 장바구니 세션 변수가 설정되지 않았다면 초기화
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// 지점 선택 처리
$selectedBranch = isset($_POST['branch']) ? $_POST['branch'] : '';

// 물품 검색 처리
$searchKeyword = isset($_POST['search']) ? $_POST['search'] : '';
// 이벤트 목록 가져오기
$eventListSql = "SELECT * FROM event_list";
$eventListResult = $conn->query($eventListSql);

// 이벤트 정보를 배열로 저장
$events = array();
while ($event = $eventListResult->fetch_assoc()) {
    $events[$event['product_name']] = $event['eventlist_info'];
}
// 상품을 장바구니에 추가하는 코드 부분
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $productID = $_POST['productID'];
    $quantity = $_POST['quantity'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['price'];
    $branchID = $_POST['branch']; // 지점 ID 추가

    // 재고 수량 확인
    $stockCheckSql = "SELECT amount FROM warehouse WHERE productID = ? AND branchID = ?";
    $stockCheckStmt = $conn->prepare($stockCheckSql);
    $stockCheckStmt->bind_param("ss", $productID, $branchID);
    $stockCheckStmt->execute();
    $stockResult = $stockCheckStmt->get_result();
    $stockAmount = 0;
    if ($stockRow = $stockResult->fetch_assoc()) {
        $stockAmount = $stockRow['amount'];
    }
    $stockCheckStmt->close();

    // 수량이 재고를 초과하지 않는지 확인
    if ($quantity > $stockAmount) {
        // JavaScript 경고 메시지 출력
        echo "<script type='text/javascript'>alert('재고 수량을 초과하는 주문은 불가능합니다.');</script>";
    } else {
        // 재고 내에서 주문 가능한 경우, 장바구니에 상품 추가
        if (isset($_SESSION['cart'][$productID])) {
            $_SESSION['cart'][$productID]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productID] = array('name' => $product_name, 'quantity' => $quantity, 'price' => $product_price, 'branchID' => $branchID);
        }
    }
}

// 장바구니 수량 업데이트 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_cart'])) {
    // quantities 배열이 설정되었는지 확인
    if (isset($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $productID => $quantity) {
        // 재고 수량 확인
        $branchID = $_SESSION['cart'][$productID]['branchID']; // 장바구니에 저장된 branchID 사용
        $stockCheckSql = "SELECT amount FROM warehouse WHERE productID = ? AND branchID = ?";
        $stockCheckStmt = $conn->prepare($stockCheckSql);
        $stockCheckStmt->bind_param("ss", $productID, $branchID);
        $stockCheckStmt->execute();
        $stockResult = $stockCheckStmt->get_result();
        $stockAmount = 0;
        if ($stockRow = $stockResult->fetch_assoc()) {
            $stockAmount = $stockRow['amount'];
        }
        $stockCheckStmt->close();

        // 수량이 재고를 초과하는 경우 경고 메시지 출력 및 수량 조정
        if ($quantity > $stockAmount) {
            echo "<script type='text/javascript'>alert('상품 " . $_SESSION['cart'][$productID]['name'] . "의 수량이 재고를 초과합니다. 재고 수량으로 조정됩니다.');</script>";
            $_SESSION['cart'][$productID]['quantity'] = $stockAmount;
        } else {
            $_SESSION['cart'][$productID]['quantity'] = $quantity;
        }
    }

    // 지점 선택 값 유지
    $selectedBranch = $_POST['selected_branch'];
}
}


// 장바구니에서 상품을 제거하는 요청 처리
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['remove_from_cart'])) {
    $productID = $_GET['remove_from_cart'];
    unset($_SESSION['cart'][$productID]);
}



// 데이터베이스에서 지점 목록을 가져옵니다.
$branch_sql = "SELECT branchID, branch_name FROM branch";
$branch_result = $conn->query($branch_sql);

// 데이터베이스에서 상품 목록을 가져오는 부분
$product_sql = "SELECT p.productID, p.product_name, p.price, w.amount, IFNULL(e.eventlist_info, '') AS eventlist_info 
                FROM product p
                JOIN warehouse w ON p.productID = w.productID
                LEFT JOIN event_list e ON p.product_name = e.product_name
                WHERE w.branchID LIKE ? AND p.product_name LIKE ?";
$stmt = $conn->prepare($product_sql);

if (false === $stmt) {
    // prepare()에 실패한 경우 오류 처리
    die("SQL 쿼리 준비 중 오류 발생: " . htmlspecialchars($conn->error));
}

$searchTerm = '%' . $searchKeyword . '%';
$stmt->bind_param("ss", $selectedBranch, $searchTerm);
$stmt->execute();
$product_result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>구매 시연</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            color: #212529; /* 변경된 색상 */
        }

        h1 {
            text-align: center;
            color: #212529; /* 변경된 색상 */
        }

        form {
            margin-bottom: 20px;
            text-align: center;
        }

        select, input[type="text"], input[type="number"] {
            padding: 10px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            color: #212529; /* 변경된 색상 */
        }

        input[type="submit"], button, a {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }

        input[type="submit"]:hover, button:hover, a:hover {
            background-color: #45a049;
        }

        .scrollable-table {
            overflow-y: auto;
            max-height: 300px;
            border: 1px solid #ddd;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f8f8;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .purchase-button {
            text-align: center;
            margin-top: 20px; /* 추가된 여백 */
        }
    </style>
</head>
<body>
    <h1>무인 편의점 구매 시연</h1>

     <!-- 지점 선택 폼 -->
    <form method="post">
        <label for="branch">지점 선택:</label>
        <select name="branch" id="branch">
            <option value="">지점을 선택하세요</option>
            <?php while ($branch = $branch_result->fetch_assoc()): ?>
                <option value="<?php echo $branch['branchID']; ?>" <?php echo $selectedBranch == $branch['branchID'] ? 'selected' : ''; ?>>
                    <?php echo $branch['branch_name']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <input type="submit" value="선택">
    </form>

    <!-- 물품 검색 폼 -->
    <form method="post">
        <input type="hidden" name="branch" value="<?php echo $selectedBranch; ?>">
        <input type="text" name="search" placeholder="물품 검색" value="<?php echo htmlspecialchars($searchKeyword); ?>">
        <input type="submit" value="검색">
    </form>

    <!-- 상품 목록 표시 -->
    <div class="scrollable-table">
        <table>
            <tr>
                <th>물품 이름</th>
                <th>가격</th>
                <th>재고</th>
                <th>이벤트</th>
                <th>수량 선택</th>
                <th>장바구니에 추가</th>
            </tr>
            <?php while($product = $product_result->fetch_assoc()): $displayedStock = $product['amount'] < 0 ? 0 : $product['amount'];?>
                <form method="post" onsubmit="addToCart(this, event);">
                    <input type="hidden" name="branch" value="<?php echo $selectedBranch; ?>">
                    <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                    <input type="hidden" name="eventlist_info" value="<?php echo $product['eventlist_info']; ?>">
                    <tr>
                        <td><?php echo $product['product_name']; ?></td>
                        <td><?php echo $product['price']; ?>원</td>
                        <td><?php echo $displayedStock; ?></td>
                        <td><?php echo $product['eventlist_info']; ?></td>
                        <td>
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['amount']; ?>">
                            <input type="hidden" name="productID" value="<?php echo $product['productID']; ?>">
                            <input type="hidden" name="product_name" value="<?php echo $product['product_name']; ?>">
                        </td>
                        <td>
                            <input type="submit" name="add_to_cart" value="추가">
                        </td>
                    </tr>
                </form>
            <?php endwhile; ?>
        </table>
    </div>

<!-- 장바구니 목록 -->
<table id="cart-table">
    <thead>
        <tr>
            <th>물품 이름</th>
            <th>수량</th>
            <th>총 가격</th>
            <th>업데이트</th>
            <th>삭제</th>
        </tr>
    </thead>
    <tbody id="cart-table-body">
        <?php foreach ($_SESSION['cart'] as $id => $item): ?>
        <tr id="cart-item-<?php echo $id; ?>">
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td>
                <input type="number" id="quantity-<?php echo $id; ?>" value="<?php echo $item['quantity']; ?>" min="1">
            </td>
            <td id="total-<?php echo $id; ?>"><?php echo $item['price'] * $item['quantity']; ?>원</td>
            <td>
                <button onclick="updateCartItem('<?php echo $id; ?>')">업데이트</button>
            </td>
            <td>
                <button onclick="removeFromCart('<?php echo $id; ?>', event)">삭제</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" style="text-align: right;">총 금액:</td>
            <td id="total-price"></td>
        </tr>
    </tfoot>
</table>


<div class="purchase-button">
        <a href="무인편의점_장바구니확인.php">구매하기</a>
</div>
</body>

<script>
function removeFromCart(productId, event) {
    event.preventDefault(); // 폼 제출 방지

    // AJAX 요청을 시작합니다.
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "무인편의점_장바구니제거.php?remove_from_cart=" + productId, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                // 서버 응답을 JSON으로 파싱합니다.
                var response = JSON.parse(this.responseText);
                if (response.status === 'success') {
                    // 해당 상품을 DOM에서 제거합니다.
                    var cartItem = document.getElementById("cart-item-" + productId);
                    if (cartItem) {
                        cartItem.remove();
                    }
                    // 총금액을 업데이트하는 함수를 호출합니다.
                    updateTotalPrice();
                } else {
                    // 서버에서 오류 메시지가 있다면 표시합니다.
                    alert('Error: ' + response.message);
                }
            } catch (e) {
                // JSON 파싱에 실패했다면 오류를 표시합니다.
                alert('Error: 응답을 파싱하는 데 실패했습니다. ' + e.message);
            }
        } else {
            // 서버 응답이 200이 아닌 경우 오류를 표시합니다.
            alert('Error: ' + xhr.status);
        }
    };
    xhr.send(); // AJAX 요청을 보냅니다.
}

// removeFromCart 함수는 이전과 동일합니다.

function updateCartItem(productId) {
    var quantity = document.getElementById('quantity-' + productId).value;

    // 서버에 업데이트 요청을 보냅니다.
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "무인편의점_장바구니_업데이트.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(this.responseText);
            if (response.status === 'success') {
                // 성공적으로 업데이트 되었다면 화면을 업데이트합니다.
                updateCartDisplay(response.cart);
            } else {
                alert('Error: ' + response.message);
            }
        } else {
            alert('Error: ' + xhr.status);
        }
    };
    xhr.send('productID=' + productId + '&quantity=' + quantity);
}

function updateCartDisplay(cart) {
    var cartTableBody = document.getElementById('cart-table-body');
    var totalPrice = 0;
    if (cartTableBody) {
        var newHtml = '';
        Object.keys(cart).forEach(function (key) {
            var item = cart[key];
            var itemTotal = item.quantity * item.price;
            totalPrice += itemTotal;
            newHtml += '<tr id="cart-item-' + key + '">' +
                       '<td>' + item.name + '</td>' +
                       '<td><input type="number" id="quantity-' + key + '" value="' + item.quantity + '" min="1"></td>' +
                       '<td id="total-' + key + '">' + itemTotal + '원</td>' +
                       '<td><button onclick="updateCartItem(\'' + key + '\')">업데이트</button></td>' +
                       '<td><button onclick="removeFromCart(\'' + key + '\', event)">삭제</button></td>' +
                       '</tr>';
        });
        cartTableBody.innerHTML = newHtml;
        document.getElementById('total-price').textContent = totalPrice + '원';
    } else {
        console.error("Element with ID 'cart-table-body' was not found.");
    }
}

// 페이지 로드 시 총 금액을 계산합니다.
document.addEventListener('DOMContentLoaded', function() {
    updateTotalPrice();
});

function updateTotalPrice() {
    var totalPrice = 0;
    var cartItems = document.querySelectorAll('#cart-table-body tr');

    cartItems.forEach(function (item) {
        var price = parseInt(item.querySelector('td:nth-child(3)').textContent.replace('원', ''), 10);
        var quantity = parseInt(item.querySelector('td:nth-child(2)').textContent, 10);
        var eventInfo = item.querySelector('td:nth-child(4)').textContent; // 이벤트 정보를 가져옵니다.

        if (eventInfo === '1+1' && quantity % 2 === 0) {
            // 1+1 이벤트이고 짝수 개를 구매했을 때
            totalPrice += (price * quantity) / 2;
        } else if (eventInfo === '2+1') {
            // 2+1 이벤트 처리
            var setsOfThree = Math.floor(quantity / 3); // 3개 세트의 개수
            var remaining = quantity % 3; // 3으로 나눈 나머지
            totalPrice += (setsOfThree * 2 * price) + (remaining * price);
        } else {
            // 일반 상품 또는 1+1 이벤트의 홀수 개
            totalPrice += price * quantity;
        }
    });

    // 총 금액을 화면에 업데이트합니다.
    document.getElementById('total-price').textContent = totalPrice + '원';
}

function addToCart(form, event) {
    event.preventDefault(); // 기본 폼 제출을 방지합니다.

    var formData = new FormData(form);
    var eventInfo = formData.get('eventlist_info'); // 이벤트 정보를 FormData에서 가져옵니다.
    var quantity = parseInt(formData.get('quantity'), 10); // 수량을 정수로 변환합니다.
    var price = parseFloat(formData.get('price')); // 가격을 부동소수점으로 변환합니다.

    // 이벤트 정보를 확인하고 할인을 적용합니다.
    if (eventInfo === '1+1') {
        // 1+1 이벤트인 경우
        if (quantity % 2 !== 0) {
            // 홀수 개를 구매한 경우
            alert('해당 상품은 1+1 이벤트 상품입니다. 물품을 더 추가해주세요.');
            price = (Math.floor(quantity / 2) * price) + price;
        } else {
            // 할인 적용: 총 가격을 반으로 나눕니다.
            price = price / 2;
        }
    } else if (eventInfo === '2+1') {
        if (quantity % 3 === 0) {
            price = price / 3 * 2;
        }
        else {
            alert('해당 상품은 2+1 이벤트 상품입니다. 물품을 하나 더 추가해주세요.');
        }
    }

    // AJAX 요청을 보내서 장바구니에 추가합니다.
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "무인편의점_장바구니에_추가.php", true);

    xhr.onload = function() {
        if (this.status === 200) {
            try {
                var response = JSON.parse(this.responseText);
                if (response.status === "success") {
                    alert(response.message); // 성공 메시지 출력
                    updateCartDisplay(response.cart);
                } else {
                    alert("Error: " + response.message); // 오류 메시지 출력
                }
            } catch (e) {
                alert("Error: 응답을 파싱하는 데 실패했습니다. " + e.message);
            }
        } else {
            alert("Error: 서버에서 오류가 발생했습니다. " + this.status);
        }
    };

    formData.set('price', price); // 할인된 가격을 FormData에 업데이트합니다.
    xhr.send(formData);
}
</script>
</html>