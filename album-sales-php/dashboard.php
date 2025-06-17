<?php
require_once 'includes/auth_check.php';
require_once 'config/database.php';

$conn = get_db_connection();

// --- Data Fetching (No changes here) ---
$sales_sql = "SELECT a.name, SUM(b.sales) as total_sales FROM artists a JOIN albums b ON a.id = b.artist_id GROUP BY a.id, a.name ORDER BY total_sales DESC";
$artists_with_sales = $conn->query($sales_sql)->fetch_all(MYSQLI_ASSOC);
$top_artist = $artists_with_sales[0] ?? null;

$count_sql = "SELECT a.name, COUNT(b.id) as album_count FROM artists a JOIN albums b ON a.id = b.artist_id GROUP BY a.id, a.name ORDER BY album_count DESC";
$artists_with_album_count = $conn->query($count_sql)->fetch_all(MYSQLI_ASSOC);

$searched_albums = [];
$search_term = '';
if (isset($_GET['search_artist']) && !empty($_GET['search_artist'])) {
    $search_term = $_GET['search_artist'];
    $stmt = $conn->prepare("SELECT al.name, al.year, al.sales, al.album_cover_path FROM albums al JOIN artists ar ON al.artist_id = ar.id WHERE ar.name LIKE ? ORDER BY al.year DESC");
    $like_term = "%" . $search_term . "%";
    $stmt->bind_param("s", $like_term);
    $stmt->execute();
    $searched_albums = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
$conn->close();

$page_title = "Dashboard";
include 'templates/header.php';
?>

<!-- Top Artist & Search Row -->
<div class="row mb-4">
    <!-- Top Artist Card -->
    <div class="col-lg-5">
        <div class="card h-100 shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-trophy-fill text-warning"></i> Top Artist by Sales</h5>
            </div>
            <div class="card-body text-center">
                <?php if ($top_artist): ?>
                    <h2 class="card-title"><?php echo htmlspecialchars($top_artist['name']); ?></h2>
                    <p class="display-6 text-primary"><?php echo number_format($top_artist['total_sales']); ?> <small class="text-muted">sales</small></p>
                <?php else: ?>
                    <p class="text-muted">No sales data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Search Card -->
    <div class="col-lg-7">
        <div class="card h-100 shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-search"></i> Find an Artist's Albums</h5>
            </div>
            <div class="card-body d-flex align-items-center">
                <form method="GET" action="dashboard.php" class="w-100">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-lg" name="search_artist" placeholder="e.g., Stray Kids, IVE, (G)I-dle..." value="<?php echo htmlspecialchars($search_term); ?>">
                        <button class="btn btn-outline-primary" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Search Results Row -->
<?php if (!empty($search_term)): ?>
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0">Search Results for "<?php echo htmlspecialchars($search_term); ?>"</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($searched_albums)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr><th>Cover</th><th>Album Name</th><th>Year</th><th>Sales</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($searched_albums as $album): ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($album['album_cover_path']); ?>" alt="Cover" width="50" height="50" class="rounded"></td>
                        <td><?php echo htmlspecialchars($album['name']); ?></td>
                        <td><?php echo htmlspecialchars($album['year']); ?></td>
                        <td><?php echo number_format($album['sales']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="alert alert-info mb-0">No albums found matching your search.</div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>


<!-- Main Data Tables Row -->
<div class="row">
    <!-- Combined Sales Table -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-bar-chart-line-fill"></i> Combined Sales per Artist</h5></div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover">
                        <thead><tr><th>#</th><th>Artist</th><th>Total Sales</th></tr></thead>
                        <tbody>
                        <?php foreach($artists_with_sales as $index => $artist): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($artist['name']); ?></td>
                                <td><?php echo number_format($artist['total_sales']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Album Count Table -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-journal-album"></i> Total Albums per Artist</h5></div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover">
                        <thead><tr><th>#</th><th>Artist</th><th># of Albums</th></tr></thead>
                        <tbody>
                        <?php foreach($artists_with_album_count as $index => $artist): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($artist['name']); ?></td>
                                <td><?php echo htmlspecialchars($artist['album_count']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>