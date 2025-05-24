<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vision Test & Brand</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Arial', sans-serif;
    }
    :root {
        --primary-blue:rgb(35, 140, 210);
        --secondary-blue:rgb(207, 222, 243);
        --dark-blue:rgb(126, 166, 215);
        --background-white:rgb(167, 190, 212);
    }
    body {
        direction: rtl;
        background-repeat: no-repeat;
        background-image:url('08cf1723-868b-4f58-9080-b2f9a5ffbaf5.png');
        min-height:20vh;
        background-size:cover;
    }
    /* Add these responsive adjustments */
    @media (max-width: 992px) {
        .letter-row {
            margin-right: 0 !important;
            gap: 1rem;
        }
        
        .eye-health-section {
            position: relative !important;
            width: 100% !important;
            margin-right: 0 !important;
            top: 0 !important;
            padding: 30px 15px !important;
        }
        
        .logo {
            margin-right: 0 !important;
        }
        
        #navbarNav {
            margin-right: 0 !important;
            background: var(--primary-blue);
            padding: 15px;
        }
        
        .article-card {
            height: auto !important;
            margin-bottom: 15px;
        }
    }

    @media (max-width: 768px) {
        .content {
            margin-top: 20% !important;
        }
        
        .brand-name {
            font-size: 1.2rem !important;
        }
        
        .letter {
            font-size: 1.2rem !important;
        }
        
        header {
            height: auto !important;
            padding: 0.5rem !important;
        }
    }

    @media (max-width: 576px) {
        .health-tip {
            padding: 15px !important;
        }
        
        .article-card {
            padding: 15px !important;
        }
        
        .letter-row {
            flex-wrap: wrap;
        }
    }

    .content {
        margin-top: 10%;
    }

    header {
        padding: 1rem 2rem;
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
        background:rgb(35, 140, 210);
        border-style:groove;
        border-radius:3%;
        border-width:2px;
        height:12%;
        border:3px groove #8cb4e8;
    }

    .logo-container {
        display: flex;
        align-items: center;
        gap: 1rem;
        cursor: pointer;
        margin-right:-10%;
       
    }

    .logo {
        height: 70px;
        width: 70px;
        border-radius: 50%;
        transition: transform 0.3s ease;
    }

    .brand-name {
        font-size: 1.8rem;
        color: white;
        font-weight: bold;
    }

    .letter-row {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-top:-5px;
        transition: all 0.3s ease;
        margin-right:120%;
        position: relative;
        top:-3%;
    }

    .letter {
        font-size: 2rem;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        color:rgb(126, 166, 215);
    }

    .letter:hover {
        transform: scale(1.2);
        color: #007bff;
    }


    @media (max-width: 768px) {
        .content {
            margin-top: 80px;
            padding: 1rem;
        }
        .letter {
            font-size: 1.5rem;
        }
    }
    .eye-health-section {
        padding: 50px 0;
        margin-right:-9%;
        top:20%;
        width: 60%;
        height:70%;
        position: absolute;
        color:white;
    }
    
    .health-tip {
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 40px;
        transition: transform 0.3s;
        background: linear-gradient(to right, var(--primary-blue),var(--dark-blue));
    }
    
    .health-tip:hover {
        transform: translateY(-5px);
    }
    
    .article-card {
        border: none;
        border-radius: 12px;
        transition: all 0.3s;
        background: linear-gradient(to right, var(--primary-blue),var(--dark-blue));
        height: 87%;
    }
    
    .article-card:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .article-icon {
        font-size: 2rem;
        color:white;
        margin-bottom: 15px;
    }
    
    @media (max-width: 768px) {
        .health-tip {
            margin: 20px;
            padding: 20px;
        }
    }
    #navbarNav
    {
        margin-right:30%;
        font-size:21px;
        font-weight: bolder;
        
    }
    #navbarNav li a
    {
       color: white;
    }
    #navbarNav ul li
    {
      padding-right:4%;
    }
    h4
    {
        font-weight:bold;
        color:rgb(5, 35, 74);
    }
</style>
<?php
include 'control/config.php';

// Fetch data from database
$menu_items = $con->query("SELECT * FROM menu_items ORDER BY display_order");
$vision_tests = $con->query("SELECT * FROM vision_tests ORDER BY font_size  DESC");
$health_tip = $con->query("SELECT * FROM health_tips ORDER BY created_at DESC LIMIT 1")->fetch_assoc();
$articles = $con->query("SELECT * FROM articles ORDER BY article_id DESC LIMIT 3");
?>
<body>
    <header>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom ">
        <div class="container position-relative">
            <!-- Navbar Toggler -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Logo Container -->
            <div class="logo-container"  >
               <a href="control/log_server.php"> <img src="1668c810-8699-41a0-a7b2-a72ab12a18cd.png" alt="Logo"
                     class="logo img-fluid me-lg-3"> </a>
                <h1 class="brand-name d-none d-lg-block">فحص الرؤية</h1>
           </div>

            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="navbarNav" >
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <?php while($item = $menu_items->fetch_assoc()): ?>
                        <li class="nav-item mx-lg-2 text-center">
                            <a class="nav-link py-lg-3" href="<?= $item['item_link'] ?>">
                                <?= $item['item_name'] ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main class="content">
    <div class="container">
        <div class="row justify-content-center">
            <!-- Vision Test Section -->
            <div class="col-12 col-xl-10">
            <?php
                    // Group tests by font size
                    $tests_by_size = [];
                    while($test = $vision_tests->fetch_assoc()) {
                        $tests_by_size[$test['font_size']][] = $test;
                    }
              ?>
                <?php foreach($tests_by_size as $font_size => $tests): ?>
                    <div class="letter-row row justify-content-center gx-2 gy-3">
                        <?php foreach($tests as $test): ?>
                            <div class="col-auto">
                                <span class="letter"><?= $test['letter'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                <!-- Health Section -->
                <section class="eye-health-section mt-5">
                    <div class="health-tip">
                        <h2 class="mb-4">
                            <i class="fas fa-eye me-2"></i><?= $health_tip['title'] ?>
                        </h2>
                        <p class="lead"><?= $health_tip['content'] ?></p>
                    </div>

                    <div class="row row-cols-1 row-cols-md-3 g-4 mt-4">
                        <?php while($article = $articles->fetch_assoc()): ?>
                            <div class="col">
                                <div class="article-card h-100 p-3 p-md-4">
                                    <i class="<?= $article['icon_class'] ?> article-icon"></i>
                                    <h4><?= $article['title'] ?></h4>
                                    <p><?= $article['content'] ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add letter click interaction
        document.querySelectorAll('.letter').forEach(letter => {
            letter.addEventListener('click', function() {
                this.style.transform = 'scale(1.5)';
                this.style.color = '#007bff';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                    this.style.color = '#333';
                }, 500);
                
                // Add pronunciation functionality
                const text = new SpeechSynthesisUtterance(this.innerText);
                window.speechSynthesis.speak(text);
            });
        });

        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            header.style.boxShadow = window.scrollY > 0 
                ? '0 4px 10px rgba(0,0,0,0.15)' 
                : '0 2px 5px rgba(0,0,0,0.1)';
        });
    </script>
</body>
</html>
</html>
