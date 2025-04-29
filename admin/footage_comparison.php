<?php
// ✅ Start session and connect to DB
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playback Requests - 3-Year Comparison</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <div class="container mt-5">
        <h2 class="mb-4">📊 3-Year Monthly Comparison of Playback Requests</h2>

        <canvas id="comparisonChart" width="400" height="150"></canvas>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetch('fetch_chart_data.php')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('comparisonChart').getContext('2d');

                    // ✅ Prepare monthly data arrays
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                    const yearLabels = {};
                    const year1 = new Date().getFullYear() - 2;
                    const year2 = new Date().getFullYear() - 1;
                    const currentYear = new Date().getFullYear();

                    [year1, year2, currentYear].forEach(year => {
                        yearLabels[year] = Array(12).fill(0);
                    });

                    // ✅ Populate data for each year
                    data.forEach(item => {
                        const year = item.year;
                        const monthIndex = item.month - 1;  // Chart.js uses 0-based months
                        yearLabels[year][monthIndex] = item.total;
                    });

                    // ✅ Create the line chart
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: months,
                            datasets: [
                                {
                                    label: `${year1}`,
                                    data: yearLabels[year1],
                                    borderColor: '#28a745',
                                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                                    fill: true
                                },
                                {
                                    label: `${year2}`,
                                    data: yearLabels[year2],
                                    borderColor: '#ffc107',
                                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                                    fill: true
                                },
                                {
                                    label: `${currentYear}`,
                                    data: yearLabels[currentYear],
                                    borderColor: '#007bff',
                                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                                    fill: true
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                                title: {
                                    display: true,
                                    text: 'Monthly Playback Requests Over 3 Years'
                                }
                            },
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Months'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Number of Requests'
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error loading chart data:', error));
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>