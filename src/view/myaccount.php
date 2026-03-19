<script>
    function changeAccInfo() {
        var fullname = document.getElementById("fullname").value;
        var phone = document.getElementById("infophone").value;
        var email = document.getElementById("infoemail").value;
        var address = document.getElementById("infoaddress").value;
        var errorMsg = document.querySelectorAll(".form-msg-error");
        var province = document.getElementById('Province').value;
        var district = document.getElementById('District').value;
        var ward = document.getElementById('Ward').value;
        
        let toastMessage = null;

        // Reset error messages
        errorMsg.forEach(function (msg) {
            msg.innerHTML = "";
        });

        if (fullname === "") {
            errorMsg[0].innerHTML = "Full name is required.";
            toastMessage = {
                title: "ERROR",
                message: "Full name is required.",
                type: "error"
            };
            showToast(toastMessage);
            return false;
        }

        if (phone === "" || !/^\d{10}$/.test(phone)) {
            errorMsg[1].innerHTML = "Phone number is required and must be exactly 10 digits.";
            toastMessage = {
                title: "ERROR",
                message: "Phone number must be exactly 10 digits.",
                type: "error"
            };
            showToast(toastMessage);
            return false;
        }

        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errorMsg[2].innerHTML = "Please enter a valid email address.";
            toastMessage = {
                title: "ERROR",
                message: "Invalid email address.",
                type: "error"
            };
            showToast(toastMessage);
            return false;
        }

        if (address === "") {
            errorMsg[3].innerHTML = "Address is required.";
            toastMessage = {
                title: "ERROR",
                message: "Address is required.",
                type: "error"
            };
            showToast(toastMessage);
            return false;
        }

        return true; // Allow form submission
    }

    function showToast(toastData) {
        if (toastData) {
            toastMsg({
                title: toastData.title,
                message: toastData.message,
                type: toastData.type,
                duration: 3000
            });
        }
    }
    <?php
    $toastMessage = isset($_SESSION['toast']) ? json_encode($_SESSION['toast']) : "null";
    ?>

    window.onload = function () {
        var toastData = <?php echo $toastMessage; ?>;
        if (toastData) {
            toastMsg({
                title: toastData.title,
                message: toastData.message,
                type: toastData.type,
                duration: 3000
            });
        }
    };



</script>
<!-- <script>
    const provinces = <?php echo json_encode($provinces); ?>;
    const districts = <?php echo json_encode($districts); ?>;
    const wards = <?php echo json_encode($wards); ?>;
    const user = <?php echo json_encode($user); ?>;
    const selectedProvinceID = user.ProvinceID;
    const selectedDistrictID = user.DistrictID;
    const selectedWardID = user.WardID;
    
    document.addEventListener("DOMContentLoaded", function () { 
        const provinceSelect = document.getElementById("province");
        const districtSelect = document.getElementById("district");
        const wardSelect = document.getElementById("ward");      
        // Load provinces
        provinces.forEach(prov => {
            const option = document.createElement("option");
            option.value = prov.province_id;
            option.textContent = prov.name;
            if (prov.province_id == selectedProvinceID) {
                option.selected = true;
            }
            provinceSelect.appendChild(option);
        });

        // Load districts if province is selected
        if (selectedProvinceID) {
            districtSelect.disabled = false;
            const filteredDistricts = districts.filter(d => d.province_id == selectedProvinceID);
            filteredDistricts.forEach(dist => {
                const option = document.createElement("option");
                option.value = dist.district_id;
                option.textContent = dist.name;
                if (dist.district_id == selectedDistrictID) {
                    option.selected = true;
                }
                districtSelect.appendChild(option);
            });
        }

        // Load wards if district is selected
        if (selectedDistrictID) {
            wardSelect.disabled = false;
            const filteredWards = wards.filter(w => w.district_id == selectedDistrictID);
            filteredWards.forEach(ward => {
                const option = document.createElement("option");
                option.value = ward.wards_id;
                option.textContent = ward.name;
                if (ward.wards_id == selectedWardID) {
                    option.selected = true;
                }
                wardSelect.appendChild(option);
            });
        }

        // Province change handler
        provinceSelect.addEventListener("change", () => {
            districtSelect.innerHTML = '<option value="">-- Select District --</option>';
            wardSelect.innerHTML = '<option value="">-- Select Ward --</option>';
            wardSelect.disabled = true;

            const selectedProvinceId = provinceSelect.value;
            if (selectedProvinceId) {
                districtSelect.disabled = false;
                const filteredDistricts = districts.filter(d => d.province_id == selectedProvinceId);
                filteredDistricts.forEach(dist => {
                    const option = document.createElement("option");
                    option.value = dist.district_id;
                    option.textContent = dist.name;
                    districtSelect.appendChild(option);
                });
            } else {
                districtSelect.disabled = true;
            }
        });

        // District change handler
        districtSelect.addEventListener("change", () => {
            wardSelect.innerHTML = '<option value="">-- Select Ward --</option>';

            const selectedDistrictId = districtSelect.value;
            if (selectedDistrictId) {
                wardSelect.disabled = false;
                const filteredWards = wards.filter(w => w.district_id == selectedDistrictId);
                filteredWards.forEach(ward => {
                    const option = document.createElement("option");
                    option.value = ward.wards_id;
                    option.textContent = ward.name;
                    wardSelect.appendChild(option);
                });
            } else {
                wardSelect.disabled = true;
            }
        });



    });



</script> -->
<?php
    $str = "";
    foreach ($provinces as $prov) {
        $str .= '<option value="'.$prov['province_id'].'">'.$prov['name'].'</option>';
    }

    if(isset($_SESSION['currentuser']) && ($_SESSION['currentuser'] != "")){
        $user = $_SESSION['currentuser'];
        if (is_array($user)) {
            extract($user);
        } else {
            // Handle the case where $user is not an array
            echo '<p>Error: Invalid user data.</p>';
        }
        echo '  <!-- TOAST -->
                <div class="container toast" id="toast"></div>

                <div class="container account-user" id="account-user">
                <div class="main-account">
                    <a href = "index.php?pg=home" class = "back2HomeBtn"><span> < Back </span></a>
                    <div class="main-account-header">
                        <h2>MANAGE YOUR ACCOUNT</h2>
                        <p>Hi there, <span class="display-username">'.$Username.'</span>!</p>
                    </div>
                    <div class="main-account-body">
                        <div class="main-account-body-col" id="user-info-changeacc">
                            <form class="info-user" id="changeacc-info-form" action="index.php?pg=changeaccount" method="post">
                                <div class="form-group">
                                    <label for="infoname" class="form-label">Username <i>(cannot change)</i></label>
                                    <input class="form-input-bar" type="text" name="infoname" id="infoname"
                                        placeholder="Username*" value=" '.$Username.' "disabled>
                                    <p class="form-msg-error"></p>
                                </div>
                                <div class="form-group">
                                    <label for="fullname" class="form-label">Full Name</label>
                                    <input class="form-input-bar" type="text" name="fullname" id="fullname"
                                        placeholder="Full Name*" value="'.$Fullname.'">
                                    <p class="form-msg-error"></p>
                                </div>
                                <div class="form-group">
                                    <label for="infophone" class="form-label">Phone number</label>
                                    <input class="form-input-bar" type="number" name="infophone" id="infophone"
                                        placeholder="Phone number*" inputmode="tel" value="'.$PhoneNumber.'">
                                    <p class="form-msg-error"></p>
                                </div>
                                <div class="form-group">
                                    <label for="infoemail" class="form-label">Email</label>
                                    <input class="form-input-bar" type="email" name="infoemail" id="infoemail"
                                        placeholder="example@email.com" inputmode="email" value="'.$Email.'">
                                    <p class="form-msg-error"></p>
                                </div>

                                <div class="form-group">
                                    <div class="address-group">
                                        <label>Location</label>
                                        
                                        <div class="select-row">
                                            <select id="Province" name="province">
                                                <option value="'.$_SESSION['currentuser']['ProvinceID'].'">'.$currProvince.'</option>
                                                '.$str.'
                                            </select>


                                            <select id="District" name="district">
                                                <option value="'.$_SESSION['currentuser']['DistrictID'].'">'.$currDistrict.'</option>
                                            </select>

                                            <select id="Ward" name="ward">
                                                <option value="'.$_SESSION['currentuser']['WardID'].'">'.$currWard.'</option>
                                            </select>
                                        </div>         
                                            
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="infoaddress" class="form-label">Address</label>
                                    <input class="form-input-bar" type="text" name="infoaddress" id="infoaddress"
                                        placeholder="Add address for shipping" value="'.$Address.'">
                                    <p class="form-msg-error"></p>
                                </div>
                                
                                <div class="form-group button-group">
                                    <label class="myAccountBtnLabel">
                                        <i class="fa-regular fa-floppy-disk"></i>
                                        <input class="myAccountBtn" type="submit" name="changeAccInfoBtn" value="Save changes" onclick="return changeAccInfo()">
                                    </label>
                                </form>

                                <form action="index.php?pg=myaccount" method="post">
                                    <label class="myAccountBtnLabel">
                                        <i class="fa-solid fa-rotate-right"></i>
                                        <input class="myAccountBtn" type="submit" name="loadUserInfo" value="Undo changes">
                                    </label>
                                </form>
                            </div>

                            
                            <a href="index.php?pg=changepassword"><i class="fa-solid fa-key"></i><span>Change
                                    Password</span></a>
                        </div>
                        
                    </div>
                </div>
            </div>';
    }
?>
<div class="modal product-detail" id=" product-detail">
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
            <script>
                window.onload = function() {
                    let toastData = <?php echo $toastMessage ?: "null"; ?>;
                    if (toastData) {
                        toastMsg({
                            title: toastData.title,
                            message: toastData.message,
                            type: toastData.type,
                            duration: 3000
                        });

                        // if (toastData.redirect) {
                        //     setTimeout(() => {
                        //         window.location.href = toastData.redirect;
                        //     }, 2000);
                        // }
                    }
                };
            </script>
            <script src="view/layout/js/main.js"> </script>    
            <script src="view/layout/js/toast-msg.js"> </script> 