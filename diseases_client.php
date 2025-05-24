<?php
 include 'control/config.php';
// Fetch data from database
$sql = "SELECT * FROM eye_diseases";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>موسوعة أمراض العيون</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="control\script.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            color:#1e3799;
            font-family: 'Tajawal', sans-serif;
        }
        .disease-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 25px;
            border: none;
        }
        .disease-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0,91,187,0.2);
        }
        .card-header {
            background: rgb(35, 140, 210);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
        }
        .card-body {
            padding: 1.5rem;
        }
        .badge-custom {
            background: #e3f2fd;
            color: #1e3799;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
        }
        .search-box {
            max-width: 600px;
            margin: 40px auto;
        }
        .section-title {
            color: rgb(35, 140, 210);
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2.2rem;
        }
        .icon-box {
            width: 50px;
            height: 50px;
            background: rgb(35, 140, 210);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.4rem;
        }
    </style>
</head>
<body>
    <div id="container"></div>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background:rgb(35, 140, 210); !important;">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-eye"></i>
                موسوعة أمراض العيون
            </a>
        </div>
       <a href="homepage.php"> <image src="undo.png"></image></a>
    </nav>

    <div class="container py-5">
        <!-- Search Box -->
        <div class="search-box">
            <form method="GET">
                <div class="input-group shadow-lg">
                    <input type="text" class="form-control form-control-lg" 
                           placeholder="ابحث عن مرض عيني..." 
                           name="search"
                           style="border-radius: 15px 0 0 15px;">
                    <button class="btn btn-primary btn-lg" type="submit"
                            style="border-radius: 0 15px 15px 0;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <h2 class="section-title">قائمة امراض العين</h2>

        <div class="row">
            <?php
            if(isset($_GET['search'])) {
                $search = $con->real_escape_string($_GET['search']);
                $sql = "SELECT * FROM eye_diseases WHERE disease_name LIKE '%$search%'";
                $result = $con->query($sql);
            }

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="disease-card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="icon-box">
                                <i class="fas fa-eye-slash"></i>
                            </div>
                            <span class="badge-custom"><?= htmlspecialchars($row['category']) ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h3 class="h5 mb-3 fw-bold " style="color=#1e3799;"><?= htmlspecialchars($row['disease_name']) ?></h3>
                        <p class=" mb-4" style="color=#1e3799;"><?= htmlspecialchars($row['description']) ?></p>
                        
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3"><i class="fas fa-exclamation-triangle me-2"></i>الأعراض</h6>
                            <div class="ps-3"><?= htmlspecialchars($row['symptoms']) ?></div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold mb-3"><i class="fas fa-capsules me-2"></i>العلاجات</h6>
                            <div class="ps-3"><?= htmlspecialchars($row['treatments']) ?></div>
                        </div>

                        <div class="mt-4">
                            <h6 class="fw-bold mb-3"><i class="fas fa-shield-alt me-2"></i>الوقاية</h6>
                            <div class="ps-3"><?= htmlspecialchars($row['prevention']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
            } else {
                echo '<div class="col-12 text-center">
                        <div class="disease-card p-5">
                            <i class="fas fa-info-circle fa-3x text-muted mb-4"></i>
                            <h4 class="text-muted">لا توجد أمراض مسجلة</h4>
                        </div>
                      </div>';
            }
            $con->close();
            ?>
        </div>
    </div>
      
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>