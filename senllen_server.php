<?php
session_start();
include 'config.php';

// Delete record
if (isset($_GET['delete_id'])) {
    $delete_id = $con->real_escape_string($_GET['delete_id']);
    $con->query("DELETE FROM reports_senllen WHERE id = $delete_id");
}

// Fetch all reports
$reports = $con->query("SELECT * FROM reports_senllen ORDER BY test_date DESC");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحةالتحكم-اختبار سنلين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script >
                        $(document).ready(function()
                        {
                            $("#nav").load("control_panel.html");
                        }
                       );
    </script>
    <style>
        .header-bg {
            background-color: #1e3799;
            color: white;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 2rem;
        }
        
        .table-header {
            background-color: #1e3799;
            color: white;
        }
        
        .custom-border {
            border: 2px solid #1e3799;
            border-radius: 5px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .btn-delete {
            background-color:#e74c3c;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .btn-delete:hover {
            background-color:rgba(204, 0, 0, 0.52);
        }
        
        input, select {
            border: 1px solid #1e3799 !important;
            padding: 8px !important;
            border-radius: 4px !important;
        }
        
        input:focus {
            border: 2px solid #1e3799 !important;
            box-shadow: 0 0 5px rgba(30, 55, 153, 0.5) !important;
        }
        .table-responsive
        {
            border: 2px groove #8cb4e8;
        }
    </style>
</head>
<body>
    <div id="nav"></div>
    <div class="container mt-5">
        <div class="header-bg">
            <h2 class="text-center mb-0">لوحةالتحكم-اختبار سنلين</h2>
        </div>

        <div class="table-responsive custom-border">
            <table class="table table-striped table-hover">
                <thead>
                    <tr class="table-header">
                        <th>رقم التقرير</th>
                        <th>رقم المريض</th>
                        <th>نتائج الفحص</th>
                        <th>تاريخ الفحص</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($report = $reports->fetch_assoc()): ?>
                    <tr>
                        <td><?= $report['id'] ?></td>
                        <td><?= $report['patient_id'] ?></td>
                        <td><?= nl2br($report['results']) ?></td>
                        <td><?= date('Y-m-d H:i', strtotime($report['test_date'])) ?></td>
                        <td>
                            <a href="?delete_id=<?= $report['id'] ?>" 
                               class="btn-delete"
                               onclick="return confirm('هل أنت متأكد من حذف هذا التقرير؟')">
                               حذف
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>