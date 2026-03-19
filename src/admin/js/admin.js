const API_BASE_URL = "api.php?action=";

async function fetchData(action, method = "GET", body = null) {
    try {
        const options = { method };
        if (body) {
            options.headers = { "Content-Type": "application/json" };
            options.body = JSON.stringify(body);
        }
        const response = await fetch(API_BASE_URL + action, options);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error("Lỗi fetch:", error);
        return { success: false, message: "Lấy dữ liệu thất bại" };
    }
}

// Lấy danh sách sản phẩm
async function getProducts() {
    return await fetchData("get_products");
}

// Thêm sản phẩm
async function addProduct(product) {
    return await fetchData("add_product", "POST", product);
}

// Cập nhật sản phẩm
async function updateProduct(product) {
    return await fetchData("update_product", "PUT", product);
}

// Xóa sản phẩm
async function deleteProduct(productId) {
    return await fetchData("delete_product", "DELETE", { ProductID: productId });
}

// Lấy danh sách đơn hàng
async function getOrders() {
    return await fetchData("get_orders");
}

// Thêm đơn hàng
async function addOrder(order) {
    return await fetchData("add_order", "POST", order);
}

// Sử dụng ví dụ
getProducts().then(products => console.log(products));