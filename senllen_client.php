<?php
// index.php
session_start();
include 'control/config.php';

$patient_id = $_SESSION['patient_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_results'])) {
    $results = $_POST['results'];
    $stmt = $con->prepare("INSERT INTO reports_senllen(patient_id, results) VALUES (?, ?)");
    $stmt->bind_param("is", $patient_id, $results);
    $stmt->execute();
    header("Location:senllen_client.php");
    exit();
}

$patient = $con->query("SELECT * FROM patient WHERE id = $patient_id")->fetch_assoc();
$reports = $con->query("SELECT * FROM reports_senllen WHERE patient_id = $patient_id ORDER BY test_date DESC");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>اختبار سنلين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --primary-color: rgb(35, 140, 210); }
        .primary-bg { background-color: var(--primary-color); }
        .chart-row { transition: all 0.3s; cursor: pointer; }
        .chart-row.selected { background-color: #e3f2fd!important; }
        .test-table { font-size: 0.9rem; }
        .report-card { border-left: 4px solid var(--primary-color); }
        .vision-percentage { font-size: 1.2rem; font-weight: bold; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
    <a href="test_interface.html" style="margin-right:95%;"> <image src="undo.png"></image></a>
        <div class="card shadow mt-3" style='margin-bottom:20px;'>
            <div class="card-header primary-bg text-white">
                <h5 class="mb-0">معلومات عن فحص النظر</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5 class="alert-heading">تعليمات الفحص:</h5>
                    <ol class="mb-0">
                        <li>اجلس على بعد 6 أقدام (1.8 متر) من الشاشة</li>
                        <li>غطي عين واحدة باستخدام اليد أو غطاء العين</li>
                        <li>اقرأ الحروف بدءًا من الأعلى إلى الأسفل</li>
                        <li>اضغط على السطر الذي يمكنك قراءته بوضوح</li>
                        <li>كرر العملية للعين الأخرى</li>
                    </ol>
                </div>

                <div class="mt-3">
                    <h5 class="primary-text">معلومات عن الفحص:</h5>
                    <p class="small">
                        فحص حدة البصر (الرؤية) هو قياس لقدرة العين على تمييز تفاصيل الأشكال والحروف. 
                        يتم التعبير عن النتيجة بنسبة مقارنة بالرؤية الطبيعية (20/20). كلما زاد الرقم في 
                        المقام (مثل 20/40) يشير ذلك إلى انخفاض في حدة البصر.
                    </p>
                    
                    <h5 class="primary-text mt-3">ماذا تقيس النتائج؟</h5>
                    <ul class="small">
                        <li>قدرتك على رؤية التفاصيل الدقيقة</li>
                        <li>وضوح الرؤية المركزية</li>
                        <li>الحاجة إلى نظارات طبية</li>
                        <li>تغيرات الرؤية مع مرور الوقت</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow">
                    <div class="card-header primary-bg text-white">
                        <h5 class="mb-0">إجراء فحص جديد</h5>
                    </div>
                    <div class="card-body">
                        <div id="eyeChart" class="text-center">
                            <?php 
                            $rows = [
                                ['value' => 200, 'size' => '2.5em', 'letters' => 'E'],
                                ['value' => 100, 'size' => '2em', 'letters' => 'F P'],
                                ['value' => 70, 'size' => '1.5em', 'letters' => 'T O Z'],
                                ['value' => 50, 'size' => '1.2em', 'letters' => 'L P E D'],
                                ['value' => 40, 'size' => '1em', 'letters' => 'P E C F D'],
                                ['value' => 30, 'size' => '0.8em', 'letters' => 'E D F C Z P'],
                                ['value' => 20, 'size' => '0.6em', 'letters' => 'F B O T E L C D'],
                                ['value' => 15, 'size' => '0.4em', 'letters' => 'c m n r g h z k l'],
                                ['value' => 10, 'size' => '0.3em', 'letters' => 'b p w d q a z m k t s']
                            ];
                            foreach ($rows as $row): ?>
                                <div class="chart-row p-2 mb-1 bg-white rounded shadow-sm" 
                                     data-value="<?= $row['value'] ?>"
                                     style="font-size: <?= $row['size'] ?>">
                                    <div class="text-muted small">20/<?= $row['value'] ?></div>
                                    <?= $row['letters'] ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="btn primary-bg text-white w-100 mt-3" onclick="submitTest()">
                            حفظ النتائج
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header primary-bg text-white">
                        <h5 class="mb-0">سجل الفحوصات</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table test-table">
                                <thead>
                                    <tr>
                                        <th>تاريخ الفحص</th>
                                        <th>نسبة الرؤية</th>
                                        <th>التوصيات</th>
                                        <th>النتائج</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($report = $reports->fetch_assoc()): 
                                        $results = json_decode($report['results'], true);
                                        $results = is_array($results) ? $results : [];
                                        $smallest_line = 200;
                                        foreach ($results as $row) {
                                            if ($row['correct'] && $row['value'] < $smallest_line) {
                                                $smallest_line = $row['value'];
                                            }
                                        }
                                        $vision_percentage = (20/$smallest_line)*100;
                                        
                                        // Determine lens recommendation
                                        if ($vision_percentage >= 100) {
                                            $lens_recommendation = "لا حاجة إلى عدسات طبية";
                                        } elseif ($vision_percentage >= 80) {
                                            $lens_recommendation = "عدسات قراءة بسيطة";
                                        } elseif ($vision_percentage >= 60) {
                                            $lens_recommendation = "عدسات طبية حسب الوصفة";
                                        } else {
                                            $lens_recommendation = "عدسات متخصصة - مراجعة طبيب العيون";
                                        }
                                    ?>
                                    <tr class="report-card">
                                        <td><?= date('Y-m-d H:i', strtotime($report['test_date'])) ?></td>
                                        <td><?= round($vision_percentage, 1) ?>%</td>
                                        <td>
                                            <?php if($vision_percentage >= 100): ?>
                                                رؤية ممتازة
                                            <?php elseif($vision_percentage >= 80): ?>
                                                رؤية جيدة
                                            <?php elseif($vision_percentage >= 60): ?>
                                                فحص تفصيلي
                                            <?php else: ?>
                                                مراجعة عاجلة
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm primary-bg text-white" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#details-<?= $report['id'] ?>">
                                                عرض التفاصيل
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="collapse" id="details-<?= $report['id'] ?>">
                                        <td colspan="4">
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <div class="card border-primary">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <span class="vision-percentage text-primary">
                                                                        نسبة الرؤية: <?= round($vision_percentage, 1) ?>%
                                                                    </span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <span class="text-success">
                                                                        العدسات المناسبة: <?= $lens_recommendation ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php foreach ($results as $row): ?>
                                                    <div class="col-3 mb-2">
                                                        <div class="card <?= $row['correct'] ? 'border-success' : 'border-danger' ?>">
                                                            <div class="card-body p-2 text-center">
                                                                <small>20/<?= $row['value'] ?></small><br>
                                                                <strong><?= $row['correct'] ? 'صحيح' : 'خطأ' ?></strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.chart-row').forEach(row => {
            row.addEventListener('click', function() {
                this.classList.toggle('selected');
            });
        });

        function submitTest() {
            const results = [];
            document.querySelectorAll('.chart-row').forEach(row => {
                results.push({
                    value: row.dataset.value,
                    correct: row.classList.contains('selected')
                });
            });

            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="save_results">
                <input type="hidden" name="results" value='${JSON.stringify(results)}'>
            `;
            document.body.appendChild(form);
            form.submit();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>