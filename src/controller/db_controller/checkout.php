<?php
include_once "db_connect.php";
session_start();

header("Content-Type: application/json");

try {
    if (!isset($_SESSION['user']) || !isset($_SESSION['user']['userID'])) {
        echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập.']);
        exit();
    }

    $userId = $_SESSION['user']['userID'];

    // --- Lấy địa chỉ & thẻ mặc định ---
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'init') {
        $defaultAddress = getOne("SELECT Address, ProvinceID, DistrictID, WardID FROM user WHERE UserID = $userId");
        $savedCard = getOne("SELECT CardOwner, CardNumber, CVV, ExpiryDate FROM savedpayments WHERE UserID = $userId");
        echo json_encode([
            "success" => true,
            "defaultAddress" => $defaultAddress,
            "savedCard" => $savedCard
        ]);
        exit();
    }

    $data = json_decode(file_get_contents("php://input"), true);

    // Debug log
    error_log("📍 Checkout request received. Action: " . ($data['action'] ?? 'none'));
    error_log("📍 Session products: " . print_r($_SESSION['checkout_products'] ?? [], true));

    if (!isset($data['action']) || !($data['action'] === 'checkout' || $data['action'] === 'buy_now_checkout')) {
        echo json_encode(["success" => false, "message" => "Action không hợp lệ."]);
        exit();
    }

    if (!isset($data['address']) || !isset($data['payment'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Dữ liệu không hợp lệ (thiếu address hoặc payment)."]);
        exit();
    }

    // 📦 Thông tin địa chỉ
    $address = $data["address"]["address"] ?? '';
    $provinceId = $data["address"]["region"]["province"] ?? 0;
    $districtId = $data["address"]["region"]["district"] ?? 0;
    $wardId = $data["address"]["region"]["ward"] ?? 0;

    // Validate địa chỉ
    if (empty($address) || !$provinceId || !$districtId || !$wardId) {
        echo json_encode(["success" => false, "message" => "Vui lòng chọn đầy đủ địa chỉ giao hàng."]);
        exit();
    }

    // 💳 Thông tin thanh toán
    $paymentMethod = $data["payment"]["method"] ?? '';
    $cardOwner = $cardNumber = $cvv = $expiryDate = null;

    if (empty($paymentMethod)) {
        echo json_encode(["success" => false, "message" => "Vui lòng chọn phương thức thanh toán."]);
        exit();
    }

    if ($paymentMethod === 'Card') {
        $cardOwner = $data["payment"]["cardOwner"] ?? null;
        $cardNumber = $data["payment"]["cardNumber"] ?? null;
        $cvv = $data["payment"]["cvv"] ?? null;
        $expiryDate = $data["payment"]["expiryDate"] ?? null;

        if (empty($cardOwner) || empty($cardNumber) || empty($cvv) || empty($expiryDate)) {
            echo json_encode(["success" => false, "message" => "Thông tin thẻ không đầy đủ."]);
            exit();
        }

        if (!empty($data["payment"]["saveCard"])) {
            $saveCardSuccess = executeQuery(
                "INSERT INTO savedpayments (UserID, CardOwner, CardNumber, CVV, ExpiryDate)
                 VALUES (?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                    CardOwner = VALUES(CardOwner),
                    CardNumber = VALUES(CardNumber),
                    CVV = VALUES(CVV),
                    ExpiryDate = VALUES(ExpiryDate),
                    CreatedAt = NOW()",
                [$userId, $cardOwner, $cardNumber, $cvv, $expiryDate]
            );
            if (!$saveCardSuccess) {
                echo json_encode(["success" => false, "message" => "Lưu thông tin thẻ thất bại."]);
                exit();
            }
        }        
    }

    // 🛒 Lấy sản phẩm từ session
    if (!isset($_SESSION['checkout_products']) || !is_array($_SESSION['checkout_products']) || count($_SESSION['checkout_products']) === 0) {
        echo json_encode(["success" => false, "message" => "Không có sản phẩm nào để thanh toán."]);
        exit();
    }

    $cart = json_decode(json_encode($_SESSION['checkout_products']), true); // ép về array chuẩn

    // ✅ Validate từng sản phẩm (nếu muốn)
    foreach ($cart as $product) {
        if (!isset($product['ProductSizeID']) || !isset($product['Price']) || !isset($product['Quantity'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "error" => "Dữ liệu sản phẩm không hợp lệ."]);
            exit();
        }
    }

    // ✅ Tính tổng tiền
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['Price'] * $item['Quantity'];
    }

    // ✅ Tạo đơn hàng
    $success = executeQuery(
        "INSERT INTO orders (UserID, ShippingAddress, ProvinceID, DistrictID, WardID) 
         VALUES (?, ?, ?, ?, ?)", 
        [$userId, $address, $provinceId, $districtId, $wardId]
    );

    if (!$success) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Tạo đơn hàng thất bại. Vui lòng thử lại."]);
        exit();
    }

    // Lấy OrderID vừa tạo
    $order = getOne("SELECT MAX(OrderID) as id FROM orders WHERE UserID = $userId");
    if (!$order || !$order['id']) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Không thể lấy ID đơn hàng."]);
        exit();
    }
    
    $orderId = $order['id'];

    // 📦 Ghi vào orderdetail
    foreach ($cart as $item) {
        $productSizeId = $item['ProductSizeID'];
        $quantity = $item['Quantity'];
        $unitPrice = $item['Price'];
        $subtotal = $quantity * $unitPrice;

        $detailSuccess = executeQuery(
            "INSERT INTO orderdetail (OrderID, ProductSizeID, Quantity, UnitPrice, Subtotal)
             VALUES (?, ?, ?, ?, ?)",
            [$orderId, $productSizeId, $quantity, $unitPrice, $subtotal]
        );
        
        if (!$detailSuccess) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Lưu chi tiết đơn hàng thất bại."]);
            exit();
        }
    }

    // 💸 Lưu thanh toán
    $paymentSuccess = executeQuery(
        "INSERT INTO paymentdetail (OrderID, PaymentMethod, CardOwner, CardNumber, CVV, ExpiryDate)
         VALUES (?, ?, ?, ?, ?, ?)",
        [$orderId, $paymentMethod, $cardOwner, $cardNumber, $cvv, $expiryDate]
    );

    if (!$paymentSuccess) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Lưu thông tin thanh toán thất bại."]);
        exit();
    }

    // 🧹 Nếu là checkout (giỏ hàng), xoá sản phẩm khỏi db cart
    if ($data['action'] === 'checkout') {
        foreach ($cart as $item) {
            $productSizeId = $item['ProductSizeID'];
            executeQuery("DELETE FROM cart WHERE UserID = ? AND ProductSizeID = ?", [$userId, $productSizeId]);
        }
    }

    echo json_encode(["success" => true, "orderId" => $orderId, "message" => "Đặt hàng thành công!"]);
    exit();

} catch (Exception $e) {
    error_log("❌ Checkout error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Có lỗi xảy ra: " . $e->getMessage()
    ]);
    exit();
}
