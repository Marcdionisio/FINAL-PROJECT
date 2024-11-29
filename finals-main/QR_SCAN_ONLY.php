
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLMUN - Student Monitoring</title>
    <link rel="icon" type="image/x-icon" href="plmun.ico">
    <link rel="stylesheet" href="QR_SCAN_ONLY.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="alerts.js" defer></script>
</head>
<body>
    <div class="sidebar">
        <div class="logo-container">
            <img src="plmun.png" alt="PLMUN Logo">
            <h2>PLMUN Library Management System</h2>
        </div>
        <ul>
            <li class="logout"><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="container">
        <div class="scanner-container">
            <h1>Library Student Monitoring</h1>
            <div class="scanner-content">
                <div id="qr-reader"></div>
                <div class="student-info">
                    <form id="scanner-form" action="process_qr.php" method="post">
                        <label for="student-number">Student Number:</label>
                        <input type="text" id="student_number" name="student_number" readonly>
                        
                        <label for="last-name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" readonly>
                        
                        <label for="first-name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" readonly>

                        <label for="college">College:</label>
                        <input type="text" id="college" name="college" readonly>
                        
                        <label for="course">Course:</label>
                        <input type="text" id="course" name="course" readonly>
                        
                        <label for="year-level">Year Level:</label>
                        <input type="text" id="year_level" name="year_level" readonly>
                        
                       
                        
                        <button type="submit">Submit</button>
                    </form>
                </div>
            </div>
    
    </script>
            </div>
        </div>
    </div>
      
    <script>
    document.addEventListener('DOMContentLoaded', function () {
    // Check for alert parameters in the URL
    const urlParams = new URLSearchParams(window.location.search);
    const alertType = urlParams.get('alert');
    const alertMessage = urlParams.get('message');
    const redirectUrl = urlParams.get('redirect');

    // Map alert types to SweetAlert icon types
    let iconType;
    switch (alertType) {
        case 'success':
            iconType = 'success';
            break;
        case 'error':
            iconType = 'error';
            break;
        case 'warning':
            iconType = 'warning';
            break;
        default:
            iconType = 'info'; // Default icon
            break;
    }

    if (alertType && alertMessage) {
        showAlertWithRedirect(alertMessage, '', iconType, redirectUrl);
    }
});
</script>
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode/minified/html5-qrcode.min.js"></script>
    <script src="student_monitoring.js"></script>
</body>
</html>
