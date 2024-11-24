<?php
include("../server.php");

if (empty($_SESSION['logged_in'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

?>

<html>

<head>
    <title>Aliman Merchandise</title>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="../css/style.css">

    <!-- Boostrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
</head>

<body>
    <div class="wrapper">
        <?php include 'side-bar.php'; ?>

        <div class="main">
            <h2 class="margin-bottom"><i class="bi-bar-chart-line-fill" style="color: #2a9d8f;"></i> Sales Chart</h2>
            <p class="margin-bottom">Here are all the charts for sales report</p>

            <div class="grid-1-1">
                <div>
                    <div class="margin-bottom">
                        <h2>Product sold by categories</h2>

                        <?php
                        // Query to get total sales by category
                        $query = "SELECT product_categories.category_name AS category, SUM(orders.total_price) AS total_sales
                        FROM orders
                        JOIN products ON orders.product_id = products.id
                        JOIN product_categories ON products.category = product_categories.id
                        WHERE orders.status = 'For Pickup' OR orders.status = 'Received' AND orders.paid_at IS NOT NULL
                        GROUP BY product_categories.category_name";
                        $result = mysqli_query($db, $query);

                        // Prepare data for Chart.js
                        $categories = [];
                        $sales = [];
                        while ($row = mysqli_fetch_assoc($result)) {
                            $categories[] = $row['category'];
                            $sales[] = $row['total_sales'];
                        }
                        ?>

                        <div>
                            <canvas id="donut-chart"></canvas>
                        </div>
                    </div>
                </div>

                <div>
                    <h2>Daily Sales Report</h2>

                    <?php
                    // Query to get total products sold per day
                    $query = "SELECT DATE(paid_at) AS sale_date, SUM(total_price) AS total_sold 
                    FROM orders 
                    WHERE paid_at IS NOT NULL 
                    GROUP BY DATE(paid_at) 
                    ORDER BY sale_date ASC";

                    // Execute the query
                    $result = mysqli_query($db, $query);

                    // Prepare data for the chart
                    $dates = [];
                    $total_sold = [];

                    while ($row = mysqli_fetch_assoc($result)) {
                        $dates[] = $row['sale_date'];
                        $total_sold[] = $row['total_sold'];
                    }
                    ?>

                    <div class="margin-bottom">
                        <canvas id="line-chart"></canvas>
                    </div>

                    <p>These are the charts fo the breakdown of sold items by category, and the total daily sales</p>
                </div>
            </div>

        </div>
    </div>

    <!-- <div class="container">
        <p>@2022 Aliman Merchandise: Ordering and Inventory Management System</p>
    </div> -->

    <!-- SCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- HANDLES THE PIE CHART FOR SALES BY CATEGORY -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Pass PHP data to JavaScript
            const categories = <?php echo json_encode($categories); ?>;
            const sales = <?php echo json_encode($sales); ?>;

            // Render the donut chart
            const ctx = document.getElementById('donut-chart');

            new Chart(ctx, {
                type: 'doughnut', // Donut chart type
                data: {
                    labels: categories, // Dynamic category labels
                    datasets: [{
                        label: 'Sales by Category',
                        data: sales, // Dynamic sales data
                        backgroundColor: [
                            '#FF6384', // Red
                            '#36A2EB', // Blue
                            '#FFCE56', // Yellow
                            '#4CAF50', // Green
                            '#9966FF' // Purple
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `${tooltipItem.label}: ₱${tooltipItem.raw.toLocaleString()}`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

    <!-- HANDLES THE LINE CHART FOR DAILY SALES -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // PHP data to JavaScript
            const saleDates = <?php echo json_encode($dates); ?>;
            const totalSales = <?php echo json_encode($total_sold); ?>;

            // Create Line Chart
            const ctx = document.getElementById('line-chart');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: saleDates, // Dates as labels
                    datasets: [{
                        label: 'Total Sales',
                        data: totalSales, // Total sales
                        fill: false,
                        borderColor: 'rgb(75, 192, 192)', // Line color
                        tension: 0.1 // Line smoothness
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true, // Show legend
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Daily Sales Report'
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total Sales'
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

</body>

</html>