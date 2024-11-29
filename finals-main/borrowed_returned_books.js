function onScanSuccess(decodedText, decodedResult) {
    try {
        const studentData = JSON.parse(decodedText);

        // Check if the scanned data contains student information
        if (studentData.student_number) {
            populateStudentInfo(studentData);
        } else {
            throw new Error("Not a student QR code");
        }
    } catch (error) {
        // Handle barcode containing ISBN
        const isbn = decodedText.trim();
        console.log("Scanned ISBN:", isbn);  // Debugging statement
        document.getElementById('isbn').value = isbn;
        fetchBookData(isbn);
    }
}

function populateStudentInfo(studentData) {
    document.getElementById('student_number').value = studentData.student_number || '';
    document.getElementById('last_name').value = studentData.last_name || '';
    document.getElementById('first_name').value = studentData.first_name || '';
    document.getElementById('college').value = studentData.college || '';
    document.getElementById('course').value = studentData.course || '';
    document.getElementById('year_level').value = studentData.year_level || '';
    document.getElementById('stat').value = 'borrowed';  // Assume borrowed status for students
}

function onScanFailure(error) {
    console.warn(`Scan failed: ${error}`);
}

function startCodeScanner() {
    let html5QrCode = new Html5Qrcode("qr-reader");

    html5QrCode.start(
        { facingMode: "environment" }, // Use rear camera
        {
            fps: 15,    // Scans per second
            qrbox: { width: 350, height: 350 },  // Scanning box size
        },
        onScanSuccess,
        onScanFailure
    ).catch(err => {
        console.error(`Unable to start scanning, error: ${err}`);
        setTimeout(startCodeScanner, 3000); // Retry scanning if it fails
    });
}

document.addEventListener('DOMContentLoaded', function () {
    startCodeScanner(); // Start scanning when the document is loaded

    const isbnField = document.getElementById('isbn');
    isbnField.focus();

    isbnField.addEventListener('change', function () {
        const isbnValue = isbnField.value.trim();
        if (isbnValue) {
            fetchBookData(isbnValue);
        }
    });
});

function fetchBookData(isbn) {
    console.log("Fetching data for ISBN:", isbn);

    fetch(`getBookDetails.php?isbn=${isbn}`)
        .then(response => response.json())
        .then(data => {
            console.log("Book Data Received:", data);
            if (data.success) {
                // Populate fields with received book data
                document.getElementById('book_name').value = data.book_name || '';
                document.getElementById('author').value = data.author || '';
                document.getElementById('year_published').value = data.year_published || '';
                document.getElementById('stat').value = 'borrowed';  // Assume borrowed status for the book
            } else {
                // ISBN not found: Clear all fields
                Swal.fire({
                    icon: 'warning',
                    title: 'Book Not Found',
                    text: 'Book details not found. Please try another ISBN.',
                    confirmButtonText: 'OK'
                });
                clearBookFields(); // Ensure fields are cleared when ISBN is not found
            }
        })
        .catch(error => {
            console.error('Error fetching book details:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while fetching book details.',
                confirmButtonText: 'OK'
            });
            clearBookFields(); // Ensure fields are cleared on error
        });
}


// Function to clear book-related input fields
function clearBookFields() {
    document.getElementById('isbn').value = '';             // Clear ISBN field
    document.getElementById('book_name').value = '';        // Clear book name field
    document.getElementById('author').value = '';           // Clear author field
    document.getElementById('year_published').value = '';   // Clear year published field
}



// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const transactionTable = document.getElementById('transactionTable');

    if (searchInput && transactionTable) {
        searchInput.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = transactionTable.querySelectorAll('tbody tr');

            rows.forEach(function(row) {
                const cells = row.querySelectorAll('td');
                let match = false;

                cells.forEach(function(cell) {
                    if (cell.textContent.toLowerCase().includes(searchValue)) {
                        match = true;
                    }
                });

                // Display or hide the row based on the search match
                row.style.display = match ? '' : 'none';
            });
        });
    } else {
        console.error('Search input or transaction table not found.');
    }
});


// Filter by date functionality
function filterByDate() {
    var dateType = document.getElementById('date-type').value;
    var startDate = document.getElementById('start-date').value;
    var endDate = document.getElementById('end-date').value;
    var rows = document.querySelectorAll('#transactionTable tbody tr');

    rows.forEach(function(row) {
        // Select the correct column based on the dateType
        var dateValue = row.querySelector('td:nth-child(' + (dateType === 'borrow_time' ? 10 : dateType === 'due_date' ? 11 : 12) + ')').textContent.trim();

        // Convert date strings to Date objects for comparison
        var dateToCompare = new Date(dateValue);
        var start = startDate ? new Date(startDate) : null;
        var end = endDate ? new Date(endDate) : null;

        var showRow = true;

        // Check if row falls within the date range
        if (start && dateToCompare < start) {
            showRow = false; // Date is before the start date
        }
        if (end && dateToCompare > end) {
            showRow = false; // Date is after the end date
        }

        // Display or hide row based on the condition
        row.style.display = showRow ? '' : 'none';
    });
}


// Print functionality
function printTable() {
    var printContents = document.querySelector('.transaction-list').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = '<h2>Transaction List</h2>' + printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}

// Export to Excel functionality
function exportTableToExcel(tableID, filename = '') {
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

    // Specify file name
    filename = filename ? filename + '.xls' : 'excel_data.xls';

    // Create download link element
    downloadLink = document.createElement('a');

    document.body.appendChild(downloadLink);

    if (navigator.msSaveOrOpenBlob) {
        var blob = new Blob(['\ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob(blob, filename);
    } else {
        // Create a link to the file
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

        // Setting the file name
        downloadLink.download = filename;

        // Triggering the function
        downloadLink.click();
    }
}


