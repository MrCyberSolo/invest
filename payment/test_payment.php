<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Generate QR Code
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="qrData">QR Code Data:</label>
                                <input type="text" class="form-control" id="qrData" name="qrData" placeholder="Enter data for QR code">
                            </div>
                            <button type="submit" class="btn btn-primary">Generate QR Code</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
// Include the Composer autoloader
require 'vendor/autoload.php';

use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the QR code data from the form
    $qrData = $_POST['qrData'];

    // Create a QR code renderer
    $renderer = new Png();

    // Set the QR code size
    $renderer->setWidth(300);
    $renderer->setHeight(300);

    // Create a QR code writer
    $writer = new Writer($renderer);

    // Generate the QR code image
    $qrCodeImage = $writer->writeString($qrData);

    // Output the QR code image
    header('Content-Type: image/png');
    echo $qrCodeImage;
}
?>
