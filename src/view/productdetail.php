<?php
require_once __DIR__ . "/../controller/db_controller/api.php";
// Lấy ID từ URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<h2>Error: Could not find the product's id</h2>");
}

$idProduct = intval($_GET['id']);
$product = getProductDetail($idProduct);

if (!$product) {
    die("<h2>Could not find the product</h2>");
}
?>
<div class="product-detail-content">
    <div class="img-container">
        <img src="<?= $product['image'] ?>" alt="" onerror="this.src='view/layout/asset/img/catalogue/coming-soon.jpg'">
    </div>
    <h2 class="product-title"><?= $product['name'] ?></h2>
    <div class="product-control">
        <div class="priceBox">
            <span class="current-price" data-price="<?= $product['price'] ?>"><?= number_format($product['price'], 0, ',', '.') ?> ₫</span>
        </div>
        <div class="buttons_added">
            <button class="minus is-form" type="button" value="-" onclick="decreasingNumber(this)">
                <i class="fa-solid fa-minus"></i>
            </button>
            
            <?php
                // Chọn size đầu tiên còn hàng để set mặc định cho input
                $defaultStock = 0;
                if (!empty($product['size'])) {
                    foreach ($product['size'] as $sizeData) {
                        if ($sizeData['Stock'] > 0) {
                            $defaultStock = $sizeData['Stock'];
                            break;
                        }
                    }
                }
            ?>

            <input class="input-qty"
                type="number"
                min="1"
                max="<?= $defaultStock ?>"
                value="1"
                data-stock="<?= $defaultStock ?>"
                readonly>
            <div class="stock-quantity" style="display:none;"><?= $defaultStock ?></div>


            <button class="plus is-form" type="button" value="+" onclick="increasingNumber(this)">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
    </div>
    
    <!-- Phần hiển thị size -->
    <div class="size-container">
    <?php
    if (!empty($product['size'])) {
        foreach ($product['size'] as $sizeData) {
            $productSizeId = $sizeData['ProductSizeID'];
            $size = $sizeData['Size'];
            $stock = $sizeData['Stock'];
    
            // Disable nút nếu stockQuantity bằng 0
            $disabled = ($stock <= 0) ? "disabled" : "";
            echo "<button class='size-button' data-sizeid='{$productSizeId}' data-stock='{$stock}' {$disabled}>{$size}</button>";
        }
    } else {
        echo "<p>Không có thông tin về size cho sản phẩm này.</p>";
    }       
    ?>
</div>

    <div class="modal-footer">
        <div class="price-total">
            <span class="thanhtien">Total</span>
            <span class="price" ><?= number_format($product['price'], 0, ',', '.') ?> ₫</span>
        </div>
        <div class="modal-footer-control">
            <button class="checkout-btn" id="checkout-modal-btn">Buy now</button>
            <button class="button-dat" id="add-cart"><i class="fa-solid fa-cart-shopping"></i></button>
        </div>
    </div>
</div>


