<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planora</title>

    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: Arial, sans-serif;
        }


        /* HEADER */
        .header {
            background: #e5e5e5;
            padding: 15px 30px;
            font-size: 20px;
            font-weight: bold;
        }

        /* MAIN — full height */
        .main {
            flex: 1; /* đẩy footer xuống đáy */
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 70px;
        }


        .hero-content {
            text-align: center;
            margin-left: 160px;
        }

        .hero-content h1 {
            font-size: 42px;
            font-weight: 700;
        }

        .hero-content p,
        .hero-content h3 {
            margin: 10px 0;
        }

        .signup-btn {
            margin-top: 20px;
            padding: 12px 28px;
            font-size: 18px;
            border-radius: 8px;
        }


        .btn {
            padding: 12px 25px;
            background: #ddd;
            border: 1px solid #000;
            cursor: pointer;
            border-radius: 5px;
        }

        .small-text {
            margin-top: 10px;
            font-size: 14px;
        }


        .small-text a {
            text-decoration: underline; 
            color: black;
        }

        /* RIGHT IMAGE BOX */
        .right {
            width: 40%;
            height: 380px;
            background: #dcdcdc;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 28px;
            font-weight: bold;
            overflow: hidden;
            margin-right: 100px;
        }

        .right img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* FOOTER */
        .footer {
            background: #e5e5e5;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }

        .footer-right a {
            margin-left: 20px;
            text-decoration: none;
            color: black;
            font-weight: bold; 
        }
    </style>

</head>
<body>

    <!-- HEADER -->
    <div class="header">
        PLANORA
    </div>

    <!-- MAIN (đã đổi từ .container thành .main) -->
    <div class="main">
        <div class="hero-content">
            <h1>
                Manage your <br>
                projects <span>smarter.</span><br>
                Not harder
            </h1>

            <p>Plan better. Work together. Deliver faster.<br>With Planora!</p>

            <p>Together, we make your projects succeed.</p>
            <a class="register" href="/register">
                <button class="btn">Sign up for free</button>
            </a>
            

            <div class="small-text">
                Bạn đã có tài khoản? <a class="login" href="/login">Log in</a>
            </div>
        </div>

        <div class="right">
            <img src="https://online.champlain.edu/sites/online/files/styles/width_1600/public/2024-10/blog-header-project-mgmt-tips.jpg?itok=GVBIh1Ec" alt="Promo Image">
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div>Have questions? Call us at 0123456789.</div>

        <div class="footer-right">
            <a href="https://www.facebook.com/">Facebook</a>
            <a href="https://www.instagram.com/">Instagram</a>
            <a href="https://www.tiktok.com/">TikTok</a>
        </div>
    </div>

</body>
</html>
