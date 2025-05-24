<?php
include 'config.php';

// Handle CRUD Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add New Disease
    if (isset($_POST['add'])) {
        $name = mysqli_real_escape_string($con, $_POST['new_name']);
        $desc = mysqli_real_escape_string($con, $_POST['new_desc']);
        $symptoms = mysqli_real_escape_string($con, $_POST['new_symptoms']);
        $treatments = mysqli_real_escape_string($con, $_POST['new_treatments']);
        $prevention = mysqli_real_escape_string($con, $_POST['new_prevention']);
        $category = mysqli_real_escape_string($con, $_POST['new_category']);
        
        $sql = "INSERT INTO eye_diseases (disease_name, description, symptoms, treatments, prevention, category)
                VALUES ('$name', '$desc', '$symptoms', '$treatments', '$prevention', '$category')";
        mysqli_query($con, $sql);
    }
    
    // Update Disease
    if (isset($_POST['update'])) {
        $id = mysqli_real_escape_string($con, $_POST['id']);
        $name = mysqli_real_escape_string($con, $_POST['disease_name']);
        $desc = mysqli_real_escape_string($con, $_POST['description']);
        $symptoms = mysqli_real_escape_string($con, $_POST['symptoms']);
        $treatments = mysqli_real_escape_string($con, $_POST['treatments']);
        $prevention = mysqli_real_escape_string($con, $_POST['prevention']);
        $category = mysqli_real_escape_string($con, $_POST['category']);
        
        $sql = "UPDATE eye_diseases SET 
                disease_name = '$name',
                description = '$desc',
                symptoms = '$symptoms',
                treatments = '$treatments',
                prevention = '$prevention',
                category = '$category'
                WHERE id = $id";
        mysqli_query($con, $sql);
    }
    
    // Delete Disease
    if (isset($_POST['delete'])) {
        $id = mysqli_real_escape_string($con, $_POST['id']);
        $sql = "DELETE FROM eye_diseases WHERE id = $id";
        mysqli_query($con, $sql);
    }
}

// Fetch all diseases
$result = mysqli_query($con, "SELECT * FROM eye_diseases");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحةالتحكم-أمراض العيون</title>
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
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8f9fa;
        }
        .editable-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            border:4px groove #8cb4e8;
        }
        h1
        {
            font-weight:bold;
        }
        .editable-table th {
            background-color: #1e3799;
            color: white;
            padding: 12px;
        }
        .editable-table td {
            padding: 10px;
            vertical-align: middle;
        }
        .form-control-sm {
            padding: 4px 8px;
            font-size: 0.875rem;
            border:1px groove  #1e3799;
        }
        .action-buttons button {
            margin: 2px;
            padding: 4px 8px;
            font-size: 0.875rem;
        }
        .add-form {
            background: #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            border:4px groove #8cb4e8;
        }
        input,textarea
        {
            border:1px groove  #1e3799;
        }
    </style>
</head>
<body>
    <div id="nav"></div>
    <div class="container py-5">
        <h1 class="text-center mb-4" style="color:#1e3799;"> لوحةالتحكم-ادارة أمراض العيون </h1>

        <!-- Add New Disease Form -->
        <form method="POST" class="add-form">
            <div class="row g-2">
                <div class="col-md-2">
                    <input type="text" name="new_name" class="form-control form-control-sm" placeholder="اسم المرض" required>
                </div>
                <div class="col-md-2">
                    <textarea name="new_desc" class="form-control form-control-sm" placeholder="الوصف" required></textarea>
                </div>
                <div class="col-md-2">
                    <textarea name="new_symptoms" class="form-control form-control-sm" placeholder="الأعراض" required></textarea>
                </div>
                <div class="col-md-2">
                    <textarea name="new_treatments" class="form-control form-control-sm" placeholder="العلاجات" required></textarea>
                </div>
                <div class="col-md-2">
                    <textarea name="new_prevention" class="form-control form-control-sm" placeholder="الوقاية" required></textarea>
                </div>
                <div class="col-md-2">
                    <input type="text" name="new_category" class="form-control form-control-sm" placeholder="الفئة" required>
                </div>
                <div class="col-md-12 mt-2">
                    <button type="submit" name="add" class="btn btn-sm " style="background:#1e3799; color:white;">إضافة جديد</button>
                </div>
            </div>
        </form>

        <!-- Diseases Table -->
        <div class="editable-table">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>اسم المرض</th>
                        <th>الوصف</th>
                        <th>الأعراض</th>
                        <th>العلاجات</th>
                        <th>الوقاية</th>
                        <th>الفئة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            
                            <td>
                                <input type="text" name="disease_name" 
                                       class="form-control form-control-sm" 
                                       value="<?= htmlspecialchars($row['disease_name']) ?>">
                            </td>
                            <td>
                                <textarea name="description" 
                                          class="form-control form-control-sm"><?= htmlspecialchars($row['description']) ?></textarea>
                            </td>
                            <td>
                                <textarea name="symptoms" 
                                          class="form-control form-control-sm"><?= htmlspecialchars($row['symptoms']) ?></textarea>
                            </td>
                            <td>
                                <textarea name="treatments" 
                                          class="form-control form-control-sm"><?= htmlspecialchars($row['treatments']) ?></textarea>
                            </td>
                            <td>
                                <textarea name="prevention" 
                                          class="form-control form-control-sm"><?= htmlspecialchars($row['prevention']) ?></textarea>
                            </td>
                            <td>
                                <input type="text" name="category" 
                                       class="form-control form-control-sm" 
                                       value="<?= htmlspecialchars($row['category']) ?>">
                            </td>
                            <td class="action-buttons">
                                <button type="submit" name="update" class="btn btn-sm " style="background:#1e3799; color:white;">حفظ</button>
                                <button type="submit" name="delete" class="btn btn-sm " style="background:#dc3545;; color:white;">حذف</button>
                            </td>
                        </form>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($con); ?>