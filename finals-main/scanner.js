function onScanSuccess(decodedText, decodedResult) {
    const isbn = decodedText.trim();
    console.log("Scanned ISBN:", isbn);
    document.getElementById('isbn').value = isbn;
    fetchBookData(isbn);
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
});

function fetchBookData(isbn) {
    console.log("Fetching data for ISBN:", isbn);

    fetch(`getBookDetails.php?isbn=${isbn}`)
        .then(response => response.json())
        .then(data => {
            console.log("Book Data Received:", data);
            if (data.success) {
                document.getElementById('book-name').value = data.book_name || '';
                document.getElementById('author').value = data.author || '';
                document.getElementById('year-published').value = data.year_published || '';
            } else {
                alert('Book details not found. Please try another ISBN.');
            }
        })
        .catch(error => {
            console.error('Error fetching book details:', error);
            alert('An error occurred while fetching book details.');
        });
}
