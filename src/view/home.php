<script>
    
    document.addEventListener("click", (e) => {
    const seeMore = e.target.closest(".see-more");
    if (!seeMore) return;

    document.querySelector(".main-container-home").classList.remove("Active");
    document.querySelector(".product-page").classList.add("Active");
    document.querySelector(".banner").style.display = "none";

    const id = seeMore.id;
    const filter = seeMore.dataset.filter; // có thể dùng .dataset

    document.querySelector(".search-bar").style.display = "flex";
    document.querySelector(".catalogue-info").style.display = "flex";
    
    let displayCatalogueName = document.getElementById(
      "display-catalogue-name"
    );
    displayCatalogueName.innerText = e.target.id;
    //  main_container
    html_mainc();
    
    // them nut nhan cho filter option
    addListener_filterOption();
    
    resetFilter();
    });
</script>
<?php 
require_once("header.php"); 
require_once "./controller/controller.php";
?>



<main>
            <!-- TOAST -->
            <div class="container toast" id="toast"></div>
            <!-- LOG-IN -->
            <div class="modal login-user" id="login-user">
                <div class="main-login mdl-cnt">
                    <i class="fa-regular fa-circle-xmark form-close" onclick="toggleModal('login-user')"></i>
                    <div class="main-login-header">
                        <h2>LOGIN</h2>
                    </div>
                    <div class="main-login-body">
                        <form class="login-form" id="login-form">
                            <input class="form-input-bar" type="text" id="username-login" name="username-login"
                                placeholder="Username or Phone number*">
                            <p class="form-msg-error"></p>
                            <input class="form-input-bar" type="password" id="password-login" name="password-login"
                                placeholder="Password*">
                            <p class="form-msg-error"></p>
                            <button onclick="handleLoginForm()">LOGIN</button>
                        </form>
                    </div>
                    <div class="main-login-footer">
                        <p>DON'T HAVE AN ACCOUNT? <span><a onclick="toggleModal('signup-user')">SIGN UP</a></span>
                        </p>
                    </div>
                </div>
            </div>
            <!-- SIGN-UP -->
            <div class="modal login-user signup-user" id="signup-user">
                <div class="main-login mdl-cnt">
                    <i class="fa-regular fa-circle-xmark form-close" onclick="toggleModal('signup-user')"></i>
                    <div class="main-login-header">
                        <h2>SIGN UP</h2>
                    </div>
                    <div class="main-login-body">
                        <form action="" class="login-form" id="signup-form">
                            <input class="form-input-bar" type="text" id="username-signup" name="username-signup"
                                placeholder="Username*" autocomplete="username">
                            <p class="form-msg-error"></p>

                            <input class="form-input-bar" type="text" id="fullname-signup" name="fullname-signup"
                                placeholder="Full Name*" autocomplete="name">
                            <p class="form-msg-error"></p>

                            <input class="form-input-bar" type="number" id="phone-signup" name="phone-signup"
                                placeholder="Phone number*" inputmode="tel">
                            <p class="form-msg-error"></p>

                            <input class="form-input-bar" type="text" id="address-signup" name="address-signup"
                                placeholder="Address*">
                            <p class="form-msg-error"></p>

                            <input class="form-input-bar" type="password" id="password-signup" name="password-signup"
                                placeholder="Password*">
                            <p class="form-msg-error"></p>

                            <input class="form-input-bar" type="password" id="confirm-password-signup"
                                name="password-signup" placeholder="Confirm Password*">
                            <p class="form-msg-error"></p>

                            <button onclick="handleSignupForm()">SIGN UP</button>
                        </form>
                    </div>
                    <div class="main-login-footer">
                        <p>ALREADY HAVE AN ACCOUNT? <span><a onclick="toggleModal('login-user')">LOGIN</a></span>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- MODAL ORDER DETAIL --> 
            <div class="modal order-detail" id="order-detail">
            </div>
            
            <!-- CART -->
            <div class="modal sidebar cart" id="cart">
                <div class="sidebar-main mdl-cnt">
                    <div class="cart-header">
                        <p>YOUR CART</p>
                        <a onclick="toggleModal('cart') ">CLOSE</a>
                    </div>
                    <div id="select-all-wrapper">
                        <input type="checkbox" id="select-all-checkbox" onclick="handleSelectAll(this)">
                        <label for="select-all-checkbox" style="cursor: pointer;">Choose all products</label>
                    </div>
                    <div class="cart-body"></div>
                    <div class="cart-footer">
                        <div class="cart-totalprice">
                            <p>GRAND TOTAL</p>
                            <p class="display-totalprice">0</p>
                        </div>
                        <div class="cart-btns">
                            <button onclick="toggleModal('cart')">CONTINUE SHOPPING</button>
                            <button onclick="handleCheckoutClick()" class="checkout-btn" id="cart-checkout-btn">TO CHECKOUT</button>
                        </div>
                    </div>
                </div>
            </div>

            
            <!-- CATALOGUE -->
            <div class="catalogue-container " id="catalogue">
                <!-- BANNER -->
                <div class="banner">
                    <div class="banner-images"></div> <!-- Images will be dynamically inserted here -->

                    <!-- Banner Navigation Buttons (Arrows) -->
                    <div class="banner-buttons">
                        <button class="prev" onclick="moveSlide(-1)">&#10094;</button>
                        <button class="next" onclick="moveSlide(1)">&#10095;</button>
                    </div>
                </div>
                <div class="main-container-home Active">
                    <div class="new-products-header">
                    <div class="line"></div>
                    <h2 class="new-products-title">Trending Styles</h2>
                    <div class="line"></div>
                    </div>
                    <br>
                    <div class="trending-container">
                        <div class="trending-box" onclick="detailProduct(6)">
                            <img src="view/layout/asset/img/catalogue/AIR-TERRA-HUMARA.jpg" alt="Sneaker"><br>
                            <h3>AIR-TERRA-HUMARA</h3>
                        </div>
                        <div class="trending-box" onclick="detailProduct(19)">
                            <img src="view/layout/asset/img/catalogue/RUN-STAR-HIKE-HI.jpg" alt="Sandal"><br>
                            <h3>RUN-STAR-HIKE-HI</h3>
                        </div>
                        <div class="trending-box" onclick="detailProduct(5)">
                            <img src="view/layout/asset/img/catalogue/AIR-MAX-90-LTR.jpg" alt="Kid"><br>
                            <h3>AIR-MAX-90-LTR</h3>
                        </div>
                    </div>
                    <div class="see-more filter-category" id="Trending Styles" data-filter="Product">see more</div>
                    
                    <div class="new-products-header">
                    <div class="line"></div>
                    <h2 class="new-products-title">Airizona Collection</h2>
                    <div class="line"></div>
                    </div>
                    <br>
                    <div class="trending-container">
                        <div class="trending-box" onclick="detailProduct(7)">
                            <img src="view/layout/asset/img/catalogue/AIRIZONA-VEG-THYME.jpg" alt="Sandal"><br>
                            <h3>AIRIZONA VEG THYME</h3>
                        </div>
                        <div class="trending-box" onclick="detailProduct(8)">
                            <img src="view/layout/asset/img/catalogue/ARIZONA-BLACKBIRKO-FLOR-SFB.jpg" alt="Sandal"><br>
                            <h3>ARIZONA BLACKBIRKO FLOR SFB</h3>
                        </div>
                        <div class="trending-box" onclick="detailProduct(9)">
                            <img src="view/layout/asset/img/catalogue/ARIZONA-TOBACCO-BROWN-OILED-LEATHER.jpg" alt="Sandal"><br>
                            <h3>ARIZONA TOBACCO BROWN OILDED LEATHER</h3>
                        </div>
                    </div>
                    <div class="see-more filter-category" id="Airizona Collection" data-filter="Sandal">see more</div>
                    
                    <div class="new-products-header">
                    <div class="line"></div>
                    <h2 class="new-products-title">Kid</h2>
                    <div class="line"></div>
                    </div>
                    <br>
                    <div class="trending-container">
                        <div class="trending-box" onclick="detailProduct(14)">
                            <img src="view/layout/asset/img/catalogue/GAZELLE-(PS).jpg" alt="Kid"><br>
                            <h3>GAZELLE (PS)</h3>
                        </div>
                        <div class="trending-box" onclick="detailProduct(22)">
                            <img src="view/layout/asset/img/catalogue/SL-72-RS-(PS).jpg" alt="Kid"><br>
                            <h3>SL72 RS (PS)</h3>
                        </div>
                        <div class="trending-box" onclick="detailProduct(23)">
                            <img src="view/layout/asset/img/catalogue/SL72-RS-(TD).jpg" alt="Kid"><br>
                            <h3>SL72 RS (TD)</h3>
                        </div>
                        <div class="trending-box" onclick="detailProduct(25)">
                            <img src="view/layout/asset/img/catalogue/STAN-SMITH-(TD).jpg" alt="Kid"><br>
                            <h3>STAN SMITH (TD)</h3>
                        </div>
                    </div>
                    <div class="see-more filter-category" id="Kid" data-filter="Kid">see more</div>
                    
                    <div class="new-products-header">
                        <div class="line"></div>
                        <h2 class="new-products-title">NEW PRODUCTS</h2>
                        <div class="line"></div>
                    </div>
                    <!-- slick start -->
                    
                    <div class="product-box-container-home slicker" id="home-product"> 
                        <?php
                            $kq = "";
                            foreach ($productlist as $item) {
                                extract($item);
                                $kq .= '
                                <div class="product-box-home" onclick= "detailProduct('. $id .')">
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
                    <div class="main-products-banner center">      
                        <div><img class="main-products-banner-img" src="view/layout/asset/img/banner/converse.jpg" alt="converse.jpg"></div>
                        <div><img class="main-products-banner-img" src="view/layout/asset/img/banner/nike.jpg" alt="nike.jpg"></div>
                        <div><img class="main-products-banner-img" src="view/layout/asset/img/banner/adidas.jpg" alt="adidas.jpg"></div>                                                             
                    </div>
                    <!-- <div class="modal product-detail" id="product-detail">
                            <button class="modal-close close-popup"><i class="fa-solid fa-xmark"
                                    style="color: white;"></i></button>
                            <div class="modal-container product-detail-content mdl-cnt" id="product-detail-content">
                            </div>
                    </div> -->
                </div>

                    <br><br>
                    <!-- product page -->
                    <div class="product-page">
                    <div class="catalogue-name" id="display-catalogue-name">HOME</div>
                <div class="search-bar" style="display:none">
                    <label for="search-bar"><i class="fas fa-search"></i></label>
                    <input class="form-input-bar filter-option" type="text" name="search-bar" id="search-bar"
                        placeholder="Search products by name">
                </div>
                <div class="details-search-bar hide-on-pc show-on-mobile"
                    onclick="toggleModal('details-search-sidebar')">
                    <i class="fa-solid fa-bars"></i>
                    <p>Filter by</p>
                </div>
                <div class="catalogue-info" style="display:none">
                    <div class="products-amount">
                       <?php
                            
                            echo ' <p><span id="display-catalogue-amount">' . count($productlist) . '</span> product(s)</p>';
                       ?>
                    </div>
                    <div class="sortby">
                        <div>
                            <span>Sort by:</span>
                            <span id="sortby-mode-display">Alphabetically, A-Z</span>
                            <span class="dropdown-arrow">&#9662;</span>
                        </div>
                        <!-- Hidden checkbox to control the dropdown -->
                        <div class="container float-dropdown">
                            <ul class="menu-list">
                                <li class="sortby-option"><a>Alphabetically, A-Z</a></li>
                                <li class="sortby-option"><a>Alphabetically, Z-A</a></li>
                                <li class="sortby-option"><a>Price, low to high</a></li>
                                <li class="sortby-option"><a>Price, high to low</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- CATALOGUE - DETAILS SEARCH SIDEBAR -->
                
                <!-- CATALOGUE - MAIN -->
                
                
                <!-- CATALOGUE - DETAILS SEARCH SIDEBAR -->
                
                <!-- CATALOGUE - MAIN -->
                    <div class="main-container">
                    
                        <!-- DISPLAY PRODUCTS -->
                    </div>
                        <!-- <div class="modal product-detail" id="product-detail">
                            <button class="modal-close close-popup"><i class="fa-solid fa-xmark"
                                    style="color: white;"></i></button>
                            <div class="modal-container product-detail-content mdl-cnt" id="product-detail-content">
                            </div>
                        </div> -->
                    </div>
                </div>
                <div class="modal product-detail" id="product-detail">
                            <button class="modal-close close-popup"><i class="fa-solid fa-xmark"
                                    style="color: white;"></i></button>
                            <div class="modal-container product-detail-content mdl-cnt" id="product-detail-content">
                            </div>
                        </div>
            
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js" integrity="sha512-HGOnQO9+SP1V92SrtZfjqxxtLmVzqZpjFFekvzZVWoiASSQgSr4cw9Kqd2+l8Llp4Gm0G8GIFJ4ddwZilcdb8A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.js" integrity="sha512-eP8DK17a+MOcKHXC5Yrqzd8WI5WKh6F1TIk5QZ/8Lbv+8ssblcz7oGC8ZmQ/ZSAPa7ZmsCU4e/hcovqR8jfJqA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript" src="view/layout/js/slick_slide.js"></script>
<script src="view/layout/js/toast-msg.js"> </script>          
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script  src="view/layout/js/main.js"></script>   
<script  src="view/layout/js/products.js"></script> 