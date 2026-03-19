<?php
// header('Content-Type: application/json');
require_once 'db_connect.php';

/*************************** PRODUCT START ***************************/

function getProduct() {
    $sql = "
        SELECT p.ProductID AS id, p.ProductName AS name, p.Price AS price, p.ImageURL AS image, 
        c.CategoryName AS category, b.BrandName AS brand, p.Gender AS sex,
        GROUP_CONCAT(ps.Size) AS size, 
        SUM(ps.StockQuantity) AS totalStock
        FROM Product p
        LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
        LEFT JOIN Brand b ON p.BrandID = b.BrandID
        LEFT JOIN ProductSize ps ON p.ProductID = ps.ProductID
        GROUP BY p.ProductID;
    ";

    $products = getAll($sql);

    // Chuyển kích thước thành mảng và thêm thuộc tính isDeleted
    foreach ($products as &$product) {
        // Chuyển kích thước thành mảng
        $product['size'] = !empty($product['size']) ? explode(',', $product['size']) : [];
        // Thêm thuộc tính isDeleted mặc định
        $product['isDeleted'] = false;
    
        // Xác định trạng thái tồn kho dựa trên tổng số lượng tồn kho
        if ($product['totalStock'] == 0) {
            $product['stockStatus'] = 'Out of Stock';
        } elseif ($product['totalStock'] <= 5) {
            $product['stockStatus'] = 'Low Stock';
        } else {
            $product['stockStatus'] = 'In Stock';
        }
    }
    

    return $products;
}







function getProductDetail($idProduct) {
    $sql = "
        SELECT p.ProductID AS id, p.ProductName AS name, p.Price AS price, p.ImageURL AS image, 
            c.CategoryName AS category, b.BrandName AS brand, p.Gender AS sex,
            GROUP_CONCAT(DISTINCT CONCAT(ps.ProductSizeID, '-', ps.Size, '-', ps.StockQuantity) ORDER BY ps.Size ASC) AS size_raw
        FROM Product p
        LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
        LEFT JOIN Brand b ON p.BrandID = b.BrandID
        LEFT JOIN ProductSize ps ON p.ProductID = ps.ProductID
        WHERE p.ProductID = $idProduct
        GROUP BY p.ProductID;
    ";

    $product = getOne($sql); // Không cần truyền tham số

    if ($product) {
        $product['size'] = [];
        if (!empty($product['size_raw'])) {
            $sizes = explode(',', $product['size_raw']);
            foreach ($sizes as $sizeInfo) {
                list($productSizeId, $size, $stock) = explode('-', $sizeInfo);
                $product['size'][] = [
                    'ProductSizeID' => $productSizeId,
                    'Size' => $size,
                    'Stock' => (int)$stock
                ];
            }
        }
    }

    return $product;
}


// function getIDCatalog ($idCatalog) {
//     $sql = "SELECT CategoryID FROM categories WHERE CategoryID =".$idCatalog;
//     $getone = getOne ($sql);
//     extract($getone);
//     return $CategoryID;
// }

function getProducts($pdo)
{
    // Truy vấn SQL lấy thông tin sản phẩm và các thuộc tính liên quan
    $stmt = $pdo->prepare("
        SELECT p.ProductID AS id, p.ProductName AS name, p.Price AS price, p.ImageURL AS image, 
               c.CategoryName AS category, b.BrandName AS brand, p.Gender AS sex,
               GROUP_CONCAT(ps.Size) AS size, 
               SUM(ps.StockQuantity) AS totalStock  -- Tính tổng số lượng tồn kho
        FROM Product p
        LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
        LEFT JOIN Brand b ON p.BrandID = b.BrandID
        LEFT JOIN ProductSize ps ON p.ProductID = ps.ProductID
        GROUP BY p.ProductID
    ");
    
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Chuyển kích thước thành mảng và thêm thông tin tồn kho
    foreach ($products as &$product) {
        // Chuyển kích thước thành mảng
        $product['size'] = explode(',', $product['size']);
        $product['isDeleted'] = false;

        // Xác định trạng thái tồn kho dựa trên tổng số lượng tồn kho
        if ($product['totalStock'] == 0) {
            $product['stockStatus'] = 'Out of Stock';  // Hết hàng
        } elseif ($product['totalStock'] <= 5) {
            $product['stockStatus'] = 'Low Stock';    // Sắp hết hàng
        } else {
            $product['stockStatus'] = 'In Stock';     // Còn hàng
        }
    }

    return $products;
}


function getSuppliers($pdo)
{
    $stmt = $pdo->prepare("SELECT SupplierID AS id, SupplierName AS name FROM Supplier");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getEmployees($pdo)
{
    $stmt = $pdo->prepare("
        SELECT e.EmployeeID AS id, e.Username AS username, e.Fullname AS fullname, e.PhoneNumber AS phone,
               e.Email AS email, e.Address AS address, e.CreatedAt AS join, p.PermissionName AS permission
        FROM Employee e
        LEFT JOIN Permission p ON e.PermissionID = p.PermissionID
    ");
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($employees as &$employee) {
        $employee['join'] = date('Y/m/d', strtotime($employee['join']));
    }

    return $employees;
}


function getCarts($pdo)
{
    $stmt = $pdo->prepare("
        SELECT c.CartID AS id, u.Username AS username, p.ProductName AS product_name, c.Size AS size, 
               c.Quantity AS quantity, c.AddedAt AS added_at
        FROM Cart c
        LEFT JOIN User u ON c.UserID = u.UserID
        LEFT JOIN Product p ON c.ProductID = p.ProductID
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getProductSizes($pdo)
{
    $stmt = $pdo->prepare("
        SELECT ps.ProductSizeID AS id, p.ProductName AS product_name, ps.Size AS size, ps.StockQuantity AS stock
        FROM ProductSize ps
        LEFT JOIN Product p ON ps.ProductID = p.ProductID
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getBrands($pdo)
{
    $stmt = $pdo->prepare("SELECT BrandID AS id, BrandName AS name FROM Brand");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getCategories($pdo)
{
    $stmt = $pdo->prepare("SELECT CategoryID AS id, CategoryName AS name FROM Categories");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Lấy danh sách tài khoản
function getAccounts($pdo)
{
    $stmt = $pdo->prepare("
        SELECT UserID AS id, Username AS username, Fullname AS fullname, PhoneNumber AS phone,
               Email AS email, Address AS address, PasswordHash AS password, 
               CreatedAt AS join
        FROM User
    ");
    $stmt->execute();
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($accounts as &$account) {
        $account['cart'] = [];
        $account['status'] = 1;
        $account['join'] = date('Y/m/d', strtotime($account['join']));
    }

    return $accounts;
}


// Lấy danh sách đơn hàng
function getOrders($pdo)
{
    $stmt = $pdo->prepare("
        SELECT o.OrderID AS id, o.UserID AS customerId, o.OrderDate AS orderDate, 
               o.ShippingAddress AS fullAddress, o.Province, o.Ward, o.Status AS method, 
               o.OrderStatus AS status, od.ProductID AS product_id, od.Size, 
               od.Quantity AS quantity, od.UnitPrice AS originalPrice
        FROM orders o
        LEFT JOIN OrderDetail od ON o.OrderID = od.OrderID
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Xử lý dữ liệu đơn hàng
    $orders = [];
    foreach ($rows as $row) {
        $orderId = $row['id'];
        if (!isset($orders[$orderId])) {
            $orders[$orderId] = [
                'id' => $orderId,
                'customerId' => $row['customerId'],
                'cart' => [],
                'address' => [
                    'fullAddress' => $row['fullAddress'],
                    'region' => [
                        'province' => $row['Province'],
                        'ward' => $row['Ward']
                    ]
                ],
                'payment' => ['method' => $row['method']],
                'orderDate' => $row['orderDate'],
                'status' => $row['status'],
                'total' => 0 // Tính tổng từ OrderDetail
            ];
        }
        if ($row['product_id']) {
            $orders[$orderId]['cart'][] = [
                'id' => $row['product_id'],
                'quantity' => $row['quantity'],
                'size' => $row['Size'],
                'originalPrice' => $row['originalPrice']
            ];
            // Tính tổng tiền
            $orders[$orderId]['total'] += $row['quantity'] * $row['originalPrice'] + 30000;
        }
    }

    // Lấy thông tin thanh toán thẻ
    $stmt = $pdo->prepare("SELECT OrderID, CardOwner, CardNumber, CVV FROM PaymentDetail");
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($payments as $payment) {
        $orderId = $payment['OrderID'];
        if (isset($orders[$orderId])) {
            $orders[$orderId]['payment'] = array_merge($orders[$orderId]['payment'], $payment);
        }
    }

    return array_values($orders);
}


function getCatalogName($catalogID) {
    try {
        $conn = connectdb();
        $sql = "SELECT CategoryName FROM categories WHERE CategoryID = :catalogID";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':catalogID', $catalogID, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['CategoryName'] : null;
    } catch (PDOException $e) {
        echo "Lỗi khi lấy tên danh mục: " . $e->getMessage();
        return null;
    }
}

function getOrdersByUserID(){
    $conn = connectdb();
    $userID = $_SESSION['user']['userID'];
    $sql = "SELECT * FROM orders WHERE UserID = :userID";    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCatalogList(){
    $conn = connectdb();
    $sql = "SELECT * FROM categories";    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getuserinfo($username, $password) {
    $conn = connectdb();
    $sql = "SELECT * FROM User WHERE Username = :username AND PasswordHash = :password";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $kq = $stmt->fetchAll();
    return $kq;
}

function getobjectuserinfo($username, $userid) {
    $conn = connectdb();
    $stmt = $conn->prepare("SELECT * FROM User WHERE Username = :username AND UserID = :userid");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function updateUser($userID, $fullname, $phonenumber, $email, $address ,$ProvinceID , $DistrictID, $WardID ) {
    $conn = connectdb();
    $sql = "UPDATE User SET Fullname = :fullname, PhoneNumber = :phonenumber, Email = :email, Address = :address , ProvinceID = :provinceid , DistrictID = :districtid , WardID = :wardid WHERE UserID = :userID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
    $stmt->bindParam(':phonenumber', $phonenumber, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':address', $address, PDO::PARAM_STR);
    $stmt->bindParam(':provinceid', $ProvinceID, PDO::PARAM_INT);
    $stmt->bindParam(':districtid', $DistrictID, PDO::PARAM_INT);
    $stmt->bindParam(':wardid', $WardID, PDO::PARAM_INT);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    return $stmt->execute();
}
function updatePassword($userID, $password) {
    $conn = connectdb();
    $sql = "UPDATE User SET PasswordHash = :password WHERE UserID = :userID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    return $stmt->execute();
}


function getBestSellerProduct(){
    $sql = "
        SELECT p.ProductID AS id, p.ProductName AS name, p.Price AS price, p.ImageURL AS image, 
               c.CategoryName AS category, b.BrandName AS brand, p.Gender AS sex,
               GROUP_CONCAT(ps.Size) AS size
        FROM Product p
        LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
        LEFT JOIN Brand b ON p.BrandID = b.BrandID
        LEFT JOIN ProductSize ps ON p.ProductID = ps.ProductID
        JOIN orderdetail od ON ps.ProductSizeID = od.ProductSizeID
        JOIN orders o ON od.OrderID = o.OrderID
        WHERE o.Status = 'Processed'
        ORDER BY SUM(od.Quantity) DESC
        LIMIT 10;
    ";

    $products = getAll($sql);

    // Chuyển kích thước thành mảng và thêm thuộc tính isDeleted
    foreach ($products as &$product) {
        $product['size'] = !empty($product['size']) ? explode(',', $product['size']) : [];
        $product['isDeleted'] = false;
    }

    return $products;
}
function getBrandProductByID($BrandID){
    $sql = "
        SELECT p.ProductID AS id, p.ProductName AS name, p.Price AS price, p.ImageURL AS image, 
               c.CategoryName AS category, b.BrandName AS brand, p.Gender AS sex,
               GROUP_CONCAT(ps.Size) AS size
        FROM Product p
        LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
        LEFT JOIN Brand b ON p.BrandID = b.BrandID
        LEFT JOIN ProductSize ps ON p.ProductID = ps.ProductID
        WHERE p.BrandID = $BrandID
        GROUP BY p.ProductID;
    ";

    $products = getAll($sql);

    // Chuyển kích thước thành mảng và thêm thuộc tính isDeleted
    foreach ($products as &$product) {
        $product['size'] = !empty($product['size']) ? explode(',', $product['size']) : [];
        $product['isDeleted'] = false;
    }

    return $products;
}

function getProductsByCatalogID($catalogID){
    $sql = "
        SELECT p.ProductID AS id, p.ProductName AS name, p.Price AS price, p.ImageURL AS image, 
               c.CategoryName AS category, b.BrandName AS brand, p.Gender AS sex,
               GROUP_CONCAT(ps.Size) AS size
        FROM Product p
        LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
        LEFT JOIN Brand b ON p.BrandID = b.BrandID
        LEFT JOIN ProductSize ps ON p.ProductID = ps.ProductID
        wHERE p.CategoryID = $catalogID
        GROUP BY p.ProductID;
    ";

    $products = getAll($sql);

    // Chuyển kích thước thành mảng và thêm thuộc tính isDeleted
    foreach ($products as &$product) {
        $product['size'] = !empty($product['size']) ? explode(',', $product['size']) : [];
        $product['isDeleted'] = false;
    }

    return $products;
}

function getProvineDistrictWard($sql){
    $conn = connectdb();   
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getName($sql){
    $conn = connectdb();   
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['name'];
}

/*************************** PRODUCT END ***************************/

$action = $_GET['action'] ?? '';
$pdo = connectdb(); // Kết nối đến cơ sở dữ liệu
switch ($action) {
    case 'get_products':
        echo json_encode(getProducts($pdo));
        break;
    case 'get_accounts':
        echo json_encode(getAccounts($pdo));
        break;
    case 'get_orders':
        echo json_encode(getOrders($pdo));
        break;
    case 'get_categories':
        echo json_encode(getCategories($pdo));
        break;
    case 'get_brands':
        echo json_encode(getBrands($pdo));
        break;
    case 'get_product_sizes':
        echo json_encode(getProductSizes($pdo));
        break;
    case 'get_carts':
        echo json_encode(getCarts($pdo));
        break;
    case 'get_employees':
        echo json_encode(getEmployees($pdo));
        break;
    case 'get_suppliers':
        echo json_encode(getSuppliers($pdo));
        break;
    default:
        // echo json_encode(['error' => 'Invalid action']);
        break;
}

function filterProduct($pdo,$condition){
    $stmt = $pdo->prepare('
     SELECT p.ProductID AS id, p.ProductName AS name, p.Price AS price, p.ImageURL AS image, 
        c.CategoryName AS category, b.BrandName AS brand, p.Gender AS sex,
        GROUP_CONCAT(ps.Size) AS size, 
        SUM(ps.StockQuantity) AS totalStock
        FROM Product p
        LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
        LEFT JOIN Brand b ON p.BrandID = b.BrandID
        LEFT JOIN ProductSize ps ON p.ProductID = ps.ProductID
        GROUP BY p.ProductID
        having 1 ' . $condition );
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as &$product) {
        // Chuyển kích thước thành mảng
        $product['size'] = !empty($product['size']) ? explode(',', $product['size']) : [];
        // Thêm thuộc tính isDeleted mặc định
        $product['isDeleted'] = false;
    
        // Xác định trạng thái tồn kho dựa trên tổng số lượng tồn kho
        if ($product['totalStock'] == 0) {
            $product['stockStatus'] = 'Out of Stock';
        } elseif ($product['totalStock'] <= 5) {
            $product['stockStatus'] = 'Low Stock';
        } else {
            $product['stockStatus'] = 'In Stock';
        }
    }

    return $products ;
}

$data_post = json_decode(file_get_contents("php://input"), true);
if ( isset($data_post['action'])){
    $pdo = connectdb();
    switch ($data_post['action']) {
        case 'filter_product' : 
            echo json_encode(filterProduct($pdo,$data_post['filter']));
    }
}

