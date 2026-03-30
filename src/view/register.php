<?php
// session_start();
require_once("./controller/db_controller/db_connect.php"); 

$toastMessage = ""; 

if (isset($_POST['btnRegister'])) {
    $username = trim($_POST['username-signup']);
    $fullname = trim($_POST['fullname-signup']);
    $email    = trim($_POST['email-signup']);
    $phone    = trim($_POST['phone-signup']);
    $address  = trim($_POST['address-signup']);
    $provinceID = $_POST['province'] ?? null;
    $districtID = $_POST['district'] ?? null;
    $wardID     = $_POST['ward'] ?? null;
    $password   = trim($_POST['password-signup']);
    $confirmPassword = trim($_POST['confirm-password-signup']);

    // Regex kiểm tra dữ liệu
    $usernamePattern = "/^[a-zA-Z0-9]{5,}$/"; 
    $fullnamePattern = "/^[a-zA-ZÀ-Ỹà-ỹ\s]+$/"; 
    $phonePattern    = "/^(0[1-9][0-9]{8,9})$/"; 
    $passwordPattern = "/^(?=.*[A-Za-z])(?=.*\d).{5,}$/";

    if (!preg_match($usernamePattern, $username)) {
        $toastMessage = json_encode(["title" => "Error", "message" => "Username must be at least 5 characters.", "type" => "error"]);
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $toastMessage = json_encode(["title" => "Error", "message" => "Invalid email format.", "type" => "error"]);
    } elseif (!preg_match($phonePattern, $phone)) {
        $toastMessage = json_encode(["title" => "Error", "message" => "Invalid phone number.", "type" => "error"]);
    } elseif (!preg_match($passwordPattern, $password)) {
        $toastMessage = json_encode(["title" => "Error", "message" => "Password must have letters and numbers.", "type" => "error"]);
    } elseif ($password !== $confirmPassword) {
        $toastMessage = json_encode(["title" => "Error", "message" => "Passwords do not match.", "type" => "error"]);
    } else {
        $pdo = connectdb();
        // Kiểm tra trùng username, phone hoặc email
        $sqlCheck = "SELECT * FROM user WHERE Username = :username OR PhoneNumber = :phone OR Email = :email";
        $stmt = $pdo->prepare($sqlCheck);
        $stmt->execute(['username' => $username, 'phone' => $phone, 'email' => $email]);
        
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $toastMessage = json_encode(["title" => "Error", "message" => "Account or Email already exists!", "type" => "error"]);
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sqlInsert = "INSERT INTO user (Username, Fullname, PhoneNumber, Email, Address, ProvinceID, DistrictID, WardID, PasswordHash, CreatedAt, isActivate) 
            VALUES (:username, :fullname, :phone, :email, :address, :provinceID, :districtID, :wardID, :password, NOW(), 1)";

            $stmt = $pdo->prepare($sqlInsert);
            $inserted = $stmt->execute([
                'username' => $username,
                'fullname' => $fullname,
                'phone'    => $phone,
                'email'    => $email,
                'address'  => $address,
                'provinceID' => $provinceID,
                'districtID' => $districtID,
                'wardID'     => $wardID,
                'password'   => $hashedPassword
            ]);

            if ($inserted) {
                $_SESSION['user'] = [
                    "Username" => $username,
                    "Fullname" => $fullname,
                    "userID" => $pdo->lastInsertId()
                ];
                $toastMessage = json_encode(["title" => "Success", "message" => "Account created!", "type" => "success", "redirect" => "index.php?pg=home"]);
            } else {
                $toastMessage = json_encode(["title" => "Error", "message" => "Database error!", "type" => "error"]);
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
            <input class="form-input-bar" type="text" name="username-signup" placeholder="Username*" required>
            <input class="form-input-bar" type="text" name="fullname-signup" placeholder="Full Name*" required>
            <input class="form-input-bar" type="email" name="email-signup" placeholder="Email*" required>
            <input class="form-input-bar" type="number" name="phone-signup" placeholder="Phone number*" required>
            <input class="form-input-bar" type="text" name="address-signup" placeholder="Address*" required>

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

            <input class="form-input-bar" type="password" name="password-signup" placeholder="Password*" required>
            <input class="form-input-bar" type="password" name="confirm-password-signup" placeholder="Confirm Password*" required>

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
            if (typeof toastMsg === 'function') {
                toastMsg({
                    title: toastData.title,
                    message: toastData.message,
                    type: toastData.type,
                    duration: 3000
                });
            } else {
                alert(toastData.message);
            }

            if (toastData.redirect) {
                setTimeout(() => { window.location.href = toastData.redirect; }, 2000);
            }
        }
    };
</script>