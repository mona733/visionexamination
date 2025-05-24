<?php
include 'config.php';
// Performance Metrics Queries
$totalPatients = $con->query("SELECT COUNT(DISTINCT patient_id) AS total FROM (
    SELECT patient_id FROM report_color1
    UNION
    SELECT patient_id FROM test_reports
    UNION
    SELECT patient_id FROM reports_senllen
    UNION
    SELECT patient_id FROM report_senllen2
) AS combined")->fetch_assoc()['total'];

$completedTests = $con->query("SELECT COUNT(*) AS total FROM (
    SELECT report_id FROM report_color1
    UNION ALL
    SELECT report_id FROM test_reports
) AS combined")->fetch_assoc()['total'];

$visionPerformance = $con->query("SELECT AVG(vision_percentage) AS avg FROM test_reports")->fetch_assoc()['avg'];

$testAccuracy = $con->query("SELECT 
    (SUM(correct_answers) / (SUM(correct_answers + incorrect_answers)) * 100) AS accuracy 
    FROM test_reports")->fetch_assoc()['accuracy'];

$upcomingTests = $con->query("SELECT COUNT(*) AS total FROM report_senllen2")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script >
                        $(document).ready(function()
                        {
                            $("#nav").load("control_panel.html");
                        }
                       );
    </script>
    <title>لوحة أداء </title>
    <style>
        :root {
            --main-color: #1e3799;
            --secondary-color: #4a69bd;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .dashboard-title {
            text-align: center;
            color: var(--main-color);
            margin: 2rem 0;
            font-size: 2.5rem;
        }

        .progress-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .progress-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .progress-circle {
            width: 150px;
            height: 150px;
            margin: 0 auto 1rem;
            position: relative;
        }

        .circle-bg {
            fill: none;
            stroke: #eee;
            stroke-width: 8;
        }

        .circle-progress {
            fill: none;
            stroke-width: 8;
            stroke-linecap: round;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
            transition: stroke-dashoffset 1.5s ease-in-out;
        }

        .progress-value {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--main-color);
        }

        .progress-label {
            font-size: 1.1rem;
            color: #333;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .progress-container {
                grid-template-columns: 1fr;
            }
            
            .progress-circle {
                width: 120px;
                height: 120px;
            }
        }

        @keyframes progress {
            from { stroke-dashoffset: 440; }
            to { stroke-dashoffset: var(--dash-offset); }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div id="nav"></div>
    <h1 class="dashboard-title">لوحة متابعة ألاداء </h1>
    
    <div class="progress-container">
        <!-- Total Patients -->
        <div class="progress-card">
            <div class="progress-circle">
                <svg viewBox="0 0 100 100">
                    <circle class="circle-bg" cx="50" cy="50" r="45"/>
                    <circle class="circle-progress" cx="50" cy="50" r="45"
                            style="stroke: var(--main-color); --dash-array: 282.743; --dash-offset: <?= 282.743 * (1 - $totalPatients/100) ?>"/>
                </svg>
                <div class="progress-value"><?= $totalPatients ?></div>
            </div>
            <div class="progress-label">إجمالي المرضى</div>
        </div>

        <!-- Completed Tests -->
        <div class="progress-card">
            <div class="progress-circle">
                <svg viewBox="0 0 100 100">
                    <circle class="circle-bg" cx="50" cy="50" r="45"/>
                    <circle class="circle-progress" cx="50" cy="50" r="45"
                            style="stroke: var(--secondary-color); --dash-array: 282.743; --dash-offset: <?= 282.743 * (1 - $completedTests/200) ?>"/>
                </svg>
                <div class="progress-value"><?= $completedTests ?></div>
            </div>
            <div class="progress-label">الفحوصات المكتملة</div>
        </div>

        <!-- Vision Performance -->
        <div class="progress-card">
            <div class="progress-circle">
                <svg viewBox="0 0 100 100">
                    <circle class="circle-bg" cx="50" cy="50" r="45"/>
                    <circle class="circle-progress" cx="50" cy="50" r="45"
                            style="stroke: var(--success-color); --dash-array: 282.743; --dash-offset: <?= 282.743 * (1 - $visionPerformance/100) ?>"/>
                </svg>
                <div class="progress-value"><?= round($visionPerformance) ?>%</div>
            </div>
            <div class="progress-label">أداء الرؤية</div>
        </div>

        <!-- Test Accuracy -->
        <div class="progress-card">
            <div class="progress-circle">
                <svg viewBox="0 0 100 100">
                    <circle class="circle-bg" cx="50" cy="50" r="45"/>
                    <circle class="circle-progress" cx="50" cy="50" r="45"
                            style="stroke: var(--warning-color); --dash-array: 282.743; --dash-offset: <?= 282.743 * (1 - $testAccuracy/100) ?>"/>
                </svg>
                <div class="progress-value"><?= round($testAccuracy) ?>%</div>
            </div>
            <div class="progress-label">دقة الفحوصات</div>
        </div>

        <!-- Upcoming Tests -->
        <div class="progress-card">
            <div class="progress-circle">
                <svg viewBox="0 0 100 100">
                    <circle class="circle-bg" cx="50" cy="50" r="45"/>
                    <circle class="circle-progress" cx="50" cy="50" r="45"
                            style="stroke: var(--danger-color); --dash-array: 282.743; --dash-offset: <?= 282.743 * (1 - $upcomingTests/50) ?>"/>
                </svg>
                <div class="progress-value"><?= $upcomingTests ?></div>
            </div>
            <div class="progress-label">الفحوصات القادمة</div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const progressCircles = document.querySelectorAll('.circle-progress');
            
            const animateProgress = () => {
                progressCircles.forEach(circle => {
                    const rect = circle.getBoundingClientRect();
                    if (rect.top < window.innerHeight) {
                        const dashArray = parseFloat(circle.style.getPropertyValue('--dash-array'));
                        const dashOffset = parseFloat(circle.style.getPropertyValue('--dash-offset'));
                        circle.style.strokeDasharray = `${dashArray}`;
                        circle.style.strokeDashoffset = dashOffset;
                    }
                });
            };

            window.addEventListener('scroll', animateProgress);
            animateProgress();
        });
    </script>
</body>
</html>