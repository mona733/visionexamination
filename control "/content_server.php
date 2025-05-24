<?php
include 'config.php';
// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $id = (int)$_POST['delete'];
        $con->query("DELETE FROM messages WHERE id = $id");
    } elseif (isset($_POST['update'])) {
        $id = (int)$_POST['id'];
        $name = $con->real_escape_string($_POST['name']);
        $email = $con->real_escape_string($_POST['email']);
        $message = $con->real_escape_string($_POST['message']);
        $con->query("UPDATE messages SET 
                    name = '$name',
                    email = '$email',
                    message = '$message'
                    WHERE id = $id");
    }
}

// Get all messages
$result = $con->query("SELECT * FROM messages ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم-رسائل المستخدمين </title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script >
                        $(document).ready(function()
                        {
                            $("#nav").load("control_panel.html");
                        }
                       );
    </script>
    <style>
        /* Keep the same CSS styles as before */
        body { 
            font-family: Arial, sans-serif;
            background: #f0f8ff;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border:4px groove #8cb4e8;
        }
        h2
        {
          font-size:30px;
          font-weight:bold;
          color: #1e3799;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            border: 1px solid#1e3799;
            text-align: center;
            cursor: pointer;
        }
        th {
            background-color: #1e3799;
            color: white;
        }
        tr:hover {
            background-color: #f0f8ff;
        }
        input[type="text"], textarea {
            width: 95%;
            padding: 5px;
            border: 1px solid #1e3799;
            border-radius: 4px;
        }
        button {
            background: #1e3799;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin: 2px;
        }
        td .del
        {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin: 2px;
        }
    </style>
</head>
<body>
    <div id="nav"></div>
    <div class="container">
        <h2 style=" text-align: center;">لوحةالتحكم-الرسائل</h2>
        
        <table>
            <tr>
                <th>ID</th>
                <th>الاسم </th>
                <th>البريد</th>
                <th>الرساله</th>
                <th>تاريخ الارسال</th>
                <th></th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <form method="POST">
                    <td><?= $row['id'] ?></td>
                    <td><input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>"></td>
                    <td><input type="text" name="email" value="<?= htmlspecialchars($row['email']) ?>"></td>
                    <td><textarea name="message"><?= htmlspecialchars($row['message']) ?></textarea></td>
                    <td><?= $row['reg_date'] ?></td>
                    <td>
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" name="update">تعديل</button>
                        <button type="submit" name="delete" value="<?= $row['id'] ?>" class="del">حذف</button>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
<?php $con->close(); ?>