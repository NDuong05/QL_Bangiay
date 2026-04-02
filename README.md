QL_Bangiay/

├── README.md                 
├── src/                         # Thư mục chính chứa code ứng dụng
│   ├── index.php                # Điểm vào chính của ứng dụng
│   └── admin/                   # Phần quản trị 
│       ├── admin.php            # Trang chính admin
│       ├── api.php              # API endpoints cho admin
│       ├── db_connection.php    # Kết nối database cho admin
│       ├── index.php            # Điểm vào admin
│       ├── login.php            # Đăng nhập admin
│       ├── logout.php           # Đăng xuất admin
│       ├── controllers/         # Controllers (logic xử lý)
│       │   ├── AccountsController.php
│       │   ├── CartController.php
│       │   ├── DecentralizationController.php
│       │   ├── EmployeesController.php
│       │   ├── ImportsController.php
│       │   ├── OrdersController.php
│       │   ├── ProductsController.php
│       │   ├── ReportsController.php
│       │   ├── StatisticsController.php
│       │   ├── SupplierController.php
│       │   └── UsersController.php
│       ├── css/                 # Stylesheets cho admin
│       │   ├── admin.css
│       │   ├── admin-responsive.css
│       │   ├── toast-msg.css
│       │   └── img/             # Hình ảnh cho CSS
│       ├── js/                  # JavaScript cho admin
│       │   ├── admin.js
│       │   ├── initialization.js
│       │   ├── main.js
│       │   └── toast-msg.js
│       ├── models/              
│       │   ├── Account.php
│       │   ├── Cart.php
│       │   ├── Employee.php
│       │   ├── Import.php
│       │   ├── Order.php
│       │   ├── Permission.php
│       │   ├── Product.php
│       │   ├── Supplier.php
│       │   └── User.php
│       └── views/               # Views (giao diện)
│           ├── accounts/        # Views cho tài khoản
│           │   ├── detail.php
│           │   ├── list.php
│           │   └── update.php
│           ├── cart/            # Views cho giỏ hàng
│           ├── employees/       # Views cho nhân viên
│           ├── imports/         # Views cho nhập hàng
│           ├── orders/          # Views cho đơn hàng
│           ├── permission/      # Views cho phân quyền
│           ├── products/        # Views cho sản phẩm
│           ├── reports/         # Views cho báo cáo
│           ├── statistics/      # Views cho thống kê
│           ├── suppliers/       # Views cho nhà cung cấp
│           └── users/           # Views cho người dùng
├── controller/                  # Controllers cho phần khách hàng
│   ├── controller.php           # Controller chính
│   └── db_controller/           # Controllers xử lý database
│       ├── admin_api.php
│       ├── api.php
│       ├── bangiay_db2.sql      # Script SQL database
│       ├── cancel_order.php
│       ├── cart.php
│       ├── checkout.php
│       ├── confirm_order.php
│       ├── db_connect.php       # Kết nối database
│       ├── getDistrict.php
│       ├── getProvince.php
│       ├── getWard.php
│       └── xulySearchProduct.php
└── view/                        # Views cho phần khách hàng
    ├── changepassword.php
    ├── footer.php
    ├── header.php
    ├── home.php
    ├── login.php
    ├── logout.php
    ├── myaccount.php
    ├── myorder.php
    ├── product.php
    ├── productdetail.php
    ├── register.php
    ├── testproduct.php
    ├── customer/                # Assets cho khách hàng
    │   └── assets/
    │       └── css/
    ├── layout/                  # Layout và assets chung
    │   ├── asset/
    │   │   ├── css/
    │   │   └── img/
    │   └── js/
    │       ├── admin.js
    │       ├── app.js
    │       ├── checkout.js
    │       ├── initialization.js
    │       ├── main.js
    │       ├── products.js
    │       ├── slick_slide.js
    │       └── toast-msg.js
    └── layout copy/             # backup

