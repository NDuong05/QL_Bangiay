<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $cataloglist = "";
    foreach($catalog_list as $item){
        extract($item);
        $nameCategory = strtoupper($item['CategoryName']);
        $link = "index.php?pg=product&idcatalog=";
        $cataloglist .= '<li><a class="filter-category" data-filter="'.$item['CategoryID'].'" href="index.php?pg=product&idcatalog='.$item['CategoryID'].'">'.$nameCategory.'</a></li>';
    }

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRO SHOES</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Audiowide&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Sen:wght@400..800&display=swap');
    </style>
    <link rel="icon" href="view/layout/asset/img/logo.jpg" type="image/x-icon">
    <link rel="stylesheet" href="view/layout/asset/css/main.css">
    <link rel="stylesheet" href="view/layout/asset/css/main-responsive.css">
    <link rel="stylesheet" href="view/layout/asset/css/toast-msg.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css" integrity="sha512-17EgCFERpgZKcm0j0fEq1YCJuyAWdz9KUtv1EjVuaOz8pDnh/0nZxmU6BBXwaaxqoi9PQXnRWqlcDB027hgv9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" integrity="sha512-yHknP1/AwR+yx26cB1y0cjvQUMvEa2PFzt1c9LlS4pRQ5NOTZFWbhBig+X9G9eYW/8m0/4OXNx8pxJ6z57x0dw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="view/layout/asset/css/main-home.css">
    <script src="https://kit.fontawesome.com/e8d4a112b7.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="view/layout/js/app.js"></script>
</head>

<body>
    <div class="wrapper">
        <!-- BODY - HEADER -->
        <header>
            <div class="container" id="header">
                <div class="header-container-left">
                    <a href="index.php?pg=home" class="header-logo">BRO SHOES</a>
                </div>
                <div class="header-container-mid hide-on-mobile">
                    <ul class="menu-list category-menu">
                        <li><a class="filter-category active" data-filter="Home">HOME</a></li>
                        <!-- <li><a class="filter-category" data-filter="Product" href="index.php?pg=product">PRODUCTS</a></li>
                        <?php // echo $cataloglist?> -->

                        <li><a class="filter-category" data-filter="Product">PRODUCTS</a></li>
                        <li><a class="filter-category" data-filter="Sneaker">SNEAKERS</a></li>
                        <li><a class="filter-category" data-filter="Sandal">SANDALS</a></li>
                        <li><a class="filter-category" data-filter="Kid">KIDS</a></li>
                    </ul>
                </div>
                <div class="header-container-right">
                    <ul class="menu-list">
                        <li><label for="search-bar"><a><i class="fa-solid fa-magnifying-glass"></i></label></a>
                        </li>
                        <li class="hide-on-mobile">
                            <!-- ACCOUNT FLOAT DROPDOWN -->
                            <a><i class="fa-regular fa-user"></i> 
                                <span class="display-username">
                                <?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['Username']) : ''; ?>
                                </span>
                            </a>
                            <div class="container float-dropdown" id="account-drop-list">
                                <ul class="menu-list" id="login-dropdown">
                                    <?php if (isset($_SESSION['user'])): ?>  
                                        <!-- Nếu đã đăng nhập -->
                                        <li class="logged-in"><a href="index.php?pg=myaccount">
                                            <i class="fa-solid fa-circle-user"></i><span>My Account</span></a>
                                        </li>
                                        <li class="logged-in"><a  href="index.php?pg=myorder">
                                            <i class="fa-solid fa-basket-shopping"></i><span>My Order</span></a>
                                        </li>
                                        <li class="logged-in"><a href="view/logout.php">
                                            <i class="fa-solid fa-right-from-bracket"></i><span>Sign Out</span></a>
                                        </li>
                                    <?php else: ?>
                                        <!-- Nếu chưa đăng nhập -->
                                        <li class="logged-out"><a href="index.php?pg=login">
                                            <i class="fa-solid fa-right-to-bracket"></i><span>Log in</span></a>
                                        </li>
                                        <li class="logged-out"><a href="index.php?pg=register">
                                            <i class="fa-solid fa-user-plus"></i><span>Sign up</span></a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </li>
                        <li class="hide-on-mobile">
                            <a onclick="toggleModal('cart'); showCart();"><i class="fas fa-shopping-cart"></i> 
                                (<span class="display-cart-total-amount">0</span>)
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="container hamburger-menu-button hide-on-pc show-on-mobile">
                    <a class="toggler" id="toggle-sidebar" onclick="toggleModal('header-sidebar')"><i
                            class="fa-solid fa-bars"></i></a>
                </div>

                <!-- SIDEBAR -->
                <div class="modal sidebar header-sidebar" id="header-sidebar">
                    <div class="sidebar-main mdl-cnt">
                        <ul class="menu-list category-menu">
                            <li>Hi there <span class="display-username"><?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['Username']) : ''; ?></span>!</li>
                            <!-- <li><a class="filter-category active" data-filter="Home" href="index.php?pg=home">HOME</a></li> -->
                            <?php // echo $cataloglist?>
                            <li><a class="filter-category" data-filter="Home">HOME</a></li>
                            <li><a class="filter-category" data-filter="Product">PRODUCTS</a></li>
                            <li><a class="filter-category" data-filter="Sneaker">SNEAKERS</a></li>
                            <li><a class="filter-category" data-filter="Sandal">SANDALS</a></li>
                            <li><a class="filter-category" data-filter="Kid">KIDS</a></li>            
                            <li>
                                <!-- SIDEBAR ACCOUNT DROPDOWN -->
                                <a onclick="toggleSidebarDropdown('account-drop-list-sidebar')"><span>ACCOUNT</span> <i
                                        class="fa-solid fa-chevron-down"></i></a>
                                <div class="container account-dropdown hidden" id="account-drop-list-sidebar">
                                    <ul class="menu-list" id="login-dropdown">
                                        <?php if (isset($_SESSION['user'])): ?>
                                                <!-- Đã đăng nhập -->                                           
                                                <li class="logged-in"><a href="index.php?pg=myaccount"><span>My Account</span></a></li>
                                                <li class="logged-in"><a href="index.php?pg=myordert"><span>My Order</span></a></li>
                                                <li class="logged-in"><a href="view/logout.php"><span>Sign Out</span></a></li>
                                            <?php else: ?>
                                                <!-- Chưa đăng nhập -->
                                                <li class="logged-out"><a href="index.php?pg=login"><span>Log in</span></a></li>
                                                <li class="logged-out"><a href="index.php?pg=register"><span>Sign up</span></a></li>
                                            <?php endif; ?>
                                    </ul>
                                </div>
                            </li>
                            <li><a onclick="toggleModal('cart'); showCart();">CART (<span class="display-cart-total-amount"><?= $_SESSION['cartQty'] ?? 0 ?></span>)</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>
   