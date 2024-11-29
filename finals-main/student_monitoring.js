function onScanSuccess(decodedText, decodedResult) {
    // Assuming the QR code contains student information in JSON format
    try {
        const studentData = JSON.parse(decodedText);

        // Populate the form fields with the scanned data
        document.getElementById('user_id').value = studentData.user_id;
        document.getElementById('student_number').value = studentData.student_number;
        document.getElementById('last_name').value = studentData.last_name;
        document.getElementById('first_name').value = studentData.first_name;
        document.getElementById('college').value = studentData.college;
        document.getElementById('course').value = studentData.course;
        document.getElementById('year_level').value = studentData.year_level;

    } catch (error) {
        alert("Invalid QR code. Please scan a valid student QR code.");
    }
}

function onScanFailure(error) {
    console.warn(`QR scan failed: ${error}`);
}

// Initialize the QR scanner
let html5QrCode = new Html5Qrcode("qr-reader");

html5QrCode.start(
    { facingMode: "environment" }, // Use the rear camera
    {
        fps: 10,    // Scans per second
        qrbox: { width: 250, height: 250 }  // Define scanning box size
    },
    onScanSuccess,
    onScanFailure
).catch(err => {
    console.error(`Unable to start scanning, error: ${err}`);
});

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const transactionTable = document.getElementById('transactionTable');

    if (searchInput && transactionTable) {
        searchInput.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = transactionTable.querySelectorAll('tbody tr');
            let matchFound = false;

            rows.forEach(function(row) {
                const cells = row.querySelectorAll('td');
                let match = false;

                cells.forEach(function(cell) {
                    if (cell.textContent.toLowerCase().includes(searchValue)) {
                        match = true;
                    }
                });

                row.style.display = match ? '' : 'none';
                matchFound = matchFound || match; // Track if at least one match is found
            });

            // Optional: Display a message if no matches found
            const noResultsMessage = document.getElementById('noResults');
            if (!matchFound) {
                if (!noResultsMessage) {
                    const message = document.createElement('div');
                    message.id = 'noResults';
                    message.textContent = 'No results found';
                    transactionTable.parentNode.insertBefore(message, transactionTable.nextSibling);
                }
            } else {
                if (noResultsMessage) {
                    noResultsMessage.remove();
                }
            }
        });
    } else {
        console.error('Search input or transaction table not found.');
    }
});

let currentPage = 1;
const rowsPerPage = 10;

function displayRows(filteredRows) {
    const table = document.getElementById('transactionTable');
    const rows = table.getElementsByTagName('tr');
    const totalRows = filteredRows.length; // Use the number of filtered rows
    const totalPages = Math.ceil(totalRows / rowsPerPage);

    // Calculate start and end row indexes for the current page
    const startRow = (currentPage - 1) * rowsPerPage; // Zero-based index
    const endRow = Math.min(startRow + rowsPerPage, totalRows);

    // Hide all rows and display only the ones for the current page
    for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the header
        rows[i].style.display = 'none'; // Hide all initially
    }

    // Show only the filtered rows for the current page
    for (let i = startRow; i < endRow; i++) {
        const rowIndex = filteredRows[i]; // Get the original row index
        rows[rowIndex + 1].style.display = ''; // +1 to skip the header
    }

    // Update pagination controls
    const pageNumbersContainer = document.getElementById('pageNumbers');
    pageNumbersContainer.innerHTML = ''; // Clear existing page numbers

    for (let i = 1; i <= totalPages; i++) {
        const pageNumber = document.createElement('div');
        pageNumber.className = 'page-number' + (i === currentPage ? ' active' : '');
        pageNumber.textContent = i;
        pageNumber.onclick = () => {
            currentPage = i;
            displayRows(filteredRows);
        };
        pageNumbersContainer.appendChild(pageNumber);
    }

    document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;
}

function filterByDate() {
    var dateType = document.getElementById('date-type').value;
    var startDate = new Date(document.getElementById('start-date').value);
    var endDate = new Date(document.getElementById('end-date').value);

    // If an end date is provided, set the time to the end of the day
    if (endDate) {
        endDate.setHours(23, 59, 59, 999); // Set to end of the day
    }

    const table = document.getElementById('transactionTable');
    const rows = table.getElementsByTagName('tr');
    const filteredRows = [];

    // Iterate through all rows (excluding header)
    for (let i = 1; i < rows.length; i++) {
        const dateValueText = rows[i].cells[dateType === 'entry_time' ? 6 : 7].textContent.trim();
        const dateValue = dateValueText ? new Date(dateValueText) : null;

        let showRow = true;

        // Check start date
        if (startDate && dateValue && dateValue < startDate) {
            showRow = false;
        }

        // Check end date
        if (endDate && dateValue && dateValue > endDate) {
            showRow = false;
        }

        // Show or hide the row based on filtering
        rows[i].style.display = showRow ? '' : 'none';

        // If the row is visible, add its index to the filteredRows array
        if (showRow) {
            filteredRows.push(i - 1); // Store the index of the original row (0-based)
        }
    }

    // Reset current page to 1
    currentPage = 1;

    // Display the rows according to the filtered results
    displayRows(filteredRows);
}

// Initial call to display rows on page load
displayRows(Array.from({ length: document.getElementById('transactionTable').rows.length - 1 }, (_, i) => i));

// Print functionality
function printTable() {
    var printContents = document.querySelector('.transaction-list').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = '<h2>In and Out List</h2>' + printContents;
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
