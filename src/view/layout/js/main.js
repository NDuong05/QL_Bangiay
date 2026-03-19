// VISIBILITY - BEGIN DEFINE /////////////////////////////////////////////////////

function toggleSidebarDropdown(elementId) {
    let x = document.getElementById(elementId);
    x.classList.toggle("hidden");
}

function toggleModal(elementId) {
    const modal = document.getElementById(elementId);
    const isOpen = modal.classList.contains("open");

    // Đóng tất cả modal khác
    document.querySelectorAll(".modal").forEach((m) => {
        m.classList.remove("open");
    });

    if (!isOpen) {
        modal.classList.add("open");
        document.body.style.overflow = "hidden"; // Khoá scroll
    } else {
        modal.classList.remove("open");
        document.body.style.overflow = "auto"; // Trả lại scroll
    }
}

function togglePage(elementId) {
    let selectedPage = document.getElementById(elementId);
    let catalougePage = document.getElementById("catalogue");
    let allPages = document.querySelectorAll(".toggle-page");


    selectedPage.classList.toggle("hidden");

    allPages.forEach((page) => {
        if (page.id !== elementId) {
            page.classList.add("hidden");
        }
    });

    // Now check if any page is active
    let anyActivePage = Array.from(allPages).some(page => !page.classList.contains("hidden"));


    // If no page is active, show catalougePage; otherwise, hide it
    if (!anyActivePage)
        catalougePage.classList.remove("hidden");
    else
        catalougePage.classList.add("hidden");


    window.scrollTo({ top: 0 });
}
// VISIBILITY - END DEFINE /////////////////////////////////////////////////////

// USER - BEGIN DEFINE /////////////////////////////////////////////////////

// Load user info to My Account after user has logged in
function loadUserInfo() {
    event.preventDefault();

    const currentuser = JSON.parse(localStorage.getItem("currentuser"));

    if (currentuser) {
        document.getElementById("infoname").value = currentuser.username || "";
        document.getElementById("fullname").value = currentuser.fullname || "";
        document.getElementById("infophone").value = currentuser.phone || "";
        document.getElementById("infoemail").value = currentuser.email || "";
        document.getElementById("infoaddress").value = currentuser.address || "";
    }
}

function toggleChangePass() {
    let changepass = document.getElementById("user-info-changepass");
    let changeacc = document.getElementById("user-info-changeacc");
    if (changepass.classList.contains("hidden")) {
        changepass.classList.remove("hidden");
        changeacc.classList.add("hidden");
    } else {
        changepass.classList.add("hidden");
        changeacc.classList.remove("hidden");
    }
    window.scrollTo({ top: 0 });
}

// Change current user"s password
function changePassword() {  
    let currentPassword = document.getElementById("password-cur-info").value.trim();
    let newPassword = document.getElementById("password-after-info").value.trim();
    let confirmNewPassword = document.getElementById("password-confirm-info").value.trim();

    let hasError = false;

    // Reset form-msg-error classes
    document.querySelectorAll(".change-password .form-msg-error").forEach(msg => {
        msg.textContent = "";
    });
 

    if (!currentPassword) {
        document.querySelector("#password-cur-info + .form-msg-error").innerText = "Please enter current password";
        hasError = true;
    }

    if (currentPassword !== currentuser.password) {
        document.querySelector("#password-cur-info + .form-msg-error").innerText = "Current password is incorrect.";
        hasError = true;
    }

    if (!newPassword || newPassword.length < 5 || /\s/.test(newPassword)) {
        document.querySelector("#password-after-info + .form-msg-error").innerText = "New password must be at least 5 characters long and cannot contain spaces";
        hasError = true;
    }

    if (newPassword === currentPassword) {
        document.querySelector("#password-after-info + .form-msg-error").innerText = "New password must be different from current password.";
        hasError = true;
    }

    if (newPassword !== confirmNewPassword) {
        document.querySelector("#password-confirm-info + .form-msg-error").innerText = "New password and confirmation do not match.";
        hasError = true;
    }

    if (hasError) {
        toastMsg({ title: "ERROR", message: "Please fill in the form correctly.", type: "error" });
        return;
    }

    return !hasError;
}

// USER - END DEFINE /////////////////////////////////////////////////////

// CATALOGUE - BEGIN DEFINE /////////////////////////////////////////////////////
// Function to toggle visibility of the dropdown menu
function toggleDropdown(menuId) {
    let menu = document.getElementById(menuId);

    // Close other dropdowns
    document.querySelectorAll(".dropdown-menu").forEach(dropdown => {
        if (dropdown.id !== menuId) {
            dropdown.style.display = "none";
        }
    });

    // Toggle the clicked dropdown
    menu.style.display = menu.style.display === "block" ? "none" : "block";
}

// Event listener to close dropdowns when clicking outside
document.addEventListener("click", function (event) {
    let isClickInside = event.target.closest(".dropdown");

    if (!isClickInside) {
        document.querySelectorAll(".dropdown-menu").forEach(dropdown => {
            dropdown.style.display = "none";
        });
    }
});

// Initialize: Hide all dropdowns on page load
window.addEventListener("load", function () {
    document.querySelectorAll(".dropdown-menu").forEach(dropdown => {
        dropdown.style.display = "none";
    });
});

function getOrderHistoryCart(orderCart) {
    let OHCarthtml = ``;

    orderCart.forEach(item => {
        let product = getProduct(item);
        OHCarthtml += `
                <div class="modal-container cart-item" data-productID="${product.id}">
                    <div class="img-container">
                        <img src="${product.image}" onerror="this.src='./asset/img/catalogue/coming-soon.jpg'">
                    </div>
                    <div class="cart-item-info">
                        <p class="display-product-name">${product.name}</p>
                        <p>Brand: <span class="display-product-brand">${product.brand}</span></p>
                        <p>Size: <span class="display-product-size">${product.size}</span></p>
                        <p class="display-product-price">${vnd(product.originalPrice)}</p>
                    </div>
                    <div class="cart-item-amount">
                        <p>x<span class="display-product-quantity">${item.quantity}</span></p>
                    </div>
                </div>
            `;
    });

    return OHCarthtml;
}

function showOrderHistory() {
    let currentuser = JSON.parse(localStorage.getItem("currentuser"));
    let orders = JSON.parse(localStorage.getItem("orders")) || [];

    if (!currentuser) {
        console.log("showOrderHistory(): User not logged in.")
        return;
    }

    let OHbody = document.querySelector(".order-history .main-account-body-col");
    OHbody.innerHTML = "";
    if (orders.length !== 0) {
        let orderhtml = ``;
        orders.forEach((order, idx) => {
            if (order.customerId != currentuser.id) return;
            orderhtml += `
                    <div class="modal-container order-history" datda-orderID="${order.id}">
                        <div class="cart-main">
                            <div class="cart-body" style="max-height: 520px; overflow-y: auto">
                                ${getOrderHistoryCart(order.cart)}
                            </div>
                            <div class="cart-footer">
                                <div class="cart-totalprice">
                                    <p>GRAND TOTAL</p>
                                    <p class="display-totalprice">${vnd(order.total)}</p>
                                </div>
                                <div class="cart-item-status">
                                    <div style="background-color: var(${order_statusColor[order.status]})">
                                        <p class="display-order-status">Status: <span>${order_statusTitle[order.status]}</span>
                                        <span><i class="${order_statusIcon[order.status]}"></i></span></p>
                                    </div>
                                    <button onclick="showOrderDetail('${order.id}')">Details</button>
                                </div>
                            </div>
                        </div>
                    </div>
            `;
        });

        // Inject generated order history HTML into OHbody
        OHbody.innerHTML = orderhtml;
        console.log(OHbody);
    } else {
        displayWhenEmpty(".order-history .main-account-body-col", displayEmptyHTML_orderhistory);
    }
}

// CATALOGUE - ORDER HISTORY - END DEFINE /////////////////////////////////////////////////////

// CATALOGUE - PRODUCTS - BEGIN DEFINE ////////////////////////////////////////////////////////

function updateTotalPrice(quantity, priceElement, totalElement) {
    let price = parseInt(priceElement.textContent.replace(/\D/g, ""));
    let totalPrice = price * quantity;
    totalElement.textContent = new Intl.NumberFormat("vi-VN").format(totalPrice) + " ₫";
}

function detailProduct(id) {
    const modal = document.querySelector('.modal.product-detail');
    const body = document.body;

    fetch(`view/productdetail.php?id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.querySelector('#product-detail-content').innerHTML = html;
            modal.classList.add('open');
            body.style.overflow = "hidden";

            // Sau khi render xong DOM mới, mới lấy và set max
            const qtyInput = document.querySelector('.input-qty');
            const stockText = document.querySelector('.stock-quantity');

            if (qtyInput && stockText) {
                const stock = parseInt(stockText.textContent.trim()) || 100;
                qtyInput.setAttribute("max", stock);
                qtyInput.setAttribute("data-stock", stock);
                if (parseInt(qtyInput.value) > stock) {
                    qtyInput.value = stock;
                }
            }
            setupEventListeners();
        })
        .catch(error => console.error("Lỗi khi tải sản phẩm:", error));
}

function increasingNumber(e) {
    let qty = e.parentNode.querySelector('.product-detail .input-qty');  // Lấy input số lượng
    let priceElement = document.querySelector('.current-price');  // Lấy phần tử giá hiện tại
    let totalElement = document.querySelector('.price-total .price');  // Lấy phần tử tổng tiền

    let currentVal = parseInt(qty.value) || 1;  // Lấy giá trị hiện tại của số lượng, mặc định là 1 nếu chưa nhập gì
    let maxStock = parseInt(qty.getAttribute("max")) || 100;  // Lấy giá trị max từ thuộc tính `max`

    if (currentVal < maxStock) {
        qty.value = currentVal + 1;  // Nếu số lượng nhỏ hơn max, tăng số lượng lên 1
    } else {
        qty.value = maxStock;  // Nếu vượt quá max, set lại giá trị bằng max
        toastMsg({
            title: "ERROR",
            message: "Cannot exceed stock quantity!",
            type: "error"
        });
    }

    // Cập nhật tổng tiền sau khi thay đổi số lượng
    updateTotalPrice(parseInt(qty.value), priceElement, totalElement);
}

function decreasingNumber(e) {
    let qty = e.parentNode.querySelector('.product-detail .input-qty');
    let priceElement = document.querySelector('.current-price');
    let totalElement = document.querySelector('.price-total .price');

    let currentVal = parseInt(qty.value) || 1;
    let minVal = parseInt(qty.min) || 1;

    if (currentVal > minVal) {
        qty.value = currentVal - 1;
    } else {
        qty.value = minVal;
    }

    updateTotalPrice(parseInt(qty.value), priceElement, totalElement);
}


// Đóng modal khi nhấn vào nút đóng
document.querySelector('.modal-close').addEventListener('click', function () {
    document.querySelector('.modal.product-detail').classList.remove('open');
    document.body.style.overflow = "auto";
});


async function checkLogin() {
    try {
        const res = await fetch("/Web2/src/controller/db_controller/cart.php?action=check_login", {
            method: "POST",
            headers: { "Content-Type": "application/json" }
        });

        // Kiểm tra phản hồi có hợp lệ và là JSON không
        if (!res.ok) throw new Error("Network response was not ok");
        
        const contentType = res.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("Expected JSON, got something else!");
        }

        const data = await res.json();

        if (!data.success) {
            toastMsg({
                title: data.title || "REMINDER",
                message: data.message || "Please login first!",
                type: data.type || "info"
            });
            return false;
        }

        return true;
    } catch (err) {
        console.error("Login check error:", err);
        toastMsg({
            title: "ERROR",
            message: "Can not detect the login status!",
            type: "error"
        });
        return false;
    }
}

// Đăng ký sự kiện chọn size và thêm vào giỏ hàng
function setupEventListeners() {
    let selectedSizeId = null;

    // Kiểm tra xem phần tử size-container có tồn tại không
    const sizeContainer = document.querySelector(".size-container");
    if (!sizeContainer) {
        console.error("Không tìm thấy phần tử size-container!");
        return;
    }

    // Xử lý chọn size
    sizeContainer.addEventListener("click", (event) => {
        if (event.target.tagName === "BUTTON") {
            // Loại bỏ active class từ tất cả các button
            document.querySelectorAll(".size-container button").forEach(button => {
                button.classList.remove("active");
            });
            event.target.classList.add("active");

            // Lấy ProductSizeID từ data-sizeid
            selectedSizeId = event.target.getAttribute('data-sizeid');
            if (selectedSizeId) {
                console.log("Selected ProductSizeID: " + selectedSizeId);
            } else {
                console.error("Không thể lấy ProductSizeID từ button!");
            }
        }
    });

    document.querySelectorAll(".size-button").forEach(btn => {
        btn.addEventListener("click", function () {
            document.querySelectorAll(".size-button").forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            const stock = this.getAttribute("data-stock");
            const qtyInput = document.querySelector(".input-qty");

            qtyInput.setAttribute("max", stock);
            qtyInput.setAttribute("data-stock", stock);

            let currentQty = parseInt(qtyInput.value) || 1;
            if (currentQty > stock) {
                qtyInput.value = stock;
                toastMsg({
                    title: "INFO",
                    message: "Stock has changed. Quantity adjusted.",
                    type: "info"
                });
            } else {
                qtyInput.value = currentQty;
            }

            const priceElement = document.querySelector(".current-price");
            const totalElement = document.querySelector(".price-total .price");
            updateTotalPrice(1, priceElement, totalElement);
        });
    });

    // Kiểm tra phần tử add-cart có tồn tại không trước khi đăng ký sự kiện
    const addCartButton = document.querySelector('#add-cart');
    if (!addCartButton) {
        console.error("Không tìm thấy nút 'Add to Cart'!");
        return;
    }

    // Sự kiện "Add to Cart"
    addCartButton.addEventListener('click', async () => {
        if (!selectedSizeId) {
            toastMsg({ title: "REMINDER", message: "Please choose a size first!", type: "info" });
            return;
        }
    
        const isLoggedIn = await checkLogin();
        if (!isLoggedIn) return;
    
        let quantityInput = document.querySelector(".input-qty");
        let quantity = quantityInput ? parseInt(quantityInput.value) : 1;
        let price = parseInt(document.querySelector(".current-price").dataset.price || 0);

        try {
            const response = await fetch("/Web2/src/controller/db_controller/cart.php?action=add_to_cart", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ productsizeid: selectedSizeId, quantity, price })
            });
    
            console.log("Response Status: ", response.status); // Log mã trạng thái HTTP
            const textResponse = await response.text(); // Đọc phản hồi dưới dạng text
    
            console.log("Raw response: ", textResponse); // Log nội dung raw của phản hồi
    
            // Kiểm tra xem phản hồi có phải là JSON không
            let data;
            try {
                data = JSON.parse(textResponse); // Cố gắng chuyển đổi nội dung thành JSON
            } catch (error) {
                console.error("Response is not valid JSON:", error);
                toastMsg({ title: "ERROR", message: "Server response is not JSON", type: "error" });
                return;
            }
    
            if (data.success) {
                toastMsg({ title: "SUCCESS", message: "Added successfully!", type: "success" });
                loadCartSummary(); 
                fetchHeaderQty();
                if (quantityInput) quantityInput.value = 1;
                document.querySelector('.modal.product-detail').classList.remove('open');
                document.body.style.overflow = "auto";

            } else {
                toastMsg({ title: "ERROR", message: data.message || "Error occurred.", type: "error" });
            }
        } catch (err) {
            console.error("Lỗi khi fetch:", err);
        }
    });
    
    

    // Kiểm tra phần tử checkout-btn có tồn tại không trước khi đăng ký sự kiện
    const checkoutButton = document.querySelector("#checkout-modal-btn");
    if (!checkoutButton) {
        console.error("Không tìm thấy nút 'Buy Now'!");
        return;
    }

    // Sự kiện "Buy Now"
    checkoutButton.addEventListener("click", async () => {
        if (!selectedSizeId) {
            toastMsg({ title: "REMINDER", message: "Please choose a size first!", type: "info" });
            return;
        }
    
        const isLoggedIn = await checkLogin();
        if (!isLoggedIn) return;
    
        let quantityInput = document.querySelector(".product-detail .input-qty");
        let quantity = quantityInput ? parseInt(quantityInput.value) : 1;
        let price = parseInt(document.querySelector(".current-price").dataset.price || 0);

        console.log("selectedSizeId:", selectedSizeId, "quantity:", quantity, "price:", price);

        const productData = {
            ProductSizeID: selectedSizeId,
            Quantity: quantity,
            Price: price,
            product_name: document.querySelector(".product-title").innerText,
            Size: document.querySelector(".size-container button.active").innerText,
            product_image: document.querySelector(".product-detail-content img").src
        };
        
        fetch("/Web2/src/controller/db_controller/cart.php?action=set_checkout_session", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ products: [productData] }) // luôn là mảng nha
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.checkoutMode = "buy_now_checkout"; 
                toggleModal("product-detail");
                toggleModal("checkout-page");
                renderCheckoutUI();
            } else {
                toastMsg({ title: "ERROR", message: data.message || "Có lỗi khi lưu session!", type: "error" });
            }
        })
        .catch(err => {
            console.error("Lỗi:", err);
            toastMsg({ title: "ERROR", message: "Không thể kết nối đến server.", type: "error" });
        });
    });        
}    

function handleReturnFromCheckout() {
    console.log("checkoutMode hiện tại:", window.checkoutMode);
        // Gọi API xoá session checkout nếu đang trong chế độ 'buy now'
    fetch("/Web2/src/controller/db_controller/cart.php?action=clear_checkout_session", {
        method: "POST"
    });

    window.checkoutMode = null; // reset lại mode
    toggleModal("checkout-page"); // đóng modal
    document.body.style.overflow = "auto";
}


function renderCheckoutUI() {
    console.log("checkoutMode trong renderCheckoutUI:", window.checkoutMode);
    const checkoutBody = document.querySelector(".checkout-page .cart-body");
    const totalPriceElem = document.querySelector(".checkout-page .display-totalprice");

    if (!checkoutBody || !totalPriceElem) {
        console.warn("Không tìm thấy .cart-body hoặc .display-totalprice trong checkout-page.");
        return;
    }

    fetch("/Web2/src/controller/db_controller/cart.php?action=get_checkout_session", {method: "POST"})
    .then(res => {
        if (!res.ok) throw new Error("Lỗi khi fetch dữ liệu từ session.");
            return res.json();
        })    
        .then(data => {
            const checkoutBody = document.querySelector(".checkout-page .cart-body");
            const totalPriceElem = document.querySelector(".checkout-page .display-totalprice");
        
            if (!data.success || !Array.isArray(data.product)) {
                checkoutBody.innerHTML = "<p>Không có sản phẩm nào trong session.</p>";
                totalPriceElem.innerHTML = "0 VND";
                return;
            }
        
            let checkoutHTML = "";
            let totalPrice = 0;
        
            data.product.forEach(product => {
                const itemTotal = product.Price * product.Quantity;
                totalPrice += itemTotal;
        
                checkoutHTML += `
                    <div class="modal-container cart-item" data-productID="${product.ProductSizeID}">
                        <div class="img-container">
                            <img src="${product.product_image}" onerror="this.src='/assets/img/placeholder.jpg'">
                        </div>
                        <div class="cart-item-info">
                            <p class="display-product-name">${product.product_name}</p>
                            <p>Size: <span class="display-product-size">${product.Size}</span></p>
                            <p class="display-product-price" style="position: absolute; bottom: 1.5rem; right: 0;">${vnd(product.Price)}</p>
                        </div>
                        <div class="cart-item-amount">
                            <p>x<span class="display-product-quantity">${product.Quantity}</span></p>
                        </div>
                    </div>
                `;
            });
        
            checkoutBody.innerHTML = checkoutHTML;
            totalPriceElem.innerHTML = vnd(totalPrice);
            document.querySelectorAll(".display-totalorder").forEach(el => el.innerText = vnd(totalPrice + 30000));
        })        
        .catch(err => {
            console.error("Lỗi khi tải dữ liệu từ session:", err);
            checkoutBody.innerHTML = "<p>Đã xảy ra lỗi khi tải dữ liệu từ session.</p>";
            totalPriceElem.innerHTML = "0 VND";
        });
}

// CART - BEGIN DEFINE /////////////////////////////////////////////

function updateTotalPriceCart() {
    let totalQty = 0;
    let totalPrice = 0;

    const selectedItems = document.querySelectorAll(".checkout-checkbox:checked");
    const checkoutBtn = document.getElementById("cart-checkout-btn");

    if (selectedItems.length === 0) {
        updateCartSummary(0, 0);
        checkoutBtn.classList.remove("active");
        checkoutBtn.disabled = true;
        return;
    } else {
        checkoutBtn.disabled = false;
        checkoutBtn.classList.add("active");
    }

    selectedItems.forEach(item => {
        const productID = item.dataset.productsizeid;
        const productElement = document.querySelector(`.cart-item[data-productID="${productID}"]`);
        const price = parseInt(productElement.querySelector(".display-product-price").innerText.replace(/[^0-9]/g, ""));
        const quantity = parseInt(productElement.querySelector(".input-qty").value);

        totalPrice += price * quantity;
        totalQty += quantity;
    });

    updateCartSummary(totalQty, totalPrice);
}

function getCheckedCartItems() {
    const checkedItems = [];
    const checkedCheckboxes = document.querySelectorAll(".checkout-checkbox:checked");

    checkedCheckboxes.forEach(checkbox => {
        const cartItem = checkbox.closest(".cart-item");
        if (!cartItem) return; // Không tìm thấy thẻ cha thì skip

        const productSizeID = parseInt(checkbox.getAttribute("data-productsizeid"));
        const quantityEl = cartItem.querySelector(".input-qty");
        const priceEl = cartItem.querySelector(".display-product-price");
        const sizeEl = cartItem.querySelector(".display-product-size");
        const nameEl = cartItem.querySelector(".display-product-name");
        const imgEl = cartItem.querySelector("img");

        // Debug từng cái nếu bị undefined
        if (!quantityEl || !priceEl || !sizeEl || !nameEl || !imgEl) {
            console.warn("Không tìm thấy đủ thông tin trong cartItem:", cartItem);
            return;
        }

        const quantity = parseInt(quantityEl.value);
        const price = vndToNumber(priceEl.textContent);  // Nhớ đảm bảo vndToNumber hoạt động đúng

        checkedItems.push({
            ProductSizeID: productSizeID,
            Quantity: quantity,
            Price: price,
            Size: sizeEl.innerText.trim(),
            product_name: nameEl.innerText.trim(),
            product_image: imgEl.src
        });
    });

    return checkedItems;
}

function vndToNumber(vndString) {
    return parseInt(vndString.replace(/[^\d]/g, ""));
}

// Hàm để cập nhật lại tổng số lượng và tổng tiền
function updateCartSummary(totalQty, totalPrice) {
    const DELIVERY_FEE = 30000;

    document.querySelectorAll(".display-totalprice").forEach(el => el.innerText = vnd(totalPrice));
    document.querySelectorAll(".display-totalorder").forEach(el => {
        const finalTotal = totalPrice + DELIVERY_FEE;
        el.innerText = vnd(finalTotal);
    });
}


// Load tóm tắt cart (dùng riêng cho header)
function loadCartSummary(callback) {
    fetch("controller/db_controller/cart.php?action=get_cart")
        .then(res => res.json())
        .then(data => {
            let totalQty = 0;
            let totalPrice = 0;
            if (data.success) {
                data.cart.forEach(item => {
                    totalQty += parseInt(item.Quantity);
                    totalPrice += item.Quantity * item.Price;
                });
            }
            updateCartSummary(totalQty, totalPrice);
            if (callback) callback();
        })
        .catch(err => {
            console.error("Failed to load cart summary", err);
            updateCartSummary(0, 0);
            if (callback) callback();
        });
}

// Xử lý cập nhật số lượng
function updateCartAll(productSizeID, size, el, stockQuantity) {
    const parent = el.closest(".cart-item");
    const input = parent.querySelector(".input-qty");
    let quantity = parseInt(input.value);

    if (quantity < 1) quantity = 1;
    if (quantity > stockQuantity) quantity = stockQuantity;
    input.value = quantity;

    fetch("controller/db_controller/cart.php?action=update_cart", {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ productsizeid: productSizeID, quantity })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showCart(); // reload cart
                loadCartSummary();
                fetchHeaderQty();
            } else {
                toastMsg({ title: "Error", message: data.message, type: "error" });
            }
        });
}

// Xử lý xóa sản phẩm
function deleteCartItem(productsizeid, el) {
    fetch("controller/db_controller/cart.php?action=delete_cart_item", {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ productsizeid })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showCart(); // reload cart
            loadCartSummary();
            fetchHeaderQty();
        } else {
            toastMsg({ title: "ERROR", message: data.message, type: "error" });
        }
    });
}

function updateCartQtyInHeader(qty) {
    document.querySelectorAll(".display-cart-total-amount").forEach(el => el.innerText = qty);
}

function handleCheckoutClick() {
    console.log("Checkout button clicked!");

    const checkedItems = getCheckedCartItems();  
    console.log("Checked items:", checkedItems);

    if (checkedItems.length === 0) {
        alert("Bạn chưa chọn sản phẩm nào để thanh toán!");
        return;
    }

    fetch("/Web2/src/controller/db_controller/cart.php?action=set_checkout_session", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ products: checkedItems })
    })
    .then(res => res.json())
    .then(data => {
        console.log("Dữ liệu đã gửi thành công:", data);
        // Sau khi set session xong thì render checkout UI luôn
        window.checkoutMode = 'checkout';
        toggleModal('checkout-page');
        renderCheckoutUI();
    })
    .catch(err => {
        console.error("Lỗi khi gửi dữ liệu lên server:", err);
    });
}

// Hiển thị giỏ hàng
function showCart() {
    const cartBody = document.querySelector(".cart .cart-body");
    const checkoutBtn = document.getElementById("cart-checkout-btn");

    if (!cartBody || !checkoutBtn) return;

    fetch("controller/db_controller/cart.php?action=get_cart")
        .then(res => res.json())
        .then(data => {
            cartBody.innerHTML = "";

            if (data.login_required === true) {
                checkoutBtn.classList.remove("active");
                checkoutBtn.disabled = true;
                document.getElementById('select-all-wrapper').style.display = 'none';
                return;
            }

            if (!data.success || !Array.isArray(data.cart)) return;

            if (data.cart.length === 0) {
                checkoutBtn.classList.remove("active");
                checkoutBtn.disabled = true;
                document.getElementById('select-all-wrapper').style.display = 'none';
                displayWhenEmpty(".cart .cart-body", displayEmptyHTML_cart);
                updateCartSummary(0, 0);
                return;
            }

            document.getElementById('select-all-wrapper').style.display = 'flex';
            checkoutBtn.classList.add("active");
            checkoutBtn.disabled = false;

            let cartItemhtml = "";
            let totalQty = 0;

            data.cart.forEach(item => {
                const stock = item.StockQuantity;
                const qty = item.Quantity;
            
                totalQty += qty;
            
                const isDisabled = stock <= 0 || qty > stock;
            
                const finalQty = (qty > stock) ? stock : Math.max(1, qty); 
            
                cartItemhtml += `
                    <div class="modal-container cart-item" data-productID="${item.ProductSizeID}">
                        <div class="cart-item-checkbox">
                            <label>
                                <input type="checkbox" 
                                       class="checkout-checkbox" 
                                       data-productsizeid="${item.ProductSizeID}" 
                                       onchange="updateTotalPriceCart()" 
                                       ${isDisabled ? 'disabled' : ''}>
                            </label>
                        </div>
                        <div class="img-container">
                            <img src="${item.product_image}" onerror="this.src='/assets/img/placeholder.jpg'">
                        </div>
                        <div class="cart-item-info">
                            <p class="display-product-name">${item.product_name}</p>
                            <p>Size: <span class="display-product-size">${item.Size}</span></p>
                            <p class="display-product-price">${vnd(item.Price)}</p>
                        </div>
                        <div class="cart-item-control">
                            <a onclick="deleteCartItem(${item.ProductSizeID}, this)">
                                <i class="fa-regular fa-circle-xmark"></i>
                            </a>
                            <div class="cart-item-amount">
                                <button class="minus is-form" 
                                        onclick="decreasingNumberCart(this)"
                                        ${isDisabled ? 'disabled' : ''}>
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <input class="input-qty" 
                                       type="number"
                                       min="1"
                                       max="${stock}"
                                       value="${finalQty}"
                                       data-stock="${stock}"
                                       ${isDisabled ? 'disabled' : ''} 
                                       oninput="handleInputChangeCart(this, ${item.ProductSizeID}, '${item.Size}')">
                                <button class="plus is-form" 
                                        onclick="increasingNumberCart(this)"
                                        ${isDisabled ? 'disabled' : ''}>
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            cartBody.innerHTML = cartItemhtml;

            // ✅ update header chính xác số lượng
            updateCartQtyInHeader(totalQty);

            // ✅ update tổng tiền nếu có sp được chọn
            updateTotalPriceCart();
        })
        .catch(err => {
            console.error("Failed to load cart:", err);
            document.getElementById('select-all-wrapper').style.display = 'none';
            displayWhenEmpty(".cart .cart-body", "<p>Error occurred while loading cart.</p>");
            updateCartSummary(0, 0);
            updateCartQtyInHeader(0);
        });
}


function increasingNumberCart(btn) {
    const input = btn.parentNode.querySelector('.input-qty');
    let currentVal = parseInt(input.value) || 1;
    let maxStock = parseInt(input.getAttribute("max")) || 100;

    if (currentVal < maxStock) {
        input.value = currentVal + 1;
    } else {
        input.value = maxStock;
        toastMsg({
            title: "ERROR",
            message: "Cannot exceed stock quantity!",
            type: "error"
        });
    }

    triggerUpdateCart(input);
}

function decreasingNumberCart(btn) {
    const input = btn.parentNode.querySelector('.input-qty');
    let currentVal = parseInt(input.value) || 1;
    let min = parseInt(input.getAttribute("min")) || 1;

    if (currentVal > min) {
        input.value = currentVal - 1;
    } else {
        input.value = min;
    }

    triggerUpdateCart(input);
}

function handleInputChangeCart(input, productSizeID, size) {
    let val = parseInt(input.value) || 1;
    let max = parseInt(input.getAttribute("max")) || 100;
    let min = parseInt(input.getAttribute("min")) || 1;

    if (val > max) {
        input.value = max;
        toastMsg({
            title: "ERROR",
            message: "Cannot exceed stock quantity!",
            type: "error"
        });
    } else if (val < min) {
        input.value = min;
    }

    triggerUpdateCart(input);
}

function triggerUpdateCart(input) {
    const container = input.closest('.cart-item');
    const productSizeID = container.getAttribute('data-productid');
    const size = container.querySelector('.display-product-size')?.textContent || "";
    const newQty = parseInt(input.value);
    const stock = parseInt(input.getAttribute('data-stock'));

    // Cập nhật lại checkbox (disable/enable)
    const checkbox = container.querySelector('.checkout-checkbox');

    if (newQty <= stock) {
        checkbox.disabled = false;
    } else {
        checkbox.disabled = true;
    }

    updateCartAll(productSizeID, size, input, stock);
    updateTotalPriceCart();
}

function handleSelectAll(el) {
    const itemCheckboxes = document.querySelectorAll(".checkout-checkbox:not(#select-all-checkbox)");

    itemCheckboxes.forEach(cb => {
        if (!cb.disabled) {
            cb.checked = el.checked;
        }
    });

    updateTotalPriceCart();
}

document.querySelectorAll(".checkout-checkbox").forEach(cb => {
    cb.addEventListener("change", updateTotalPriceCart);
});

// Format tiền VNĐ
function vnd(x) {
    return x.toLocaleString("vi-VN", { style: "currency", currency: "VND" });
}


// CATALOGUE - CART - END DEFINE /////////////////////////////////////////////////////


// CATALOGUE - END DEFINE /////////////////////////////////////////////////////

// BANNER - BEGIN /////////////////////////////////////////////////////

const imageArray = [
    '/Web2/src/view/layout/asset/img/banner/banner1.jpg',
    '/Web2/src/view/layout/asset/img/banner/banner2.jpg',
    '/Web2/src/view/layout/asset/img/banner/banner3.jpg'
];

const bannerImagesContainer = document.querySelector('.banner-images');
let currentIndex = 0;


// Tạo ảnh từ mảng
imageArray.forEach((src) => {
    const img = document.createElement('img');
    img.src = src;
    img.classList.add('banner-img');
    bannerImagesContainer.appendChild(img);
});

// Thêm ảnh đầu tiên vào cuối
const firstImageClone = document.createElement('img');
firstImageClone.src = imageArray[0];
firstImageClone.classList.add('banner-img');
bannerImagesContainer.appendChild(firstImageClone);

const totalImages = imageArray.length;

// Hàm di chuyển slide
function moveSlide(direction) {
    currentIndex += direction;

    if (currentIndex === totalImages) {
        // Chuyển đến ảnh clone
        bannerImagesContainer.style.transition = 'transform 1s ease';
        bannerImagesContainer.style.transform = `translateX(-${currentIndex * 100}%)`;

        setTimeout(() => {
            // Nhảy về ảnh đầu tiên
            bannerImagesContainer.style.transition = 'none';
            currentIndex = 0;
            bannerImagesContainer.style.transform = `translateX(0%)`;
        }, 1000); // Phù hợp với thời gian transition
    } else if (currentIndex < 0) {
        // Lùi về ảnh cuối
        bannerImagesContainer.style.transition = 'none';
        currentIndex = totalImages - 1;
        bannerImagesContainer.style.transform = `translateX(-${currentIndex * 100}%)`;
    } else {
        // Di chuyển bình thường
        bannerImagesContainer.style.transition = 'transform 1s ease';
        bannerImagesContainer.style.transform = `translateX(-${currentIndex * 100}%)`;
    }
}

// Chuyển ảnh tự động sau mỗi 10 giây
setInterval(() => {
    moveSlide(1);
}, 10000); // 10 giây


// BANNER - END /////////////////////////////////////////////////////


document.addEventListener("DOMContentLoaded", function () {
    loadCartSummary(); // gọi để update số lượng trong header khi trang vừa load
    fetchHeaderQty();
});
