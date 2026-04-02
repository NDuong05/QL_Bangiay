<?php
require_once("./controller/db_controller/db_connect.php");

$toastMessage = "";
$errorField = ""; // Biến mới để lưu ID của trường bị lỗi
$errorMessage = ""; // Biến mới để lưu nội dung lỗi cụ thể

if (isset($_POST['btnRegister'])) {
    // Lấy dữ liệu và giữ lại để đổ vào value của input
    $username = trim($_POST['username-signup']);
    $fullname = trim($_POST['fullname-signup']);
    $phone = trim($_POST['phone-signup']);
    $address = trim($_POST['address-signup']);
    $provinceID = $_POST['province'] ?? "";
    $districtID = $_POST['district'] ?? "";
    $wardID = $_POST['ward'] ?? "";
    $password = trim($_POST['password-signup']);
    $confirmPassword = trim($_POST['confirm-password-signup']);

    $usernamePattern = "/^[a-zA-Z0-9]{5,}$/";
    $fullnamePattern = "/^[a-zA-ZÀ-Ỹà-ỹ\s]+$/";
    $phonePattern = "/^(0[1-9][0-9]{8,9})$/";
    $passwordPattern = "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{5,}$/";

    // Kiểm tra lỗi và gán ID trường tương ứng
    if (!preg_match($usernamePattern, $username)) {
        $errorField = "username-signup";
        $errorMessage = "Username must be at least 5 characters, no spaces.";
    } elseif (!preg_match($fullnamePattern, $fullname)) {
        $errorField = "fullname-signup";
        $errorMessage = "Full name must contain only letters and spaces.";
    } elseif (!preg_match($phonePattern, $phone)) {
        $errorField = "phone-signup";
        $errorMessage = "Invalid phone number format.";
    } elseif (!preg_match($passwordPattern, $password)) {
        $errorField = "password-signup";
        $errorMessage = "Password must be at least 5 characters, include 1 letter and 1 number.";
    } elseif ($password !== $confirmPassword) {
        $errorField = "confirm-password-signup";
        $errorMessage = "Passwords do not match.";
    } else {
        $pdo = connectdb();
        // Kiểm tra trùng username, phone hoặc email
        $sqlCheck = "SELECT * FROM user WHERE Username = :username OR PhoneNumber = :phone OR Email = :email";
        $stmt = $pdo->prepare($sqlCheck);
        $stmt->execute(['username' => $username, 'phone' => $phone]);
        $userExists = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userExists) {
            $errorField = "username-signup"; // Hoặc phone-signup tùy logic bạn muốn
            $errorMessage = "Username or phone number already exists!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sqlInsert = "INSERT INTO user (Username, Fullname, PhoneNumber, Email, Address, ProvinceID, DistrictID, WardID, PasswordHash, CreatedAt, IsActivate) 
                          VALUES (:username, :fullname, :phone, NULL, :address, :provinceID, :districtID, :wardID, :password, NOW(), 1)";
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

                // Sau đó mới gọi Toast và chuyển hướng
                $toastMessage = json_encode([
                    "title" => "Success",
                    "message" => "Account created successfully!",
                    "type" => "success",
                    "redirect" => "index.php?pg=home"
                ]);
            }
        }
    }

    // Nếu có lỗi, tạo JSON cho Toast
    if ($errorField !== "") {
        $toastMessage = json_encode(["title" => "Error", "message" => $errorMessage, "type" => "error"]);
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
            <input class="form-input-bar" type="text" id="username-signup" name="username-signup"
                placeholder="Username*" value="<?= htmlspecialchars($username ?? '') ?>" required>
            <p class="form-msg-error"></p>

            <input class="form-input-bar" type="text" id="fullname-signup" name="fullname-signup"
                placeholder="Full Name*" value="<?= htmlspecialchars($fullname ?? '') ?>" required>
            <p class="form-msg-error"></p>

            <input class="form-input-bar" type="number" id="phone-signup" name="phone-signup"
                placeholder="Phone number*" value="<?= htmlspecialchars($phone ?? '') ?>" required>
            <p class="form-msg-error"></p>

            <input class="form-input-bar" type="text" id="address-signup" name="address-signup"
                placeholder="Address*" value="<?= htmlspecialchars($address ?? '') ?>" required>
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

            <input class="form-input-bar" type="password" name="password-signup" placeholder="Password*" required>
            <input class="form-input-bar" type="password" name="confirm-password-signup" placeholder="Confirm Password*" required>

            <button type="submit" name="btnRegister">SIGN UP</button>
        </form>
    </div>
</div>

<script>
    window.onload = function() {
        let toastData = <?php echo $toastMessage ?: "null"; ?>;
        let errorFieldId = "<?php echo $errorField; ?>";
        let errorMessage = "<?php echo $errorMessage; ?>";

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

            // Nếu có lỗi cụ thể ở một trường
            if (errorFieldId) {
                let inputElement = document.getElementById(errorFieldId);
                if (inputElement) {
                    inputElement.focus();
                    inputElement.style.borderColor = "red"; // Làm nổi bật ô lỗi

                    // Hiển thị text lỗi dưới input 
                    let errorMsgElement = inputElement.nextElementSibling;
                    if (errorMsgElement && errorMsgElement.classList.contains('form-msg-error')) {
                        errorMsgElement.innerText = errorMessage;
                        errorMsgElement.style.color = "red";
                    }
                }
            }

            if (toastData.redirect) {
                setTimeout(() => { window.location.href = toastData.redirect; }, 2000);
            }
        }
    };
</script>