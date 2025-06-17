<?php
require_once 'includes/auth_check.php';
require_once 'config/database.php';

$conn = get_db_connection();

// --- 1. Data Fetching for KPIs ---
$kpi_sql = "
    SELECT 
        (SELECT COUNT(*) FROM artists) as total_artists,
        (SELECT COUNT(*) FROM albums) as total_albums,
        (SELECT SUM(sales) FROM albums) as total_sales
";
$kpis = $conn->query($kpi_sql)->fetch_assoc();

$top_artist_sql = "SELECT a.name, SUM(b.sales) as total_sales FROM artists a JOIN albums b ON a.id = b.artist_id GROUP BY a.id ORDER BY total_sales DESC LIMIT 1";
$top_artist = $conn->query($top_artist_sql)->fetch_assoc();


// --- 2. Data Fetching for Charts (Top 10 Artists) ---
$chart_data_sql = "
    SELECT a.name, SUM(b.sales) as total_sales 
    FROM artists a 
    JOIN albums b ON a.id = b.artist_id 
    GROUP BY a.id, a.name 
    ORDER BY total_sales DESC 
    LIMIT 10
";
$chart_results = $conn->query($chart_data_sql)->fetch_all(MYSQLI_ASSOC);

$chart_labels = json_encode(array_column($chart_results, 'name'));
$chart_sales = json_encode(array_column($chart_results, 'total_sales'));


// --- 3. Data Fetching for the Main Table ---
$all_albums_sql = "
    SELECT al.id, al.name as album_name, ar.name as artist_name, al.year, al.sales 
    FROM albums al 
    JOIN artists ar ON al.artist_id = ar.id 
    ORDER BY al.sales DESC
";
$all_albums = $conn->query($all_albums_sql)->fetch_all(MYSQLI_ASSOC);

$conn->close();

$page_title = "Dashboard";
include 'templates/header.php';
?>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm kpi-card">
            <div class="card-body">
                <div class="icon-circle bg-primary text-white"><i class="bi bi-cash-stack"></i></div>
                <div>
                    <div class="text-muted">Total Sales</div>
                    <div class="h4 fw-bold mb-0"><?php echo number_format($kpis['total_sales']); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm kpi-card">
            <div class="card-body">
                <div class="icon-circle bg-info text-white"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="text-muted">Total Artists</div>
                    <div class="h4 fw-bold mb-0"><?php echo number_format($kpis['total_artists']); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm kpi-card">
            <div class="card-body">
                <div class="icon-circle bg-success text-white"><i class="bi bi-journal-album"></i></div>
                <div>
                    <div class="text-muted">Total Albums</div>
                    <div class="h4 fw-bold mb-0"><?php echo number_format($kpis['total_albums']); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm kpi-card">
            <div class="card-body">
                <div class="icon-circle bg-warning text-white"><i class="bi bi-trophy-fill"></i></div>
                <div>
                    <div class="text-muted">Top Artist</div>
                    <div class="h4 fw-bold mb-0"><?php echo htmlspecialchars($top_artist['name']); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bar-chart-line-fill me-2"></i>Top 10 Artists by Sales</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="height: 270px;">
                <canvas id="salesBarChart" style="max-height: 100%; width: 100%;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pie-chart-fill me-2"></i>Sales Distribution</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="height: 270px;">
                <canvas id="salesDoughnutChart" style="max-height: 100%; width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>




<!-- Interactive Data Table -->
<div class="card shadow-sm">
    <div class="card-header"><h5 class="mb-0"><i class="bi bi-table me-2"></i>All Album Sales Data</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="albumsTable" class="table table-striped table-hover" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th>Artist</th>
                        <th>Album</th>
                        <th>Year</th>
                        <th>Sales</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_albums as $album): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($album['artist_name']); ?></td>
                        <td><?php echo htmlspecialchars($album['album_name']); ?></td>
                        <td><?php echo htmlspecialchars($album['year']); ?></td>
                        <td><?php echo number_format($album['sales']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Initialize DataTables
    new DataTable('#albumsTable', {
        responsive: true,
        order: [[3, 'desc']] // Sort by Sales column descending by default
    });

    // 2. Prepare Chart Data (passed from PHP)
    const chartLabels = <?php echo $chart_labels; ?>;
    const chartSalesData = <?php echo $chart_sales; ?>;
    
    // Custom colors for charts
    const chartColors = [
        'rgba(54, 162, 235, 0.6)', 'rgba(255, 99, 132, 0.6)', 'rgba(75, 192, 192, 0.6)',
        'rgba(255, 206, 86, 0.6)', 'rgba(153, 102, 255, 0.6)', 'rgba(255, 159, 64, 0.6)',
        'rgba(99, 255, 132, 0.6)', 'rgba(132, 99, 255, 0.6)', 'rgba(235, 54, 162, 0.6)',
        'rgba(162, 235, 54, 0.6)'
    ];

    // 3. Initialize Bar Chart
    const barCtx = document.getElementById('salesBarChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Total Sales',
                data: chartSalesData,
                backgroundColor: chartColors,
                borderColor: chartColors.map(c => c.replace('0.6', '1')),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return value.toLocaleString(); }
                    }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // 4. Initialize Doughnut Chart (Top 5 for clarity)
    const doughnutCtx = document.getElementById('salesDoughnutChart').getContext('2d');
    new Chart(doughnutCtx, {
        type: 'doughnut',
        data: {
            labels: chartLabels.slice(0, 5),
            datasets: [{
                data: chartSalesData.slice(0, 5),
                backgroundColor: chartColors.slice(0, 5),
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
});
</script>

<?php include 'templates/footer.php'; ?>