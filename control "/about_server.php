<?php
header('Content-Type: text/html; charset=utf-8');
include 'config.php';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new record
    if (isset($_POST['add'])) {
        $heading = mysqli_real_escape_string($con, $_POST['new_heading']);
        $paragraph = mysqli_real_escape_string($con, $_POST['new_paragraph']);
        mysqli_query($con, "INSERT INTO site_content (heading, paragraph) VALUES ('$heading', '$paragraph')");
    }
    
    // Update record
    if (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        $heading = mysqli_real_escape_string($con, $_POST['heading']);
        $paragraph = mysqli_real_escape_string($con, $_POST['paragraph']);
        mysqli_query($con, "UPDATE site_content SET heading='$heading', paragraph='$paragraph' WHERE id=$id");
    }
    
    // Delete record
    if (isset($_POST['delete'])) {
        $id = (int)$_POST['id'];
        mysqli_query($con, "DELETE FROM site_content WHERE id=$id");
    }
}

// Fetch all records
$result = mysqli_query($con, "SELECT * FROM site_content ORDER BY id DESC");
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <title>لوحة التحكم - عنا </title>
    <meta charset="UTF-8">
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
            background: #f0f8ff;
            padding: 20px;
        }

        .admin-container {
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border:3px groove #8cb4e8;
        }

        .add-form {
            margin-bottom: 20px;
            padding:15px;
            background: #f8f9ff;
            border-radius: 6px;
            border:3px groove #8cb4e8;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .data-table th,
        .data-table td {
            padding: 12px;
            border: 1px solid #8cb4e8;
            text-align: center;
        }

        .data-table th {
            background: #1e3799;
            color: white;
        }

        input, textarea {
            width: 95%;
            padding:10px;
            border:1px groove  #1e3799;
            border-radius: 4px;
            font-family: 'Arial', sans-serif;
        }

        button {
            padding: 6px 12px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background: #1e3799;
            color: white;
        }

        button.delete-btn {
            background: #dc3545;
        }

        button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div id="nav"></div>
    <div class="admin-container">
        <h1 style="text-align: center; color: #1e3799;">لوحة التحكم - عن الموقع</h1>

        <!-- Add New Record Form -->
        <form class="add-form" method="POST">
            <input type="text" name="new_heading" placeholder="العنوان الجديد" required>
            <textarea name="new_paragraph" placeholder="المحتوى الجديد" rows="2" required></textarea>
            <button type="submit" name="add">إضافة جديد</button>
        </form>

        <!-- Records Table -->
        <table class="data-table">
            <tr>
                <th>ID</th>
                <th>العنوان</th>
                <th>المحتوى</th>
                <th>تاريخ الإضافة</th>
                <th>الإجراءات</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="text" name="heading" value="<?= htmlspecialchars($row['heading']) ?>">
                </td>
                <td>
                        <textarea name="paragraph"><?= htmlspecialchars($row['paragraph']) ?></textarea>
                </td>
                <td><?= $row['created_at'] ?></td>
                <td>
                        <button type="submit" name="update">حفظ</button>
                    </form>
                    <form method="POST" style="display: inline-block;">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" name="delete" class="delete-btn">حذف</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>

<?php mysqli_close($con); ?>