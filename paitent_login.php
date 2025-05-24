<?php
session_start();
require_once 'control/config.php';

// Handle form submissions
$error = '';
$success = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // عملية تسجيل الدخول
        $email = $con->real_escape_string($_POST['email']);
        $password = $_POST['password'];
        
        $stmt = $con->prepare("SELECT id, password FROM patient WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $patient = $result->fetch_assoc();
            if (password_verify($password, $patient['password'])) {
                $_SESSION['patient_id'] = $patient['id'];
                header("Location:choice.php");
                exit();
            } else {
                $error = "كلمة المرور غير صحيحة!";
            }
        } else {
            $error = "البريد الإلكتروني غير مسجل!";
        }
        $stmt->close();
        
    } elseif (isset($_POST['register'])) {
        // عملية التسجيل
        $name = $con->real_escape_string($_POST['name']);
        $email = $con->real_escape_string($_POST['email']);
        $password = $con->real_escape_string($_POST['password']);
        $confirm_password = $con->real_escape_string($_POST['confirm_password']);
        $birthdate = $con->real_escape_string($_POST['birthdate']);
        $gender = $con->real_escape_string($_POST['gender']);
        $phone = $con->real_escape_string($_POST['phone']);
        
        if ($password !== $confirm_password) {
            $error = "كلمات المرور غير متطابقة!";
        } else {
            $stmt = $con->prepare("SELECT id FROM patient WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = "البريد الإلكتروني مسجل مسبقاً!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert = $con->prepare("INSERT INTO patient (name, email, password, birthdate, gender, phone) VALUES (?, ?, ?, ?, ?, ?)");
                $insert->bind_param("ssssss", $name, $email, $hashed_password, $birthdate, $gender, $phone);
                
                if ($insert->execute()) {
                    $success = "تم التسجيل بنجاح! يرجى تسجيل الدخول";
                } else {
                    $error = "فشل التسجيل: " . $con->error;
                }
                $insert->close();
            }
            $stmt->close();
        }
    }
}

$is_register = isset($_GET['action']) && $_GET['action'] == 'register';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_register ? 'تسجيل مريض جديد' : 'تسجيل الدخول' ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            transition: all 0.3s ease;
        }

        h1 {
            color: rgb(35, 140, 210);
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        input, select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input:focus, select:focus {
            outline: none;
            border-color: rgb(35, 140, 210);
        }

        button {
            width: 100%;
            padding: 1rem;
            background: rgb(35, 140, 210);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: rgb(25, 120, 190);
        }

        .toggle-form {
            text-align: center;
            margin-top: 1rem;
        }

        .toggle-form a {
            color: rgb(35, 140, 210);
            text-decoration: none;
            font-weight: 500;
        }

        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            text-align: center;
        }

        .error {
            background: #ffe3e3;
            color: #c00;
        }

        .success {
            background: #e3ffe3;
            color: #090;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php elseif ($success): ?>
            <div class="message success"><?= $success ?></div>
        <?php endif; ?>

        <h1><?= $is_register ? 'تسجيل مريض جديد' : 'تسجيل الدخول' ?></h1>
        
        <form method="POST">
            <?php if ($is_register): ?>
                <div class="form-group">
                    <label>الاسم الكامل</label>
                    <input type="text" name="name" required>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            
            <div class="form-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" required>
            </div>

            <?php if ($is_register): ?>
                <div class="form-group">
                    <label>تأكيد كلمة المرور</label>
                    <input type="password" name="confirm_password" required>
                </div>
                
                <div class="form-group">
                    <label>تاريخ الميلاد</label>
                    <input type="date" name="birthdate" required>
                </div>
                
                <div class="form-group">
                    <label>الجنس</label>
                    <select name="gender" required>
                        <option value="ذكر">ذكر</option>
                        <option value="أنثى">أنثى</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>رقم الهاتف</label>
                    <input type="tel" name="phone" required>
                </div>
            <?php endif; ?>

            <button type="submit" name="<?= $is_register ? 'register' : 'login' ?>">
                <?= $is_register ? 'تسجيل الحساب' : 'الدخول' ?>
            </button>
        </form>

        <div class="toggle-form">
            <?php if ($is_register): ?>
                لديك حساب بالفعل؟ <a href="?">سجل الدخول هنا</a>
            <?php else: ?>
                لا تمتلك حساب؟ <a href="?action=register">انشاء حساب جديد</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $con->close(); ?>