<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>اطباء العيون</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f8ff;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            background:  rgb(35, 140, 210);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        .doctor-card {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: 0.3s;
        }
        .doctor-card:hover {
            transform: translateX(-10px);
        }
        .doctor-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-left: 20px;
            border: 3px solid rgb(35, 140, 210);
        }
        .doctor-info {
            flex-grow: 1;
        }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            display: none;
        }
        input, textarea, button {
            width: 100%;
            padding-bottom: 10px;
            margin: 10px 0;
            border: 2px solid#8cb4e8;
            border-radius: 5px;
        }
        button {
            background: rgb(35, 140, 210);
            color: white;
            font-weight: bold;
            cursor: pointer;
            font-size:15px;
        }
    </style>
</head>
<body>
    <?php
   include 'control/config.php';

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $doctor_id = (int)$_POST['doctor_id'];
        $patient_name = htmlspecialchars($_POST['patient_name']);
        $patient_email = htmlspecialchars($_POST['patient_email']);
        $message = htmlspecialchars($_POST['message']);

        // Save consultation
        $stmt = $con->prepare("INSERT INTO consultations 
            (doctor_id, patient_name, patient_email, message)
            VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $doctor_id, $patient_name, $patient_email, $message);
        
        if ($stmt->execute()) {
            // Get doctor's email
            $doctor = $con->query("SELECT * FROM doctors WHERE id = $doctor_id")->fetch_assoc();
            
            // Send simple email
            $to = $doctor['email'];
            $subject = "استشارة جديدة من $patient_name";
            $headers = "From: $patient_email\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            $email_body = "اسم المريض: $patient_name\n";
            $email_body .= "البريد الإلكتروني: $patient_email\n\n";
            $email_body .= "الاستشارة:\n$message";
            
            if(mail($to, $subject, $email_body, $headers)) {
                echo "<script>alert('تم إرسال الاستشارة بنجاح!');</script>";
            } else {
                echo "<script>alert('حدث خطأ في الإرسال!');</script>";
            }
        }
    }
    ?>
      <a href="homepage.php" style="margin-right:95%;"> <image src="undo.png"></image></a>
    <div class="container">
        <div class="header">
            <h1>أطباء العيون المتخصصين</h1>
        </div>

        <?php
        // Sample doctors (add your own data)
        $doctors = $con->query("SELECT * FROM doctors");
        while($doctor = $doctors->fetch_assoc()): ?>
            <div class="doctor-card" onclick="showForm(<?= $doctor['id'] ?>)">
                <img src="<?= $doctor['image'] ?>" alt="صورة الطبيب" class="doctor-image">
                <div class="doctor-info">
                    <h3><?= $doctor['name'] ?></h3>
                    <p style="color: #007bff;"><?= $doctor['specialty'] ?></p>
                </div>
            </div>
        <?php endwhile; ?>

        <div id="consultForm" class="form-container">
            <h2 style="color: #007bff;">إرسال استشارة</h2>
            <form method="POST">
                <input type="hidden" id="doctorId" name="doctor_id" required>
                <input type="text" name="patient_name" placeholder="اسمك الكامل" required>
                <input type="email" name="patient_email" placeholder="بريدك الإلكتروني" required>
                <textarea name="message" rows="5" placeholder="تفاصيل الاستشارة" required></textarea>
                <button type="submit">إرسال الاستشارة</button>
            </form>
        </div>
    </div>

    <script>
        function showForm(doctorId) {
            document.getElementById('consultForm').style.display = 'block';
            document.getElementById('doctorId').value = doctorId;
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: 'smooth'
            });
        }
    </script>

</body>
</html>
<?php $con->close(); ?>