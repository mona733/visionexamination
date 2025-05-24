<?php
include 'control/config.php';
// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM medicines WHERE name LIKE '%".$con->real_escape_string($search)."%' 
        OR generic_name LIKE '%".$con->real_escape_string($search)."%'";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>موسوعة أدوية العيون</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>   
    <script src="script.js"></script>   
    <style>
        body { background-color: #f0f8ff; color: #1e3799; font-family: Arial, sans-serif; }
        .navbar {  background: linear-gradient(to left, var(--primary-blue),rgb(35, 140, 210) !important);}
        .medicine-card { background: white; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .medicine-header { background: rgb(35, 140, 210) ;color: white; border-radius: 15px 15px 0 0; }
        .container a {color:#071e3d;}
        .badge { background: rgb(150, 193, 222) !important; font-size:20px;color: #1e3799;}
        @media (max-width: 768px) {
            .medicine-card { margin: 10px 0; }
        }
        h1, h4, h6 { font-weight: bold; }
        h1{color: rgb(35, 140, 210) }
        .text-muted { color: rgb(35, 140, 210) !important; }
    </style>
</head>
<body>
<a href="homepage.php" style="position:absolute; top:2%;left:2%;"> <image src="undo.png"></image></a>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <form class="d-flex ms-auto" method="GET">
                <input class="form-control me-2 text-end" type="search" placeholder="ابحث عن أدوية العين..." name="search" value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-light" type="submit">بحث</button>
            </form>
        </div>
    </nav>

    <div class="container py-5">
        <h1 class="text-center mb-4">موسوعة أدوية العيون</h1>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php if ($result->num_rows > 0):
                while($row = $result->fetch_assoc()): ?>
                <div class="col">
                    <div class="medicine-card">
                        <div class="medicine-header p-3">
                            <h4 class="mb-1"><?= htmlspecialchars($row["name"]) ?></h4>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary"><?= htmlspecialchars($row["generic_name"]) ?></span>
                                <small><?= htmlspecialchars($row["manufacturer"]) ?></small>
                            </div>
                        </div>
                        <div class="p-3 text-end">
                            <h6 class="text-primary mb-3">الاستخدامات:</h6>
                            <p><?= htmlspecialchars($row["uses"]) ?></p>
                            
                            <h6 class="text-primary mt-3">الجرعة:</h6>
                            <p><?= htmlspecialchars($row["dosage"]) ?></p>
                            
                            <h6 class="text-primary mt-3">الآثار الجانبية:</h6>
                            <p><?= htmlspecialchars($row["side_effects"]) ?></p>
                        </div>
                    </div>
                </div>
                <?php endwhile;
            else: ?>
                <div class="col-12 text-center py-5">
                    <h3 class="text-muted">لم يتم العثور على أدوية</h3>
                    <p class="text-muted">حاول البحث باستخدام مصطلحات مختلفة</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $con->close(); ?>