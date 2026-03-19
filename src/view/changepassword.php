<script>
function changePassword(event) {
    event.preventDefault();
    const form = document.getElementById('changepass-form');
    const currPass = document.getElementById('password-cur-info').value;
    const newPass = document.getElementById('password-after-info').value;
    const confirmPass = document.getElementById('password-confirm-info').value;
    const errorMsg = document.querySelectorAll('.form-msg-error');

    // Reset error messages
    errorMsg.forEach(msg => msg.innerHTML = '');

    // Client-side validation
    if (!currPass) {
        errorMsg[0].innerHTML = 'Current password is required.';
        return;
    }
    if (!newPass) {
        errorMsg[1].innerHTML = 'New password is required.';
        return;
    }
    if (newPass.length < 8 || !/[A-Z]/.test(newPass) || !/[a-z]/.test(newPass) || !/[0-9]/.test(newPass) || !/[^A-Za-z0-9]/.test(newPass)) {
        errorMsg[1].innerHTML = 'New password must be at least 8 characters and include uppercase, lowercase, number, and special character.';
        return;
    }
    if (!confirmPass) {
        errorMsg[2].innerHTML = 'Confirm new password is required.';
        return;
    }
    if (newPass !== confirmPass) {
        errorMsg[2].innerHTML = 'Passwords do not match.';
        return;
    }

    // Send data to server
    const formData = new FormData(form);
    fetch('index.php?pg=changepass', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Password changed successfully!');
            form.reset();
        } else {
            errorMsg[0].innerHTML = data.error || 'An error occurred.';
        }
    })
    .catch(error => {
        errorMsg[0].innerHTML = 'Failed to connect to server.';
        console.error(error);
    });
}

// Attach event listener to form
document.getElementById('changepass-form').addEventListener('submit', changePassword);
</script>

<?php
    // session_start();
    // Ensure user is authenticated
    if (!isset($_SESSION['currentuser'])) {
        header('Location: index.php?pg=login');
        exit;
    }

    // Generate CSRF token
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    echo '
        <div class="main-login">
        <div class="main-account-body-col" id="user-info-changepass">
        <div>
            <h4>CHANGE YOUR PASSWORD</h4>
        </div>
        <div class="main-login-body">
        <form action="index.php?pg=changepass" method="post" class="change-password login-form" id="changepass-form">
            <input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token']) . '">
            <div class="form-group">
                <label for="curr-pass" class="form-label">Current password</label>
                <input class="form-input-bar" type="password" name="curr-pass" id="password-cur-info" placeholder="Enter current password*" required>
                <p class="form-msg-error"></p>
            </div>
            <div class="form-group">
                <label for="new-pass" class="form-label">New password</label>
                <input class="form-input-bar" type="password" name="new-pass" id="password-after-info" placeholder="Enter new password*" required>
                <p class="form-msg-error"></p>
            </div>
            <div class="form-group">
                <label for="confirm-new-pass" class="form-label">Confirm new password</label>
                <input class="form-input-bar" type="password" name="confirm-new-pass" id="password-confirm-info" placeholder="Confirm new password*" required>
                <p class="form-msg-error"></p>
            </div>
            <div class="form-group">
                <label class="myAccountBtnLabel">
                    <i class="fa-solid fa-key"></i>
                    <input type="submit" name="changePassBtn" id="save-password" value="Change password" class="myAccountBtn">
                </label>
            </div>
        </form></div>
        <a href="index.php?pg=myaccount"><i class="fa-solid fa-user"></i><span>Update user info</span></a>
    </div></div>';
?>

