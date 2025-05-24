<?php
session_start();
include 'config.php';
// معالجة تحديث البيانات
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $table = mysqli_real_escape_string($con, $_POST['table']);
    $column = mysqli_real_escape_string($con, $_POST['column']);
    $value = mysqli_real_escape_string($con, $_POST['value']);
    $id = mysqli_real_escape_string($con, $_POST['id']);
    
    $query = "UPDATE $table SET $column = '$value' WHERE id = $id";
    mysqli_query($con, $query);
}

// معالجة حذف البيانات
if (isset($_GET['delete'])) {
    $table = mysqli_real_escape_string($con, $_GET['table']);
    $id = mysqli_real_escape_string($con, $_GET['id']);
    
    $query = "DELETE FROM $table WHERE id = $id";
    mysqli_query($con, $query);
}

// جلب البيانات
$patients = mysqli_query($con, "SELECT * FROM patient");
$diagnoses = mysqli_query($con, 
    "SELECT diagnosis.*, patient.name 
    FROM diagnosis 
    JOIN patient ON diagnosis.patient_id = patient.id"
);
$diseases = mysqli_query($con, "SELECT * FROM eye_diseases");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم-المرضى,تشخيصات الامراض,الامراض </title>
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
        * { box-sizing: border-box; font-family: 'Arial'; }
        body { background: #f5f6fa; margin: 0; }
        
        .container { padding: 20px; max-width: 1200px; margin: auto; }
        .tabs { display: flex; gap: 10px; margin-bottom: 20px; }
        .tab-btn {
            padding: 10px 20px;
            background: #fff;
            border: 1px solid #ddd;
            cursor: pointer;
            border-radius: 4px;
        }
        .tab-btn.active { background: var(--main-color); color: white; }
        
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
            border: 2px groove #8cb4e8;
        }
        th, td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #eee;
        }
        th { background: var(--main-color); color: white; }
        tr:hover td { background: #f8f9fa; }
        
        .editable { cursor: pointer; }
        .editable:hover { background: #e9f5ff; }
        
        .delete-btn {
            padding: 5px 10px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .disease-list {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .disease-item {
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 6px;
            color: #1e3799;
            border: 1px solid #ddd;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div id="nav"></div>
    <div class="container">
        <h1 style="color:#1e3799;">لوحة التحكم-المرضى,تشخيصات الامراض,الامراض</h1>
        
        <div class="tabs">
            <button class="tab-btn active" onclick="showTable('patients')">المرضى</button>
            <button class="tab-btn" onclick="showTable('diagnoses')">التشخيصات</button>
            <button class="tab-btn"><a href="disease_server.php">الأمراض</a></button>
        </div>

        <!-- جدول المرضى -->
        <div id="patients-table" class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>الاسم</th>
                    <th>تاريخ التسجيل</th>
                    <th>إجراءات</th>
                </tr>
                <?php while($row = mysqli_fetch_assoc($patients)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td class="editable" onclick="editCell(this, 'patient', 'name', <?= $row['id'] ?>)">
                        <?= htmlspecialchars($row['name']) ?>
                    </td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <button class="delete-btn" 
                                onclick="deleteRecord('patient', <?= $row['id'] ?>)">حذف</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <!-- جدول التشخيصات -->
        <div id="diagnoses-table" class="table-container" style="display:none;">
            <table>
                <tr>
                    <th>ID</th>
                    <th>اسم المريض</th>
                    <th>التقرير</th>
                    <th>التاريخ</th>
                    <th>إجراءات</th>
                </tr>
                <?php while($row = mysqli_fetch_assoc($diagnoses)): 
                    $report = json_decode($row['report'], true);
                    $detected_diseases = [];
                    
                    if(is_array($report)) {
                        foreach($report as $disease) {
                            if(isset($disease['score']) && $disease['score'] > 0) {
                                $detected_diseases[] = $disease['disease_name'];
                            }
                        }
                    }
                ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td>
                        <div class="disease-list">
                            <?php if(!empty($detected_diseases)): ?>
                                <?php foreach($detected_diseases as $disease): ?>
                                    <div class="disease-item"><?= htmlspecialchars($disease) ?></div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="disease-item">لا توجد أمراض مكتشفة</div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><?= $row['date'] ?></td>
                    <td>
                        <button class="delete-btn" 
                                onclick="deleteRecord('diagnosis', <?= $row['id'] ?>)">حذف</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <form id="editForm" method="post" style="display:none;">
            <input type="hidden" name="table" id="editTable">
            <input type="hidden" name="column" id="editColumn">
            <input type="hidden" name="value" id="editValue">
            <input type="hidden" name="id" id="editId">
            <input type="submit" name="update">
        </form>
    </div>

    <script>
        function showTable(tableName) {
            document.querySelectorAll('.table-container').forEach(t => t.style.display = 'none');
            document.getElementById(tableName + '-table').style.display = 'block';
            
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            event.target.classList.add('active');
        }

        function editCell(cell, table, column, id) {
            const oldValue = cell.innerText;
            const newValue = prompt('أدخل القيمة الجديدة:', oldValue);
            
            if (newValue && newValue !== oldValue) {
                document.getElementById('editTable').value = table;
                document.getElementById('editColumn').value = column;
                document.getElementById('editValue').value = newValue;
                document.getElementById('editId').value = id;
                document.getElementById('editForm').submit();
            }
        }

        function deleteRecord(table, id) {
            if (confirm('هل أنت متأكد من الحذف؟')) {
                window.location.href = `?delete=1&table=${table}&id=${id}`;
            }
        }
    </script>
</body>
</html>