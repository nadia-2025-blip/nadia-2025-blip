<?php
session_start();

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_POST) {
    $admin_username = 'admin';
    $admin_password = 'nadia2025'; // غيّر كلمة المرور هذه!
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: index.php');
        exit();
    } else {
        $error = 'اسم المستخدم أو كلمة المرور غير صحيحين';
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - لوحة التحكم</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 25%, #0f3460 50%, #e94560 75%, #f38ba8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            padding: 50px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        
        .logo {
            margin-bottom: 40px;
        }
        
        .logo i {
            font-size: 4rem;
            color: #f38ba8;
            margin-bottom: 20px;
            text-shadow: 0 0 20px rgba(243, 139, 168, 0.5);
        }
        
        .logo h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #f38ba8, #e94560);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .logo p {
            opacity: 0.8;
            font-size: 1rem;
        }
        
        .login-form {
            text-align: right;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 1rem;
            font-family: 'Cairo', sans-serif;
            transition: all 0.3s;
        }
        
        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #f38ba8;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 20px rgba(243, 139, 168, 0.3);
        }
        
        .login-btn {
            width: 100%;
            background: linear-gradient(45deg, #e94560, #f38ba8);
            color: white;
            padding: 18px;
            border: none;
            border-radius: 15px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Cairo', sans-serif;
            margin-top: 20px;
        }
        
        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(233, 69, 96, 0.4);
            background: linear-gradient(45deg, #f38ba8, #e94560);
        }
        
        .error {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.5);
            color: #ff6b6b;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
        }
        
        .security-note {
            margin-top: 30px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .security-note h3 {
            color: #f38ba8;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        
        .security-note p {
            font-size: 0.9rem;
            opacity: 0.8;
            line-height: 1.5;
        }
        
        @media (max-width: 768px) {
            .login-container {
                margin: 20px;
                padding: 30px 25px;
            }
            
            .logo i {
                font-size: 3rem;
            }
            
            .logo h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <i class="fas fa-crown"></i>
            <h1>لوحة التحكم</h1>
            <p>دورة الأستاذة نادية للحلويات</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i> اسم المستخدم
                </label>
                <input type="text" id="username" name="username" placeholder="أدخل اسم المستخدم" required>
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> كلمة المرور
                </label>
                <input type="password" id="password" name="password" placeholder="أدخل كلمة المرور" required>
            </div>
            
            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
            </button>
        </form>
        
        <div class="security-note">
            <h3><i class="fas fa-shield-alt"></i> ملاحظة أمنية</h3>
            <p>هذه المنطقة مخصصة للمديرين فقط. جميع محاولات الدخول مُسجلة ومُراقبة.</p>
        </div>
    </div>
</body>
</html>