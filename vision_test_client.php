<?php
// Database configuration
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "vision_system";

// Create connection
$con= new mysqli($servername, $username, $password, $dbname);

// Create vision1_test table
$con->query("
CREATE TABLE IF NOT EXISTS vision1_test (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(6) UNSIGNED NOT NULL,
    test_data TEXT NOT NULL,
    test_results TEXT NOT NULL,
    test_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patient(id)
)");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $con->real_escape_string($_POST['patient_id']);
    $responses = $con->real_escape_string(json_encode($_POST['responses'], JSON_UNESCAPED_UNICODE));
    
    $con->query("INSERT INTO vision1_test (patient_id, test_results) 
                VALUES ('$patient_id', '$responses')");
    
    header("Location: ".$_SERVER['PHP_SELF']."?report=true&patient_id=$patient_id");
    exit;
}

// Display content
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>فحص النظر - نظام الفحص البصري</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            background-color: #f0f4f7;
        }
        .container {
            background-color: rgb(35, 140, 210);
            padding: 25px;
            border-radius: 15px;
            color: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            margin: 20px auto;
            max-width: 800px;
        }
        .test-row {
            margin: 20px 0;
            padding: 15px;
            background-color: rgba(255,255,255,0.1);
            border-radius: 8px;
        }
        input[type="text"] {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 200px;
        }
        button {
            background-color: #fff;
            color: rgb(35, 140, 210);
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        table {
            width: 100%;
            margin: 20px 0;
        }
        td, th {
            padding: 12px;
            text-align: center;
            border: 1px solid #fff;
        }
        img.test-image {
            height: 40px;
            margin: 5px;
            filter: contrast(1.2);
        }
    </style>
</head>
<body>
<a href="homepage.php" style="position:absolute; top:2%;left:2%;"> <image src="undo.png"></image></a>
<?php if(isset($_GET['report'])): ?>
<!-- Report Section -->
<div class="container">
    <h1>تقرير فحص النظر</h1>
    <?php
    $patient_id = $_GET['patient_id'];
    $result = $conn->query("SELECT * FROM vision1_test 
                          WHERE patient_id = $patient_id 
                          ORDER BY test_date DESC LIMIT 1");
    $row = $result->fetch_assoc();
    $responses = json_decode($row['test_results'], true);
    ?>
    
    <table>
        <tr>
            <th>الصورة</th>
            <th>الإجابة الصحيحة</th>
            <th>إجابة المريض</th>
        </tr>
        <?php foreach($responses as $correct => $answer): ?>
        <tr>
            <td><img src="images/<?= explode(' ', $correct)[0] ?>.png" class="test-image"></td>
            <td><?= $correct ?></td>
            <td><?= $answer ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <div class="test-details">
        <h3>تفاصيل الفحص:</h3>
        <p>تاريخ الفحص: <?= $row['test_date'] ?></p>
        <p>عدد العناصر: <?= count($responses) ?></p>
    </div>
</div>

<?php else: ?>
<!-- Test Interface -->
<div class="container">
    <h1>فحص حدة النظر</h1>
    <form method="POST">
        <input type="hidden" name="patient_id" value="1"> <!-- Update patient ID -->
        
        <div class="test-content">
            <?php
            $test_data = [
                'b2f5f481-ac60-446f-8667-1aa5511adc82' => 'E',
                'line2' => 'F T',
                'line3' => 'B F E S T',
                'line4' => '39 F T E E T 2',
                'line5' => '0.3',
                // Add all test lines
            ];
            
            foreach($test_data as $img => $correct): ?>
            <div class="test-row">
                <img src="images/<?= $img ?>.png" class="test-image" alt="test image">
                <input type="text" name="responses[<?= $correct ?>]" 
                     placeholder="اكتب ما تراه هنا" required>
            </div>
            <?php endforeach; ?>
        </div>
        
        <button type="submit">حفظ النتائج</button>
    </form>
</div>
<?php endif; ?>

</body>
</html>