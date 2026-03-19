<?php 
if (!isset($_GET['pg']) || $_GET['pg'] == "") {
    header("Location: index.php?pg=home");
    exit();
}


include_once("controller/db_controller/api.php");
$catalog_list = getCatalogList();
include_once("view/header.php");

if(isset($_GET['pg'])&&($_GET['pg'])!=""){

    switch ($_GET['pg']) {
            
        // case 'product' :
        //     // include product
        //     $catalogname = "";
        //     $productlist = getProduct();
        //     $brandList = getAll("SELECT * FROM brand");
        //     $sizeList = getAll("SELECT * FROM productsize");
        //     if ((isset($_GET['idcatalog']))&&($_GET['idcatalog']>0)) {     
        //         $catalogname = getCatalogname($_GET['idcatalog']);  
        //         $productlist = getProductsByCatalogID($_GET['idcatalog']);    
        //     }
        //     if ((isset($_GET['idBrand']))&&($_GET['idBrand']>0)) {   
        //         $productlist = getBrandProductByID($_GET['idBrand']);
        //     }
        //     include_once("view/product.php");
        //     break;

        

        case 'admin':
        
        // login registration logout
        case 'register':
            include_once("view/register.php");
            break;
            

        case 'login':
            include_once("view/login.php");
            break;


        case 'myaccount':
            $provinces = getProvineDistrictWard('SELECT * FROM province');
            $districts = getProvineDistrictWard('SELECT * FROM district');
            $wards = getProvineDistrictWard('SELECT * FROM wards');
            $currProvince = "Province";
            $currDistrict = "District";
            $currWard = "Ward";
            if(isset($_SESSION['user']) && ($_SESSION['user'] != "")){
                $username = $_SESSION['user']['Username'];
                $userid = $_SESSION['user']['userID'];
                $user = getobjectuserinfo($username, $userid);
                if ($user) {
                    $_SESSION['currentuser'] = $user;
                    $currProvince = getName('SELECT * FROM province WHERE province_id = '.$user['ProvinceID'].'');
                    $currDistrict = getName('SELECT * FROM district WHERE district_id = '.$user['DistrictID'].'');
                    $currWard = getName('SELECT * FROM wards WHERE wards_id = '.$user['WardID'].'');
                    
                }
            }
            
        include_once("view/myaccount.php");
        break;
        
        case 'changeaccount':
            if(isset($_POST['changeAccInfoBtn']) && ($_POST['changeAccInfoBtn'])){
                $fullname = $_POST['fullname'];
                $phonenumber = $_POST['infophone'];
                $email = $_POST['infoemail'];
                $address = $_POST['infoaddress'];
                $userID = $_SESSION['user']['userID'];
                $ProvinceID = $_POST['province'];
                $DistrictID = $_POST['district'];
                $WardID = $_POST['ward'];
                updateUser($userID, $fullname, $phonenumber, $email, $address , $ProvinceID ,$DistrictID, $WardID);
    
                echo "<script>window.location.href = 'index.php?pg=myaccount';</script>";
            }
            break;
    
        case 'changepassword':
            include_once("view/changepassword.php");
            break;
    
        case 'changepass':           
            if(isset($_POST['changePassBtn']) && ($_POST['changePassBtn'])){
                // header('Content-Type: application/json');
                // Verify CSRF token
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token.']);
                    exit;
                }

                // Check if user is authenticated
                if (!isset($_SESSION['currentuser'])) {
                    echo json_encode(['success' => false, 'error' => 'User not authenticated.']);
                    exit;
                }

                $currPass = $_POST['curr-pass'] ?? '';
                $newPass = $_POST['new-pass'] ?? '';
                $confirmPass = $_POST['confirm-new-pass'] ?? '';

                // Server-side validation
                if (empty($currPass)) {
                    echo json_encode(['success' => false, 'error' => 'Current password is required.']);
                    exit;
                }
                if (empty($newPass)) {
                    echo json_encode(['success' => false, 'error' => 'New password is required.']);
                    exit;
                }
                if (strlen($newPass) < 8 || !preg_match('/[A-Z]/', $newPass) || !preg_match('/[a-z]/', $newPass) || 
                    !preg_match('/[0-9]/', $newPass) || !preg_match('/[^A-Za-z0-9]/', $newPass)) {
                    echo json_encode(['success' => false, 'error' => 'New password must be at least 8 characters and include uppercase, lowercase, number, and special character.']);
                    exit;
                }
                if (empty($confirmPass)) {
                    echo json_encode(['success' => false, 'error' => 'Confirm new password is required.']);
                    exit;
                }
                if ($newPass !== $confirmPass) {
                    echo json_encode(['success' => false, 'error' => 'Passwords do not match.']);
                    exit;
                }

                // Verify current password (assuming database connection)
                $storedHash = $_SESSION['currentuser']['PasswordHash']; // Replace with DB query
                if (!password_verify($currPass, $storedHash)) {
                    echo json_encode(['success' => false, 'error' => 'Current password is incorrect.']);
                    exit;
                }

                // Hash new password and update database
                $newHash = password_hash($newPass, PASSWORD_DEFAULT);
                // Example: $db->query("UPDATE users SET PasswordHash = ? WHERE user_id = ?", [$newHash, $_SESSION['currentuser']['id']]);
                updatePassword($_SESSION['user']['userID'] , $newHash);
                // Update session
                $_SESSION['currentuser']['PasswordHash'] = $newHash;

                // Regenerate CSRF token
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                echo json_encode(['success' => true]);
                exit;
            }
            break;

        case 'myorder':
            $paymentdetail = getAll("SELECT * FROM paymentdetail");
            $orders = getOrdersByUserID();
            $provinces = getProvineDistrictWard('SELECT * FROM province');
            $districts = getProvineDistrictWard('SELECT * FROM district');
            $wards = getProvineDistrictWard('SELECT * FROM wards');
            include_once("view/myorder.php");
            break;


        default:
            //include home
            // $newproductlist = getNewProduct();
            $productlist = getProduct();
            // $productNewlist = getNewProduct();
            include_once("view/home.php");

            break;


    }
}else{
    include_once("view/home.php");
}


// include footer
include_once("view/footer.php");
?>