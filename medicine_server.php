<?php
include 'config.php';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new medicine
    if (isset($_POST['add'])) {
        $stmt = $con->prepare("INSERT INTO medicines (name, generic_name, uses, dosage, side_effects, manufacturer) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $_POST['name'], $_POST['generic'], $_POST['uses'], $_POST['dosage'], $_POST['side_effects'], $_POST['manufacturer']);
        $stmt->execute();
    }
    // Update medicine
    elseif (isset($_POST['update'])) {
        $allowedFields = ['name', 'generic_name', 'uses', 'dosage', 'side_effects', 'manufacturer'];
        $field = $_POST['field'];
        if (!in_array($field, $allowedFields)) {
            die('Invalid field');
        }
        $stmt = $con->prepare("UPDATE medicines SET $field = ? WHERE id = ?");
        $stmt->bind_param("si", $_POST['value'], $_POST['id']);
        $stmt->execute();
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $con->prepare("DELETE FROM medicines WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
}

// Fetch all medicines
$result = $con->query("SELECT * FROM medicines");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم-الأدوية</title>
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
        body { background-color: #f0f8ff; font-family: 'Arial', sans-serif; }
        .container { max-width: 800px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        form { border: 2px groove #8cb4e8; }
        .form-control { border: 1px groove #8cb4e8; }
        table { background: white; border: 2px groove #8cb4e8; }
        .table th { background: #1e3799; color: white; }
        .edit-input { 
            width: 100%; 
            padding: 3px; 
            border: 2px groove #8cb4e8;
            text-align: right;
            direction: rtl;
        }
        .btn-primary { background: #1e3799; border: none; }
        td { cursor: pointer; }
    </style>
</head>
<body>
    <div id="nav"></div>
    <div class="container mt-5">
        <h3 class="text-center mb-4" style="color: #1e3799; font-size:40px; font-weight:bold;">لوحة التحكم-الأدوية</h3>
        
        <!-- Add Medicine Form -->
        <form method="POST" class="mb-4 p-3 bg-white shadow-sm">
            <div class="row g-2">
                <div class="col-md-2">
                    <input type="text" name="name" placeholder="الاسم" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="generic" placeholder="الاسم العام" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="uses" placeholder="الاستخدامات" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="dosage" placeholder="الجرعة" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="side_effects" placeholder="الآثار الجانبية" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="manufacturer" placeholder="الشركة المصنعة" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-12 mt-2">
                    <button type="submit" name="add" class="btn btn-primary btn-sm w-100">إضافة دواء جديد</button>
                </div>
            </div>
        </form>

        <!-- Medicines Table -->
        <table class="table table-sm table-bordered table-hover shadow">
            <thead>
                <tr>
                    <th>الرقم</th>
                    <th>الاسم</th>
                    <th>الاسم العام</th>
                    <th>الاستخدامات</th>
                    <th>الجرعة</th>
                    <th>الآثار الجانبية</th>
                    <th>الشركة المصنعة</th>
                    <th>الإجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td data-id="<?= $row['id'] ?>" data-field="name" class="editable"><?= htmlspecialchars($row['name']) ?></td>
                    <td data-id="<?= $row['id'] ?>" data-field="generic_name" class="editable"><?= htmlspecialchars($row['generic_name']) ?></td>
                    <td data-id="<?= $row['id'] ?>" data-field="uses" class="editable"><?= htmlspecialchars($row['uses']) ?></td>
                    <td data-id="<?= $row['id'] ?>" data-field="dosage" class="editable"><?= htmlspecialchars($row['dosage']) ?></td>
                    <td data-id="<?= $row['id'] ?>" data-field="side_effects" class="editable"><?= htmlspecialchars($row['side_effects']) ?></td>
                    <td data-id="<?= $row['id'] ?>" data-field="manufacturer" class="editable"><?= htmlspecialchars($row['manufacturer']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id'] ?>">حذف</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
    // Edit Functionality
    document.querySelectorAll('.editable').forEach(cell => {
        cell.addEventListener('click', function() {
            const id = this.dataset.id;
            const field = this.dataset.field;
            const originalValue = this.textContent.trim();

            const input = document.createElement('input');
            input.className = 'edit-input';
            input.value = originalValue;

            this.innerHTML = '';
            this.appendChild(input);
            input.focus();

            const saveHandler = () => {
                const newValue = input.value.trim();
                if (newValue === originalValue) {
                    this.textContent = originalValue;
                    return;
                }

                const formData = new FormData();
                formData.append('update', true);
                formData.append('id', id);
                formData.append('field', field);
                formData.append('value', newValue);

                fetch(location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) this.textContent = newValue;
                    else throw new Error('فشل في التحديث');
                })
                .catch(() => {
                    alert('حدث خطأ أثناء الحفظ');
                    this.textContent = originalValue;
                });
            };

            input.addEventListener('blur', saveHandler);
            input.addEventListener('keypress', e => e.key === 'Enter' && saveHandler());
        });
    });

    // Delete Functionality
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            if (confirm('هل تريد حذف هذا الدواء؟')) {
                fetch(`?delete=${id}`)
                    .then(response => {
                        if (response.ok) this.closest('tr').remove();
                        else alert('فشل الحذف');
                    })
                    .catch(() => alert('حدث خطأ في الاتصال'));
            }
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $con->close(); ?>