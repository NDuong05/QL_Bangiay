<?php
require_once("./controller/db_controller/db_connect.php"); // Đảm bảo đã import file kết nối DB

$toastMessage = ""; // Biến lưu thông báo toast

if (isset($_POST['btnRegister'])) {
    $username = trim($_POST['username-signup']);
    $fullname = trim($_POST['fullname-signup']);
    $phone = trim($_POST['phone-signup']);
    $address = trim($_POST['address-signup']);
    $provinceID = $_POST['province'];
    $districtID = $_POST['district'];
    $wardID = $_POST['ward'];
    $password = trim($_POST['password-signup']);
    $confirmPassword = trim($_POST['confirm-password-signup']);

    // Regex kiểm tra dữ liệu nhập vào
    $usernamePattern = "/^[a-zA-Z0-9]{5,}$/"; 
    $fullnamePattern = "/^[a-zA-ZÀ-Ỹà-ỹ\s]+$/"; 
    $phonePattern = "/^(0[1-9][0-9]{8,9})$/"; 
    $passwordPattern = "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{5,}$/"; 

    // Kiểm tra lỗi
    if (!preg_match($usernamePattern, $username)) {
        $toastMessage = json_encode(["title" => "Error", "message" => "Username must be at least 5 characters, no spaces, no special symbols.", "type" => "error"]);
    } elseif (!preg_match($fullnamePattern, $fullname)) {
        $toastMessage = json_encode(["title" => "Error", "message" => "Full name must contain only letters and spaces.", "type" => "error"]);
    } elseif (!preg_match($phonePattern, $phone)) {
        $toastMessage = json_encode(["title" => "Error", "message" => "Invalid phone number format.", "type" => "error"]);
    } elseif (!preg_match($passwordPattern, $password)) {
        $toastMessage = json_encode(["title" => "Error", "message" => "Password must be at least 5 characters, include at least 1 letter and 1 number.", "type" => "error"]);
    } elseif ($password !== $confirmPassword) {
        $toastMessage = json_encode(["title" => "Error", "message" => "Passwords do not match.", "type" => "error"]);
    } else {
        // Kiểm tra trùng username hoặc phone bằng prepared statement
        $pdo = connectdb();
        $sqlCheck = "SELECT * FROM user WHERE Username = :username OR PhoneNumber = :phone";
        $stmt = $pdo->prepare($sqlCheck);
        $stmt->execute(['username' => $username, 'phone' => $phone]);
        $userExists = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userExists) {
            $toastMessage = json_encode(["title" => "Error", "message" => "Username or phone number already exists!", "type" => "error"]);
        } else {
            // Hash mật khẩu
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Lưu vào database
            $sqlInsert = "INSERT INTO user (Username, Fullname, PhoneNumber, Email, Address, ProvinceID, DistrictID, WardID, PasswordHash, CreatedAt, IsActivate) 
                          VALUES (:username, :fullname, :phone, NULL, :address, :provinceID, :districtID, :wardID, :password, NOW(), 1)";
            $stmt = $pdo->prepare($sqlInsert);
            $inserted = $stmt->execute([
                'username' => $username,
                'fullname' => $fullname,
                'phone' => $phone,
                'address' => $address,
                'provinceID' => $provinceID,
                'districtID' => $districtID,
                'wardID' => $wardID,
                'password' => $hashedPassword
            ]);

            if ($inserted) {
                // Lưu thông tin vào session ngay sau khi đăng ký thành công
                $_SESSION['user'] = [
                    "Username" => $username,
                    "Fullname" => $fullname,
                    "PhoneNumber" => $phone,
                    "Address" => $address,
                    'provinceID' => $provinceID,
                    'districtID' => $districtID,
                    'wardID' => $wardID,
                    "userID" => $pdo->lastInsertId() // Lưu userID mới vào session
                ];
            
                $toastMessage = json_encode([
                    "title" => "Success",
                    "message" => "Account created successfully!",
                    "type" => "success",
                    "redirect" => "index.php?pg=home"
                ]);
            } else {
                $toastMessage = json_encode([
                    "title" => "Error", 
                    "message" => "Registration failed, please try again!", 
                    "type" => "error"
                ]);
            }            
        }
    }
}
?>

<div class="container toast" id="toast"></div>
<div class="main-login">
    <div class="main-login-header">
        <h2>SIGN UP</h2>
    </div>
    <div class="main-login-body">
        <form class="login-form" id="signup-form" method="post">
            <input class="form-input-bar" type="text" id="username-signup" name="username-signup" placeholder="Username*" required>
            <p class="form-msg-error"></p>

            <input class="form-input-bar" type="text" id="fullname-signup" name="fullname-signup" placeholder="Full Name*" required>
            <p class="form-msg-error"></p>

            <input class="form-input-bar" type="number" id="phone-signup" name="phone-signup" placeholder="Phone number*" required>
            <p class="form-msg-error"></p>

            <input class="form-input-bar" type="text" id="address-signup" name="address-signup" placeholder="Address*" required>
            <p class="form-msg-error"></p>

            <div class="region-selector sign-up-region">
                <select id="province" name="province" class="region-select" required>
                    <option value="" disabled selected hidden>Province/City</option>
                </select>
                <select id="district" name="district" class="region-select" required>
                    <option value="" disabled selected hidden>District</option>
                </select>

                <select id="ward" name="ward" class="region-select" required>
                    <option value="" disabled selected hidden>Ward/Commune</option>
                </select>
            </div>
            <p class="form-msg-error"></p>

            <input class="form-input-bar" type="password" id="password-signup" name="password-signup" placeholder="Password*" required>
            <p class="form-msg-error"></p>

            <input class="form-input-bar" type="password" id="confirm-password-signup" name="confirm-password-signup" placeholder="Confirm Password*" required>
            <p class="form-msg-error"></p>

            <button type="submit" name="btnRegister">SIGN UP</button>
        </form>
    </div>
    <div class="main-login-footer">
        <p>ALREADY HAVE AN ACCOUNT? <span><a href="index.php?pg=login">LOGIN</a></span></p>
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

            if (toastData.redirect) {
                setTimeout(() => {
                    window.location.href = toastData.redirect;
                }, 2000);
            }
        }
    };
</script>
