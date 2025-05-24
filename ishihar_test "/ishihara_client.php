<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vision_system";

// Create connection
$con = mysqli_connect($servername, $username, $password, $dbname);
if (!$con) die("فشل الاتصال: " . mysqli_connect_error());
mysqli_set_charset($con, "utf8mb4");

// Fetch test images
$test_images = array();
$result = mysqli_query($con, "SELECT image_path, correct_answer FROM ishihara_images ORDER BY image_id");
while ($row = mysqli_fetch_assoc($result)) {
    $test_images[] = $row;
}

// Save test results function
function saveTestResult($con, $data) {
    mysqli_query($con, "START TRANSACTION");
    
    // Insert to ishihara_tests
    $test_query = "INSERT INTO ishihara_tests (patient_id) VALUES ('".mysqli_real_escape_string($con, $data['patient_id'])."')";
    if(!mysqli_query($con, $test_query)) {
        mysqli_query($con, "ROLLBACK");
        return false;
    }
    $test_id = mysqli_insert_id($con);
    
    // Insert to test_reports with patient_id
    $report_query = "INSERT INTO test_reports 
                    (test_id, patient_id, correct_answers, incorrect_answers, vision_percentage, color_type)
                    VALUES (
                        '".$test_id."',
                        '".mysqli_real_escape_string($con, $data['patient_id'])."',
                        '".mysqli_real_escape_string($con, $data['correct'])."',
                        '".mysqli_real_escape_string($con, $data['incorrect'])."',
                        '".mysqli_real_escape_string($con, $data['vision_percent'])."',
                        '".mysqli_real_escape_string($con, $data['color_type'])."'
                    )";
    
    if(!mysqli_query($con, $report_query)) {
        mysqli_query($con, "ROLLBACK");
        return false;
    }
    
    mysqli_query($con, "COMMIT");
    return true;
}

// Fetch previous tests
$previous_tests = array();
if (isset($_SESSION['patient_id'])) {
    $query = "SELECT t.test_id, t.patient_id, t.test_date, 
             r.correct_answers, r.incorrect_answers, 
             r.vision_percentage, r.color_type
             FROM ishihara_tests t
             JOIN test_reports r ON t.test_id = r.test_id
             WHERE t.patient_id = '".mysqli_real_escape_string($con, $_SESSION['patient_id'])."'
             ORDER BY t.test_date DESC";
    
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $previous_tests[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result_data = array(
        'patient_id' => $_SESSION['patient_id'],
        'correct' => intval($_POST['correct']),
        'incorrect' => intval($_POST['incorrect']),
        'vision_percent' => floatval($_POST['vision_percent']),
        'color_type' => $_POST['color_type']
    );
    
    if(saveTestResult($con, $result_data)) {
        $_SESSION['message'] = "تم حفظ النتائج بنجاح!";
        $_SESSION['show_report'] = true;
    } else {
        $_SESSION['error'] = "حدث خطأ أثناء حفظ النتائج!";
    }
    
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام اختبار عمى الألوان</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        :root { --main-color: #236ed2; --secondary-color: #1a365d; }
        body { background: linear-gradient(45deg, var(--main-color), var(--secondary-color)); min-height: 100vh; }
        .test-card { background: rgba(255,255,255,0.98); border-radius: 15px; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); }
        .progress-bar { height: 15px; transition: width 0.3s ease; }
        .answer-btn { background: var(--main-color); color: white; transition: all 0.2s ease; }
        .answer-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .test-image { border: 3px solid var(--main-color); border-radius: 10px; max-width: 300px; }
        .table-custom th { background-color: var(--main-color); color: white; text-align: right; }
    </style>
</head>
<body class="py-4">
    <a href="\test_interface.html" style="margin-right:95%;"><img src="undo.png" alt="رجوع"></a>
    <div class="container">
        <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Test Interface -->
        <div class="test-card p-4 mb-4 <?php echo isset($_SESSION['show_report']) ? 'd-none' : '' ?>">
            <div class="row g-4">
                <div class="col-md-6">
                    <h1 class="h2 text-center mb-4 text-dark">اختبار إيشيهارا لعمى الألوان</h1>
                    <div class="progress mb-4">
                        <div id="progress-bar" class="progress-bar bg-primary progress-bar-striped" style="width: 0%"></div>
                    </div>
                    <img id="test-image" src="<?php echo $test_images[0]['image_path']; ?>" class="test-image img-fluid d-block mx-auto mb-4" alt="صفحة اختبار">
                    <div class="row g-2">
                        <?php for($i=1; $i<=9; $i++): ?>
                        <div class="col-4">
                            <button onclick="submitAnswer(<?php echo $i; ?>)" class="answer-btn btn w-100 py-3 rounded-2">
                                <?php echo $i; ?>
                            </button>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="bg-light p-4 rounded-3">
                        <h2 class="h4 mb-3 text-dark">معلومات عن الاختبار</h2>
                        <div class="accordion" id="testInfo">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#purpose">
                                        الغرض من الاختبار
                                    </button>
                                </h2>
                                <div id="purpose" class="accordion-collapse collapse show" data-bs-parent="#testInfo">
                                    <div class="accordion-body">
                                    اختبار إيشيهارا للكشف عن عمى الألوان
                                    اختبار إيشيهارا هو أشهر اختبار عالمي للكشف عن عمى الألوان، وخاصةً حالات عمى الألوان الأحمر-الأخضر (الأنواع الأكثر شيوعًا). صمم هذا الاختبار الطبيب الياباني شينوبو إيشيهارا في عام 1917، ويُستخدم حتى اليوم في التشخيص الأولي لمشاكل رؤية الألوان.

                                    كيف يعمل الاختبار؟
                                    يتكون الاختبار من مجموعة من الصور (عادةً 38 صورة) تحتوي على دوائر ملونة بأحجام مختلفة تُشكل أرقامًا أو أشكالًا يراها الأشخاص ذوو الرؤية الطبيعية بوضوح. أما الأشخاص المصابون بعمى الألوان، فقد يجدون صعوبة في تمييز الرقم أو الشكل، أو قد يرون رقمًا مختلفًا تمامًا بسبب عدم قدرتهم على التمييز بين ألوان معينة.

                                    أنواع المشاكل التي يكشفها الاختبار:

                                    عمى الأحمر-الأخضر (Protanopia/Deuteranopia): عدم القدرة على التمييز بين الأحمر والأخضر.

                                    عمى الأزرق-الأصفر (Tritanopia): أقل شيوعًا، وقد لا يتم تشخيصه بدقة عبر هذا الاختبار.

                                    استخدامات الاختبار:

                                    التشخيص الطبي في العيادات والمستشفيات.

                                    فحص العمال في مجالات تتطلب تمييزًا دقيقًا للألوان (كالطيران أو الكهرباء).

                                    فحص الأطفال في المدارس لاكتشاف المشاكل مبكرًا.

                                    ملاحظات هامة:

                                    الاختبار غير كافٍ لتشخيص جميع أنواع عمى الألوان، فهناك اختبارات أخرى مثل اختبار فارنسورث-مونسل.

                                    يجب إجراء الفحص تحت إضاءة طبيعية لتجنب نتائج خاطئة.

                                    بعض الصور مصممة خصيصًا لاكتشاف محاولات توقع الإجابات من قبل المُختَبَر.

                                    يُعتبر اختبار إيشيهارا أداة بسيطة وفعّالة، لكنه يحتاج إلى تفسير من قبل أخصائي لتأكيد النتائج وتحديد نوع وشدة الحالة.


                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#instructions">
                                        تعليمات الاختبار
                                    </button>
                                </h2>
                                <div id="instructions" class="accordion-collapse collapse" data-bs-parent="#testInfo">
                                    <div class="accordion-body">
                                        <ul class="list-unstyled">
                                            <li class="mb-2">✔️ اجلس على بعد 75 سم من الشاشة</li>
                                            <li class="mb-2">🔍 اختر الرقم الظاهر في كل صورة</li>
                                            <li class="mb-2">⏱ متوسط وقت الاختبار: 5 دقائق</li>
                                            <li>📋 النتائج تظهر فور إنهاء جميع الأسئلة</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Results Section -->
        <div id="result-section" class="test-card p-4 <?php echo isset($_SESSION['show_report']) ? '' : 'd-none' ?>">
            <h2 class="h2 text-center mb-4 text-dark">نتائج الاختبار</h2>
            <form method="POST">
                <input type="hidden" name="patient_id" value="<?php echo $_SESSION['patient_id']; ?>">
                <input type="hidden" name="correct" id="hidden-correct">
                <input type="hidden" name="incorrect" id="hidden-incorrect">
                <input type="hidden" name="vision_percent" id="hidden-vision-percent">
                <input type="hidden" name="color_type" id="hidden-color-type">
                
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3">
                            <p class="fs-5 mb-3">الإجابات الصحيحة: <span id="res-correct" class="fw-bold text-primary">0</span></p>
                            <p class="fs-5 mb-3">الإجابات الخاطئة: <span id="res-incorrect" class="fw-bold text-danger">0</span></p>
                            <p class="fs-5 mb-3">نسبة الرؤية: <span id="res-vision" class="fw-bold text-success">0</span>%</p>
                            <p class="fs-5">نوع المشكلة: <span id="res-type" class="fw-bold text-dark">-</span></p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="bg-light p-4 rounded-3">
                            <h3 class="h4 mb-3 text-dark">التوصيات الطبية</h3>
                            <p id="recommendations" class="mb-0 text-secondary">...</p>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-3">
                    <button type="submit" class="btn btn-primary btn-lg py-3">حفظ النتائج</button>
                    <button type="button" onclick="resetTest()" class="btn btn-outline-secondary btn-lg py-3">إعادة الاختبار</button>
                </div>
            </form>

            <?php if(!empty($previous_tests)): ?>
            <div class="mt-5">
                <h3 class="h4 mb-3 text-dark">السجل التاريخي للاختبارات</h3>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>التاريخ</th>
                                <th>رقم المريض</th>
                                <th>الإجابات الصحيحة</th>
                                <th>الإجابات الخاطئة</th>
                                <th>النسبة المئوية</th>
                                <th>التشخيص</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($previous_tests as $test): ?>
                            <tr>
                                <td><?php echo date('Y/m/d H:i', strtotime($test['test_date'])); ?></td>
                                <td><?php echo $test['patient_id']; ?></td>
                                <td><?php echo $test['correct_answers']; ?></td>
                                <td><?php echo $test['incorrect_answers']; ?></td>
                                <td><?php echo $test['vision_percentage']; ?>%</td>
                                <td><span class="badge bg-primary"><?php echo $test['color_type']; ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const testImages = <?= json_encode($test_images) ?>;
    let currentTestIndex = 0;
    let correctAnswers = 0;
    let incorrectAnswers = 0;

    function updateProgress() {
        const progress = (currentTestIndex / testImages.length) * 100;
        document.getElementById('progress-bar').style.width = `${progress}%`;
    }

    function calculateResults() {
        const totalQuestions = testImages.length;
        const visionPercentage = ((correctAnswers / totalQuestions) * 100).toFixed(1);
        const colorType = getColorType(visionPercentage);
        const recommendations = {
            'رؤية طبيعية': 'لا توجد مشاكل في تمييز الألوان، النتائج ضمن المعدل الطبيعي',
            'عمى ألوان بسيط': 'يوصى بإجراء فحوصات دورية ومتابعة مع أخصائي العيون',
            'عمى ألوان متوسط': 'يوصى باستخدام أدوات مساعدة واجراء فحوصات متخصصة',
            'عمى ألوان شديد': 'يوصى بمراجعة عاجلة مع أخصائي العيون لإجراء تقييم كامل'
        };

        return {
            visionPercentage,
            colorType,
            recommendation: recommendations[colorType]
        };
    }

    function getColorType(percentage) {
        if (percentage >= 90) return 'رؤية طبيعية';
        if (percentage >= 70) return 'عمى ألوان بسيط';
        if (percentage >= 50) return 'عمى ألوان متوسط';
        return 'عمى ألوان شديد';
    }

    function showResults() {
        const results = calculateResults();
        
        // Update display
        document.getElementById('res-correct').textContent = correctAnswers;
        document.getElementById('res-incorrect').textContent = incorrectAnswers;
        document.getElementById('res-vision').textContent = results.visionPercentage;
        document.getElementById('res-type').textContent = results.colorType;
        document.getElementById('recommendations').textContent = results.recommendation;

        // Update hidden inputs
        document.getElementById('hidden-correct').value = correctAnswers;
        document.getElementById('hidden-incorrect').value = incorrectAnswers;
        document.getElementById('hidden-vision-percent').value = results.visionPercentage;
        document.getElementById('hidden-color-type').value = results.colorType;

        document.querySelector('.test-card').classList.add('d-none');
        document.getElementById('result-section').classList.remove('d-none');
    }

    function resetTest() {
        currentTestIndex = 0;
        correctAnswers = 0;
        incorrectAnswers = 0;
        document.getElementById('progress-bar').style.width = '0%';
        document.getElementById('test-image').src = testImages[0].image_path;
        document.getElementById('result-section').classList.add('d-none');
        document.querySelector('.test-card').classList.remove('d-none');
    }

    function submitAnswer(answer) {
        if (currentTestIndex < testImages.length) {
            if (answer == testImages[currentTestIndex].correct_answer) {
                correctAnswers++;
            } else {
                incorrectAnswers++;
            }
            
            currentTestIndex++;
            updateProgress();
            
            if (currentTestIndex < testImages.length) {
                document.getElementById('test-image').src = testImages[currentTestIndex].image_path;
            } else {
                showResults();
            }
        }
    }
    </script>
</body>
</html>