<?php
include 'config.php';
// استرجاع بيانات اختبار الألوان
$اختبار_الألوان = [];
$نتيجة = $con->query("SELECT * FROM color1_test");
if ($نتيجة->num_rows > 0) {
    while($صف = $نتيجة->fetch_assoc()) {
        $اختبار_الألوان[] = $صف;
    }
}


// استرجاع التقارير
$التقارير = [];
$نتيجة = $con->query("SELECT * FROM report_color1");
if ($نتيجة->num_rows > 0) {
    while($صف = $نتيجة->fetch_assoc()) {
        $التقارير[] = $صف;
    }
}

$con->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحةالتحكم-نتائج اختبارات تمييز الألوان،</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script >
                        $(document).ready(function()
                        {
                            $("#nav").load("control_panel.html");
                        }
                       );
    </script>
    <style>
        :root {
            --اللون-الرئيسي: #1e3799;
        }
        
        .عنوان-رئيسي {
            color: var(--اللون-الرئيسي);
            border-bottom: 3px solid var(--اللون-الرئيسي);
            padding-bottom: 10px;
            margin: 30px 0;
            font-size: 28px;
        }
        
        .جدول-مخصص {
            border: 2px solid var(--اللون-الرئيسي);
            border-collapse: collapse;
            margin: 25px 0;
            width: 100%;
            font-size: 16px;
        }
        
        .جدول-مخصص thead th {
            background-color: var(--اللون-الرئيسي);
            color: white;
            padding: 15px;
            font-weight: bold;
        }
        
        .جدول-مخصص td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        
        .جدول-مخصص tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .لون-خلفية {
            width: 60px;
            height: 30px;
            margin: 0 auto;
            border: 1px solid #666;
        }
        
        .محتوى-رئيسي {
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div id="nav"></div>
    <div class="محتوى-رئيسي">
        <h2 class="عنوان-رئيسي">لوحةالتحكم-نتائج اختبارات تمييز الألوان</h2>
        <table class="جدول-مخصص">
            <thead>
                <tr>
                    <th>رقم المعرف</th>
                    <th>رقم المريض</th>
                    <th>اللون الأول</th>
                    <th>اللون الثاني</th>
                    <th>نسبة التشابه</th>
                    <th>الحالة</th>
                    <th>عدد المحاولات</th>
                    <th>تاريخ الإجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($اختبار_الألوان as $صف): ?>
                <tr>
                    <td><?= $صف['test_id'] ?></td>
                    <td><?= $صف['patient_id'] ?></td>
                    <td>
                        <div class="لون-خلفية" style="background-color: <?= $صف['color1'] ?>"></div>
                        <small><?= $صف['color1'] ?></small>
                    </td>
                    <td>
                        <div class="لون-خلفية" style="background-color: <?= $صف['color2'] ?>"></div>
                        <small><?= $صف['color2'] ?></small>
                    </td>
                    <td><?= number_format($صف['similarity'], 2) ?>%</td>
                    <td>
                        <?php 
                        $حالة = [
                            'passed' => 'ناجح',
                            'failed' => 'فشل',
                            'pending' => 'قيد المراجعة'
                        ];
                        echo $حالة[$صف['status']] ?? $صف['status'];
                        ?>
                    </td>
                    <td><?= $صف['attempt_number'] ?></td>
                    <td><?= date('Y/m/d H:i', strtotime($صف['test_date'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

       
    </div>
</body>
</html>