<?php
session_start();
include 'control/config.php';

$showTestForm = true;
$patient_id = $_SESSION['patient_id'];

// Handle test submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_test'])) {
    // Validate and sanitize inputs
    $color1 = isset($_POST['color1']) ? $con->real_escape_string($_POST['color1']) : '#ffffff';
    $color2 = isset($_POST['color2']) ? $con->real_escape_string($_POST['color2']) : '#ffffff';

    // Validate hex colors
    if (!preg_match('/^#[a-f0-9]{6}$/i', $color1) || !preg_match('/^#[a-f0-9]{6}$/i', $color2)) {
        die("Invalid color format");
    }

    // Calculate similarity
    function calculateSimilarity($color1, $color2) {
        $rgb1 = [
            hexdec(substr($color1, 1, 2)),
            hexdec(substr($color1, 3, 2)),
            hexdec(substr($color1, 5, 2))
        ];
        
        $rgb2 = [
            hexdec(substr($color2, 1, 2)),
            hexdec(substr($color2, 3, 2)),
            hexdec(substr($color2, 5, 2))
        ];
        
        $distance = sqrt(
            pow($rgb1[0] - $rgb2[0], 2) +
            pow($rgb1[1] - $rgb2[1], 2) +
            pow($rgb1[2] - $rgb2[2], 2)
        );
        
        return max(0, min(100, round(100 - ($distance / 441.67 * 100))));
    }

    $similarity = calculateSimilarity($color1, $color2);
    $status = $similarity >= 90 ? 'ممتاز' : ($similarity >= 60 ? 'جيد' : 'ضعيف');

    // Get attempt number using prepared statement
    $stmt = $con->prepare("SELECT COUNT(*) FROM color1_test WHERE patient_id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $attempt = $stmt->get_result()->fetch_row()[0] + 1;
    $stmt->close();

    // Save test using prepared statement
    $stmt = $con->prepare("INSERT INTO color1_test (patient_id, color1, color2, similarity, status, attempt_number) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdsi", $patient_id, $color1, $color2, $similarity, $status, $attempt);
    $stmt->execute();
    $test_id = $stmt->insert_id;
    $stmt->close();
    
    // Generate and save report
    $report_data = json_encode([
        'color1' => $color1,
        'color2' => $color2,
        'similarity' => $similarity,
        'status' => $status,
        'attempt' => $attempt
    ]);
    
    $stmt = $con->prepare("INSERT INTO report_color1 (patient_id, test_id, report_data) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $patient_id, $test_id, $report_data);
    $stmt->execute();
    $stmt->close();

    $_SESSION['show_report'] = true;
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// View state management
if (isset($_GET['show_form'])) {
    $showTestForm = true;
} elseif (isset($_SESSION['show_report'])) {
    $showTestForm = false;
    unset($_SESSION['show_report']);
}

// Fetch previous tests using prepared statement
$stmt = $con->prepare("SELECT * FROM color1_test WHERE patient_id = ? ORDER BY test_date DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$previousTests = $stmt->get_result();
$stmt->close();

// Recommendation array moved here
$recommendations = [
    'ممتاز' => 'نتيجة ممتازة! نوصي بإجراء فحص دوري كل 6 أشهر.',
    'جيد' => 'نتيجة جيدة، نوصي بفحص إضاءة الشاشة وإعادة الاختبار.',
    'ضعيف' => 'نوصي بمراجعة أخصائي عيون لإجراء فحص مفصل.'
];
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام اختبار الألوان</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .color-box { 
            width: 100px; 
            height: 100px; 
            border: 3px solid #ddd;
            border-radius: 10px;
            transition: transform 0.3s; 
        }
        .color-box:hover { transform: scale(1.05); }
        .test-instructions { background: #f8f9fa; border-radius: 10px; }
        .report-card { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); }
        .status-badge { font-size: 1rem; padding: 0.5rem 1rem; }
        .form-control-color{width:100%;}
    </style>
</head>
<body class="bg-light">
    <a href="test_interface.html" style="margin-right:95%;"> <img src="undo.png" alt="رجوع"></a>
    <div class="container py-5">
    <?php if ($showTestForm): ?>
        <!-- Test Form Section -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card report-card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"><i class="fas fa-eye-dropper"></i> اختبار تمييز الألوان</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <form method="POST">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">اللون الأول:</label>
                                        <input type="color" name="color1" class="form-control form-control-color" value="#ff0000" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">اللون الثاني:</label>
                                        <input type="color" name="color2" class="form-control form-control-color" value="#00ff00" required>
                                    </div>
                                    <button type="submit" name="submit_test" class="btn btn-success btn-lg w-100">
                                        <i class="fas fa-vial"></i> بدء الاختبار
                                    </button>
                                </form>
                            </div>
                            <div class="accordion" id="testInfo">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#purpose">
                                        الغرض من الاختبار
                                    </button>
                                </h2>
                                <div id="purpose" class="accordion-collapse collapse show" data-bs-parent="#testInfo">
                                    <div class="accordion-body">
                                    اختبار مطابقة الألوان (Color Matching Test)
                                    هو اختبار يُستخدم لتقييم قدرة الفرد على تمييز الألوان ومطابقتها بدقة تحت ظروف إضاءة مختلفة. يُعتبر هذا النوع من الاختبارات مفيدًا في تشخيص اضطرابات رؤية الألوان أو قياس الدقة في التمييز بين درجات الألوان المتقاربة، خاصة في المجالات التي تتطلب حساسية عالية للألوان مثل التصميم الجرافيكي أو الطب أو الصناعات الإلكترونية.

                                    كيفية عمل الاختبار:
                                    الهدف: مطابقة لونين صادرين عن مصدرين ضوئيين مختلفين (مثل إضاءة دافئة وباردة) لإنشاء نفس الإحساس اللوني.

                                    التنفيذ:

                                    يعرض الاختبار للمُختَبَر مصدرين ضوئيين (أو لونين) ويطلب منه ضبطهما حتى يتطابقا.

                                    تظهر نتائج فورية مثل "تطابق جيد" أو "لا يوجد تطابق ممكن"، مما يشير إلى قدرة المستخدم على التمييز بين الألوان.

                                    قد يُظهر مؤشر تقدم (مثل 0% تقدم) مراحل الاختبار أو مستوى الدقة في المطابقة.

                                    ما الذي يكشفه الاختبار؟
                                    عمى الألوان الجزئي: مثل صعوبة التمييز بين الأحمر والأخضر أو الأزرق والأصفر.
                                                
                                    ضعف الإدراك اللوني: بسبب عوامل مثل إجهاد العين أو الشيخوخة أو أمراض الشبكية.

                                    قدرات التمييز الدقيق: مهمة في المهن الفنية أو التقنية التي تعتمد على الألوان.


                                    </div>
                                </div>
                            </div>
                                    <h4 class="text-primary"><i class="fas fa-info-circle"></i> تعليمات الاختبار</h4>
                                    <ol class="mt-3">
                                        <li class="mb-2">اختر اللون الأول من مربع الألوان العلوي</li>
                                        <li class="mb-2">اختر اللون الثاني من مربع الألوان السفلي</li>
                                        <li class="mb-2">انقر على زر "بدء الاختبار"</li>
                                        <li>ستظهر النتائج مع تحليل مفصل</li>
                                    </ol>
                                    <hr>
                                    <h5 class="text-primary"><i class="fas fa-lightbulb"></i> نصائح:</h5>
                                    <ul class="mt-2">
                                        <li class="mb-2">تأكد من إضاءة جيدة في الغرفة</li>
                                        <li class="mb-2">استخدم شاشة معايرة بشكل صحيح</li>
                                        <li>خذ وقتك في اختيار الألوان</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card report-card">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h3 class="mb-0"><i class="fas fa-file-medical"></i> تقرير الاختبار</h3>
                            <a href="?show_form=1" class="btn btn-light">
                                <i class="fas fa-plus"></i> اختبار جديد
                            </a>
                        </div>
                        <div class="card-body">
                            <!-- Previous Tests -->
                            <h4 class="mt-5 mb-3"><i class="fas fa-history"></i> المحاولات السابقة</h4>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#المحاولة</th>
                                            <th>التاريخ</th>
                                            <th>النسبة</th>
                                            <th>الحالة</th>
                                            <th>الألوان</th>
                                            <th>التوصيات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($previousTests->num_rows > 0): ?>
                                            <?php while($test = $previousTests->fetch_assoc()): 
                                                $report = json_decode(
                                                    $con->query("SELECT report_data FROM report_color1 WHERE test_id = ".$test['test_id'])->fetch_assoc()['report_data'],
                                                    true
                                                );
                                            ?>
                                            <tr>
                                                <td>#<?= $report['attempt'] ?></td>
                                                <td><?= date('Y/m/d H:i', strtotime($test['test_date'])) ?></td>
                                                <td><?= $report['similarity'] ?>%</td>
                                                <td>
                                                    <span class="badge bg-<?= $report['status'] === 'ممتاز' ? 'success' : ($report['status'] === 'جيد' ? 'warning' : 'danger') ?>">
                                                        <?= $report['status'] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2 justify-content-center">
                                                        <div class="color-box" style="width:30px;height:30px;background:<?= $report['color1'] ?>"></div>
                                                        <div class="color-box" style="width:30px;height:30px;background:<?= $report['color2'] ?>"></div>
                                                    </div>
                                                </td>
                                                <td class="recommendation-box small">
                                                    <?= $recommendations[$report['status'] ?? 'ضعيف'] ?? 'لا توجد توصيات' ?>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">لا توجد اختبارات سابقة</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>