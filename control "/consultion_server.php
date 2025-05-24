<?php
include('config.php');
// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Doctors operations
    if (isset($_POST['delete_doctor'])) {
        $id = (int)$_POST['delete_doctor'];
        $con->query("DELETE FROM doctors WHERE id = $id");
    } elseif (isset($_POST['update_doctor'])) {
        $id = (int)$_POST['id'];
        $name = $con->real_escape_string($_POST['name']);
        $specialty = $con->real_escape_string($_POST['specialty']);
        $email = $con->real_escape_string($_POST['email']);
        $image = $con->real_escape_string($_POST['image']);
        $con->query("UPDATE doctors SET 
            name = '$name',
            specialty = '$specialty',
            email = '$email',
            image = '$image'
            WHERE id = $id");
    } elseif (isset($_POST['add_doctor'])) {
        $name = $con->real_escape_string($_POST['name']);
        $specialty = $con->real_escape_string($_POST['specialty']);
        $email = $con->real_escape_string($_POST['email']);
        $image = $con->real_escape_string($_POST['image']);
        $con->query("INSERT INTO doctors (name, specialty, email, image)
            VALUES ('$name', '$specialty', '$email', '$image')");
    }
    
    // Consultations operations
    if (isset($_POST['delete_consultation'])) {
        $id = (int)$_POST['delete_consultation'];
        $con->query("DELETE FROM consultations WHERE id = $id");
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم - الأطباء</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script >
                        $(document).ready(function()
                        {
                            $("#nav").load("control_panel.html");
                        }
                       );
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f8ff;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border: 3px solid #8cb4e8;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 0.9em;
        }
        th, td {
            padding: 8px;
            border: 1px solid #1e3799;
            text-align: center;
        }
        th {
            background-color: #1e3799;
            color: white;
        }
        tr:hover {
            background-color: #f0f8ff;
        }
        input, textarea, button {
            width: 95%;
            padding:5px;
            border: 1px solid #1e3799;
            border-radius: 4px;
            font-size: 0.9em;
        }
        button {
            background: #1e3799;
            color: white;
            padding: 6px 12px;
            cursor: pointer;
            margin: 2px;
        }
        .add-form {
            margin: 15px 0;
            padding: 15px;
            background: #f0f8ff;
            border-radius: 8px;
        }
        .add-form input
        {
            padding:10px;
            margin-bottom:10px;
        }
        h2 {
            color: #1e3799;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div id="nav"></div>
    <div class="container">
        <h2>لوحةالتحكم-إدارة الأطباء</h2>
        
        <!-- Add Doctor Form -->
        <div class="add-form">
            <form method="POST">
                <input type="text" name="name" placeholder="اسم الطبيب" required>
                <input type="text" name="specialty" placeholder="التخصص" required>
                <input type="email" name="email" placeholder="البريد الإلكتروني" required>
                <input type="text" name="image" placeholder="رابط الصورة" required>
                <button type="submit" name="add_doctor">إضافة طبيب</button>
            </form>
        </div>

        <!-- Doctors Table -->
        <table>
            <tr>
                <th>ID</th>
                <th>الاسم</th>
                <th>التخصص</th>
                <th>البريد الإلكتروني</th>
                <th>الصورة</th>
                <th>الإجراءات</th>
            </tr>
            <?php 
            $doctors = $con->query("SELECT * FROM doctors");
            while($doctor = $doctors->fetch_assoc()): ?>
            <tr>
                <form method="POST">
                    <td><?= $doctor['id'] ?></td>
                    <td><input type="text" name="name" value="<?= $doctor['name'] ?>"></td>
                    <td><input type="text" name="specialty" value="<?= $doctor['specialty'] ?>"></td>
                    <td><input type="email" name="email" value="<?= $doctor['email'] ?>"></td>
                    <td><input type="text" name="image" value="<?= $doctor['image'] ?>"></td>
                    <td>
                        <input type="hidden" name="id" value="<?= $doctor['id'] ?>">
                        <button type="submit" name="update_doctor">تحديث</button>
                        <button type="submit" name="delete_doctor" value="<?= $doctor['id'] ?>"style="background:#dc3545;; color:white;">حذف</button>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </table>

        <h2>لوحةالتحكم-إدارةالاستشارات  </h2>

        <!-- Consultations Table -->
        <table>
            <tr>
                <th>ID</th>
                <th>اسم المريض</th>
                <th>بريد المريض</th>
                <th>الرسالة</th>
                <th>الطبيب</th>
                <th>التاريخ</th>
                <th>الإجراءات</th>
            </tr>
            <?php 
            $consultations = $con->query("SELECT c.*, d.name AS doctor_name 
                                        FROM consultations c
                                        LEFT JOIN doctors d ON c.doctor_id = d.id");
            while($consult = $consultations->fetch_assoc()): ?>
            <tr>
                <td><?= $consult['id'] ?></td>
                <td><?= $consult['patient_name'] ?></td>
                <td><?= $consult['patient_email'] ?></td>
                <td><?= substr($consult['message'], 0, 30) ?>...</td>
                <td><?= $consult['doctor_name'] ?? 'غير محدد' ?></td>
                <td><?= $consult['created_at'] ?></td>
                <td>
                    <form method="POST">
                        <button type="submit" name="delete_consultation" 
                                value="<?= $consult['id'] ?>" style="background:#dc3545;; color:white;">حذف</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
<?php $con->close(); ?>