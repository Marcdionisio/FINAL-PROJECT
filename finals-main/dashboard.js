document.addEventListener('DOMContentLoaded', () => {
    // TIME AND DATE
    function updateDateTime() {
        const now = new Date();
        const datetimeElement = document.getElementById('datetime');
        datetimeElement.textContent = now.toLocaleString();
    }
    // Update the date and time every second
    setInterval(updateDateTime, 1000);

    // Update the dashboard statistics every minute
    setInterval(updateDashboard, 60000);


    // UPDATE DATA FROM DB
    function updateDashboard() {
        fetch('dashboardprocess.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('total-books').textContent = data.total_books;
                document.getElementById('total-borrowed').textContent = data.total_borrowed;
                document.getElementById('total-returned').textContent = data.total_returned;
                document.getElementById('total-returned-late').textContent = data.total_returned_late;
                document.getElementById('total-due').textContent = data.total_due;

                // Store historical data for the graph
                window.historicalData = data.historical_data;
                console.log('Historical Data:', window.historicalData);
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    
    // UPDATE
    updateDashboard();

    // Modal functionality
    const modal = document.getElementById("graphModal");
    const btn = document.getElementById("total-books-card");
    const span = document.getElementsByClassName("close");

    btn.onclick = function() {
        modal.style.display = "block";
        displayGraph();
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // GRAPH
    function displayGraph() {
        const ctx = document.getElementById('booksChart').getContext('2d');
        const labels = window.historicalData.map(item => item.month);
        const data = window.historicalData.map(item => item.total_books);

        const booksChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Books Added Per Month',
                    data: data,
                    backgroundColor: 'rgba(0, 75, 0, 0.2)',
                    borderColor: 'rgba(0, 75, 0, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
            
        });
    }
    
    // EXCEL
    const createReportButton = document.getElementById('create-report');
    createReportButton.addEventListener('click', () => {
        const reportData = window.historicalData.map(item => ({
            Month: item.month,
            'Books Added': item.total_books
        }));

        const worksheet = XLSX.utils.json_to_sheet(reportData);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Books Report');

        XLSX.writeFile(workbook, 'books_report.xlsx');
        console.log('Report created');
    });
});
