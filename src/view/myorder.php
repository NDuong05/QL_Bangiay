<script>
    $(document).on('click', '.btn-cancel', function () {
        const orderID = $(this).data('id');
        const $button = $(this); // Save reference to the button
        cancelOrder(orderID, $button);
    });


    function cancelOrder(orderID, $button) {
        const confirmCancel = confirm("❓Bạn có chắc chắn muốn hủy đơn hàng này không?");
        if (!confirmCancel) {
            return;
        }

        $.ajax({
            url: 'http://localhost/Web2/src/controller/db_controller/cancel_order.php',
            type: 'POST',
            data: { order_id: orderID },
            success: function (response) {
                alert("🗑️ Đơn hàng đã được hủy!");
                $button.remove();
                $(`#${orderID}`).html(`
                    <div style="background-color: var(--stat-cancel)">
                        <p class="display-order-status">
                            Status: <span>Cancelled</span>
                            <span><i class="fa-solid fa-xmark"></i></span>
                        </p>
                    </div> 
                    <button onclick="showOrderDetail('${orderID}')">Details</button>
                `);

            },
            error: function (xhr, status, error) {
                alert("❌ Hủy đơn hàng thất bại: " + error);
            }
        });
    }


    function showOrderDetail(orderID) {
        const order = <?php echo json_encode($orders);?>.find(order => order.OrderID == orderID);
        const paymentDetail = <?php echo json_encode($paymentdetail);?>.find(paymentdetail => paymentdetail.OrderID == orderID);
        const province = <?php echo json_encode($provinces);?>.find(prov => prov.province_id == order.ProvinceID);
        const district =  <?php echo json_encode($districts);?>.find(dis => dis.district_id == order.DistrictID);
        const ward =  <?php echo json_encode($wards);?>.find(war =>war.wards_id == order.WardID);

        const provinceName = province ? province.name : '(Unknown Province)';
        const districtName = district ? district.name : '(Unknown District)';
        const wardName = ward ? ward.name : '(Unknown Ward)';

        const FullAddress = `${wardName}, ${districtName}, ${provinceName}`;
        if (!order) {
            console.error("Order not found");
            return;
        }
        // console.log(order);
    const orderDetail = document.getElementById("order-detail");
    orderDetail.innerHTML = `
                    <div class="modal-container mdl-cnt">
                    <h3 class="modal-container-title">ORDER DETAIL</h3>
                    <a onclick="closeModal()" style="position: absolute; right: 16px"><i class="fa-regular fa-circle-xmark"></i></a>
                    <div class="order-detail-row">
                        <span><i class="fa-solid fa-hashtag"></i>Order ID: </span>
                        <span>${order.OrderID}</span>
                    </div>
                    <div class="order-detail-row">
                        <span><i class="fa-regular fa-calendar"></i>Purchase date: </span>
                        <span>${formatDate(order.OrderDate)}</span>
                    </div>
                    <div class="order-detail-row">
                        <span><i class="fa-solid fa-map-location-dot"></i>Delivery Address: </span>
                        <p>${order.ShippingAddress},${FullAddress}</p>
                    </div>
                    <div class="order-detail-row">
                        <span><i class="fa-solid fa-cash-register"></i>Payment method: </span>
                        <span>${paymentDetail.PaymentMethod}</span>
                    </div>
                    <div class="order-detail-row card-payment">
                        <span><i class="fa-solid fa-user"></i>Card owner: </span>
                        <span>${paymentDetail.CardOwner}</span>
                    </div>
                    <div class="order-detail-row card-payment">
                        <span><i class="fa-regular fa-credit-card"></i>Card number: </span>
                        <span>${paymentDetail.CardNumber}</span>
                    </div>
                </div>
    `;

    // if (order.payment.method.toLowerCase() != "card") {
    //     orderDetail.querySelectorAll(".card-payment").forEach(item => item.style.display = "none");
    // }

    orderDetail.classList.toggle("open");
}

    function closeModal() {
        const orderDetail = document.getElementById("order-detail");
        orderDetail.classList.remove("open");
    }

    function formatDate(dateString) {
        const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
        return new Date(dateString).toLocaleDateString('en-GB', options);
    }
</script>

<?php
function order_statusColor($status) {
    switch ($status) {
        case "Pending":
            return "--stat-pending";
        case "Processed":
            return "--stat-delivering";
        case "Received":
            return "--stat-completed";
        case "Cancelled":
            return "--stat-cancel";
        default:
            return "--stat-default"; // Fallback color
    }
}
function order_statusIcon($status) {
    switch ($status) {
        case "Pending":
            return "fa-regular fa-hourglass-half";
        case "Processed":
            return "fa-solid fa-truck";
        case "Received":
            return "fa-solid fa-circle-check";
        case "Cancelled":
            return "fa-solid fa-xmark";
        default:
            return "fa-solid fa-question"; // Fallback icon
    }
}

function getOrderDetailByOrderID($order_id){
    $conn = connectdb();
    $sql = "SELECT * FROM orderdetail WHERE OrderID = :OrderID";    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':OrderID', $order_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductBySizeID($product_size_id){
    $conn = connectdb();
    $sql = "SELECT * FROM product p
            JOIN productsize ps ON p.ProductID = ps.ProductID
            JOIN orderdetail od ON ps.ProductSizeID = od.ProductSizeID
            WHERE ps.ProductSizeID = :ProductSizeID";    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':ProductSizeID', $product_size_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getBrandByID($brand_id){
    $conn = connectdb();
    $sql = "SELECT * FROM brand WHERE BrandID = :BrandID";    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':BrandID', $brand_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['BrandName'];
}

function getSizeByProductSizeID($ProductSizeID){
    $conn = connectdb();
    $sql = "SELECT * FROM productsize WHERE ProductSizeID = :ProductSizeID";    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':ProductSizeID', $ProductSizeID, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['Size'];
}
?>
<!-- USER - ORDER HISTORY -->
<div class="container order-history toggle-page" id="order-history">
                <div class="main-account">
                <a href = "index.php?pg=home" class = "back2HomeBtn"><span> < Back </span></a>
                    <div class="main-account-header">
                        <h2>ORDER HISTORY</h2>
                        <?php
                            echo '<p>Hey, <span class="display-username">'. $_SESSION['user']['Username'].'</span>! Check out what you have ordered so far!</p>';
                        ?>
                    </div>
                    <div class="main-account-body">
                        <div class="main-account-body-col">
                            <?php 
                                $kq = "";
                                foreach ($orders as $order) {
                                    $total = 0;
                                    $order_statusIcon = order_statusIcon($order['Status']);
                                    $order_statusColor = order_statusColor($order['Status']);
                                    $order_id = $order['OrderID'];
                                    extract($order); // Lấy các thông tin từ đơn hàng
                                    $kqdetail = ""; // Bắt đầu chuỗi chi tiết đơn hàng
                                    $total = 0;
                                    $cancelBtn = "";
                                    if($Status == "Pending"){
                                        $cancelBtn = '<button style="background-color: var(--stat-cancel); color: #f5f5f5" class="btn-cancel" data-id="' . $OrderID . '">Cancel</button>';
                                    }

                                    $orderdetail = getOrderDetailByOrderID($order_id);
                                    foreach ($orderdetail as $item) {
                                        $product = getProductBySizeID($item['ProductSizeID']);
                                        $brand = getBrandByID($product['BrandID']);
                                        $size = getSizeByProductSizeID($item['ProductSizeID']);
                                        $total += $product['Price'] * $item['Quantity'];

                                        $kqdetail .= '<div class="modal-container cart-item" data-productID="'.$product['ProductID'].'">
                                                        <div class="img-container">
                                                            <img src="'.$product['ImageURL'].'">
                                                        </div>
                                                        <div class="cart-item-info">
                                                            <p class="display-product-name">'.$product['ProductName'].'</p>
                                                            <p>Brand: <span class="display-product-brand">'.$brand.'</span></p>
                                                            <p>Size: <span class="display-product-size">'.$size.'</span></p>
                                                            <p class="display-product-price">'.number_format($product['Price'], 0, ',', '.').' ₫</p>
                                                        </div>
                                                        <div class="cart-item-amount">
                                                            <p>x<span class="display-product-quantity">'.$item['Quantity'].'</span></p>
                                                        </div>
                                                    </div>';
                                    }

                                    // Bây giờ nối vào $kq
                                    $kq .= '<div class="modal-container order-history" data-orderID="'.$OrderID.'">
                                                <div class="cart-main">
                                                    <div class="cart-body" style="max-height: 520px; overflow-y: auto">'
                                                        . $kqdetail .
                                                    '</div>
                                                    <div class="cart-footer">
                                                        <div class="cart-totalprice">
                                                            <p>GRAND TOTAL</p>
                                                            <p class="display-totalprice">' . number_format($total, 0, ',', '.') . ' ₫</p>
                                                        </div>
                                                        <div class="cart-item-status" id="'.$OrderID.'">
                                                            <div style="background-color: var('.$order_statusColor.')">
                                                                <p class="display-order-status">Status: <span>'.$Status.'</span>
                                                                <span><i class="'.$order_statusIcon.'"></i></span></p>
                                                            </div> 
                                                            '.$cancelBtn.'
                                                            <button onclick="showOrderDetail('.$OrderID.')">Details</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>';
                                                }
                                echo $kq;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- MODAL ORDER DETAIL -->
            <div class="modal order-detail" id="order-detail">
            </div>
            <div class="modal product-detail">
                        <button class="modal-close close-popup"><i class="fa-solid fa-xmark"
                                style="color: white;"></i></button>
                        <div class="modal-container product-detail-content mdl-cnt" id="product-detail-content">
                        </div>
                    </div>
<!-- CART -->
<div class="modal sidebar cart" id="cart">
                <div class="sidebar-main mdl-cnt">
                    <div class="cart-header">
                        <p>YOUR CART</p>
                        <a onclick="toggleModal('cart') ">CLOSE</a>
                    </div>
                    <div class="cart-body"></div>
                    <div class="cart-footer">
                        <div class="cart-totalprice">
                            <p>GRAND TOTAL</p>
                            <p class="display-totalprice">0</p>
                        </div>
                        <div class="cart-btns">
                            <button onclick="toggleModal('cart')">CONTINUE SHOPPING</button>
                            <button onclick="window.checkoutMode = 'cart'; toggleModal('checkout-page'); showCartCheckout()" class="checkout-btn"
                                id="cart-checkout-btn">TO CHECKOUT</button>
                        </div>
                    </div>
                </div>
            </div>
<!-- CHECKOUT -->
<div class="checkout-page" id="checkout-page">
                <div class="checkout-header">
                    <div class="checkout-return">
                        <a onclick="handleReturnFromCheckout()"><i class="fa-solid fa-angle-left"></i></a>
                    </div>
                    <h1 class="checkout-title">CHECKOUT</h1>
                </div>
                <div class="checkout-section container">
                    <div class="checkout-col-left">
                        <h2 class="checkout-col-title">ORDER INFO</h2>
                        <div class="checkout-col-content">
                            <div class="content-group">
                                <h3 class="checkout-content-label">Address</h3>
                                <!-- Tùy chọn "Nhập từ tài khoản" -->
                                <div class="payment-option">
                                    <label for="default-address-option" onclick="toggleAddressMethod('default')">
                                        <input class="checkout-option" type="radio" name="address-option"
                                            id="default-address-option" checkout-type="default-address" checked>
                                        <span>Default address <i>(from your account)</i></span>
                                    </label>
                                    <input id="default-address" class="form-input-bar option-detail display-address"
                                        type="text" disabled style="display:block">
                                </div>
                                <!-- Tùy chọn "Nhập mới" -->
                                <div class="payment-option">
                                    <label for="new-address-option" onclick="toggleAddressMethod('new')">
                                        <input class="checkout-option" type="radio" name="address-option"
                                            id="new-address-option" checkout-type="new-address">
                                        <span>New address</span>
                                    </label>
                                    <div class="option-detail content-group" id="new-address">
                                        <input type="text" class="note-order form-input-bar"
                                            placeholder="Enter new address" id="checkout-address-new"></input>
                                        <p class="form-msg-error"></p>
                                    </div>
                                </div>
                                <!-- Chọn vùng miền -->
                                <h3 class="checkout-content-label">Choose a region for delivery destination</h3>
                                <div class="region-selector">
                                    <select id="province" class="region-select">
                                        <option value="" disabled selected hidden>Province/City</option>
                                    </select>
                                    <select id="district" class="region-select">
                                        <option value="" disabled selected hidden>District</option>
                                    </select>
                                    <select id="ward" class="region-select">
                                        <option value="" disabled selected hidden>Ward/Commune</option>
                                    </select>
                                </div>
                                <p class="form-msg-error"></p>
                            </div>
                            <div class="content-group">
                                <h3 class="checkout-content-label">Payment method</h3>
                                <!-- Phần thanh toán bằng tiền mặt -->
                                <div class="payment-option">
                                    <label for="cash" onclick="togglePaymentMethod('cash')">
                                        <input class="checkout-option" type="radio" name="payment-method" id="cod"
                                            value="cod" checked>
                                        <span>COD (cash on deivery)</span>
                                    </label>
                                    <div id="cash-option" class="option-detail" style="display:block">
                                        <p><b><i>You will pay with cash on delivery.</i></b></p>
                                    </div>
                                </div>
                                <!-- Phần thanh toán bằng thẻ -->
                                <div class="payment-option">
                                    <label for="card" onclick="togglePaymentMethod('card')">
                                        <input class="checkout-option" type="radio" name="payment-method" id="card"
                                            value="card">
                                        <span>Credit card</span>
                                    </label>
                                    <div id="card-option" class="option-detail">
                                        <form>
                                            <div class="form-group">
                                                <label for="card-owner-name">Owner</label>
                                                <input type="text" class="form-input-bar"
                                                    placeholder="Card owner's name" id="card-owner-name"
                                                    style="text-transform: uppercase;">
                                                <p class="form-msg-error"></p>
                                            </div>
                                            <div class="form-group">
                                                <label for="cvv">CVV</label>
                                                <input type="password" class="form-input-bar" placeholder="CVV"
                                                    minlength="3" maxlength="3" id="cvv">
                                                <p class="form-msg-error"></p>
                                            </div>
                                            <div class="form-group">
                                                <label for="card-number">Card Number</label>
                                                <input type="text" class="form-input-bar" placeholder="Card number"
                                                    id="card-number" minlength="16" maxlength="16">
                                                <p class="form-msg-error"></p>
                                            </div>
                                            <div class="form-group">
                                                <label for="card-expdate">Exp. date</label>
                                                <input type="month" name="card-expdate" id="card-expdate">
                                                <p class="form-msg-error"></p>
                                            </div>
                                            <div class="cardsthanhtoan">
                                                <div class="form-group">
                                                <label>
                                                    <input type="checkbox" id="save-card-checkbox">
                                                    Save this card for future purchases
                                                </label>
                                                </div>
                                                <div class="card-icons">
                                                    <div class="img-container">
                                                        <img src="view/layout/asset/img/card/mc.png" alt="">
                                                    </div>
                                                    <div class="img-container">
                                                        <img src="view/layout/asset/img/card/pp.png" alt="">
                                                    </div>
                                                    <div class="img-container">
                                                        <img src="view/layout/asset/img/card/vi.png" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="checkout-col-right">
                        <h2 class="checkout-col-title">ORDER DETAIL</h2>
                        <div class="checkout-col-content">
                            <div class="content-group">
                                <h2 class="checkout-content-label">Products</h2>
                                <div class="cart-body"></div>
                            </div>
                            <div class="content-group">
                                <div class="price-row">
                                    <span>Products cost</span>
                                    <span class="display-totalprice"></span>
                                </div>
                                <div class="price-row">
                                    <span>Delivery fee</span>
                                    <span class="display-deliveryfee"></span>
                                </div>
                                <div class="sum-bill">
                                    <h2>Total</h2>
                                    <span class="display-totalorder">0<sup>đ</sup></span>
                                </div>
                                <div class="confirm-check">
                                    <button onclick="handleCheckout()">ORDER NOW</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script src="view/layout/js/main.js"> </script>    
            <script src="view/layout/js/toast-msg.js"> </script> 