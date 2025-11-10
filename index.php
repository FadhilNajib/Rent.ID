<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RENT.ID</title>
    <link rel="icon" type="image/png" sizes="32x32" href="asset/icon.png">


    <!-- Import font Poppins dari Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            height: 100vh;
            background: url('public/asset/BG_PROJEK.jpg') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            position: relative;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 1000px;
            padding: 20px;
        }

        h1 {
            font-size: 99.56px;
            font-weight: 700;
            margin-bottom: 0px; /* jarak teks RENT.ID dan paragraf */
            letter-spacing: 1px;
            line-height: 1.1;
        }

        p {
            font-size: 24px;
            margin-top: 8px;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        /* Tombol LOGIN */
        .btn-login {
            background-color: #ffffff;           /* lingkaran putih */
            color: #93BAFF;                     /* warna teks LOGIN */
            border: none;
            border-radius: 30px;
            padding: 12px 60px;                 /* ✅ tombol lebih panjang */
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-login:hover {
            background-color: #f3f3f3;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>RENT.<span style="color:#ffffff;">ID</span></h1>
        <p>
            Kami hadir untuk memberikan solusi transportasi yang praktis,<br>
            cepat, dan terpercaya sesuai kebutuhan Anda.
        </p>
        <a href="public/login.php" class="btn-login">LOGIN</a>
    </div>
</body>
</html>
