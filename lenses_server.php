<?php
include 'config.php';

// Delete Record
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    mysqli_query($con, "DELETE FROM contact_lenses WHERE id=$id");
}

// Add New Record
if(isset($_POST['add'])){
    $fields = [
        'lens_type' => mysqli_real_escape_string($con, $_POST['lens_type']),
        'material' => mysqli_real_escape_string($con, $_POST['material']),
        'oxygen_permeability' => mysqli_real_escape_string($con, $_POST['oxygen_permeability']),
        'replacement_schedule' => mysqli_real_escape_string($con, $_POST['replacement_schedule']),
        'focal_measurement' => mysqli_real_escape_string($con, $_POST['focal_measurement']),
        'recommended_conditions' => mysqli_real_escape_string($con, $_POST['recommended_conditions']),
        'features' => mysqli_real_escape_string($con, $_POST['features']),
        'quality_rating' => (int)$_POST['quality_rating']
    ];
    
    $sql = "INSERT INTO contact_lenses (".implode(',', array_keys($fields)).")
            VALUES ('".implode("','", $fields)."')";
    mysqli_query($con, $sql);
}

// Update Record
if(isset($_POST['update'])){
    $id = (int)$_POST['id'];
    $field = mysqli_real_escape_string($con, $_POST['field']);
    $value = mysqli_real_escape_string($con, $_POST['value']);
    
    $allowed_fields = ['lens_type','material','oxygen_permeability',
                     'replacement_schedule','focal_measurement',
                     'recommended_conditions','features','quality_rating'];
                     
    if(in_array($field, $allowed_fields)){
        $sql = "UPDATE contact_lenses SET $field='$value' WHERE id=$id";
        mysqli_query($con, $sql);
    }
}

$result = mysqli_query($con, "SELECT * FROM contact_lenses");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحةالتحكم- اداره العدسات</title>
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
        .manage-table {
            font-size: 14px;
            min-width: 1200px;
            border:2px groove #8cb4e8;
        }
        .manage-table th {
            background:#1e3799;
            color: white;
        }
        h4
        {
          color:#1e3799;
        }
        .editable-cell {
            cursor: pointer;
            transition:  0.2s;
        }
        .editable-cell:hover {
            background: #e3f2fd;
        }
        .edit-input {
            width: 100%;
            padding: 2px 5px;
            font-size: 14px;
            border: 1px solid #8cb4e8;
        }
        .action-btns {
            min-width: 100px;
        }
        form 
        {
            border:2px groove #8cb4e8;
        }
        .form-control 
        {
            border:2px groove #8cb4e8;
        }
    </style>
</head>
<body class="p-4">
    <div id="nav"></div>
    <div class="container-fluid">
        <!-- Add New Form -->
        <form method="post" class="mb-4 p-3 bg-light rounded shadow-sm">
            <h4 class="mb-3">إضافة عدسة جديدة</h4>
            <div class="row g-2">
                <!-- Lens Type -->
                <div class="col-2">
                    <input type="text" name="lens_type" placeholder="نوع العدسة" 
                           class="form-control form-control-sm" required>
                </div>
                
                <!-- Material -->
                <div class="col-2">
                    <input type="text" name="material" placeholder="المادة" 
                           class="form-control form-control-sm" required>
                </div>
                
                <!-- Oxygen Permeability -->
                <div class="col-2">
                    <input type="number" step="0.1" name="oxygen_permeability" 
                           placeholder="نفاذية الأكسجين" 
                           class="form-control form-control-sm" required>
                </div>
                
                <!-- Replacement Schedule -->
                <div class="col-2">
                    <input type="text" name="replacement_schedule" 
                           placeholder="جدول الاستبدال" 
                           class="form-control form-control-sm" required>
                </div>
                
                <!-- Focal Measurement -->
                <div class="col-2">
                    <input type="text" name="focal_measurement" 
                           placeholder="القياسات البؤرية" 
                           class="form-control form-control-sm" required>
                </div>
                
                <!-- Recommended Conditions -->
                <div class="col-2">
                    <input type="text" name="recommended_conditions" 
                           placeholder="الحالات الموصى بها" 
                           class="form-control form-control-sm" required>
                </div>
                
                <!-- Features -->
                <div class="col-2">
                    <input type="text" name="features" placeholder="المميزات" 
                           class="form-control form-control-sm" required>
                </div>
                
                <!-- Quality Rating -->
                <div class="col-2">
                    <select name="quality_rating" class="form-select form-select-sm" required>
                        <option value="">التقييم</option>
                        <?php for($i=1;$i<=5;$i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> نجوم</option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <!-- Submit Button -->
                <div class="col-2">
                    <button type="submit" name="add" 
                            class="btn btn-sm btn-primary"  style="background:#1e3799; color:white;">إضافة</button>
                </div>
            </div>
        </form>

        <!-- Main Table -->
        <div class="table-responsive">
            <table class="manage-table table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>النوع</th>
                        <th>المادة</th>
                        <th>نفاذية الأكسجين</th>
                        <th>جدول الاستبدال</th>
                        <th>القياسات البؤرية</th>
                        <th>الحالات الموصى بها</th>
                        <th>المميزات</th>
                        <th>التقييم</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td class="editable-cell" onclick="startEdit(this, 'lens_type', <?= $row['id'] ?>)"><?= $row['lens_type'] ?></td>
                        <td class="editable-cell" onclick="startEdit(this, 'material', <?= $row['id'] ?>)"><?= $row['material'] ?></td>
                        <td class="editable-cell" onclick="startEdit(this, 'oxygen_permeability', <?= $row['id'] ?>)"><?= $row['oxygen_permeability'] ?></td>
                        <td class="editable-cell" onclick="startEdit(this, 'replacement_schedule', <?= $row['id'] ?>)"><?= $row['replacement_schedule'] ?></td>
                        <td class="editable-cell" onclick="startEdit(this, 'focal_measurement', <?= $row['id'] ?>)"><?= $row['focal_measurement'] ?></td>
                        <td class="editable-cell" onclick="startEdit(this, 'recommended_conditions', <?= $row['id'] ?>)"><?= $row['recommended_conditions'] ?></td>
                        <td class="editable-cell" onclick="startEdit(this, 'features', <?= $row['id'] ?>)"><?= $row['features'] ?></td>
                        <td class="editable-cell" onclick="startEditRating(<?= $row['id'] ?>)">
                            <?= str_repeat('★', $row['quality_rating']) ?>
                        </td>
                        <td class="action-btns">
                            <button onclick="deleteRecord(<?= $row['id'] ?>)" 
                                    class="btn btn-sm btn-danger">حذف</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function startEdit(cell, field, id) {
        const original = cell.innerHTML;
        const input = document.createElement('input');
        input.className = 'edit-input';
        input.value = original;
        
        cell.innerHTML = '';
        cell.appendChild(input);
        input.focus();
        
        input.addEventListener('blur', () => saveEdit(cell, field, id, input.value));
    }

    function saveEdit(cell, field, id, value) {
        const formData = new FormData();
        formData.append('update', true);
        formData.append('id', id);
        formData.append('field', field);
        formData.append('value', value);

        fetch('', {
            method: 'POST',
            body: new URLSearchParams(formData)
        })
        .then(response => {
            if(response.ok) {
                cell.innerHTML = value;
            } else {
                alert('Error saving changes');
                cell.innerHTML = original;
            }
        });
    }

    function deleteRecord(id) {
        if(confirm('هل أنت متأكد من الحذف؟')) {
            window.location.href = `?delete=${id}`;
        }
    }

    function startEditRating(id) {
        const newRating = prompt('أدخل التقييم الجديد (1-5):');
        if(newRating >=1 && newRating <=5) {
            saveEditRating(id, newRating);
        }
    }

    function saveEditRating(id, rating) {
        const formData = new FormData();
        formData.append('update', true);
        formData.append('id', id);
        formData.append('field', 'quality_rating');
        formData.append('value', rating);

        fetch('', {
            method: 'POST',
            body: new URLSearchParams(formData)
        })
        .then(() => window.location.reload());
    }
    </script>
</body>
</html>
