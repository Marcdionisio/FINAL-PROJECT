<?php
include 'session_check.php';
checkSession();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> PLMUN - My QR</title>
    <link rel="icon" type="image/x-icon" href="plmun.ico">
    <link rel="stylesheet" href="my_qr.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
    <div class="navbar">
        <div class="logo-container">
            <img src="plmun.png" alt="PLMUN Logo">
            <h2>PLMUN Library Management System</h2>
        </div>
        <ul>
            <li><a href="home_user.php"><i class="fas fa-home"></i> Home</a></li>
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropbtn"><i class="fas fa-book"></i> Books <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="list_of_books.php">List of Books</a>
                    <a href="transactions.php">Transactions</a>
                </div>
            </li>
            <li><a href="my_qr.php"><i class="fas fa-qrcode"></i> My QR</a></li>
            <li class="logout"><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="container">
        <h1>My QR Code</h1>
        <div id="qrcode"></div>
        <button id="download-btn" class="download-button">
            <i class="fas fa-download"></i> Download QR Code
        </button>
    </div>

    <script>
        // Fetch user data from the server
        fetch('my_qr_process.php')
            .then(response => response.text()) // Get response as text
            .then(text => {
                console.log('Raw response text:', text); // Debugging statement
                try {
                    const data = JSON.parse(text); // Parse JSON
                    console.log('Parsed JSON data:', data); // Debugging statement
                    if (data.error) {
                        alert(data.error);
                    } else {
                        // Convert user data to JSON string
                        const userDataString = JSON.stringify(data);
                        console.log('User data stringified:', userDataString); // Debugging statement

                        // Generate QR code with regular dimensions
                        const qrcode = new QRCode(document.getElementById("qrcode"), {
                            text: userDataString,
                            width: 256, // Set to a standard size
                            height: 256, // Set to a standard size
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });
                        console.log('QR code generated'); // Debugging statement

                        // Add download functionality with white background
                        document.getElementById('download-btn').addEventListener('click', () => {
                            const canvas = document.querySelector('#qrcode canvas');
                            const context = canvas.getContext('2d');

                            // Create a new canvas with a white background
                            const whiteCanvas = document.createElement('canvas');
                            whiteCanvas.width = canvas.width + 40; // Add margin (20 pixels on each side)
                            whiteCanvas.height = canvas.height + 40; // Add margin (20 pixels on each side)
                            const whiteContext = whiteCanvas.getContext('2d');

                            // Fill the white background
                            whiteContext.fillStyle = "#ffffff";
                            whiteContext.fillRect(0, 0, whiteCanvas.width, whiteCanvas.height);

                            // Draw the QR code onto the new canvas
                            whiteContext.drawImage(canvas, 20, 20); // Offset by 20 pixels

                            // Create a download link
                            const link = document.createElement('a');
                            link.href = whiteCanvas.toDataURL('image/png');
                            link.download = 'qrcode.png';
                            link.click();
                        });
                    }
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                    alert('Failed to parse JSON data. Please check the console for more details.');
                }
            })
            .catch(error => {
                console.error('Error fetching user data:', error);
            });
    </script>
</body>
</html>
