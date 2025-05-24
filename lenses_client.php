<?php include 'control/config.php';?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>العدسات الطبية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
       
    :root {
        --primary-blue: #00008b;
        --secondary-blue: #8cb4e8;
        --accent-white: #ffffff;
    }

    body { 
        background: linear-gradient(45deg, var(--accent-white) 60%, var(--secondary-blue) 150%);
        font-family: 'Tajawal', sans-serif;
        min-height: 100vh;
    }

    .navbar { 
        background: rgb(35, 140, 210) ;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-bottom: 2px solid var(--secondary-blue);
    }

    .card { 
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid var(--secondary-blue);
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(5px);
        position: relative;
        overflow: hidden;
        margin-bottom:10px;
    }

    .card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgb(35, 140, 210) , transparent);
        transform: rotate(45deg);
        opacity: 0.1;
        transition: all 0.5s;
    }

    .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        border-color: var(--primary-blue);
    }

    .card:hover::before {
        opacity: 0.3;
        top: -30%;
        left: -30%;
    }

    .tech-specs { 
        border-right: 3px solid rgb(35, 140, 210) ;
        padding-right: 1.5rem;
    }

    .star-rating { 
        color: #ffd700; 
        font-size: 1.2rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    h1, h4, h6 {
        color: rgb(35, 140, 210) ;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--secondary-blue);
        box-shadow: 0 0 0 0.25rem rgba(140, 180, 232, 0.25);
    }

    .alert-info {
        background: rgba(140, 180, 232, 0.3);
        border-color: var(--secondary-blue);
        color: var(--primary-blue);
    }

    .navbar-brand {
        font-weight: 700;
        letter-spacing: 1px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }

    .card-title {
        position: relative;
        padding-bottom: 0.5rem;
    }

    .card-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 50px;
        height: 3px;
        background: var(--secondary-blue);
        transition: width 0.3s;
    }

    .card:hover .card-title::after {
        width: 100px;
        background: var(--primary-blue);
    }
    </style>
</head>
<body>
   <div id="nav"></div>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#"> العدسات الطبية</a>
            <a href="homepage.php" style="margin-right:95%;"> <image src="undo.png"></image></a>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center mb-4" style="color:rgb(35, 140, 210) ;">قائمة العدسات المتوفرة</h1>
        
        <!-- Updated Filter Section -->
        <div class="row mb-4 g-3">
            <div class="col-md-4">
                <div class="input-group shadow-sm">
                    <span class="input-group-text filter-icon"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="بحث بالاسم...">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group shadow-sm">
                    <span class="input-group-text filter-icon"><i class="bi bi-funnel"></i></span>
                    <select class="form-select" id="typeSelect">
                        <option value="all">جميع الأنواع</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group shadow-sm">
                    <span class="input-group-text filter-icon"><i class="bi bi-star-fill"></i></span>
                    <select class="form-select" id="ratingSelect">
                        <option value="all">جميع التقييمات</option>
                        <option value="5">5 نجوم</option>
                        <option value="4">4 نجوم</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <?php
            $sql = "SELECT * FROM contact_lenses";
            $result = $con->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '
                    <div class="col-md-6 col-lg-4" 
                         data-name="'.htmlspecialchars($row['lens_type']).'" 
                         data-type="'.htmlspecialchars($row['lens_type']).'" 
                         data-rating="'.$row['quality_rating'].'">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="card-title">'.$row['lens_type'].'</h4>
                                    <div class="star-rating">'
                                        .str_repeat('<i class="bi bi-star-fill"></i>', $row['quality_rating'])
                                        .str_repeat('<i class="bi bi-star"></i>', 5 - $row['quality_rating']).
                                    '</div>
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-md-6 tech-specs">
                                        <h6 class="text-primary">المواصفات الفنية:</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>المادة:</strong> '.$row['material'].'</li>
                                            <li><strong>نفاذية الأكسجين:</strong> '.$row['oxygen_permeability'].' Dk/t</li>
                                            <li><strong>مدة الاستبدال:</strong> '.$row['replacement_schedule'].'</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h6 class="text-primary">التفاصيل الطبية:</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>القياسات البؤرية:</strong><br>'.$row['focal_measurement'].'</li>
                                            <li><strong>الحالات الموصى بها:</strong><br>'.$row['recommended_conditions'].'</li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <h6 class="text-primary">المميزات الإضافية:</h6>
                                    <p>'.$row['features'].'</p>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<div class="col-12"><div class="alert alert-info">لا توجد عدسات متوفرة حالياً</div></div>';
            }
            $con->close();
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const typeSelect = document.getElementById('typeSelect');
            const ratingSelect = document.getElementById('ratingSelect');
            const cards = document.querySelectorAll('.row > .col-md-6.col-lg-4');

            function filterCards() {
                const searchTerm = searchInput.value.trim().toLowerCase();
                const selectedType = typeSelect.value;
                const selectedRating = ratingSelect.value;

                cards.forEach(card => {
                    const cardName = card.dataset.name.toLowerCase();
                    const cardType = card.dataset.type;
                    const cardRating = card.dataset.rating;

                    const nameMatch = cardName.includes(searchTerm);
                    const typeMatch = selectedType === 'all' || cardType === selectedType;
                    const ratingMatch = selectedRating === 'all' || cardRating === selectedRating;

                    if (nameMatch && typeMatch && ratingMatch) {
                        card.style.opacity = '1';
                        card.style.display = 'block';
                    } else {
                        card.style.opacity = '0';
                        card.style.display = 'none';
                    }
                });
            }

            // Add event listeners
            [searchInput, typeSelect, ratingSelect].forEach(element => {
                element.addEventListener('input', filterCards);
                element.addEventListener('change', filterCards);
            });
        });
    </script>
</body>
</html>