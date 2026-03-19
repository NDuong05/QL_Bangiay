// API URLs
const API_BASE_URL = "http://localhost/Web2/src/controller/db_controller/api.php?action=";

// Fetch dữ liệu từ API
async function fetchData(action, method = "GET", body = null) {
    try {
        const options = { method, headers: { "Content-Type": "application/json" } };
        if (body) options.body = JSON.stringify(body);
        
        const response = await fetch(API_BASE_URL + action, options);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error("Fetch error:", error);
        return [];
    }
}

// phân trang hiển thị
function togglePage(elementId) {
    let selectedPage = document.getElementById(elementId);
    let allPages = document.querySelectorAll(".toggle-page");


    selectedPage.classList.remove("none");
    selectedPage.classList.add("show");

    allPages.forEach((page) => {
        if (page.id !== elementId) {
            page.classList.add("none");
            page.classList.remove("show");
        }
    });

    switch (elementId) {
        case "homepage":
            displayHomepageStatistic();
            break;
        case "bill":
            initializeProvinces();
            renderOrders();
            break;
        case "showProducts":
            filterProducts();
            break;
        case "customer":
            renderAccounts();
            break;
        case "statistical":
            filterStat();
            break;
        default: return false;
    }

    window.scrollTo({ top: 0 });
}

// Lấy danh sách sản phẩm từ API
async function getProducts() {
    return await fetchData("get_products");
}

// Lấy danh sách tài khoản từ API
async function getAccounts() {
    return await fetchData("get_accounts");
}

// Lấy danh sách đơn hàng từ API
async function getOrders() {
    return await fetchData("get_orders");
}

function toggleSideBar() {
    const sideBar = document.getElementById("admin-sidebar");
    sideBar.classList.toggle("open");
}

//thêm dòng này
let productNeedRender = []; // biến để lưu danh sách hiển thị
//sửa hàm này
async function showProductList() {
    let products = await getProducts();
    if (!Array.isArray(products)) {
        console.error("Invalid products data:", products);
        return;
    }
    
    let html = "";
    products.forEach((product, index) => {
        html += `
            <tr>
                <td>${index + 1}</td>
                <td>${product.id}</td>
                <td><img src="../${product.image}" alt="${product.name}"></td>
                <td class="align-left">${product.name}</td>
                <td>${product.price}</td>
                <td>${product.category}</td>
                <td class="align-left">${product.brand}</td>
                <td>${product.sex}</td>
                <td>${Array.isArray(product.size) ? product.size.join(", ") : "N/A"}</td>
                <td>
                    <button class="details-btn" onclick="editProduct('${product.id}')">
                        <i class="fa-solid fa-pen-to-square"></i>Edit</button>
                    <button class="delete-btn details-btn" onclick="deleteProduct('${product.id}')">
                        <i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>`;
    });
    document.getElementById("products").innerHTML = html;
    setPagination(products);
}


const PRODUCT_PER_PAGE = 10;
//sửa hàm này
function setPagination(arr = []) {
    let sotrang = Math.ceil(arr.length / PRODUCT_PER_PAGE);
    let button = "";
    if (arr.length <= PRODUCT_PER_PAGE) {
        document.querySelector(".listPage").innerHTML = ``;
        return;
    }
    for (let i = 1; i <= sotrang; i++) {
        let vitri = (i - 1) * PRODUCT_PER_PAGE;
        button += `<button class="active" onClick="showProductList(${vitri}, productNeedRender)">${i}</button>`;
    }
    document.querySelector(".listPage").innerHTML = button;
}
function setActivePage(pageNumber) {
    document.querySelectorAll(".pagination-btn").forEach((btn, index) => {
        btn.classList.toggle("active", index + 1 === pageNumber);
    });
}


async function addProduct() {
    const name = document.querySelector("#productName").value;
    const price = document.querySelector("#productPrice").value;
    const category = document.querySelector("#productCategory").value;
    const brand = document.querySelector("#productBrand").value;
    const sex = document.querySelector("#sex").value;
    const image = document.querySelector("#productImage").value;
    
    // Lấy danh sách size đã chọn
    const selectedSizes = document.querySelectorAll("input[name='size']:checked");
    const sizeValues = Array.from(selectedSizes).map(checkbox => Number(checkbox.value));

    if (!name || !price || !category) {
        toastMsg({ title: "ERROR", message: "Please fill in all the fields.", type: "error" });
        return false;
    }

    const productData = {
        name: name,
        price: price,
        category: category,
        brand: brand,
        sex: sex,
        image: image,
        size: sizeValues
    };

    const response = await fetchData("add_product", "POST", productData);
    console.log(response);
    toastMsg({ title: "SUCCESS", message: "Product added successfully!", type: "success" });
    resetForm();
    showProductList();
}

function resetForm() {
    document.getElementById("edit").scrollIntoView({ behavior: "smooth", block: "start" });

    document.querySelector(".add h2").textContent = "ADD NEW PRODUCT";
    document.querySelector("#productName").value = "";
    document.querySelector("#productPrice").value = "";
    document.querySelector("#productCategory").value = "Sneaker";
    document.querySelector("#productBrand").value = "Nike";
    document.querySelector("#sex").value = "M";
    document.getElementById("productImage").src = ".view/layout/asset/img/temp.jpg";

    // Bỏ chọn tất cả checkbox size
    const checkboxes = document.querySelectorAll("input[name='size']");
    checkboxes.forEach((checkbox) => {
        checkbox.checked = false;
        checkbox.parentElement.classList.remove("select");
    });
}
//click size thì đổi màu
function click() {
    const productCategory = document.querySelectorAll(".size-options.visible");
    Array.from(productCategory).map((category) => {
        const checkbox = Array.from(
            category.querySelectorAll("input[type='checkbox']")
        );
        checkbox.forEach((box) => {
            box.addEventListener("change", function () {
                if (box.checked) {
                    box.parentElement.classList.add("select");
                } else {
                    box.parentElement.classList.remove("select");
                }
            });
        });
    });
}

document.addEventListener("DOMContentLoaded", () => {
    showProductList();
    renderAccounts();
    renderOrders();
    click();
});
// đổi loại sản phẩm sang kid thì đổi size
function changeSize() {
    const productCategory = document.querySelector("#productCategory");
    const normal = document.querySelector("#normal");
    const option = document.querySelector("#option-Kid");

    productCategory.addEventListener("change", function () {
        if (productCategory.value === "Kid") {
            normal.classList.add("hidden");
            normal.classList.remove("visible");
            option.classList.add("visible");
            option.classList.remove("hidden");
        } else {
            normal.classList.remove("hidden");
            normal.classList.add("visible");
            option.classList.remove("visible");
            option.classList.add("hidden");
        }
        const checkboxes = document.querySelectorAll(
            ".size-options.visible input[type='checkbox']"
        );
        checkboxes.forEach((box) => {
            box.checked = false;
            box.parentElement.classList.remove("select");
        });
        click();
    });
}
changeSize();
let productSize = "";

if (productCategory === "Kid") {
    const sizes = document.querySelectorAll(
        "#option-Kid input[name=size]:checked"
    );
    productSize = Array.from(sizes)
        .map((size) => size.value)
        .join(",");
} else {
    const sizes = document.querySelectorAll("#normal input[name=size]:checked");
    productSize = Array.from(sizes)
        .map((size) => size.value)
        .join(",");
}

function size() {
    const sizes = document.querySelectorAll("#normal input[name=size]:checked");
    const selectedSizes = Array.from(sizes).map((checkbox) => checkbox.value);
    return selectedSizes;
}

// Bắt sự kiện thay đổi (change) của các checkbox
document.querySelectorAll("#normal input[name=size]").forEach((checkbox) => {
    checkbox.addEventListener("change", size); // Khi có sự thay đổi, gọi hàm size()
});
/* hết thêm sản phẩm */

//Xóa sản phẩm
async function deleteProduct(id) {
    if (!confirm("Delete this product?")) return;
    
    const response = await fetchData("delete_product", "PUT", { id });
    console.log(response);
    toastMsg({ title: "SUCCESS", message: "Product deleted successfully!", type: "success" });
    showProductList();
}

async function restoreProduct(id) {
    if (!confirm("Restore this product?")) return;
    
    const response = await fetchData("restore_product", "PUT", { id });
    console.log(response);
    toastMsg({ title: "SUCCESS", message: "Product restored successfully!", type: "success" });
    showProductList();
}

document.getElementById("showProducts").addEventListener("input", filterProducts);

async function filterProducts() {
    let result = await getProducts();

    let search = document.getElementById("form-search-product").value.trim().toLowerCase();
    let status = document.getElementById("status-product").value;
    let category = document.getElementById("category-product").value.toLowerCase();
    let brand = document.getElementById("brand-product").value.toLowerCase();
    let gender = document.getElementById("gender-product").value.toLowerCase();
    let minprice = parseInt(document.getElementById("minprice-product").value) || 0;
    let maxprice = parseInt(document.getElementById("maxprice-product").value) || Infinity;

    result = result.filter(item => {
        let match = true;

        if (search && !item.name.toLowerCase().includes(search)) match = false;
        if (status === "1" && item.isDeleted === true) match = false;
        else if (status === "0" && item.isDeleted === false) match = false;
        if (category !== "0" && item.category.toLowerCase() !== category) match = false;
        if (brand !== "0" && item.brand.toLowerCase() !== brand) match = false;
        if (gender !== "0" && item.sex.toLowerCase() !== gender) match = false;
        if (parseInt(item.price) < minprice) match = false;
        if (parseInt(item.price) > maxprice) match = false;

        return match;
    });

    console.log("filterProducts(): ", result);
    showProductList(0, result);
}


function resetFilterProducts() {
    document.getElementById("form-search-product").value = "";
    document.getElementById("status-product").value = "1";
    document.getElementById("category-product").value = "0";
    document.getElementById("brand-product").value = "0";
    document.getElementById("gender-product").value = "0";
    document.getElementById("minprice-product").value = "";
    document.getElementById("maxprice-product").value = "";

    const validData = localStorage.getItem("products");
    const products = validData ? JSON.parse(validData).filter (product => product.isDeleted === false) : [];

    showProductList(0, products);
}

function editProduct(id) {
    // showProductDetail(index);

    document.getElementById("edit").scrollIntoView({ behavior: "smooth", block: "start" });

    const products = JSON.parse(localStorage.getItem("products"));
    const product = products.find(item => item.id === id);

    // Điền thông tin của sản phẩm vào form
    document.querySelector("#productName").value = product.name;
    document.querySelector("#productPrice").value = product.price;
    document.querySelector("#productCategory").value = product.category;
    document.querySelector("#productBrand").value = product.brand;
    document.querySelector("#sex").value = product.sex;
    document.querySelector("#imagePreview").src = product.image;

    // Chuyển đổi chế độ size dựa vào category
    const normal = document.querySelector("#normal");
    const option = document.querySelector("#option-Kid");
    if (product.category === "Kid") {
        normal.classList.add("hidden");
        normal.classList.remove("visible");
        option.classList.add("visible");
        option.classList.remove("hidden");
    } else {
        normal.classList.remove("hidden");
        normal.classList.add("visible");
        option.classList.remove("visible");
        option.classList.add("hidden");
    }

    // Chọn các size đã lưu vào
    const editSize = product.size;
    const labels = document.querySelectorAll(".size-options.visible label");
    labels.forEach((label) => {
        const checkbox = label.querySelector("input[type='checkbox']");
        if (checkbox) {
            const value = Number(checkbox.value);
            if (editSize.includes(value)) {
                label.classList.add("select");
                checkbox.checked = true; // Đánh dấu checkbox đã chọn
            } else {
                label.classList.remove("select");
                checkbox.checked = false; // Bỏ đánh dấu checkbox
            }
        }
    });

    document.querySelector(".add h2").textContent = "EDIT PRODUCT";
    document.querySelector("#form").textContent = "SAVE";

    document.querySelector("#form").onclick = function () {
        updateProduct(id);
    };
}

// Cập nhật sản phẩm
async function updateProduct(id) {
    const name = document.querySelector("#productName").value;
    const price = document.querySelector("#productPrice").value;
    const category = document.querySelector("#productCategory").value;
    const brand = document.querySelector("#productBrand").value;
    const sex = document.querySelector("#sex").value;
    const imageInput = document.getElementById("productImage");
    const image = imageInput.files[0] ? ".view/layout/asset/img/catalogue/" + imageInput.files[0].name : null;

    // Lấy danh sách size đã chọn
    const selectedSizes = document.querySelectorAll("input[name='size']:checked");
    const sizeValues = Array.from(selectedSizes).map(checkbox => Number(checkbox.value));

    if (!name || !price || !category) {
        toastMsg({ title: "ERROR", message: "Please fill in all the fields.", type: "error" });
        return false;
    }

    const productData = {
        id: id,
        name: name,
        price: price,
        category: category,
        brand: brand,
        sex: sex,
        image: image,
        size: sizeValues
    };

    const response = await fetchData("update_product", "PUT", productData);
    console.log(response);
    toastMsg({ title: "SUCCESS", message: "Product updated successfully!", type: "success" });
    resetForm();
    showProductList();
    
    // Reset lại form và nút
    document.querySelector(".add h2").textContent = "ADD NEW PRODUCT";
    document.querySelector("#form").textContent = "ADD";
    document.querySelector(".form-group img").src = ".view/layout/asset/img/temp.jpg";
    document.querySelector("#form").onclick = addProduct;
}

// Tìm kiếm sản phẩm

/*CUSTOM ALERT BOX*/
function customAlert(message, type) {
    if (type == "success") {
        document.getElementById("customAlert").style.backgroundColor = "#4CAF50";
    }
    if (type == "warning") {
        document.getElementById("customAlert").style.backgroundColor = "#f44336";
    }
    document.getElementById("customAlert").innerHTML = message;
    var x = document.getElementById("customAlert");
    x.className = "show";
    setTimeout(function () {
        x.className = x.classList.remove("show");
    }, 3000);
}
// chọn file hiển thị ảnh
document
    .querySelector("#productImage")
    .addEventListener("change", function (event) {
        const file = event.target.files[0]; // Lấy tệp đầu tiên người dùng chọn
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                // Khi tệp được đọc xong, cập nhật ảnh hiển thị
                document.querySelector("#imagePreview").src = e.target.result;
            };
            reader.readAsDataURL(file); // Đọc tệp dưới dạng URL
        }
    });

// BILLS/ORDERS MAMANGER - BEGIN DEFINE ///////////////////////////////////////////////
const MODAL_CONTAINER = document.getElementById("modal-container");
function openModal() {
    document.getElementById("modal").classList.add("open");
}

document.getElementById("bill").addEventListener("input", filterOrders);

async function renderOrders() {
    let orders = await getOrders();
    document.getElementById("show-orders").innerHTML = orders.map((order, index) => `
        <tr>
            <td>${index + 1}</td>
            <td>${order.id}</td>
            <td>${order.customerId}</td>
            <td>${order.orderDate}</td>
            <td>${order.total}</td>
            <td><button class="details-btn" onclick="showOrderDetail(${index})"><i class="fa-solid fa-eye"></i>Details</button></td>
        </tr>
    `).join("");
}

function filterOrders() {
    let status = parseInt(document.getElementById("status-order").value);
    let search = document.getElementById("form-search-order").value.trim().toLowerCase();
    let payment = document.getElementById("payment-method").value.toLowerCase();
    let timeStart = document.getElementById("timestart-order").value;
    let timeEnd = document.getElementById("timeend-order").value;
    let minprice = parseInt(document.getElementById("minprice-order").value) || 0;
    let maxprice = parseInt(document.getElementById("maxprice-order").value) || Infinity;

    // Retrieve selected province, district, and ward
    let province = document.getElementById("province").value.toLowerCase();
    let district = document.getElementById("district").value.toLowerCase();
    let ward = document.getElementById("ward").value.toLowerCase();

    // Validate date range
    if (timeEnd < timeStart && timeEnd != "" && timeStart != "") {
        toastMsg({ title: "ERROR", message: "Invalid date range.", type: "error" });
        return;
    }

    let result = JSON.parse(localStorage.getItem("orders")) || [];
    const accounts = JSON.parse(localStorage.getItem("accounts")) || [];

    if (search) {
        result = result.filter((item) => {
            const account = accounts.find(acc => acc.id === item.customerId);
            return (
                item.customerId.toLowerCase().includes(search.toLowerCase()) ||
                item.id.toString().toLowerCase().includes(search.toLowerCase()) |
                (account && account.phone.includes(search))
            );
        });

    }


    if (province) {
        result = result.filter(item => item.address.region.province.toLowerCase() == province);
    }

    if (district) {
        result = result.filter(item => item.address.region.district.toLowerCase() == district);
    }

    if (ward) {
        result = result.filter(item => item.address.region.ward.toLowerCase() == ward);
    }

    result = payment == "0" ? result : result.filter(item => item.payment.method.toLowerCase() == payment);

    result = status == "4" ? result : result.filter((item) => item.status == status);

    if (timeStart != "" && timeEnd == "") {
        result = result.filter((item) => {
            return new Date(item.orderDate) >= new Date(timeStart).setHours(0, 0, 0);
        });
    } else if (timeStart == "" && timeEnd != "") {
        result = result.filter((item) => {
            return new Date(item.orderDate) <= new Date(timeEnd).setHours(23, 59, 59);
        });
    } else if (timeStart != "" && timeEnd != "") {
        result = result.filter((item) => {
            return (
                new Date(item.orderDate) >= new Date(timeStart).setHours(0, 0, 0) &&
                new Date(item.orderDate) <= new Date(timeEnd).setHours(23, 59, 59)
            );
        });
    }

    result = result.filter(item => item.total >= minprice && item.total <= maxprice);


    console.log("filterOrders()", result);
    renderOrders(result);
}

function resetFilterOrders() {
    document.getElementById("status-order").value = 4;
    document.getElementById("payment-method").value = 0;
    document.getElementById("form-search-order").value = "";
    document.getElementById("timestart-order").value = "";
    document.getElementById("timeend-order").value = "";
    document.getElementById("province").value = ""
    document.getElementById("district").value = ""
    document.getElementById("ward").value = ""
    renderOrders();
}

function showOrderOptions(idx) {
    const orders = JSON.parse(localStorage.getItem("orders"));
    const order = orders[idx];
    const orderOptionsHTML = `
                    <div class="order-detail-row">
                        <span><i class="fa-solid fa-hashtag"></i>Order ID: </span>
                        <span>${order.id}</span>
                    </div>
                    <div class="order-detail-row">
                        <span><i class="fa-regular fa-calendar"></i>Purchase date: </span>
                        <span>${formatDate(order.orderDate)}</span>
                    </div>
                    <div class="order-detail-row">
                    <span><i class="fa-solid fa-cash-register"></i>Payment method: </span>
                    <span>${order.payment.method}</span>
                    </div>
                    <div class="order-detail-row card-payment">
                    <span><i class="fa-solid fa-user"></i>Card owner: </span>
                    <span>${order.payment.cardOwner}</span>
                    </div>
                    <div class="order-detail-row card-payment">
                    <span><i class="fa-regular fa-credit-card"></i>Card number: </span>
                    <span>${order.payment.cardNumber}</span>
                    </div>
                    <div class="order-detail-row address">
                        <span><i class="fa-solid fa-location-dot"></i>Delivery address: </span>
                        <p>${order.address.fullAddress}</p>
                    </div>
                    <div class="order-detail-row address">
                        <span><i class="fa-solid fa-map-location-dot"></i>Region: </span>
                        <p>${order.address.region.ward}, ${order.address.region.district}, ${order.address.region.province}</p>
                    </div>
                    `;

    return orderOptionsHTML;
}

function showCartDetail(orderCart) {
    let cartHTML = ``;

    orderCart.forEach(item => {
        let product = getProduct(item);
        cartHTML += `
                <div class="modal-container cart-item" data-productID="${product.id}">
                    <div class="img-container">
                        <img src="${product.image}">
                    </div>              
                    <div class="cart-item-info">
                        <p class="display-product-name">${product.name}</p>
                        <p>Brand: <span class="display-product-brand">${product.brand}</span></p>
                        <p>Size: <span class="display-product-size">${product.size}</span></p>
                        <p class="display-product-price" style="position: absolute; bottom: 0; right: 0">${vnd(product.price)}</p>
                    </div>              
                    <div class="cart-item-amount">
                        <p>x<span class="display-product-quantity">${item.quantity}</span></p>
                    </div>
                </div>
            `;
    });

    return cartHTML;
}

function showOrderDetail(orderIdx) {
    const orders = JSON.parse(localStorage.getItem("orders"));
    const order = orders[orderIdx];
    MODAL_CONTAINER.innerHTML = ``;

    const modalHTML = `
                  <button class="modal-close" onclick="closeModal()"><i class="fa-regular fa-circle-xmark"></i></button>
                  <div class="modal-container-header">
                      <h2 class="modal-container-title">ORDER DETAILS</h2>
                  </div>
                  <div class="modal-container-body">
                    <div class="cart">
                      <div>
                        ${showCartDetail(order.cart)}
                      </div>
                      <div>
                        <div>
                          <span>Total price:</span>
                          <span style="font-weight: bold"><i>${vnd(order.total)}</i></span>
                        </div>
                        <div>
                          <span>Total quantity:</span>
                          <span style="font-weight: bold"><i>${order.cart.length}</i></span>
                        </div>
                      </div>
                    </div>                
                    <div class="order-detail">
                      ${showOrderOptions(orderIdx)}
                    </div>
                  </div>
                  <div class="modal-container-footer">
                      <div class="control-container">
                        <div class="group">
                          <label for="pending">
                            <input type="radio" name="status" id="pending" value="0"> Pending
                          </label>
                          <label for="processed">
                            <input type="radio" name="status" id="processed" value="1"> Processed
                          </label>
                          <label for="received">
                            <input type="radio" name="status" id="received" value="2"> Received
                          </label>
                          <label for="cancelled">
                            <input type="radio" name="status" id="cancelled" value="3"> Cancelled
                          </label>
                        </div>
                        <button class="modal-save" onclick="saveOrderDetail(${orderIdx})">SAVE</button>
                      </div>
                  </div>`    ;

    MODAL_CONTAINER.innerHTML = modalHTML;
    if (order.payment.method.toLowerCase() != "card") {
        MODAL_CONTAINER.querySelectorAll(".card-payment").forEach(item => item.style.display = "none");
    }

    const radios = MODAL_CONTAINER.querySelectorAll(".control-container .group input[type=radio]")[order.status].checked = true;

    // MODAL_CONTAINER.querySelector(".control-container").addEventListener("click", (event) => {
    //     if (event.target.tagName == "INPUT") {
    //         const radio = event.target;
    //         if (radio.checked) order.status = radio.value;
    //         console.log(`Changed status of order id=${order.id} to ${order_statusTitle[order.status]}`);
    //     } else if (event.target.classList.contains("modal-save")) {
    //         orders[orderIdx] = order;
    //         localStorage.setItem("orders", JSON.stringify(orders));
    //         renderOrders();
    //         closeModal();
    //     }
    // });

    openModal();
}

function saveOrderDetail(idx) {
    const orders = JSON.parse(localStorage.getItem("orders")) || [];
    orders[idx].status = MODAL_CONTAINER.querySelector(".control-container .group input[type=radio]:checked").value;
    localStorage.setItem("orders", JSON.stringify(orders));
    console.log(`Changed status of order id=${orders[idx].id} to ${order_statusTitle[orders[idx].status]}`);
    renderOrders();
    closeModal();
}

// BILLS/ORDERS MAMANGER - END DEFINE ///////////////////////////////////////////////

// ACCOUNTS/CUSTOMERS MAMANGER - BEGIN DEFINE ///////////////////////////////////////////////

document.getElementById("customer").addEventListener("input", filterAccounts);

function renderAccounts(arr = JSON.parse(localStorage.getItem("accounts")) || []) {
    console.log("renderAccounts():", arr);
    const content = document.getElementById("accounts");
    content.innerHTML = "";
    let s = "";

    if (arr.length === 0) {
        displayWhenEmpty("#customer .display-when-empty", displayEmptyHTML_nodata);
        return;
    } else document.querySelector("#customer .display-when-empty").innerHTML = "";

    for (let i = 0; i < arr.length; i++) {
        s += `<tr style="color: ${arr[i].isAdmin ? "red" : "inherit"}; font-weight: ${arr[i].isAdmin ? "bold" : "normal"}">
        <td>${i + 1}</td>
        <td>${arr[i].id}</td>
        <td class="align-left">${arr[i].fullname}</td>
        <td class="align-left">${arr[i].username}</td>
        <td>${arr[i].phone}</td>
        <td>${formatDate(arr[i].join)}</td>
        <td><span class="order-status account-status-${arr[i].status}">${arr[i].status ? "Active" : "Locked"}</span></td>
        <td><button class="details-btn" onclick="showAccountDetail('${arr[i].id}')"><i class="fa-solid fa-eye"></i>Details</button></td>
      </tr>`;
    }

    // Output the generated table rows
    content.innerHTML = s;
}

function filterAccounts() {
    let status = parseInt(document.getElementById("status-user").value);
    let search = document.getElementById("form-search-user").value.trim().toLowerCase();
    let timeStart = document.getElementById("timestart-user").value;
    let timeEnd = document.getElementById("timeend-user").value;

    if (timeEnd < timeStart && timeEnd != "" && timeStart != "") {
        toastMsg({ title: "ERROR", message: "Invalid date range.", type: "error" });
        return;
    }

    const accounts = JSON.parse(localStorage.getItem("accounts")) || [];
    let result = status == 2 ? accounts : accounts.filter(item => item.status == status);

    if (search != "") {
        result = result.filter(item => item.fullname.toLowerCase().includes(search)
            || item.phone.toString().toLowerCase().includes(search));
    }

    if (timeStart != "" && timeEnd == "") {
        result = result.filter((item) => {
            return new Date(item.join) >= new Date(timeStart).setHours(0, 0, 0);
        });
    } else if (timeStart == "" && timeEnd != "") {
        result = result.filter((item) => {
            return new Date(item.join) <= new Date(timeEnd).setHours(23, 59, 59);
        });
    } else if (timeStart != "" && timeEnd != "") {
        result = result.filter((item) => {
            return (new Date(item.join) >= new Date(timeStart).setHours(0, 0, 0) &&
                new Date(item.join) <= new Date(timeEnd).setHours(23, 59, 59));
        });
    }

    console.log("filterAccounts()", result);
    renderAccounts(result);
}

function resetFilterAccounts() {
    renderAccounts();
    document.getElementById("status-user").value = 2;
    document.getElementById("form-search-user").value = "";
    document.getElementById("timestart-user").value = "";
    document.getElementById("timeend-user").value = "";
}

// Show account details
function showAccountDetail(id) {
    const accounts = JSON.parse(localStorage.getItem("accounts")) || [];
    const account = getAccount(id);
    console.log(account);

    const modalHTML = `
                    <button class="modal-close" onclick="closeModal()"><i class="fa-regular fa-circle-xmark"></i></button>
                <div class="modal-container-header">
                    <h2 class="modal-container-title">ACCOUNT DETAILS</h2>
                </div>
                <div class="modal-container-body">
                    <form>
                        <div class="group">
                            <label for="username" class="form-label">Username*</label>
                            <input id="username" name="username" type="text" placeholder="username" value="${account.username || ""}"
                                class="form-input-bar">
                            <span class="form-msg-error"></span>
                        </div>
                        <div class="group">
                            <label for="fullname" class="form-label">Full name*</label>
                            <input id="fullname" name="fullname" type="text" placeholder="Full name" value="${account.fullname || ""}"
                                class="form-input-bar">
                            <span class="form-msg-error"></span>
                        </div>
                        <div class="group">
                            <label for="phone" class="form-label">Phone number*</label>
                            <input id="phone" name="phone" type="tel" placeholder="Phone number" value="${account.phone || ""}"
                            class="form-input-bar">
                            <span class="form-msg-error"></span>
                        </div>
                        <div class="group">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email" placeholder="Email" value="${account.email || ""}"
                            class="form-input-bar">
                            <span class="form-msg-error"></span>
                        </div>
                        <div class="group">
                            <label for="password" class="form-label">Password*</label>
                            <input id="password" name="password" type="text" placeholder="Password" value="${account.password || ""}"
                                class="form-input-bar">
                            <span class="form-msg-error"></span>
                        </div>
                        <div class="group">
                            <label for="address" class="form-label">Address*</label>
                            <input id="address" name="address" type="text" placeholder="Address" value="${account.address || ""}"
                            class="form-input-bar">
                            <span class="form-msg-error"></span>
                        </div>
                        <div class="group edit-account-e" style="display: ${account.isAdmin ? "none" : "initial"}">
                            <label for="" class="form-label">Active status</label>
                            <input type="checkbox" id="user-status" class="switch-input">
                            <label for="user-status" class="switch"></label>
                        </div>
                    </form>
                </div>
                <div class="modal-container-footer">
                    <div class="control-container">
                        <button class="modal-save" onclick="saveAccountDetail('${account.id}')">SAVE</button>
                    </div>
                </div>
    `;

    MODAL_CONTAINER.innerHTML = modalHTML;
    if (account.status == 1) MODAL_CONTAINER.querySelector("#user-status").checked = true;

    openModal();
}

function showError(selector, message) {
    const inputGroup = MODAL_CONTAINER.querySelector(selector).closest(".group");
    inputGroup.querySelector(".form-msg-error").textContent = message;
}

function clearError(selector) {
    const inputGroup = MODAL_CONTAINER.querySelector(selector).closest(".group");
    inputGroup.querySelector(".form-msg-error").textContent = "";
}

function validateAccountDetails(account) {
    const errors = {};

    // Validation logic
    if (!account.username || account.username.trim() === "" || /\W|\s/.test(account.username.trim())) {
        errors.username = "Username cannot be empty";
    }

    if (!account.fullname || account.fullname.trim() === "") {
        errors.fullname = "Full name cannot be empty";
    }

    if (account.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(account.email)) {
        errors.email = "Please enter a valid email address.";
    }

    if (!account.phone || !/^\d{10}$/.test(account.phone)) {
        errors.phone = "Invalid phone number (must be 10 digits)";
    }

    if (!account.password || account.password.length < 5) {
        errors.password = "Password must be at least 5 characters";
    }

    if (!account.address || account.address.trim() === "") {
        errors.address = "Address cannot be empty";
    }

    return errors;
}

function saveAccountDetail(id) {
    const fields = {
        username: MODAL_CONTAINER.querySelector("#username").value.trim(),
        fullname: MODAL_CONTAINER.querySelector("#fullname").value.trim(),
        phone: MODAL_CONTAINER.querySelector("#phone").value.trim(),
        email: MODAL_CONTAINER.querySelector("#email").value.trim(),
        password: MODAL_CONTAINER.querySelector("#password").value.trim(),
        address: MODAL_CONTAINER.querySelector("#address").value.trim(),
        status: MODAL_CONTAINER.querySelector("#user-status").checked ? 1 : 0,
    };

    // Validate account details
    const errors = validateAccountDetails(fields);
    // Clear errors if everything is valid
    ["username", "fullname", "phone", "password", "address"].forEach((field) => {
        clearError(`#${field}`);
    });

    // Display errors if any
    if (Object.keys(errors).length > 0) {
        Object.keys(errors).forEach((key) => {
            showError(`#${key}`, errors[key]);
        });
        return;
    }


    // Save the account
    const accounts = JSON.parse(localStorage.getItem("accounts")) || [];
    let idx = accounts.findIndex(user => user.id == id);

    // Check for duplicates if username or phone was changed
    const isPhoneChanged = fields.phone !== accounts[idx].phone;
    const isUsernameChanged = fields.username !== accounts[idx].username;

    const duplicatePhoneIdx = isPhoneChanged
        ? accounts.findIndex(user => user.phone === fields.phone && user.id != id)
        : -1;

    console.log("duplicatePhoneIdx:", duplicatePhoneIdx);

    const duplicateUsernameIdx = isUsernameChanged
        ? accounts.findIndex(user => user.username === fields.username && user.id != id)
        : -1;

    console.log("duplicateUsernameIdx:", duplicateUsernameIdx);

    // Handle duplicates
    if (duplicatePhoneIdx !== -1) {
        toastMsg({ title: "ERROR", message: "Phone number already exists!", type: "error" });
    }

    if (duplicateUsernameIdx !== -1) {
        toastMsg({ title: "ERROR", message: "Username already exists!", type: "error" });
    }

    if (duplicatePhoneIdx !== -1 || duplicateUsernameIdx !== -1) return;

    // No duplicates, proceed to save
    accounts[idx] = { ...accounts[idx], ...fields };
    localStorage.setItem("accounts", JSON.stringify(accounts));

    // Update admin info if the modified details is Admin account
    const currentuser = JSON.parse(localStorage.getItem("currentuser"));
    if (currentuser.id === id)
        localStorage.setItem("currentuser", JSON.stringify({ ...currentuser, ...fields }));


    console.log("saveAccountDetail()", id, accounts[idx]);
    toastMsg({ title: "SUCCESS", message: "Account saved", type: "success" });
    filterAccounts();
    closeModal();
}

function showModalAccount() {
    const modalHTML = `
    <button class="modal-close" onclick="closeModal()"><i class="fa-regular fa-circle-xmark"></i></button>
<div class="modal-container-header">
    <h2 class="modal-container-title">ADD ACCOUNT</h2>
</div>
<div class="modal-container-body">
    <form>
        <div class="group">
            <label for="username" class="form-label">Username</label>
            <input id="username" name="username" type="text" placeholder="username"
                class="form-input-bar">
            <span class="form-msg-error"></span>
        </div>
        <div class="group">
            <label for="fullname" class="form-label">Full name</label>
            <input id="fullname" name="fullname" type="text" placeholder="Full name"
                class="form-input-bar">
            <span class="form-msg-error"></span>
        </div>
        <div class="group">
            <label for="phone" class="form-label">Phone number</label>
            <input id="phone" name="phone" type="tel" placeholder="Phone number"
            class="form-input-bar">
            <span class="form-msg-error"></span>
        </div>
        <div class="group">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" placeholder="Email"
            class="form-input-bar">
            <span class="form-msg-error"></span>
        </div>
        <div class="group">
            <label for="password" class="form-label">Password</label>
            <input id="password" name="password" type="text" placeholder="Password"
                class="form-input-bar">
            <span class="form-msg-error"></span>
        </div>
        <div class="group">
            <label for="address" class="form-label">Address</label>
            <input id="address" name="address" type="text" placeholder="Address"
            class="form-input-bar">
            <span class="form-msg-error"></span>
        </div>
    </form>
</div>
<div class="modal-container-footer">
    <div class="control-container">
        <button class="modal-save" onclick="addAccount()">ADD</button>
    </div>
</div>
`;
    MODAL_CONTAINER.innerHTML = modalHTML;
    openModal();
}

function addAccount() {

    const fields = {
        username: MODAL_CONTAINER.querySelector("#username").value.trim(),
        fullname: MODAL_CONTAINER.querySelector("#fullname").value.trim(),
        phone: MODAL_CONTAINER.querySelector("#phone").value.trim(),
        email: MODAL_CONTAINER.querySelector("#email").value.trim(),
        password: MODAL_CONTAINER.querySelector("#password").value.trim(),
        address: MODAL_CONTAINER.querySelector("#address").value.trim(),
    };

    // Validate account details
    const errors = validateAccountDetails(fields);

    // Reset errors
    ["username", "fullname", "phone", "password", "address"].forEach((field) => {
        clearError(`#${field}`);
    });

    // Display errors if any
    if (Object.keys(errors).length > 0) {
        Object.keys(errors).forEach((key) => {
            showError(`#${key}`, errors[key]);
        });
        toastMsg({ title: "ERROR", message: "Please fill in the fields correctly.", type: "error" });
        return;
    }
    const accounts = JSON.parse(localStorage.getItem("accounts")) || [];

    const duplicatePhoneIdx = accounts.findIndex(user => user.phone === fields.phone);
    console.log("duplicatePhoneIdx:", duplicatePhoneIdx);

    const duplicateUsernameIdx = accounts.findIndex((user, i) => user.username === fields.username);
    console.log("duplicateUsernameIdx:", duplicateUsernameIdx);

    // Handle duplicates
    if (duplicatePhoneIdx !== -1) {
        toastMsg({ title: "ERROR", message: "Phone number already exists!", type: "error" });
    }

    if (duplicateUsernameIdx !== -1) {
        toastMsg({ title: "ERROR", message: "Username already exists!", type: "error" });
    }

    if (duplicatePhoneIdx !== -1 || duplicateUsernameIdx !== -1) return;


    let user = {
        ...fields,
        id: ID_TYPE[0] + (accounts.length + 1),
        join: new Date(),
        cart: [],
        isAdmin: 0,
        status: 1
    }

    accounts.push(user);
    localStorage.setItem("accounts", JSON.stringify(accounts));
    toastMsg({ title: "SUCCESS", message: "Account created successfully!", type: "success" });
    filterAccounts();
    closeModal();
}

// ACCOUNTS/CUSTOMERS MAMANGER - END DEFINE ///////////////////////////////////////////////

// HOMEPAGE - BEGIN DEFINE ///////////////////////////////////////////////

const ADMIN_HOMEPAGE = document.getElementById("homepage");
function displayHomepageStatistic() {
    const accounts = JSON.parse(localStorage.getItem("accounts")) || [];
    const activeAccounts = accounts.filter(account => account.status == 1 && account.isAdmin == false).length;
    ADMIN_HOMEPAGE.querySelector(".display-user-count").innerHTML = activeAccounts;

    const products = JSON.parse(localStorage.getItem("products")) || [];
    const activeProducts = products.filter(product => product.isDeleted == false).length;
    ADMIN_HOMEPAGE.querySelector(".display-product-count").innerHTML = activeProducts;

    const orders = JSON.parse(localStorage.getItem("orders")) || [];
    const activeOrders = orders.filter(order => (order.status == 2 && order.payment.method.toLowerCase() == "cash")
        || (order.status == 1 && order.payment.method.toLowerCase() == "card"));

    let totalIncome = 0;
    activeOrders.forEach(order => totalIncome += order.total);
    ADMIN_HOMEPAGE.querySelector(".display-total-income").innerHTML = vnd(totalIncome);
}

function logOut() {
    localStorage.removeItem("currentuser");
    window.location.href = "index.php";
}

// HOMEPAGE - END DEFINE ///////////////////////////////////////////////

// STATISTIC - BEGIN DEFINE ///////////////////////////////////////////////
// let filteredOrders_global = JSON.parse(localStorage.getItem("orders")) || [];
// let filteredAccounts_global = JSON.parse(localStorage.getItem("accounts")) || [];
// let filteredProducts_global = JSON.parse(localStorage.getItem("products")) || [];

document.getElementById("statistical").addEventListener("input", filterStat);
// STATISTIC  - STATS - BEGIN DEFINE

function showMilestones(orders) {
    let soldIDs = [];
    let totalQuantity = 0;
    let totalRevenue = 0;

    for (let i = 0; i < orders.length; i++) {
        if ((orders[i].payment.method.toLowerCase() == "cash" && orders[i].status == 2) ||
            (orders[i].payment.method.toLowerCase() == "card" && orders[i].status == 1)) {

            for (let j = 0; j < orders[i].cart.length; j++) {
                let productId = orders[i].cart[j].id;

                if (!soldIDs.includes(productId)) {
                    soldIDs.push(productId);
                }
            }

            totalQuantity += orders[i].cart.length;
            totalRevenue += orders[i].total;
        }
    }

    document.getElementById("quantity-product").innerText = soldIDs.length;
    document.getElementById("quantity-order").textContent = totalQuantity;
    document.getElementById("quantity-sale").innerHTML = vnd(totalRevenue);
}

// function filterStat(sortby = "asc") {
//     filteredOrders_global = JSON.parse(localStorage.getItem("orders")) || [];
//     filteredAccounts_global = JSON.parse(localStorage.getItem("accounts")) || [];
//     filteredProducts_global = JSON.parse(localStorage.getItem("products")) || [];

//     let brand = document.getElementById("brand-product-stat").value.toLowerCase();
//     let search = document.getElementById("form-search-stat").value.toLowerCase();
//     let timeStart = document.getElementById("timestart-stat").value;
//     let timeEnd = document.getElementById("timeend-stat").value;

//     if (timeEnd < timeStart && timeEnd != "" && timeStart != "") {
//         toastMsg({ title: "ERROR", message: "Invalid date range.", type: "error" });
//         return;
//     }

//     if (brand && brand != "0") {
//         filteredProducts_global = filteredProducts_global.filter(product => product.brand.toLowerCase().includes(brand));
//         filteredOrders_global = filteredOrders_global.filter(order => {
//             return order.cart.some(cartItem => {
//                 const product = filteredProducts_global.find(p => p.id === cartItem.productId);
//                 return product && product.brand.toLowerCase().includes(brand);
//             });
//         });
//     }


//     if (search != "") {
//         filteredProducts_global = filteredProducts_global.filter(product =>
//             product.name.toLowerCase().includes(search) ||
//             product.id.toLowerCase().includes(search)
//         );
//         filteredOrders_global = filteredOrders_global.filter(order => {
//             return order.cart.some(cartItem => {
//                 const product = filteredProducts_global.find(p => p.id === cartItem.productId);
//                 return product && (product.name.toLowerCase().includes(search) || product.id.toLowerCase().includes(search));
//             });
//         });
//     }

//     if (timeStart && timeEnd) {
//         filteredOrders_global = filteredOrders_global.filter(order =>
//             new Date(order.orderDate) >= new Date(timeStart) && new Date(order.orderDate) <= new Date(timeEnd)
//         );
//     } else if (timeStart) {
//         filteredOrders_global = filteredOrders_global.filter(order =>
//             new Date(order.orderDate) >= new Date(timeStart)
//         );
//     } else if (timeEnd) {
//         filteredOrders_global = filteredOrders_global.filter(order =>
//             new Date(order.orderDate) <= new Date(timeEnd)
//         );
//     }

//     showMilestones(filteredOrders_global);
//     renderTopAccounts(filteredAccounts_global, filteredOrders_global);
//     renderTopProducts(filteredProducts_global, filteredOrders_global);
// }

function resetFilterStat() {
    document.getElementById("brand-product-stat").value = "0";
    document.getElementById("form-search-stat").value = "";
    document.getElementById("timestart-stat").value = "";
    document.getElementById("timeend-stat").value = "";

    filterStat();
}


function filterStat(sortby = "none") {
    filteredOrders_global = JSON.parse(localStorage.getItem("orders")) || [];
    filteredAccounts_global = JSON.parse(localStorage.getItem("accounts")) || [];
    filteredProducts_global = JSON.parse(localStorage.getItem("products")) || [];

    let brand = document.getElementById("brand-product-stat").value.toLowerCase();
    let search = document.getElementById("form-search-stat").value.toLowerCase();
    let timeStart = document.getElementById("timestart-stat").value;
    let timeEnd = document.getElementById("timeend-stat").value;

    if (timeEnd < timeStart && timeEnd != "" && timeStart != "") {
        toastMsg({ title: "ERROR", message: "Invalid date range.", type: "error" });
        return;
    }

    // Filter products by brand
    if (brand && brand != "0") {
        filteredProducts_global = filteredProducts_global.filter(product => product.brand.toLowerCase().includes(brand));
    }

    // Filter products by search term (name or id)
    if (search != "") {
        filteredProducts_global = filteredProducts_global.filter(product =>
            product.name.toLowerCase().includes(search) ||
            product.id.toLowerCase().includes(search)
        );
    }

    // Filter orders based on the selected time range
    if (timeStart && timeEnd) {
        filteredOrders_global = filteredOrders_global.filter(order =>
            new Date(order.orderDate) >= new Date(timeStart).setHours(0,0,0) &&
            new Date(order.orderDate) <= new Date(timeEnd).setHours(23,59,59)
        );
    } else if (timeStart) {
        filteredOrders_global = filteredOrders_global.filter(order =>
            new Date(order.orderDate) >= new Date(timeStart)
        );
    } else if (timeEnd) {
        filteredOrders_global = filteredOrders_global.filter(order =>
            new Date(order.orderDate) <= new Date(timeEnd)
        );
    }


    // Filter orders by products' brand (based on the cart and selected brand)
    if (brand && brand != "0") {
        filteredOrders_global = filteredOrders_global.filter(order => {
            return order.cart.some(cartItem => {
                const product = filteredProducts_global.find(p => p.id === cartItem.id);
                return product && product.brand.toLowerCase() == brand;
            });
        });
    }

    // Sort by revenue for products and customers/accounts
    if (sortby === "asc") {
        filteredProducts_global = filteredProducts_global.sort((a, b) => 
            getProductRevenue(a.id, filteredOrders_global).revenue - 
            getProductRevenue(b.id, filteredOrders_global).revenue
        );
        filteredAccounts_global = filteredAccounts_global.sort((a, b) =>
            getAccountRevenue(a.id, filteredOrders_global) - 
            getAccountRevenue(b.id, filteredOrders_global)
        );
    } else if (sortby === "desc") {
        filteredProducts_global = filteredProducts_global.sort((a, b) => 
            getProductRevenue(b.id, filteredOrders_global).revenue - 
            getProductRevenue(a.id, filteredOrders_global).revenue
        );
        filteredAccounts_global = filteredAccounts_global.sort((a, b) =>
            getAccountRevenue(b.id, filteredOrders_global) - 
            getAccountRevenue(a.id, filteredOrders_global)
        );
    }
    
    

    // Show the filtered results
    showMilestones(filteredOrders_global);
    renderTopAccounts(filteredAccounts_global, filteredOrders_global);
    renderTopProducts(filteredProducts_global, filteredOrders_global);
}


// STATISCITC - STATS - END DEFINE

// STATISCITC - TOP PRODUCTS - BEGIN DEFINE
function showProductRevenue(id) {

    const productOrders = filteredOrders_global.filter(order =>
        order.cart.some(item => item.id === id) && (
            (order.payment.method.toLowerCase() === "cash" && order.status == 2) ||
            (order.payment.method.toLowerCase() === "card" && order.status == 1)
        )
    );

    let modalHTML = `
        <button class="modal-close" onclick="closeModal()"><i class="fa-regular fa-circle-xmark"></i></button>
        <div class="modal-container-header">
            <h2 class="modal-container-title">REVENUE DETAILS OF ${id}</h2>
        </div>
        <div class="modal-container-body">
            <table class="product-table">
                <thead>
                    <tr>
                        <th>INDEX</th>
                        <th>ORDER</th>
                        <th>DATE</th>
                        <th>QUANTITY</th>
                        <th>PRICE</th>
                        <th>REVENUE</th>
                    </tr>
                </thead>
                <tbody>
    `;

    productOrders.forEach((order, index) => {
        const productInOrder = order.cart.find(item => item.id === id);

        const revenue = productInOrder.quantity * productInOrder.originalPrice;

        modalHTML += `
            <tr>
                <td>${index + 1}</td>
                <td>${order.id}</td>
                <td>${formatDate(order.orderDate)}</td>
                <td>${productInOrder.quantity}</td>
                <td>${vnd(productInOrder.originalPrice)}</td>
                <td>${vnd(revenue)}</td>
            </tr>
        `;
    });

    modalHTML += `
                </tbody>
            </table>
        </div>
    `;

    // Insert the modal content into the modal container
    MODAL_CONTAINER.innerHTML = modalHTML;
    openModal();

}

function getProductRevenue(id, orders) {
    let revenue = 0;
    let soldQuantity = 0;
    let inOrder = [];

    for (let i = 0; i < orders.length; i++) {
        const order = orders[i];
        const productInOrder = order.cart.find((item) => item.id === id);

        if (productInOrder) {
            let paymentMethod = order.payment.method.toLowerCase();

            if ((paymentMethod === "cash" && order.status == 2) ||
                (paymentMethod === "card" && order.status == 1)) {
                revenue += productInOrder.originalPrice;
                soldQuantity += productInOrder.quantity;
                inOrder.push(order.id);
            }
        }
    }
    return { revenue, soldQuantity };
}

function renderTopProducts(products, orders) {
    console.log("renderTopProducts():", products, orders);
    const content = document.getElementById("statistic-products");
    content.innerHTML = "";
    let modalHTML = "";
    let validIndex = 1;
    if (products.length === 0 || orders.length === 0) {
        displayWhenEmpty("#statistical .display-when-empty", displayEmptyHTML_nodata);
        return;
    } else document.querySelector("#statistical .display-when-empty").innerHTML = "";

    products.forEach((product) => {
        let revenueDetails = getProductRevenue(product.id, orders);
        if (revenueDetails.revenue <= 0) return;

        modalHTML += `<tr>
        <td>${validIndex++}</td>
        <td>${product.id}</td>
        <td class="align-left">${product.name}</td>
        <td>${revenueDetails.soldQuantity}</td>
        <td>${vnd(revenueDetails.revenue)}</td>
        <td><button class="details-btn" onclick="showProductRevenue('${product.id}')"><i class="fa-solid fa-eye"></i>Details</button></td>
      </tr>`;
    });

    // Output the generated table rows
    content.innerHTML = modalHTML;
}
// STATISCITC - TOP PRODUCTS - BEGIN DEFINE

// STATISCITC - TOP ACCOUNTS - BEGIN DEFINE

function showAccountRevenue(id) {

    const accountOrders = filteredOrders_global.filter(order =>
        order.customerId === id && (
            (order.payment.method.toLowerCase() === "cash" && order.status == 2) ||
            (order.payment.method.toLowerCase() === "card" && order.status == 1)
        )
    );

    let modalHTML = `
        <button class="modal-close" onclick="closeModal()"><i class="fa-regular fa-circle-xmark"></i></button>
        <div class="modal-container-header">
            <h2 class="modal-container-title">REVENUE DETAILS OF ${id}</h2>
        </div>
        <div class="modal-container-body">
            <table class="product-table">
                <thead>
                    <tr>
                        <th>INDEX</th>
                        <th>ORDER</th>
                        <th>QUANTITY</th>
                        <th>DATE</th>
                        <th>REVENUE</th>
                    </tr>
                </thead>
                <tbody>
    `;

    accountOrders.forEach((order, index) => {
        modalHTML += `
            <tr>
                <td>${index + 1}</td>
                <td>${order.id}</td>
                <td>${order.cart.length}</td>
                <td>${formatDate(order.orderDate)}</td>
                <td>${vnd(order.total)}</td>
            </tr>
        `;
    });

    modalHTML += `
                </tbody>
            </table>
        </div>
    `;

    MODAL_CONTAINER.innerHTML = modalHTML;
    openModal();
}

function getAccountRevenue(id, orders) {
    let revenue = 0;

    for (let i = 0; i < orders.length; i++) {
        if (orders[i].customerId === id) {
            const order = orders[i];
            let paymentMethod = order.payment.method.toLowerCase();

            if ((paymentMethod === "cash" && order.status == 2) ||
                (paymentMethod === "card" && order.status == 1)) {
                revenue += order.total;
            }
        }
    }

    return revenue;
}

function renderTopAccounts(accounts, orders) {
    console.log("renderTopAccounts():", accounts, orders);
    const content = document.getElementById("statistic-accounts");
    content.innerHTML = "";
    let modalHTML = "";
    let validAccountIndex = 1;

    if (accounts.length === 0 || orders.length === 0) {
        displayWhenEmpty("#statistical .admin-content-main__center .product-table:last-child .display-when-empty", displayEmptyHTML_nodata);
        return;
    } else document.querySelector("#statistical .admin-content-main__center .product-table:last-child .display-when-empty").innerHTML = "";

    accounts.forEach((account) => {
        let revenue = getAccountRevenue(account.id, orders);
        if (revenue <= 0) return;

        modalHTML += `<tr>
        <td>${validAccountIndex++}</td>
        <td>${account.id}</td>
        <td class="align-left">${account.fullname}</td>
        <td>${account.phone}</td>
        <td>${vnd(revenue)}</td>
        <td><button class="details-btn" onclick="showAccountRevenue('${account.id}')"><i class="fa-solid fa-eye"></i>Details</button></td>
      </tr>`;
    });

    // Output the generated table rows
    content.innerHTML = modalHTML;
}

// STATISCITC - TOP ACCOUNTS - END DEFINE

// STATISTIC - END DEFINE ///////////////////////////////////////////////


// ONLOAD
window.onload = () => {
    displayHomepageStatistic();
    const currentuser = JSON.parse(localStorage.getItem("currentuser")) || [];
    document.querySelector(".display-username").innerText = currentuser.username || "";
}