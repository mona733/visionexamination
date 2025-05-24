<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vision_system";

// Create connection
$con = new mysqli($servername, $username, $password, $dbname);
if ($con->connect_error) die("Connection failed: " . $con->connect_error);
$con->set_charset("utf8mb4");
// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add/Edit Image
    if (isset($_POST['action_image'])) {
        $image_path = $con->real_escape_string($_POST['image_path']);
        $correct_answer = intval($_POST['correct_answer']);
        
        if ($_POST['action_image'] === 'add_image') {
            $con->query("INSERT INTO ishihara_images (image_path, correct_answer) 
                        VALUES ('$image_path', $correct_answer)");
        } elseif ($_POST['action_image'] === 'edit_image') {
            $image_id = intval($_POST['image_id']);
            $con->query("UPDATE ishihara_images 
                        SET image_path='$image_path', correct_answer=$correct_answer 
                        WHERE image_id=$image_id");
        }
    }
    
    // Add/Edit Test
    if (isset($_POST['action_test'])) {
        $patient_id = intval($_POST['patient_id']);
        
        if ($_POST['action_test'] === 'add_test') {
            $con->query("INSERT INTO ishihara_tests (patient_id) VALUES ($patient_id)");
        } elseif ($_POST['action_test'] === 'edit_test') {
            $test_id = intval($_POST['test_id']);
            $con->query("UPDATE ishihara_tests 
                        SET patient_id=$patient_id 
                        WHERE test_id=$test_id");
        }
    }
    
    // Add/Edit Report
    if (isset($_POST['action_report'])) {
        $test_id = intval($_POST['test_id']);
        $correct = intval($_POST['correct_answers']);
        $incorrect = intval($_POST['incorrect_answers']);
        $vision = floatval($_POST['vision_percentage']);
        $color_type = $con->real_escape_string($_POST['color_type']);
        
        if ($_POST['action_report'] === 'add_report') {
            $con->query("INSERT INTO test_reports 
                        (test_id, correct_answers, incorrect_answers, vision_percentage, color_type) 
                        VALUES ($test_id, $correct, $incorrect, $vision, '$color_type')");
        } elseif ($_POST['action_report'] === 'edit_report') {
            $report_id = intval($_POST['report_id']);
            $con->query("UPDATE test_reports 
                        SET test_id=$test_id, correct_answers=$correct, 
                            incorrect_answers=$incorrect, vision_percentage=$vision, 
                            color_type='$color_type' 
                        WHERE report_id=$report_id");
        }
    }
}


// Handle Delete actions
if (isset($_GET['delete'])) {
    $id = intval($_GET['id']);
    $table = $con->real_escape_string($_GET['table']);
    
    // Validate allowed tables
    $allowed_tables = ['ishihara_images', 'ishihara_tests', 'test_reports'];
    if (!in_array($table, $allowed_tables)) {
        die("Invalid table specified");
    }

    // Get correct primary key column name
    $table_parts = explode('_', $table);
    $last_part = end($table_parts);
    $pk_column = rtrim($last_part, 's') . '_id'; // Converts 'images' to 'image_id'

    // Delete record
    $sql = "DELETE FROM `$table` WHERE `$pk_column` = $id";
    if (!$con->query($sql)) {
        die("Delete error: " . $con->error);
    }
}

// Fetch all data
$images = $con->query("SELECT * FROM ishihara_images")->fetch_all(MYSQLI_ASSOC);
$tests = $con->query("SELECT * FROM ishihara_tests")->fetch_all(MYSQLI_ASSOC);
$reports = $con->query("SELECT * FROM test_reports")->fetch_all(MYSQLI_ASSOC);

$con->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام اختبار إيشيهارا</title>
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
            font-family: 'Arial', sans-serif;
            margin: 20px;
            color: #333;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .crud-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            border: 3px groove #8cb4e8;
        }

        .table-container {
            overflow-x: auto;
            margin-bottom: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            border: 3px groove #8cb4e8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        th, td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #1e3799;
            color: white;
        }

        tr:hover {
            background-color: #f5f6fa;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
            background-color: #1e3799;
        }

        .btn-edit {
            background-color: #1e3799;
        }

        .btn-delete {
            background-color: #c0392b;
        }

        @media (max-width: 768px) {
            body {
                margin: 10px;
            }
            
            .crud-form {
                padding: 15px;
            }
            
            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div id="nav"></div>
    <div class="container">
        <h1 style="color: #1e3799; text-align: center;">إدارة اختبارات إيشيهارا</h1>

        <!-- Images Section -->
        <div class="crud-form">
            <h2>إدارة الصور</h2>
            <form method="POST">
                <input type="hidden" name="action_image" value="<?= isset($_GET['edit_image']) ? 'edit_image' : 'add_image' ?>">
                <?php if (isset($_GET['edit_image'])): ?>
                    <input type="hidden" name="image_id" value="<?= $_GET['id'] ?>">
                <?php endif; ?>
                
                <input type="text" name="image_path" placeholder="مسار الصورة" required>
                <input type="number" name="correct_answer" placeholder="الإجابة الصحيحة" required>
                <button type="submit" class="btn"><?= isset($_GET['edit_image']) ? 'تحديث' : 'إضافة' ?></button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>رقم الصورة</th>
                        <th>مسار الصورة</th>
                        <th>الإجابة الصحيحة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($images as $image): ?>
                    <tr>
                        <td><?= $image['image_id'] ?></td>
                        <td><?= $image['image_path'] ?></td>
                        <td><?= $image['correct_answer'] ?></td>
                        <td>
                            <a href="?delete&id=<?= $image['image_id'] ?>&table=ishihara_images" class="btn btn-delete">حذف</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>


        <!-- Reports Section -->
        <div class="crud-form">
            <h2>إدارة التقارير</h2>
            <form method="POST">
                <input type="hidden" name="action_report" value="<?= isset($_GET['edit_report']) ? 'edit_report' : 'add_report' ?>">
                <?php if (isset($_GET['edit_report'])): ?>
                    <input type="hidden" name="report_id" value="<?= $_GET['id'] ?>">
                <?php endif; ?>
                
                <select name="test_id" required>
                    <?php foreach ($tests as $test): ?>
                        <option value="<?= $test['test_id'] ?>">اختبار #<?= $test['test_id'] ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="correct_answers" placeholder="الإجابات الصحيحة" required>
                <input type="number" name="incorrect_answers" placeholder="الإجابات الخاطئة" required>
                <input type="number" step="0.01" name="vision_percentage" placeholder="نسبة الرؤية" required>
                <input type="text" name="color_type" placeholder="نوع اللون" required>
                <button type="submit" class="btn"><?= isset($_GET['edit_report']) ? 'تحديث' : 'إضافة' ?></button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>رقم التقرير</th>
                        <th>رقم الاختبار</th>
                        <th>رقم المريض</th>
                        <th>الإجابات الصحيحة</th>
                        <th>الإجابات الخاطئة</th>
                        <th>نسبة الرؤية</th>
                        <th>نوع اللون</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?= $report['report_id'] ?></td>
                        <td><?= $report['test_id'] ?></td>
                        <td><?= $report['patient_id'] ?></td>
                        <td><?= $report['correct_answers'] ?></td>
                        <td><?= $report['incorrect_answers'] ?></td>
                        <td><?= $report['vision_percentage'] ?>%</td>
                        <td><?= $report['color_type'] ?></td>
                        <td>
                            <a href="?delete&id=<?= $report['report_id'] ?>&table=test_reports" class="btn btn-delete">حذف</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>