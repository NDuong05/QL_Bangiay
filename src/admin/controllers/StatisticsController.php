<?php
require_once 'models/Order.php';
require_once 'models/User.php';
require_once 'models/Product.php';
require_once 'db_connection.php';
require_once 'models/Permission.php';
// $permissionModel = new Permission($pdo);
// $userRoleId = $_SESSION['user']['RoleID'] ?? null;
// $permissions = $permissionModel->getPermissionsByRole($userRoleId);
class StatisticsController {
    private $pdo;
    private $orderModel;
    private $userModel;
    private $productModel;
    private $permissionModel;
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->orderModel = new Order($pdo);
        $this->userModel = new User();
        // Giả định rằng bạn có một Product model, nếu không hãy điều chỉnh
        $this->productModel = new Product($pdo);
        $this->permissionModel = new Permission($pdo);
    }
    
    public function orderDetails($id) {
        $userRoleId = $_SESSION['user']['RoleID'] ?? null;
        $permissions = $this->permissionModel->getPermissionsByRole($userRoleId);
        $hasStatisticViewPermission = in_array('statistic_view', $permissions);
        if(!$hasStatisticViewPermission) {
            // echo "Bạn không có quyền xem thống kê.";
            header('Location: admin.php?page=statistics&action=index');
            exit;
        }
        $orderID = (int)$id;
        if ($orderID <= 0) {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">ID đơn hàng không hợp lệ.</div>';
            return;
        }
    
        // Gán biến $pdo để view có thể dùng được
        $pdo = $this->pdo;
    
        include 'views/statistics/order_details.php';
    }
    

    public function index() {
        $userRoleId = $_SESSION['user']['RoleID'] ?? null;
        $permissions = $this->permissionModel->getPermissionsByRole($userRoleId);
        $hasStatisticViewPermission = in_array('statistic_view', $permissions);
        $orders = $this->orderModel->getAll();
        $products = $this->productModel->getAll();
        $accounts = $this->userModel->getAll();
        
        // Tính tổng thu nhập
        $totalIncome = $this->orderModel->getTotalIncome();
        
        // Khởi tạo biến cho dữ liệu top khách hàng
        $topCustomers = [];
        $startDate = '';
        $endDate = '';
        
        // Xử lý khi form được submit
        if (isset($_POST['filter_submit'])) {
            $startDate = $_POST['start_date'] ?? '';
            $endDate = $_POST['end_date'] ?? '';
            
            // Lấy top 5 khách hàng có mức mua hàng cao nhất trong khoảng thời gian
            $topCustomers = $this->userModel->getTopCustomersByPurchase($startDate, $endDate, 5);
            
            // Cho mỗi khách hàng, lấy danh sách đơn hàng của họ trong khoảng thời gian
            foreach ($topCustomers as &$customer) {
                $customer['orders'] = $this->orderModel->getOrdersByUserAndDateRange(
                    $customer['UserID'], 
                    $startDate, 
                    $endDate
                );
            }
        }
        
        // Gọi view
        include 'views/statistics/index.php';
    }
}

// Khởi tạo và chạy controller nếu file này được gọi trực tiếp
if (basename($_SERVER['PHP_SELF']) == 'statistics.php') {
    $controller = new StatisticsController();
    $controller->index();
}