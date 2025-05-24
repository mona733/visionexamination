<?php
session_start();
include 'control/config.php';

// Fetch patient data
$patient_id = $_SESSION['patient_id'];
$stmt = $con->prepare("SELECT name FROM patient WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch all diseases from the database to ensure coverage
$stmt = $con->prepare("SELECT disease_name FROM eye_diseases");
$stmt->execute();
$diseases_result = $stmt->get_result();
$all_diseases = [];
while ($row = $diseases_result->fetch_assoc()) {
    $all_diseases[] = $row['disease_name'];
}
$stmt->close();

$questions = [
    ['الظفرة', 'هل تعاني من احمرار وتهيج في العين مع شعور بوجود جسم غريب؟'],
    ['التهاب القرنية الفطري', 'هل تشعر بألم شديد في العين مع احمرار وضبابية في الرؤية؟'],
    ['انغلاق الزاوية الحاد', 'هل تشعر بألم حاد في العين مع غثيان أو رؤية هالات حول الأضواء؟'],
    ['الانكسار الضوئي غير المنتظم', 'هل تعاني من رؤية مشوشة أو صعوبة في الرؤية الليلية؟'],
    ['التهاب العصب البصري', 'هل تشعر بألم عند تحريك عينيك أو فقدان مؤقت للرؤية؟'],
    ['الحَوَل', 'هل تعاني من ازدواجية في الرؤية أو إمالة الرأس لتركيز النظر؟'],
    ['عمى الألوان', 'هل تواجه صعوبة في تمييز الألوان، خاصة الأحمر والأخضر؟'],
    ['التهاب الجيوب الحجاجية', 'هل تعاني من ألم حول العينين وتورم في الجفون؟'],
    ['اعتلال الشبكية الخداجي', 'هل لاحظت أي تغيرات غير طبيعية في عيون طفلك الخديج؟'],
    ['الوذمة البقعية', 'هل تلاحظ تشوهًا في الرؤية المركزية أو تبدو الألوان باهتة؟'],
    ['التهاب الصلبة', 'هل تشعر بألم عميق في العين مع احمرار موضعي وحساسية تجاه الضوء؟'],
    ['التهاب القناة الدمعية', 'هل تعاني من دمعان مستمر أو التهابات متكررة في العين؟'],
    ['الورم الميلانيني العيني', 'هل لاحظت ظهور بقع داكنة في قزحية العين أو تغيرات في الرؤية؟'],
    ['التهاب القرنية الهربسي', 'هل تعاني من قرح مؤلمة في العين مع حساسية للضوء؟'],
    ['التهاب الشبكية الصباغي', 'هل تواجه صعوبة في الرؤية ليلاً أو فقدانًا للرؤية المحيطية؟'],
    ['الوذمة الحليمية', 'هل تعاني من فقدان مفاجئ للرؤية مصحوبًا بصداع؟'],
    ['التهاب القرنية الشوكميبي', 'هل تشعر بألم شديد في العين مع احمرار وفقدان البصر؟'],
    ['الانفصال الزجاجي', 'هل ترى عوائم مفاجئة أو ومضات ضوئية في مجال رؤيتك؟'],
    ['التهاب العنبية', 'هل تعاني من ألم في العين مع احمرار وحساسية للضوء؟'],
    ['التهاب العين الدرقي', 'هل تعاني من جحوظ في العينين أو ازدواجية في الرؤية؟'],
    ['التهاب القرنية الجاف', 'هل تشعر بحرقة أو وجود رمل في عينيك مع احمرار؟'],
    ['التهاب القرنية الفيروسي', 'هل لديك تقرحات في العين مع ألم وضبابية في الرؤية؟'],
    ['الوذمة اللمفية القرنية', 'هل تعاني من ضبابية الرؤية أو تورم في القرنية؟'],
    ['التهاب الملتحمة التحسسي', 'هل تعاني من حكة شديدة في العين مع دمعان وتورم الجفون؟'],
    ['التهاب القرنية البكتيري', 'هل لديك قرحة مؤلمة في العين مع إفرازات صديدية؟'],
    ['التهاب الشبكية النضحي', 'هل ترى عوائم في مجال الرؤية أو فقدان الرؤية المركزية؟'],
    ['التهاب القرنية الشريطي، التهاب القرنية الترسيبي', 'هل تعاني من تهيج في العين مع رؤية ضبابية؟'],
    ['التهاب العين الفطري', 'هل تشعر بألم في العين مع احمرار وإفرازات سميكة؟'],
    ['التهاب القرنية الفقاعي', 'هل تشعر بألم شديد في العين وحساسية تجاه الضوء؟'],
    ['التهاب القزحية الأمامي', 'هل تعاني من ألم واحمرار في العين مع تغير في شكل الحدقة؟'],
    ['التهاب الشبكية المضاد للفيروسات', 'هل تعاني من فقدان مفاجئ للرؤية أو ظهور عوائم؟'],
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = $_POST['answers'];
    $disease_scores = array_fill_keys($all_diseases, 0); // Initialize all scores to 0

    foreach ($answers as $index => $answer) {
        if ($answer === 'yes') {
            $related_diseases = explode('، ', $questions[$index][0]);
            foreach ($related_diseases as $disease) {
                if (isset($disease_scores[$disease])) {
                    $disease_scores[$disease]++;
                }
            }
        }
    }

    // Fetch disease details
    $diagnosis_report = [];
    $stmt = $con->prepare("SELECT * FROM eye_diseases");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $row['score'] = $disease_scores[$row['disease_name']] ?? 0;
        $row['percentage'] = number_format(($row['score'] / count($questions)) * 100, 2);
        $diagnosis_report[$row['disease_name']] = $row;
    }
    $stmt->close();

    // Sort by percentage descending
    usort($diagnosis_report, function($a, $b) {
        return $b['percentage'] <=> $a['percentage'];
    });

    // Save report
    $report_json = json_encode($diagnosis_report, JSON_UNESCAPED_UNICODE);
    $stmt = $con->prepare("INSERT INTO diagnosis (patient_id, report, date) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $patient_id, $report_json);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> تشخيص أمراض العيون</title>
<style>
        :root {
            --main-color: rgb(35, 140, 210);
            --hover-color: rgb(25, 120, 190);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f8f9fa;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .patient-name {
            color: var(--main-color);
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .question-card {
            background: #fff;
            border: 2px solid #eee;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .question-card:hover {
            border-color: var(--main-color);
            transform: translateY(-2px);
        }

        .question-text {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .answer-btns {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
        }

        .btn-yes {
            background: var(--main-color);
            color: white;
        }

        .btn-yes:hover {
            background: var(--hover-color);
        }

        .btn-no {
            background: #f8f9fa;
            color: #666;
            border: 2px solid #ddd;
        }

        .btn-no:hover {
            border-color: var(--main-color);
            color: var(--main-color);
        }

        .report-section {
            margin-top: 2rem;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 15px;
        }

        .disease-card {
            background: white;
            border-left: 4px solid var(--main-color);
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 8px;
        }

        .progress-bar {
            height: 8px;
            background: #eee;
            border-radius: 4px;
            margin: 2rem 0;
        }

        .progress {
            height: 100%;
            background: var(--main-color);
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        .simple-report {
            margin: 1rem 0;
            border: 2px solid #eee;
            border-radius: 10px;
            padding: 1rem;
        }
        
        .disease-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem;
            margin: 0.5rem 0;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .disease-name {
            font-weight: bold;
            color: var(--main-color);
        }
        
        .disease-percentage {
            background: var(--main-color);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .full-details {
            display: none;
            padding: 1rem;
            margin-top: 1rem;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #eee;
        }
    </style>
</head>
<body>
<a href="choice.php" style="position:absolute; top:2%;left:2%;"> <image src="undo.png"></image></a>
    <div class="container">
        <div class="header">
            <h1> تشخيص أمراض العيون</h1>
            <div class="patient-name">مرحبًا <?= $patient['name'] ?></div>
        </div>

        <?php if (!isset($diagnosis_report)): ?>
            <form method="POST">
                <div class="progress-bar">
                    <div class="progress" style="width: 0%"></div>
                </div>

                <?php foreach ($questions as $index => $question): ?>
                    <div class="question-card">
                        <div class="question-text"><?= $question[1] ?></div>
                        <div class="answer-btns">
                            <button type="button" class="btn btn-yes" onclick="setAnswer(<?= $index ?>, 'yes')">نعم</button>
                            <button type="button" class="btn btn-no" onclick="setAnswer(<?= $index ?>, 'no')">لا</button>
                        </div>
                        <input type="hidden" name="answers[<?= $index ?>]" id="answer-<?= $index ?>" class="answer-input">
                    </div>
                <?php endforeach; ?>

                <button type="submit" class="btn btn-yes" style="width: 100%; padding: 1.2rem;">إظهار النتائج</button>
            </form>
            <?php else: ?>
            <div class="report-section">
                <h2>التقرير المبسط</h2>
                <p>تاريخ التشخيص: <?= date('Y-m-d H:i') ?></p>
                
                <div class="simple-report">
                    <?php foreach ($diagnosis_report as $disease): ?>
                        <?php if ($disease['percentage'] > 0): ?>
                            <div class="disease-item">
                                <span class="disease-name"><?= $disease['disease_name'] ?></span>
                                <span class="disease-percentage"><?= $disease['percentage'] ?>%</span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <button onclick="toggleDetails()" class="btn btn-yes" style="margin-top: 1rem;">
                    عرض التفاصيل الكاملة
                </button>

                <div class="full-details" id="fullDetails">
                    <?php foreach ($diagnosis_report as $disease): ?>
                        <div class="disease-card">
                            <h3><?= $disease['disease_name'] ?></h3>
                            <p><strong>نسبة الاحتمال:</strong> <?= $disease['percentage'] ?>%</p>
                            <p><strong>الأعراض:</strong> <?= $disease['symptoms'] ?></p>
                            <p><strong>العلاجات:</strong> <?= $disease['treatments'] ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="recommendations">
                    <h3>التوصيات العامة:</h3>
                    <ul>
                        <li>مراجعة طبيب عيون متخصص في أقرب وقت</li>
                        <li>إجراء الفحوصات الدورية اللازمة</li>
                        <li>اتباع التعليمات العلاجية بدقة</li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Maintain previous JavaScript and add new function
        function toggleDetails() {
            const details = document.getElementById('fullDetails');
            details.style.display = details.style.display === 'none' ? 'block' : 'none';
            event.target.textContent = details.style.display === 'none' 
                ? 'عرض التفاصيل الكاملة' 
                : 'إخفاء التفاصيل الكاملة';
        }
        function setAnswer(index, value) {
            const answerInput = document.getElementById(`answer-${index}`);
            answerInput.value = value;
            const questionCard = document.getElementsByClassName('question-card')[index];
            questionCard.style.borderColor = value === 'yes' ? 'rgb(35, 140, 210)' : '#eee';
            
            // Update progress bar
            const answeredInputs = Array.from(document.querySelectorAll('.answer-input')).filter(input => input.value !== '');
            const progress = (answeredInputs.length / <?= count($questions) ?>) * 100;
            document.querySelector('.progress').style.width = `${progress}%`;
        }
    </script>
</body>
</html>
<?php $con->close(); ?>