<?php
// session_start();
include_once("./controller/db_controller/api.php");

$toastMessage = "";

if (isset($_POST['btnlogin'])) {
    $user = trim($_POST['user']);
    $password = $_POST['password'];
    $sql = "SELECT UserID, PasswordHash, isActivate FROM user WHERE Username = '$user'";
    $userData = getOne($sql);

    if ($userData) {
        if (!password_verify($password, $userData['PasswordHash'])) {
            $toastMessage = json_encode([
                "title" => "Error",
                "message" => "Wrong username or password!",
                "type" => "error"
            ]);
        } elseif ($userData['isActivate'] == 0) {
            $toastMessage = json_encode([
                "title" => "Account Locked",
                "message" => "Your account has been deactivated.",
                "type" => "error"
            ]);
        } else {
            $_SESSION['user'] = [
                'userID' => $userData['UserID'],
                'Username' => $user
            ];

            // Cập nhật số lượng sản phẩm trong giỏ
            $userID = $userData['UserID'];
            $cartQtySql = "SELECT SUM(Quantity) as TotalQty FROM cart WHERE UserID = '$userID'";
            $cartResult = getOne($cartQtySql);
            $_SESSION['cartQty'] = $cartResult['TotalQty'] ?? 0;

            $toastMessage = json_encode([
                "title" => "Success",
                "message" => "Login successful!",
                "type" => "success",
                "redirect" => "index.php?pg=home"
            ]);
        }
    } else {
        $toastMessage = json_encode([
            "title" => "Error",
            "message" => "Wrong username or password!",
            "type" => "error"
        ]);
    }
}
?>

<!-- UI -->
<div class="container toast" id="toast"></div>

<div class="main-login">
    <div class="main-login-header">
        <h2>LOGIN</h2>
    </div>
    <div class="main-login-body">
        <form class="login-form" method="post">
            <input class="form-input-bar" type="text" name="user" placeholder="Username*" required>
            <p class="form-msg-error"></p>

            <input class="form-input-bar" type="password" name="password" placeholder="Password*" required>
            <p class="form-msg-error"></p>

            <button type="submit" name="btnlogin">LOGIN</button>
        </form>
    </div>
    <div class="main-login-footer">
        <p>DON'T HAVE AN ACCOUNT? <a href="index.php?pg=register">SIGN UP</a></p>
    </div>
</div>

<script>
    window.onload = function () {
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
                }, 1000);
            }
        }
    };
</script>
