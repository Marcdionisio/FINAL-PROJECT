<?php
include 'session_check.php';
checkSession();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLMUN - Generate Barcode</title>
    <link rel="icon" type="image/x-icon" href="plmun.ico">
    <link rel="stylesheet" href="barcodegen.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #barcode-container, #barcode-container * {
                visibility: visible;
            }
            #barcode-container {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo-container">
            <img src="plmun.png" alt="PLMUN Logo">
            <h2>PLMUN Library Management System</h2>
        </div>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="announcement.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropbtn"><i class="fas fa-list"></i> Books <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="list_of_books.php">Book List</a>
                    <a href="generate_barcode.php">Generate Barcode</a>
                </div>
            </li>
            <li><a href="student_monitoring.php"><i class="fas fa-user-graduate"></i> Student Monitoring</a></li>
            <li><a href="borrowed_returned_books.php"><i class="fas fa-book"></i> Borrowed-Returned Books</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Manage Users</a></li>
           
            <li class="logout"><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="container">
        <div class="form-container">
            <h1>Generate Barcode</h1>
            <form id="barcode-form">
                <div class="input-container">
                    <label for="isbn">ISBN:</label>
                    <input type="text" id="isbn" name="isbn" required>
                </div>
                <div class="input-container">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="1" required>
                </div>
                <button type="button" onclick="generateBarcodes()">Generate</button>
            </form>
            <div id="barcode-container"></div>
            <button id="print-button" onclick="printBarcodes()" style="display:none;">Print Barcodes</button>
        </div>
    </div>
    
    <script>
        function generateBarcodes() {
            const isbn = document.getElementById('isbn').value.trim();
            const quantity = parseInt(document.getElementById('quantity').value);
            const barcodeContainer = document.getElementById('barcode-container');
            barcodeContainer.innerHTML = '';

            if (isbn.length !== 13 || isNaN(isbn)) {
                alert('Please enter a valid 13-digit ISBN.');
                return;
            }

            for (let i = 0; i < quantity; i++) {
                const canvas = document.createElement('canvas');
                JsBarcode(canvas, isbn, { format: 'EAN13' });
                barcodeContainer.appendChild(canvas);
                console.log(`Barcode ${i + 1} generated for ISBN: ${isbn}`);
            }

            if (barcodeContainer.innerHTML.trim() !== '') {
                document.getElementById('print-button').style.display = 'block';
            }
        }

        function printBarcodes() {
            window.print();
        }
    </script>
</body>
</html>
