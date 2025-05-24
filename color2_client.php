<?php
session_start();
require 'control/config.php'; 

$rows = [
    ['size' => 88,  'snellen' => '20/200', 'count' => 4],
    ['size' => 44,  'snellen' => '20/100', 'count' => 4],
    ['size' => 22,  'snellen' => '20/50',  'count' => 4],
    ['size' => 8.8, 'snellen' => '20/20',  'count' => 4],
    ['size' => 4.4, 'snellen' => '20/10',  'count' => 4],
];

// Generate random directions
if (!isset($_SESSION['test_data'])) {
    $test_data = [];
    foreach ($rows as $row) {
        $letters = [];
        for ($i = 0; $i < $row['count']; $i++) {
            $type = ($i % 2 == 0) ? 'E' : 'C';
            $directions = ($type == 'E') ? ['up', 'down', 'left', 'right'] : ['top', 'bottom', 'left', 'right'];
            $letters[] = [
                'type' => $type,
                'direction' => $directions[array_rand($directions)],
                'size' => $row['size']
            ];
        }
        $test_data[] = $letters;
    }
    $_SESSION['test_data'] = $test_data;
}

// Process results
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assume patient_id is stored in session
    $patient_id = $_SESSION['patient_id'] ?? null;
    
    // Start transaction
    mysqli_begin_transaction($con);

    try {
        // Insert main report
        $stmt = mysqli_prepare($con, "INSERT INTO report_senllen2 (patient_id, test_date) VALUES (?, NOW())");
        mysqli_stmt_bind_param($stmt, 'i', $patient_id);
        mysqli_stmt_execute($stmt);
        $report_id = mysqli_insert_id($con);
        
        // Insert test details
        $stmt = mysqli_prepare($con, "INSERT INTO senllen2_test 
            (report_id, letter_type, direction, size, user_answer, is_correct) 
            VALUES (?, ?, ?, ?, ?, ?)");
            
        foreach ($_SESSION['test_data'] as $row_idx => $row) {
            foreach ($row as $letter_idx => $letter) {
                $user_answer = $_POST["answer_{$row_idx}_{$letter_idx}"] ?? '';
                $is_correct = ($user_answer === $letter['direction']) ? 1 : 0;
                
                mysqli_stmt_bind_param($stmt, 'issdsi', 
                    $report_id,
                    $letter['type'],
                    $letter['direction'],
                    $letter['size'],
                    $user_answer,
                    $is_correct
                );
                mysqli_stmt_execute($stmt);
            }
        }
        
        mysqli_commit($con);
    } catch(Exception $e) {
        mysqli_rollback($con);
        die("حدث خطأ في حفظ النتائج: " . $e->getMessage());
    }
    
    // Calculate best acuity
    $score = [];
    foreach ($_SESSION['test_data'] as $row_idx => $row) {
        $correct = 0;
        foreach ($row as $letter_idx => $letter) {
            if ($_POST["answer_{$row_idx}_{$letter_idx}"] === $letter['direction']) {
                $correct++;
            }
        }
        $score[$row_idx] = $correct >= 3 ? $rows[$row_idx]['snellen'] : null;
    }
    
    $best_acuity = '20/200';
    foreach ($score as $acuity) {
        if ($acuity !== null) {
            $best_acuity = $acuity;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فحص حدة البصر الرقمي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: rgb(35, 140, 210);
            --secondary: rgba(35, 140, 210, 0.1);
        }
        
        body {
            font-family: 'Tahoma', Arial, sans-serif;
        }
        
        .letter {
            display: inline-block;
            border: 2px solid var(--primary);
            border-radius: 8px;
            padding: 10px;
            margin: 10px;
            background: var(--secondary);
        }
        
        .E, .C {
            font-weight: 900;
            color: var(--primary);
        }
        
        .up { transform: rotate(0deg); }
        .right { transform: rotate(90deg); }
        .down { transform: rotate(180deg); }
        .left { transform: rotate(270deg); }
    </style>
</head>
<body>
<a href="test_interface.html" style="position:absolute; top:2%;left:2%;"> <image src="undo.png"></image></a>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4 text-primary">فحص حدة البصر الرقمي</h1>
            <div class="alert alert-info mt-4">
                <h4 class="alert-heading">تعليمات الفحص:</h4>
                <ul class="list-unstyled text-start">
                    <li>📐 قف على بعد 3 أمتار من الشاشة</li>
                    <li>👁️ قم بتغطية عين واحدة أثناء الفحص</li>
                    <li>🔄 حدد اتجاه الحروف المعروضة</li>
                    <li>✅ ستحصل على النتائج فور الإنتهاء</li>
                </ul>
            </div>
        </div>

        <?php if (!isset($best_acuity)): ?>
        <form method="post">
            <?php foreach ($_SESSION['test_data'] as $row_idx => $row): ?>
            <div class="row justify-content-center mb-5">
                <?php foreach ($row as $letter_idx => $letter): ?>
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <div class="letter" style="font-size: <?= $letter['size'] ?>px;">
                                <span class="<?= $letter['type'] ?> <?= $letter['direction'] ?>">
                                    <?= $letter['type'] ?>
                                </span>
                            </div>
                            <div class="btn-group-vertical w-100 mt-3">
                                <?php $options = ($letter['type'] === 'E') ? ['up', 'down', 'left', 'right'] : ['top', 'bottom', 'left', 'right']; ?>
                                <?php foreach ($options as $opt): ?>
                                <label class="btn btn-outline-primary text-end">
                                    <input type="radio" name="answer_<?= $row_idx ?>_<?= $letter_idx ?>" 
                                           value="<?= $opt ?>" required>
                                    <?= match($opt) {
                                        'up' => 'أعلى',
                                        'down' => 'أسفل',
                                        'left' => 'يسار',
                                        'right' => 'يمين',
                                        'top' => 'أعلى',
                                        'bottom' => 'أسفل'
                                    } ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
            
            <div class="text-center mt-5">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    عرض النتائج
                </button>
            </div>
        </form>
        <?php else: ?>
        <div class="card border-success shadow-lg">
            <div class="card-header bg-success text-white">
                <h2 class="card-title mb-0">نتيجة الفحص</h2>
            </div>
            <div class="card-body text-center py-5">
                <div class="display-1 text-success mb-4"><?= $best_acuity ?></div>
                <p class="lead text-muted">
                    هذه النتيجة تقريبية. يرجى مراجعة طبيب العيون لإجراء فحص شامل.
                </p>
                
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>