<?php
session_start();
include('config.php');

if(isset($_SESSION['loggedin'])) {
    header("Location:home_server.php");
    exit;
}

$error = '';
if(isset($_POST['log'])) {
    $name = $_POST['name'];
    $pass = $_POST['password'];
    
    $result = mysqli_query($con, "SELECT * FROM users");
    
    if(mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            if($row['username'] == $name && $row['password'] == $pass) {
                $_SESSION['loggedin'] = true;
                $_SESSION['name'] = $name;
                header("Location:home_server.php");
                exit;
            }
        }
        $error = "بيانات الدخول غير صحيحة!";
    } else {
        $error = "لا يوجد مستخدمين مسجلين!";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <style>
        body {
            background: #f0f8ff;
            font-family: Arial, sans-serif;
        }
        .login-box {
            width: 300px;
            margin:200px auto;
            padding: 60px;
            background: white;
            border: 1px solid #1e3799;
            border-radius: 5px;
            border:3px groove #8cb4e8;
        }
        input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border:3px groove #8cb4e8;
        }
        button {
            background:  #1e3799;
            color: white;
            height:40px;
            border: none;
            width: 108%;
            margin-top: 10px;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 style="color: #1e3799; text-align: center;">تسجيل الدخول</h2>
        <?php if($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post">
            <input type="text" name="name" placeholder="اسم المستخدم" required>
            <input type="password" name="password" placeholder="كلمة المرور" required>
            <button type="submit" name="log">دخول</button>
        </form>
    </div>
</body>
</html>
