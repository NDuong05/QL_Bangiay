<?php
    $categoryname = strtoupper($catalogname);  
    if($categoryname == ""){
        $categoryname = "PRODUCTS";
    }

    $kq = "";
    foreach ($productlist as $item) {
        extract($item);
        $kq .= '
        <div class="product-box" onclick= "detailProduct('. $id .')">
            <div class="img-container">
                <img src="' . $image . '" alt="' . $name . '" onerror="this.src=\'view/layout/asset/img/catalogue/coming-soon.jpg\'" />
            </div>
            <div class="shoes-name">' . $name . '</div>
            <div class="shoes-price">' . number_format($price, 0, ',', '.') . ' ₫</div>
        </div>';
    }
?>

<script type="text/javascript">
    $(document).ready(function(){
        let timeout = null;

        $('#search-bar').on('input', function(){
            clearTimeout(timeout);
            const keyword = $(this).val().trim();

            timeout = setTimeout(function(){
                $.ajax({
                    type: "GET",
                    url: "http://localhost/Web2/src/controller/db_controller/xulySearchProduct.php",
                    data: { tensp: keyword , catalogID:<?php 
                                                            if((isset($_GET['idcatalog']))&&($_GET['idcatalog']>0))
                                                                echo $_GET['idcatalog'];
                                                            else echo 0; 
                                                        ?>},
                    success: function(result){
                        if(result.trim() === ""){
                            $('#home-product').html("<p>Không tìm thấy sản phẩm phù hợp.</p>");
                        } else {
                            $('#home-product').html(result);
                        }
                    }
                });
            }, 300); // delay 300ms sau khi ngưng gõ
        });

        $('.apply-filter-btn').click(function() {
            clearTimeout(timeout);
            const brands = $('.filter-option.filter-brand.active').map(function() {
                return $(this).data('filter');
            }).get();

            const sizes = $('.filter-option.filter-size.active').map(function() {
                return $(this).data('filter');
            }).get();

            const genders = $('.filter-option.filter-gender.active').map(function() {
                return $(this).data('filter');
            }).get();

            const pricelowerbound = $('#price-lowerbound').val();
            const priceupperbound = $('#price-upperbound').val();
            // console.log(brands);
            // console.log(sizes);
            // console.log(genders);
            timeout = setTimeout(function(){
                $.ajax({
                    type: "GET",
                    url: "http://localhost/Web2/src/controller/db_controller/xulySearchProduct.php",
                    data: {catalogID:<?php 
                                                            if((isset($_GET['idcatalog']))&&($_GET['idcatalog']>0))
                                                                echo $_GET['idcatalog'];
                                                            else echo 0; 
                                                        ?>,filterbrands: brands , filtersizes: sizes , filtergender: genders ,
                                                        pricelowerbound: pricelowerbound, priceupperbound: priceupperbound},
                    success: function(result){
                        if(result.trim() === ""){
                            $('#home-product').html("<p>Không tìm thấy sản phẩm phù hợp.</p>");
                        } else {
                            $('#home-product').html(result);
                        }
                    }
                });
            }, 300); // delay 300ms sau khi ngưng gõ
        });

        $('.apply-filter-btn').click(function() {
            clearTimeout(timeout);
            const brands = $('.filter-option.filter-brand.active').map(function() {
                return $(this).data('filter');
            }).get();

            const sizes = $('.filter-option.filter-size.active').map(function() {
                return $(this).data('filter');
            }).get();

            const genders = $('.filter-option.filter-gender.active').map(function() {
                return $(this).data('filter');
            }).get();

            const pricelowerbound = $('#price-lowerbound').val();
            const priceupperbound = $('#price-upperbound').val();
            // console.log(brands);
            // console.log(sizes);
            // console.log(genders);
            timeout = setTimeout(function(){
                $.ajax({
                    type: "GET",
                    url: "http://localhost/Web2/src/controller/db_controller/xulySearchProduct.php",
                    data: {catalogID:<?php 
                                                            if((isset($_GET['idcatalog']))&&($_GET['idcatalog']>0))
                                                                echo $_GET['idcatalog'];
                                                            else echo 0; 
                                                        ?>,filterbrands: brands , filtersizes: sizes , filtergender: genders ,
                                                        pricelowerbound: pricelowerbound, priceupperbound: priceupperbound},
                    success: function(result){
                        if(result.trim() === ""){
                            $('#home-product').html("<p>Không tìm thấy sản phẩm phù hợp.</p>");
                        } else {
                            $('#home-product').html(result);
                        }
                    }
                });
            }, 300); // delay 300ms sau khi ngưng gõ
        });
    });
</script>


<main>
            <!-- TOAST -->
            <div class="container toast" id="toast"></div>
            <!-- CART -->
            <div class="modal sidebar cart" id="cart">
                <div class="sidebar-main mdl-cnt">
                    <div class="cart-header">
                        <p>YOUR CART</p>
                        <a onclick="toggleModal('cart')">CLOSE</a>
                    </div>
                    <div class="cart-body"></div>
                    <div class="cart-footer">
                        <div class="cart-totalprice">
                            <p>GRAND TOTAL</p>
                            <p class="display-totalprice">0</p>
                        </div>
                        <div class="cart-btns">
                            <button onclick="toggleModal('cart')">CONTINUE SHOPPING</button>
                            <button onclick="toggleModal('checkout-page'); showCartCheckout()" class="checkout-btn"
                                id="cart-checkout-btn">TO CHECKOUT</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- CATALOGUE -->
            <div class="catalogue-container always-active-page" id="catalogue">
                <!-- BANNER -->
                <div class="banner">
                    <div class="banner-images"></div> <!-- Images will be dynamically inserted here -->

                    <!-- Banner Navigation Buttons (Arrows) -->
                    <div class="banner-buttons">
                        <button class="prev" onclick="moveSlide(-1)">&#10094;</button>
                        <button class="next" onclick="moveSlide(1)">&#10095;</button>
                    </div>
                </div>
                <div class="catalogue-name" id="display-catalogue-name"><?=$categoryname?></div>
                <div class="search-bar">
                    <label for="search-bar"><i class="fas fa-search"></i></label>
                    <input class="form-input-bar filter-option" type="text" name="search-bar" id="search-bar"
                        placeholder="Search products by name">
                </div>
                <div class="details-search-bar hide-on-pc show-on-mobile"
                    onclick="toggleModal('details-search-sidebar')">
                    <i class="fa-solid fa-bars"></i>
                    <p>Filter by</p>
                </div>
                <div class="catalogue-info">
                    <div class="products-amount">
                        <p><span id="display-catalogue-amount"></span>product(s)</p>
                    </div>
                    <div class="sortby">
                        <div>
                            <span>Sort by:</span>
                            <span id="sortby-mode-display">None</span>
                            <span class="dropdown-arrow">&#9662;</span>
                        </div>
                        <!-- Hidden checkbox to control the dropdown -->
                        <div class="container float-dropdown">
                            <ul class="menu-list">
                                <li class="sortby-option" data-filter="1"><a>None</a></li>
                                <li class="sortby-option" data-filter="2"><a>Alphabetically, A-Z</a></li>
                                <li class="sortby-option" data-filter="3"><a>Alphabetically, Z-A</a></li>
                                <li class="sortby-option" data-filter="4"><a>Price, low to high</a></li>
                                <li class="sortby-option" data-filter="5"><a>Price, high to low</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- CATALOGUE - DETAILS SEARCH SIDEBAR -->
                <div class="modal details-search sidebar" id="details-search-sidebar">
                    <div class="sidebar-main mdl-cnt">
                        <a><i class="fa-solid fa-xmark" onclick="toggleModal('details-search-sidebar')"></i></a>
                        <div class="dropdown">
                            <ul class="dropdown-header">
                                <li onclick="toggleDropdown('brand-menu-sidebar')">BRAND</li>
                            </ul>
                            <ul class="dropdown-menu" id="brand-menu-sidebar">
                                <li class="filter-option filter-brand" data-filter="Adidas">ADIDAS</li>
                                <li class="filter-option filter-brand" data-filter="Converse">CONVERSE</li>
                                <li class="filter-option filter-brand" data-filter="Nike">NIKE</li>
                                <li class="filter-option filter-brand" data-filter="BirkenStock">BIRKENSTOCK</li>
                                <li class="filter-option filter-brand" data-filter="Teva">TEVA</li>
                                <li class="filter-option filter-brand" data-filter="Fila">FILA</li>
                            </ul>
                        </div>
                        <!-- SIZE Dropdown -->
                        <div class="dropdown">
                            <ul class="dropdown-header">
                                <li onclick="toggleDropdown('size-menu-sidebar')">SIZE</li>
                            </ul>
                            <ul class="dropdown-menu" id="size-menu-sidebar">
                                <li class="filter-option filter-size" data-filter="20">20</li>
                                <li class="filter-option filter-size" data-filter="21">21</li>
                                <li class="filter-option filter-size" data-filter="22">22</li>
                                <li class="filter-option filter-size" data-filter="23">23</li>
                                <li class="filter-option filter-size" data-filter="24">24</li>
                                <li class="filter-option filter-size" data-filter="35">35</li>
                                <li class="filter-option filter-size" data-filter="36">36</li>
                                <li class="filter-option filter-size" data-filter="37">37</li>
                                <li class="filter-option filter-size" data-filter="38">38</li>
                                <li class="filter-option filter-size" data-filter="39">39</li>
                                <li class="filter-option filter-size" data-filter="41">41</li>
                                <li class="filter-option filter-size" data-filter="42">42</li>
                                <li class="filter-option filter-size" data-filter="43">43</li>
                            </ul>
                        </div>

                        <!-- GENDER Dropdown -->
                        <div class="dropdown">
                            <ul class="dropdown-header">
                                <li onclick="toggleDropdown('gender-menu-sidebar')">GENDER</li>
                            </ul>
                            <ul class="dropdown-menu" id="gender-menu-sidebar">
                                <li class="filter-option filter-gender" data-filter="M">Male</li>
                                <li class="filter-option filter-gender" data-filter="F">Female</li>
                                <li class="filter-option filter-gender" data-filter="U">Unisex</li>
                            </ul>
                        </div>
                        <!-- PRICE Dropdown -->
                        <div class="dropdown">
                            <ul class="dropdown-header">
                                <li onclick="toggleDropdown('price-menu-sidebar')">PRICE</li>
                            </ul>
                            <ul class="dropdown-menu" id="price-menu-sidebar">
                                <label for="price-lowerbound-sidebar">Between</label>
                                <input class="form-input-bar filter-option" type="number"
                                    name="price-lowerbound-sidebar" id="price-lowerbound-sidebar"
                                    placeholder="Minimun price" inputmode="tel">
                                <label for="price-upperbound-sidebar">and</label>
                                <input class="form-input-bar filter-option" type="number"
                                    name="price-upperbound-sidebar" id="price-upperbound-sidebar"
                                    placeholder="Maximum price" inputmode="tel">
                            </ul>
                        </div>
                        <div>
                            <button class="apply-filter-btn" onclick="toggleModal('details-search-sidebar'); ">APPLY
                                FILTER</button>
                            <a onclick="resetFilter()"><i class="fa-solid fa-rotate-left"></i><span>RESET
                                    FILTER</span></a>
                        </div>
                    </div>
                </div>
                <!-- CATALOGUE - MAIN -->
                <div class="main-container">
                    <div class="details-search hide-on-mobile">
                        <div class="dropdown">
                            <ul class="dropdown-header">
                                <li onclick="toggleDropdown('brand-menu')">BRAND</li>
                            </ul>
                            <ul class="dropdown-menu" id="brand-menu">
                                <?php
                                    $brands = "";
                                    foreach ($brandList as $item) {
                                        extract($item);
                                        $brands .= '<li class="filter-option filter-brand" data-filter="'.$BrandID.'">'.$BrandName.'</li>';
                                    }
                                    echo $brands;   
                                ?>
                            </ul>
                        </div>
                        <!-- SIZE Dropdown -->
                        <div class="dropdown">
                            <ul class="dropdown-header">
                                <li onclick="toggleDropdown('size-menu')">SIZE</li>
                            </ul>
                            <ul class="dropdown-menu" id="size-menu">
                                <li class="filter-option filter-size" data-filter="20">20</li>
                                <li class="filter-option filter-size" data-filter="21">21</li>
                                <li class="filter-option filter-size" data-filter="22">22</li>
                                <li class="filter-option filter-size" data-filter="23">23</li>
                                <li class="filter-option filter-size" data-filter="24">24</li>
                                <li class="filter-option filter-size" data-filter="35">35</li>
                                <li class="filter-option filter-size" data-filter="36">36</li>
                                <li class="filter-option filter-size" data-filter="37">37</li>
                                <li class="filter-option filter-size" data-filter="38">38</li>
                                <li class="filter-option filter-size" data-filter="39">39</li>
                                <li class="filter-option filter-size" data-filter="41">41</li>
                                <li class="filter-option filter-size" data-filter="42">42</li>
                                <li class="filter-option filter-size" data-filter="43">43</li>
                            </ul>
                        </div>

                        <!-- GENDER Dropdown -->
                        <div class="dropdown">
                            <ul class="dropdown-header">
                                <li onclick="toggleDropdown('gender-menu')">GENDER</li>
                            </ul>
                            <ul class="dropdown-menu" id="gender-menu">
                                <li class="filter-option filter-gender" data-filter="M">Male</li>
                                <li class="filter-option filter-gender" data-filter="F">Female</li>
                                <li class="filter-option filter-gender" data-filter="U">Unisex</li>
                            </ul>
                        </div>

                        <!-- PRICE Dropdown -->
                        <div class="dropdown">
                            <ul class="dropdown-header">
                                <li onclick="toggleDropdown('price-menu')">PRICE</li>
                            </ul>
                            <ul class="dropdown-menu" id="price-menu">
                                <label for="price-lowerbound">Between</label>
                                <input class="form-input-bar filter-option" type="number" name="price-lowerbound"
                                    id="price-lowerbound" placeholder="Minimun price" inputmode="numeric">
                                <label for="price-upperbound">and</label>
                                <input class="form-input-bar filter-option" type="number" name="price-upperbound"
                                    id="price-upperbound" placeholder="Maximum price" inputmode="numeric">
                            </ul>
                        </div>
                        <!-- RESET FILTER -->
                        <div class="details-search-control">
                            <button class="apply-filter-btn">APPLY FILTER</button>
                            <a onclick="resetFilter()">RESET FILTER</a>
                        </div>
                    </div>
                    <!-- DISPLAY PRODUCTS -->
                    <div class="shoes-box-container show-on-mobile">
                        <div class="product-box-container" id="home-product">
                        <?php
                           $kq = "";
                           foreach ($productlist as $item) {
                               extract($item);
                               $kq .= '
                               <div class="product-box" onclick= "detailProduct('. $id .')">
                                   <div class="img-container">
                                       <img src="' . $image . '" alt="' . $name . '" onerror="this.src=\'view/layout/asset/img/catalogue/coming-soon.jpg\'" />
                                   </div>
                                   <div class="shoes-name">' . $name . '</div>
                                   <div class="shoes-price">' . number_format($price, 0, ',', '.') . ' ₫</div>
                               </div>';
                           }
                           echo $kq;                           
                            ?>
                        </div>
                        <div class="page-nav">
                            <ul class="page-nav-list">
                            </ul>
                        </div>
                    </div>
                    <div class="modal product-detail">
                        <button class="modal-close close-popup"><i class="fa-solid fa-xmark"
                                style="color: white;"></i></button>
                        <div class="modal-container product-detail-content mdl-cnt" id="product-detail-content">
                        </div>
                    </div>
                </div>
            </div>
            <!-- CHECKOUT -->
            <div class="checkout-page" id="checkout-page">
                <div class="checkout-header">
                    <div class="checkout-return">
                        <a onclick="toggleModal('checkout-page')"><i class="fa-solid fa-angle-left"></i></a>
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
                                            placeholder="Enter new address (full)" id="checkout-address-new"></input>
                                        <p class="form-msg-error"></p>
                                    </div>
                                </div>
                                <!-- Chọn vùng miền -->
                                <h3 class="checkout-content-label">Choose a region for delivery destination <i
                                        style="font-weight: normal;">(this will help us locate you faster)</i></h3>
                                <div class="region-selector">
                                    <select id="province" class="region-select" onchange="updateDistricts()">
                                        <option value="">Province/City</option>
                                    </select>
                                    <select id="district" class="region-select" onchange="updateWards()">
                                        <option value="">District</option>
                                    </select>
                                    <select id="ward" class="region-select">
                                        <option value="">Ward/Commune</option>
                                    </select>
                                </div>
                                <p class="form-msg-error"></p>
                            </div>
                            <div class="content-group">
                                <h3 class="checkout-content-label">Payment method</h3>
                                <!-- Phần thanh toán bằng tiền mặt -->
                                <div class="payment-option">
                                    <label for="cash" onclick="togglePaymentMethod('cash')">
                                        <input class="checkout-option" type="radio" name="payment-method" id="cash"
                                            value="cash" checked>
                                        <span>Cash</span>
                                    </label>
                                    <div id="cash-option" class="option-detail" style="display:block">
                                        <p><b><i>You will pay with cash.</i></b></p>
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
                                                <input type="number" class="form-input-bar" placeholder="CVV"
                                                    minlength="3" maxlength="3" id="cvv">
                                                <p class="form-msg-error"></p>
                                            </div>
                                            <div class="form-group">
                                                <label for="card-number">Card Number</label>
                                                <input type="number" class="form-input-bar" placeholder="Card number"
                                                    id="card-number" minlength="12">
                                                <p class="form-msg-error"></p>
                                            </div>
                                            <div class="form-group">
                                                <label for="card-expdate">Exp. date</label>
                                                <input type="date" name="card-expdate" id="card-expdate">
                                                <p class="form-msg-error"></p>
                                            </div>
                                            <div class="cardsthanhtoan">
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
        </main>
<script src="view/layout/js/main.js"></script>