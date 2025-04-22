<?php
session_start();
$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
];
$activeForm = $_SESSION['action_form'] ?? 'login';

session_unset(); // Clear session variables after using them

function showError($error)
{
    return !empty($error) ? "<div class='error-message' style='color: red; font-size: 12px;'>$error</div>" : '';
}
function isActiveForm($formName, $activeForm)
{
    return $formName === $activeForm ? 'active' : '';
}

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Login/Register</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Prompt', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        @import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap');

        body {
            background-image: url('/api/placeholder/1600/900');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(31, 64, 104, 0.8), rgba(29, 67, 80, 0.75));
            z-index: -1;
        }

        .moving-clouds {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .cloud {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            filter: blur(20px);
        }

        .cloud-1 {
            width: 300px;
            height: 300px;
            top: 10%;
            left: -150px;
            animation: moveCloud 120s linear infinite;
        }

        .cloud-2 {
            width: 200px;
            height: 200px;
            top: 30%;
            left: -100px;
            animation: moveCloud 80s linear infinite;
        }

        .cloud-3 {
            width: 250px;
            height: 250px;
            top: 60%;
            left: -120px;
            animation: moveCloud 100s linear infinite;
        }

        @keyframes moveCloud {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(calc(100vw + 300px));
            }
        }

        .container {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            width: 420px;
            overflow: hidden;
            position: relative;
            transition: all 0.5s cubic-bezier(0, 0.88, 0.52, 0.99);
            transform: translateY(0);
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .logo {
            text-align: center;
            margin: 30px 0 10px;
        }

        .logo img {
            width: 50%;
            height: 60px;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 5px;
            background-color: white;
        }

        .form-container {
            display: flex;
            width: 200%;
            transition: transform 0.6s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }

        .login-form,
        .register-form {
            width: 50%;
            padding: 30px 40px 40px;
        }

        h2 {
            color: #1A5276;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
            position: relative;
        }

        h2::after {
            content: '';
            position: absolute;
            width: 50px;
            height: 3px;
            background: linear-gradient(to right, #3498DB, #1ABC9C);
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .welcome-text {
            text-align: center;
            color: #5D6D7E;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .input-group {
            margin-bottom: 25px;
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 14px 15px;
            border: none;
            background-color: rgba(236, 240, 241, 0.8);
            border-radius: 10px;
            font-size: 16px;
            outline: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .input-group input:focus {
            background-color: #fff;
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.1);
            border-left: 4px solid #3498DB;
        }

        .input-group label {
            position: absolute;
            top: 14px;
            left: 15px;
            color: #7F8C8D;
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }

        .input-group input:focus+label,
        .input-group input:not(:placeholder-shown)+label {
            top: -20px;
            left: 0;
            font-size: 13px;
            color: #3498DB;
            font-weight: 500;
        }

        .input-group .icon {
            position: absolute;
            right: 15px;
            top: 15px;
            color: #7F8C8D;
        }

        button {
            width: 100%;
            padding: 16px;
            border: none;
            background: linear-gradient(135deg, #3498DB, #1ABC9C);
            color: white;
            font-size: 16px;
            font-weight: 500;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #2E86C1, #16A085);
            transition: all 0.5s;
            z-index: -1;
        }

        button:hover::before {
            left: 0;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(52, 152, 219, 0.3);
        }

        .tabs {
            display: flex;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 20px 0;
            cursor: pointer;
            color: #7F8C8D;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
        }

        .tab.active {
            color: #3498DB;
        }

        .tab-indicator {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50%;
            height: 3px;
            background: linear-gradient(to right, #3498DB, #1ABC9C);
            border-radius: 3px;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }

        .social-login {
            margin-top: 25px;
            text-align: center;
        }

        .social-text {
            color: #5D6D7E;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(0);
        }

        .social-icon:hover {
            background-color: #f8f9fa;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background-color: #ddd;
        }

        .divider span {
            padding: 0 15px;
            color: #7F8C8D;
            font-size: 14px;
        }

        .hidden {
            display: none;
        }

        .forgot-password {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 25px;
        }

        .forgot-password a {
            color: #3498DB;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }

        .forgot-password a:hover {
            color: #2980B9;
            text-decoration: underline;
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #7F8C8D;
        }

        .travel-icons {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.05;
            pointer-events: none;
        }

        .travel-icon {
            position: absolute;
            font-size: 24px;
            color: #1A5276;
        }

        .icon1 {
            top: 10%;
            left: 10%;
        }

        .icon2 {
            top: 20%;
            right: 15%;
        }

        .icon3 {
            bottom: 15%;
            left: 20%;
        }

        .icon4 {
            bottom: 25%;
            right: 10%;
        }

        @keyframes floating {
            0% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-10px) rotate(5deg);
            }

            100% {
                transform: translateY(0) rotate(0deg);
            }
        }

        .travel-icon:nth-child(1) {
            animation: floating 4s ease-in-out infinite;
        }

        .travel-icon:nth-child(2) {
            animation: floating 5s ease-in-out infinite 0.5s;
        }

        .travel-icon:nth-child(3) {
            animation: floating 6s ease-in-out infinite 1s;
        }

        .travel-icon:nth-child(4) {
            animation: floating 5s ease-in-out infinite 1.5s;
        }

        /* Mobile responsiveness */
        @media (max-width: 480px) {
            .container {
                width: 90%;
                margin: 0 15px;
            }

            .login-form,
            .register-form {
                padding: 20px 25px 30px;
            }
        }

        /* Travel themes additions */
        .travel-theme {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 18px;
            color: white;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            letter-spacing: 1px;
        }

        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            color: white;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            backdrop-filter: blur(5px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(30deg);
        }

        /* Back Button Styles */
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background-color: rgba(255, 255, 255, 0.85);
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background-color: white;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
        }

        .back-button svg {
            width: 24px;
            height: 24px;
            fill: #13547a;
        }

        @media (max-width: 768px) {
            .back-button {
                width: 40px;
                height: 40px;
                top: 15px;
                left: 15px;
            }

            .back-button svg {
                width: 20px;
                height: 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Back Button -->
    <a href="javascript:history.back()" class="back-button" title="กลับไปหน้าก่อนหน้า">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
        </svg>
    </a>
    <div class="moving-clouds">
        <div class="cloud cloud-1"></div>
        <div class="cloud cloud-2"></div>
        <div class="cloud cloud-3"></div>
    </div>

    <div class="travel-theme mb-4"></div>
    <button class="theme-toggle">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
        </svg>
    </button>

    <div class="container">
        <div class="travel-icons">
            <div class="travel-icon icon1">✈️</div>
            <div class="travel-icon icon2">🏝️</div>
            <div class="travel-icon icon3">🗺️</div>
            <div class="travel-icon icon4">🧳</div>
        </div>

        <div class="logo">
            <img src="assets/images/logovr.png" alt="Logo">
        </div>

        <div class="tabs">
            <div class="tab active" id="login-tab">เข้าสู่ระบบ</div>
            <div class="tab-indicator"></div>
        </div>

        <div class="form-container">
            <!-- Login Form -->
            <div class="login-form <?= isActiveForm('login', $activeForm) ?>" id="login-section">
                <div class="welcome-text">ยินดีต้อนรับกลับสู่การผจญภัยของคุณ</div>
                <form action="login.php" method="POST">
                    <div class="input-group">
                        <?= showError($errors['login'], 'login'); ?>
                        <input type="email" id="login-email" name="email_user" placeholder=" ">
                        <label for="login-email">อีเมลของคุณ</label>
                        <div class="icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7F8C8D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div class="input-group">
                        <input type="password" id="login-password" name="password_user" placeholder=" ">
                        <label for="login-password">รหัสผ่าน</label>
                        <div class="icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7F8C8D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="forgot-password">
                        <a href="#">ลืมรหัสผ่าน?</a>
                    </div>
                    <button type="submit">เข้าสู่ระบบ</button>
                </form>
                <div class="footer-text">เริ่มเดินทางท่องเที่ยวของคุณวันนี้</div>
            </div>
        </div>
    </div>
    <script>
        // Ensure both forms are visible but only one is displayed based on the active tab
        document.addEventListener('DOMContentLoaded', function() {
            // Make sure the forms are properly initialized
            if (loginTab.classList.contains('active')) {
                loginSection.classList.remove('hidden');
                registerSection.classList.add('hidden');
            } else {
                registerSection.classList.remove('hidden');
                loginSection.classList.add('hidden');
            }
        });

        // If you need JavaScript for the back button functionality
        document.addEventListener('DOMContentLoaded', function() {
            const backButton = document.querySelector('.back-button');
            backButton.addEventListener('click', function() {
                window.history.href = 'javascript:history.back()';
            });
        });
    </script>