<?php
require 'config.php';
session_start();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new report
    if (isset($_POST['add'])) {
        $patient_id = mysqli_real_escape_string($con, $_POST['patient_id']);
        $test_date = mysqli_real_escape_string($con, $_POST['test_date']);
        mysqli_query($con, "INSERT INTO report_senllen2 (patient_id, test_date) VALUES ('$patient_id', '$test_date')");
    }
    // Update existing report
    elseif (isset($_POST['update'])) {
        $report_id = mysqli_real_escape_string($con, $_POST['report_id']);
        $patient_id = mysqli_real_escape_string($con, $_POST['patient_id']);
        $test_date = mysqli_real_escape_string($con, $_POST['test_date']);
        mysqli_query($con, "UPDATE report_senllen2 SET patient_id='$patient_id', test_date='$test_date' WHERE id='$report_id'");
    }
}

// Handle delete action
if (isset($_GET['delete'])) {
    $report_id = mysqli_real_escape_string($con, $_GET['delete']);
    mysqli_query($con, "DELETE FROM senllen2_test WHERE report_id='$report_id'");
    mysqli_query($con, "DELETE FROM report_senllen2 WHERE id='$report_id'");
}

// Fetch all reports with patient names
$reports = mysqli_query($con, 
    "SELECT r.*, p.name AS patient_name 
    FROM report_senllen2 r 
    JOIN patient p ON r.patient_id = p.id
    ORDER BY r.test_date DESC");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - فحص النظر</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script >
                        $(document).ready(function()
                        {
                            $("#nav").load("control_panel.html");
                        }
                       );
    </script>
    <style>
        :root { --main-color: #1e3799; }
        body { font-family: 'Tahoma', sans-serif; background-color: #f8f9fa; }
        .header { background: var(--main-color); padding: 20px; color: white; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { padding: 12px; text-align: right; border-bottom: 1px solid #ddd; }
        th { background-color: var(--main-color); color: white; }
        .btn-edit { background: var(--main-color); color: white; padding: 5px 15px; }
        .btn-delete { background: #c0392b; color: white; padding: 5px 15px; }
        .form-container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div id="nav"></div>
    <div class="header">
        <h2>إدارة تقارير فحص حدة البصر</h2>
    </div>

    <div class="container">
        <!-- Add/Edit Form -->
        <div class="form-container mt-4">
            <h4><?= isset($_GET['edit']) ? 'تعديل' : 'إضافة' ?> تقرير</h4>
            <form method="POST">
                <?php if (isset($_GET['edit'])): 
                    $edit_id = $_GET['edit'];
                    $edit_data = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM report_senllen2 WHERE id='$edit_id'"));
                ?>
                    <input type="hidden" name="report_id" value="<?= $edit_data['id'] ?>">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-4">
                        <input type="number" name="patient_id" 
                            value="<?= $edit_data['patient_id'] ?? '' ?>" 
                            placeholder="رقم المريض" required>
                    </div>
                    <div class="col-md-4">
                        <input type="datetime-local" name="test_date" 
                            value="<?= isset($edit_data) ? date('Y-m-d\TH:i', strtotime($edit_data['test_date'])) : '' ?>" 
                            required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" name="<?= isset($_GET['edit']) ? 'update' : 'add' ?>" 
                            class="btn btn-edit">
                            <?= isset($_GET['edit']) ? 'تحديث' : 'إضافة' ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Reports Table -->
        <div class="table-responsive mt-4">
            <table>
                <thead>
                    <tr>
                        <th>رقم التقرير</th>
                        <th>اسم المريض</th>
                        <th>تاريخ الفحص</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($reports)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['patient_name'] ?></td>
                        <td><?= date('Y-m-d H:i', strtotime($row['test_date'])) ?></td>
                        <td>
                            <a href="?edit=<?= $row['id'] ?>" class="btn btn-edit">تعديل</a>
                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-delete" 
                            onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Add confirmation for delete action
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', (e) => {
                if (!confirm('هل أنت متأكد من حذف هذا التقرير؟')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>