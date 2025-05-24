<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبارات +تشخيص
    </title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .cards-container {
            display: flex;
            gap: 2rem;
            padding: 2rem;
            max-width: 1200px;
            width: 100%;
        }

        .card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            flex: 1;
            border: 3px solid rgb(35, 140, 210);
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .card-image {
            height: 250px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .card-content {
            padding: 2rem;
        }

        .card-title {
            color: #1a365d;
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }

        .card-text {
            color: #4a5568;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .card-button {
            display: inline-block;
            padding: 1rem 2rem;
            background: #4299e1;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .card-button:hover {
            background: #3182ce;
        }

        /* Different colors for second card */
        .card:nth-child(2) .card-button {
            background: #48bb78;
        }

        .card:nth-child(2) .card-button:hover {
            background: #38a169;
        }

        @media (max-width: 768px) {
            .cards-container {
                flex-direction: column;
            }
            
            .card {
                width: 100%;
            }
        }
    </style>
</head>
<body> 
<a href="paitent_login.php" style="position:absolute; top:2%;left:2%;"> <image src="undo.png"></image></a>
    <div class="cards-container">
        <!-- Eye Disease Diagnosis Card -->
        <div class="card">
            <div class="card-image" style="background-image: url('368944c9-8f9b-45fe-b534-5f8b2af5f169.png')">
            </div>
            <div class="card-content">
                <h2 class="card-title">  تشخيص امراض العين</h2>
                <p class="card-text">
                 تحليل للكشف عن الأمراض المحتملة بما في ذلك إعتام عدسة العين والزرق واعتلال الشبكية السكري. احصل على الفور النتائج والتوصيات الأولية.
                </p>
                <a href="question_client.php" class="card-button">بدء التشخيص </a>
            </div>
        </div>

        <!-- Distance Vision Examination Card -->
        <div class="card">
            <div class="card-image" style="background-image: url('d7935a65-7091-437d-9e6c-b73a8712d344.png')">
            </div>
            <div class="card-content">
                <h2 class="card-title">فحص الرؤية</h2>
                <p class="card-text">
                 حدة الرؤية الرقمي الشامل لتقييم المسافة رؤية. يتضمن توصيات مخصصة وتتبع التقدم لصحة العين المثلى.
                </p>
                <a href="test_interface.html" class="card-button">بدء الفحص</a>
            </div>
        </div>
    </div>
</body>
</html>