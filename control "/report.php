<?php
include 'config.php';

// Function to safely display data
function safeDisplay($data) {
    return htmlspecialchars($data ?? '');
}

// Get all patients
$patients = [];
$patients_query = "SELECT * FROM patient";
$patients_result = mysqli_query($con, $patients_query);
if ($patients_result) {
    while ($row = mysqli_fetch_assoc($patients_result)) {
        $patients[] = $row;
    }
}

// Define all related tables and their Arabic display names
$tables = [
    'report_color1' => 'تقارير رؤية الألوان',
    'test_reports' => 'التقارير المخبرية',
    'reports_senllen' => 'تقارير سينلين',
    'report_senllen2' => 'تقارير سينلين 2'
];

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقارير المرضى</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Amiri&display=swap" rel="stylesheet">
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
            font-family: 'Amiri', serif;
            line-height: 2;
        }
        .container
        {
            width:97%;
        }
        .patient-card {
            margin-bottom: 2rem;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table-title {
            background-color: #1e3799;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 1.2rem;
        }
        .table-custom th {
            background-color: #1e3799;
            color: white;
            text-align: right;
        }
        .table-custom td {
            text-align: right;
        }
        .alert-info {
            font-size: 1.1rem;
        }
        h1, h3, h4 {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div id="nav"></div>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">سجلات المرضى الكاملة</h1>

        <?php foreach ($patients as $patient): ?>
        <div class="patient-card">
            <div class="card-header  text-dark">
                <h3><?= safeDisplay($patient['name']) ?></h3>
                <p class="mb-0">رقم المريض: <?= $patient['id'] ?></p>
            </div>

            <div class="card-body">
                <!-- معلومات المريض -->
                <div class="table-section">
                    <h4 class="table-title">معلومات المريض</h4>
                    <div class="table-responsive">
                        <table class="table table-custom table-striped">
                            <thead>
                                <tr>
                                    <?php foreach (array_keys($patient) as $column): ?>
                                        <th><?= safeDisplay(translateColumn($column)) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php foreach ($patient as $value): ?>
                                        <td><?= safeDisplay($value) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- التقارير الأخرى -->
                <?php foreach ($tables as $table => $title): 
                    $conn = mysqli_connect($servername, $username, $password, $dbname);
                    mysqli_set_charset($conn, "utf8");
                    $data = [];
                    $query = "SELECT * FROM $table WHERE patient_id = " . $patient['id'];
                    $result = mysqli_query($conn, $query);
                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $data[] = $row;
                        }
                    }
                    mysqli_close($conn);
                ?>
                
                <div class="table-section">
                    <h4 class="table-title"><?= $title ?></h4>
                    
                    <?php if (!empty($data)): ?>
                    <div class="table-responsive">
                        <table class="table table-custom table-striped">
                            <thead>
                                <tr>
                                    <?php foreach (array_keys($data[0]) as $column): ?>
                                        <th><?= safeDisplay(translateColumn($column)) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $row): ?>
                                <tr>
                                    <?php foreach ($row as $value): ?>
                                        <td><?= safeDisplay($value) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-info">لا توجد سجلات</div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
function translateColumn($column) {
    $translations = [
        'id' => 'رقم التعريف',
        'name' => 'الاسم',
        'dob' => 'تاريخ الميلاد',
        'email' => 'البريد الإلكتروني',
        'phone' => 'الهاتف',
        'created_at' => 'تاريخ الإنشاء',
        'report_id' => 'رقم التقرير',
        'test_id' => 'رقم الاختبار',
        'correct_answers' => 'الإجابات الصحيحة',
        'incorrect_answers' => 'الإجابات الخاطئة',
        'vision_percentage' => 'نسبة الرؤية',
        'color_type' => 'نوع اللون',
        'test_date' => 'تاريخ الاختبار',
        'results' => 'النتائج'
    ];
    
    return $translations[$column] ?? $column;
}
?>