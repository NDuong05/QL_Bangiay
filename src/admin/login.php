<?php
session_start();
require_once 'db_connection.php';
// echo password_hash('123456', PASSWORD_DEFAULT);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM employee WHERE Username = ? AND isActivate = 1");
    $stmt->execute([$username]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    // if ($employee && password_verify($password, $employee['PasswordHash']) && $employee['RoleID'] == 1) {
    //     $_SESSION['user'] = $employee;
    //     header('Location: admin.php');
    //     exit;
    // } else {
    //     echo "Đăng nhập thất bại. Vui lòng kiểm tra lại thông tin.";
    // }

    if ($employee && password_verify($password, $employee['PasswordHash'])) {
        $_SESSION['user'] = $employee;
        header('Location: admin.php');
        exit;
    } else {
        echo "Đăng nhập thất bại. Vui lòng kiểm tra lại thông tin.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Account</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f7ff;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            background-color: white;
        }
        
        .left-side {
            background: linear-gradient(135deg, #0d6efd, #0099ff);
            color: white;
            height: 100%;
            position: relative;
            padding: 0;
            overflow: hidden;
        }
        
        .background-grid {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            z-index: 1;
        }
        
        .wave-top, .wave-bottom {
            position: absolute;
            left: 0;
            width: 100%;
            height: 25%;
            z-index: 1;
        }
        
        .wave-top {
            top: 0;
            background: linear-gradient(to bottom, rgba(79, 172, 254, 0.8), transparent);
            border-radius: 0 0 50% 50% / 0 0 100% 100%;
        }
        
        .wave-bottom {
            bottom: 0;
            background: linear-gradient(to top, rgba(79, 172, 254, 0.8), transparent);
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
        }
        
        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            z-index: 2;
        }
        
        .circle-1 {
            width: 60px;
            height: 60px;
            top: 15%;
            right: 15%;
        }
        
        .circle-2 {
            width: 40px;
            height: 40px;
            top: 50%;
            right: 25%;
        }
        
        .circle-3 {
            width: 50px;
            height: 50px;
            bottom: 20%;
            left: 15%;
        }
        
        .circle-4 {
            width: 30px;
            height: 30px;
            top: 30%;
            left: 10%;
        }
        
        .company-name {
            position: absolute;
            top: 25px;
            left: 25px;
            z-index: 3;
            font-size: 14px;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
        }
        
        .company-name:before {
            content: "";
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid white;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        .welcome-content {
            position: relative;
            z-index: 3;
            text-align: center;
            padding: 40px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .welcome-content p {
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .welcome-content h1 {
            font-size: 44px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 0 0 15px 0;
        }
        
        .welcome-content hr {
            width: 80px;
            margin: 0 auto 20px;
            border-top: 3px solid white;
            opacity: 1;
        }
        
        .welcome-content .subtitle {
            font-size: 13px;
            opacity: 0.9;
            line-height: 1.6;
            max-width: 350px;
            margin: 0 auto;
        }
        
        .right-side {
            background: white;
            padding: 50px 40px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-form {
            max-width: 350px;
            margin: 0 auto;
        }
        
        .login-form h2 {
            color: #0d6efd;
            font-size: 28px;
            margin-bottom: 15px;
            font-weight: 500;
        }
        
        .login-form .description {
            color: #aaa;
            font-size: 12px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .form-control {
            border: none;
            background-color: #f5f5f5;
            border-radius: 4px;
            padding: 12px 15px;
            font-size: 14px;
            margin-bottom: 15px;
            height: auto;
        }
        
        .form-control::placeholder {
            color: #aaa;
        }
        
        .btn-subscribe {
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 12px;
            font-size: 15px;
            font-weight: 500;
            letter-spacing: 1px;
            width: 100%;
            margin-top: 20px;
        }
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 15px 0;
        }
        
        .form-check-input {
            background-color: #f5f5f5;
            border: none;
        }
        
        .form-check-label {
            color: #888;
            font-size: 13px;
            margin-left: 5px;
        }
        
        .already-member {
            color: #0d6efd;
            text-decoration: none;
            font-size: 13px;
        }
        
        .freepik-credit {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }
        
        .freepik-credit img {
            vertical-align: middle;
            margin: 0 3px;
        }
        
        /* Make both columns equal height */
        .row {
            height: 600px;
        }
        
        @media (max-width: 767.98px) {
            .row {
                height: auto;
            }
            
            .left-side, .right-side {
                padding: 40px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row login-container g-0">
            <!-- Left side with welcome message -->
            <div class="col-md-6 left-side">
                <div class="background-grid"></div>
                <div class="wave-top"></div>
                <div class="wave-bottom"></div>
                
                <div class="circle circle-1"></div>
                <div class="circle circle-2"></div>
                <div class="circle circle-3"></div>
                <div class="circle circle-4"></div>
                
                <div class="company-name">BRO SHOES</div>

                
                <div class="welcome-content">
                    <h1>WELCOME BACK</h1>
                    <hr>
                    <p class="subtitle">Chức năng quản trị của shop bán giày BRO SHOES</p>

                </div>
            </div>
            
            <!-- Right side with login form -->
            <div class="col-md-6 right-side">
                <div class="login-form">
                    <h2>ĐĂNG NHẬP </h2>
                    <p class="description">Vui lòng đăng nhập bằng tài khoản được cung cấp. Mọi hành vi của bạn đều được hệ thống ghi nhận, hãy thận trọng khi thao tác và chịu trách nhiệm về hành động của mình!
                        Nghiêm cấm dùng tài khoản của người khác!
                    </p>
                    
                    <form method="POST" action="login.php">
                    <label>Tên đăng nhập</label><br>
                    <input type="text" class="form-control" name="username" required>
                    <label>Mật khẩu</label><br>
                    <input type="password" class="form-control" name="password" required>
                        
                        <div class="form-options">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="keepSigned">
                                <!-- <label class="form-check-label" for="keepSigned">
                                    Keep me signed in
                                </label> -->
                            </div>
                            
                        </div>
                        
                        <button type="submit" class="btn btn-subscribe">Đăng nhập</button>
                    </form>
                </div>
            </div>
        </div>
        
       
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

