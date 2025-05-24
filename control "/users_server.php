<?php
include 'config.php';

// معالجة عملية الحذف
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $sql = "DELETE FROM users WHERE id = '$id'";
    mysqli_query($con, $sql);
}

// جلب البيانات من الجدول
$sql = "SELECT * FROM users";
$result = mysqli_query($con, $sql);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إدارة المستخدمين</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bs-primary: #1e3799;
            --bs-success: #27ae60;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .card
        {
            margin-right:10%;
            margin-top:10%;
        }
        .custom-table {
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
          
        }
        
        .custom-table thead th {
            background-color: var(--bs-primary);
            color: white;
        }
        
        .password-cell {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body class="container mt-3">
    <div id="nav"></div>
    
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">قائمة المستخدمين</h3>
            <a href="add_user.php" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> إضافة مستخدم
            </a>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered custom-table">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>اسم المستخدم</th>
                            <th width="20%">كلمة المرور</th>
                            <th width="15%">تاريخ التسجيل</th>
                            <th width="20%">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td class="password-cell">
                                <?= substr(htmlspecialchars($user['password']), 0, 15) . '...' ?>
                            </td>
                            <td><?= htmlspecialchars($user['created_at']) ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="edit_user.php?id=<?= $user['id'] ?>" 
                                       class="btn btn-sm btn-success">
                                        <i class="bi bi-pencil"></i> تعديل
                                    </a>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                        <button type="submit" name="delete" 
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                            <i class="bi bi-trash"></i> حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#nav").load("control_panel.html");
        });
    </script>
</body>
</html>