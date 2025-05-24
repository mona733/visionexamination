<?php
session_start();
include 'control/config.php';
$showButton = false;
$status = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $message = mysqli_real_escape_string($con, $_POST['message']);

    // Check if user exists
    $result = mysqli_query($con, "SELECT * FROM messages WHERE email = '$email'");
    $user_exists = mysqli_num_rows($result) > 0;

    // Insert new message
    $sql = "INSERT INTO messages (name, email, message) VALUES ('$name', '$email', '$message')";
    
    if (mysqli_query($con, $sql)) {
        $_SESSION['user_email'] = $email;
        $showButton = $user_exists;

        // Simple email configuration
        $to = "babyaugust449@gmail.com";
        $subject = "رسالة جديدة من $name";
        $headers = "From: noreply@example.com\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        
        $email_body = "اسم المرسل: $name\n";
        $email_body .= "البريد الإلكتروني: $email\n\n";
        $email_body .= "الرسالة:\n$message";

        if (mail($to, $subject, $email_body, $headers)) {
            $status = "<p style='color:green'>تم إرسال الرسالة بنجاح!</p>";
        } else {
            $status = "<p style='color:orange'>تم حفظ الرسالة ولكن هناك مشكلة في الإرسال!</p>";
        }
    } else {
        $status = "<p style='color:red'>خطأ في حفظ الرسالة!</p>";
    }
}

// Get messages if exists
$messages = [];
if (isset($_SESSION['user_email'])) {
    $email = $_SESSION['user_email'];
    $result = mysqli_query($con, "SELECT * FROM messages WHERE email = '$email' ORDER BY reg_date DESC");
    if ($result) {
        $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
        if (count($messages) === 0) {
            unset($_SESSION['user_email']);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ارسال الرسائل</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 0 auto; padding: 20px;  background-color: #f0f8ff;}
        .header { color: rgb(35, 140, 210); text-align: center; margin-bottom: 30px; }
        .message-form { background:#f0f8ff; border:4px groove rgb(35, 140, 210);padding:100px; border-radius: 10px; margin-bottom:10px; }
        input, textarea { width: 100%; padding:15px; margin: 10px 0;border:1px groove  rgb(35, 140, 210); border-radius: 5px; }
        button { background:rgb(35, 140, 210); color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        .message { background: white; border-right: 5px solid rgb(35, 140, 210); padding: 15px; margin: 10px 0; }
        #messagesContainer { display: none; }
    </style>
</head>
<body>
    <a href="homepage.php" style="margin-right:140%;"><img src="undo.png" alt="رجوع"></a>
    <div class="header">
        <h1>تواصل معنا</h1>
    </div>

    <div class="message-form">
        <?= $status ?>
        <form method="POST">
            <input type="text" name="name" placeholder="اسمك" required>
            <input type="email" name="email" placeholder="بريدك الإلكتروني" required>
            <textarea name="message" rows="4" placeholder="رسالتك" required></textarea>
            <button type="submit">إرسال الرسالة</button>
        </form>
    </div>

    <?php if ($showButton || !empty($messages)): ?>
        <button onclick="toggleMessages()" style="margin-bottom: 20px">عرض/إخفاء الرسائل</button>
        <div id="messagesContainer">
            <h2>الرسائل السابقة</h2>
            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="message">
                        <h3><?= htmlspecialchars($msg['name']) ?></h3>
                        <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                        <small><?= $msg['reg_date'] ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>لا توجد رسائل حالياً</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <script>
        function toggleMessages() {
            const container = document.getElementById('messagesContainer');
            container.style.display = container.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>

<?php mysqli_close($con); ?>