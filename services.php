<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Online Service</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }

        .appHeader {
            background-color: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .appHeader .pageTitle {
            font-size: 18px;
            font-weight: bold;
        }

        .appHeader .icon {
            color: white;
            font-size: 18px;
            text-decoration: none;
        }

        #appCapsule {
            padding: 20px;
        }

        .iconedBox {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 15px;
            transition: all 0.3s ease-in-out;
        }

        .iconedBox:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .iconedBox .icon {
            font-size: 40px;
            margin-right: 15px;
            color: #25D366; /* Default WhatsApp color */
        }

        .iconedBox .icon.telegram {
            color: #0088cc; /* Telegram blue color */
        }

        .iconedBox .title {
            font-size: 16px;
            font-weight: 500;
        }

        .divider {
            height: 1px;
            background: #ddd;
            margin: 20px 0;
        }

        a {
            text-decoration: none;
            color: inherit;
        }
    </style>

    <style>body, .appCapsule { background-color: #007749 !important; }</style>
</head>

<body style="background-color: #007749;">

    <!-- App Header -->
    <div class="appHeader">
        <a href="index.php" class="icon">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="pageTitle" style="padding-right: 150px;">Online Service</div>
    </div>

    <!-- App Capsule -->
    <div id="appCapsule">
        <div class="appContent">
            <div class="row">
                <!-- Telegram Service -->
                <div class="col-12">
                    <a href="https://t.me/btradeservice" class="totelegram">
                        <div class="iconedBox">
                            <i class="fab fa-telegram icon telegram"></i>
                            <div>
                                <h4 class="title">Telegram Service</h4>
                                <p class="mb-0">Contact via Telegram for quick service.</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="divider"></div>
                <!-- WhatsApp Service -->
                <div class="col-12">
                    <a href="https://api.whatsapp.com/send?phone=919876543210" class="towhatsapp">
                        <div class="iconedBox">
                            <i class="fab fa-whatsapp icon"></i>
                            <div>
                                <h4 class="title">WhatsApp Service</h4>
                                <p class="mb-0">Contact via WhatsApp for fast assistance.</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
