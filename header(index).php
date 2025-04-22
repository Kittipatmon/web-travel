<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<!-- Additional CSS Files -->
<link rel="stylesheet" href="assets/css/fontawesome.css">
<link rel="stylesheet" href="assets/css/travel.css">
<link rel="stylesheet" href="assets/css/owl.css">
<link rel="stylesheet" href="assets/css/animate.css">
<link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />

<style>
    .main-nav {
        display: flex;
        gap: 30px;
    }

    .main-nav a {
        color: white;
        text-decoration: none;
        font-size: 16px;
        font-weight: 500;
        position: relative;
        padding: 5px 0;
    }

    .main-nav a::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background-color: white;
        transition: width 0.3s ease;
    }

    .main-nav a:hover::after {
        width: 100%;
    }

    .main-nav .logo {
        text-decoration: none;
        position: relative;
    }

    .main-nav .logo::after {
        content: none;
    }

    .avatar-dropdown {
        position: relative;
        top: 25%;
        right: -80px;
        transform: translateY(-50%);
        display: inline-block;
        margin-left: 10px;
    }

    .avatar-dropdown img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        margin-right: 0px;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
        right: 0;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content a:hover {
        background-color: rgb(42, 141, 240);
    }

    .avatar-dropdown:hover .dropdown-content {
        display: block;
    }

    @media (max-width: 992px) {
        .avatar-dropdown {
            position: static;
            transform: none;
            margin-top: 10px;
            float: none;
            text-align: center;
            display: contents;
        }

        .dropdown-content {
            position: static;
            box-shadow: none;
        }
    }

    @media (max-width: 768px) {
        .avatar-dropdown {
            position: static;
            transform: none;
            margin-top: 10px;
            float: none;
            text-align: center;
        }

        .dropdown-content {
            position: static;
            box-shadow: none;
        }
    }
</style>

<header class="header-area header-sticky">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                    <!-- ***** Logo Start ***** -->
                    <a href="index.php" class="logo">
                        <img src="assets/images/logovr.png" alt="Logo">
                    </a>
                    <!-- ***** Logo End ***** -->
                    <!-- ***** Menu Start ***** -->
                    <ul class="nav">
                        <li><a href="index.php" class="active"><img src="assets/images/home.png" alt="" style="width: 20px; height: 20px; margin-bottom: 10px;"> Home</a></li>
                        <li><a href="contact.php"><img src="assets/images/contact.png" alt="" style="width: 20px; height: 20px; margin-bottom: 10px;"> Contact Us</a></li>

                        <div class="avatar-dropdown">
                            <div class="col-5 text-center w-auto mt-2 mb-1 ml-2">
                                <li><a href="#" class="btn"><img src="assets/images/avatra.png" alt="Avatar" class="avatar" id="avatarDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Profile</a></li>
                                <div class="dropdown-content">
                                    <a href="frm_login.php"><img src="assets/images/log.png" alt="">
                                        <span>Login</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </ul>
                    <a class='menu-trigger'>
                        <span>Menu</span>
                    </a>
                    <!-- ***** Menu End ***** -->
                </nav>
            </div>
        </div>
    </div>
</header>