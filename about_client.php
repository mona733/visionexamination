<?php
header('Content-Type: text/html; charset=utf-8');
include 'control/config.php';
// Fetch content from database
$query = "SELECT heading, paragraph FROM site_content ORDER BY id DESC LIMIT 1";
$result = $con->query($query);

if ($result->num_rows > 0) {
    $content = $result->fetch_assoc();
} else {
    die("No content found in database");
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<a href="homepage.php" style="position:absolute; top:2%;left:2%;"> <image src="undo.png"></image></a>
    <title>فحص النظر الإلكتروني</title>
    <meta charset="UTF-8">
    <style>
        /* Keep the same CSS styles from previous example */
        body { background-color: #f0f8ff; font-family: 'Arial', sans-serif; line-height: 1.8; margin: 0; padding: 20px; }
        .container {border:4px groove black; max-width: 800px; margin: 140px auto; background-color:rgb(35, 140, 210) ; padding:40px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        h1 { color: white; text-align: center; padding-bottom: 20px; border-bottom: 2px solid #8cb4e8 ; margin-bottom: 25px; }
        .content { color: white; font-size: 18px; text-align: justify; font-weight:bold;font-size:20px;}
    </style>
</head>
<body>
    <center>
    <div class="container">
        <h1><?php echo htmlspecialchars($content['heading']); ?></h1>
        <div class="content">
            <?php echo nl2br(htmlspecialchars($content['paragraph'])); ?>
        </div>
    </div>
    </center>
</body>
</html>

<?php $con->close(); ?>