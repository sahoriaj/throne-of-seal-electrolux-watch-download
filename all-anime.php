<?php
$page_title = 'All Anime';
require_once 'header.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 24;
$offset = ($page - 1) * $perPage;

// Get total count
$totalAnime = $pdo->query("SELECT COUNT(*) FROM anime")->fetchColumn();
$totalPages = ceil($totalAnime / $perPage);

// Get anime with pagination
$stmt = $pdo->prepare("SELECT * FROM anime ORDER BY update_date DESC LIMIT ? OFFSET ?");
$stmt->execute([$perPage, $offset]);
$allAnime = $stmt->fetchAll();
?>

<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        padding: 40px 0;
        text-align: center;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(102,126,234,0.3);
    }
    
    .page-header h1 {
        font-size: 36px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
    }
    
    .page-header p {
        opacity: 0.9;
    }
    
    .anime-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 5px;
    }
    
    .anime-card {
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: all 0.3s;
        text-decoration: none;
        color: #333;
        position: relative;
    }
    
    .anime-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }
    
    .anime-card-image {
        position: relative;
        padding-top: 100%;
        overflow: hidden;
    }
    
    .anime-card-image img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    
    .anime-card:hover .anime-card-image img {
        transform: scale(1.1);
    }
    
    .badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: linear-gradient(135deg, #FF6B35, #F7931E);
        color: #fff;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 2px 10px rgba(255,107,53,0.5);
    }
    
    .badge.hot {
        background: linear-gradient(135deg, #FF416C, #FF4B2B);
    }
    
    .badge.new {
        background: linear-gradient(135deg, #4ECDC4, #44A08D);
    }
    
    .anime-card-info {
        padding: 15px;
    }
    
    .anime-card-title {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 40px;
    }
    
    .anime-card-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        color: #666;
    }
    
    .anime-card-meta i {
        margin-right: 5px;
        color: #667eea;
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-top: 40px;
    }
    
    .pagination a,
    .pagination span {
        padding: 10px 15px;
        background: #fff;
        border-radius: 8px;
        text-decoration: none;
        color: #333;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .pagination a:hover {
        background: #667eea;
        color: #fff;
        transform: translateY(-2px);
    }
    
    .pagination .active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        font-weight: 600;
    }
    
    .pagination .disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>

<main class="main-content">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-th"></i> All Anime</h1>
            <p>Browse through our collection of <?php echo $totalAnime; ?> anime series</p>
        </div>
        
        <div class="anime-grid">
            <?php foreach($allAnime as $anime): ?>
                <a href="anime.php?slug=<?php echo $anime['slug']; ?>" class="anime-card">
                    <div class="anime-card-image">
                        <img src="<?php echo $anime['poster'] ? 'uploads/'.$anime['poster'] : 'uploads/default.jpg'; ?>" alt="<?php echo htmlspecialchars($anime['title']); ?>">
                        <?php if($anime['is_hot']): ?>
                            <span class="badge hot"><i class="fas fa-fire"></i> HOT</span>
                        <?php elseif($anime['is_new']): ?>
                            <span class="badge new"><i class="fas fa-sparkles"></i> NEW</span>
                        <?php endif; ?>
                    </div>
                    <div class="anime-card-info">
                        <div class="anime-card-title"><?php echo htmlspecialchars($anime['title']); ?></div>
                        <div class="anime-card-meta">
                            <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($anime['update_date'])); ?></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        
        <?php if($totalPages > 1): ?>
        <div class="pagination">
            <?php if($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            <?php else: ?>
                <span class="disabled">
                    <i class="fas fa-chevron-left"></i> Previous
                </span>
            <?php endif; ?>
            
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            
            for($i = $start; $i <= $end; $i++):
            ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            <?php else: ?>
                <span class="disabled">
                    Next <i class="fas fa-chevron-right"></i>
                </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'footer.php'; ?>