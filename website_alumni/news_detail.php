<?php
require_once 'config.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirect to news page if no ID is provided
    header('Location: news.php');
    exit;
}

// Get news ID and sanitize it
$news_id = sanitize_input($_GET['id']);

// Fetch the news item
$news_query = "SELECT n.*, u.full_name as author_name 
               FROM news n 
               LEFT JOIN users u ON n.author_id = u.id 
               WHERE n.id = ?";
$stmt = mysqli_prepare($conn, $news_query);
mysqli_stmt_bind_param($stmt, "i", $news_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if news exists
if (mysqli_num_rows($result) === 0) {
    // Redirect to news page if news not found
    header('Location: news.php');
    exit;
}

// Get news data
$news = mysqli_fetch_assoc($result);

// Set page title
$page_title = $news['title'];

// Get related news
$related_query = "SELECT * FROM news 
                  WHERE id != ? 
                  ORDER BY published_at DESC 
                  LIMIT 3";
$stmt_related = mysqli_prepare($conn, $related_query);
mysqli_stmt_bind_param($stmt_related, "i", $news_id);
mysqli_stmt_execute($stmt_related);
$related_result = mysqli_stmt_get_result($stmt_related);

// Include header
include 'includes/header.php';
?>

<main>
    <div class="container">
        <a href="news.php" class="back-button"><i class="fas fa-arrow-left"></i> Retour aux actualités</a>
        
        <article class="news-detail">
            <div class="news-header">
                <h1><?php echo htmlspecialchars($news['title']); ?></h1>
                <div class="news-meta">
                    <span><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($news['published_at'])); ?></span>
                    
                    <?php if (!empty($news['category'])): ?>
                        <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($news['category']); ?></span>
                    <?php endif; ?>
                    
                    <?php if (!empty($news['author_name'])): ?>
                        <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($news['author_name']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!empty($news['image_url'])): ?>
                <div class="news-featured-image">
                    <img src="<?php echo htmlspecialchars($news['image_url']); ?>" alt="<?php echo htmlspecialchars($news['title']); ?>">
                </div>
            <?php endif; ?>
            
            <div class="news-content">
                <?php 
                // Display content with paragraph breaks
                echo nl2br(htmlspecialchars($news['content'])); 
                ?>
            </div>
            
            <?php if (!empty($news['source_url'])): ?>
                <div class="news-source">
                    <p>Source: <a href="<?php echo htmlspecialchars($news['source_url']); ?>" target="_blank"><?php echo htmlspecialchars($news['source_name'] ?? 'En savoir plus'); ?></a></p>
                </div>
            <?php endif; ?>
            
            <div class="news-share">
                <h4>Partager cette actualité</h4>
                <div class="social-share">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(ROOT_URL . '/news_detail.php?id=' . $news_id); ?>" target="_blank" class="facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(ROOT_URL . '/news_detail.php?id=' . $news_id); ?>&text=<?php echo urlencode($news['title']); ?>" target="_blank" class="twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(ROOT_URL . '/news_detail.php?id=' . $news_id); ?>&title=<?php echo urlencode($news['title']); ?>" target="_blank" class="linkedin"><i class="fab fa-linkedin-in"></i></a>
                    <a href="mailto:?subject=<?php echo urlencode($news['title']); ?>&body=<?php echo urlencode('Découvrez cette actualité de l\'ENSAF: ' . ROOT_URL . '/news_detail.php?id=' . $news_id); ?>" class="email"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
        </article>
        
        <?php if (mysqli_num_rows($related_result) > 0): ?>
            <section class="related-news">
                <h3>Articles similaires</h3>
                <div class="related-news-grid">
                    <?php while ($related = mysqli_fetch_assoc($related_result)): ?>
                        <div class="related-news-card">
                            <?php if (!empty($related['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($related['image_url']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>" class="related-news-image">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/400x300?text=ENSAF+News" alt="Image par défaut" class="related-news-image">
                            <?php endif; ?>
                            <div class="related-news-content">
                                <h4><?php echo htmlspecialchars($related['title']); ?></h4>
                                <p class="related-news-date"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($related['published_at'])); ?></p>
                                <a href="news_detail.php?id=<?php echo $related['id']; ?>" class="read-more">Lire l'article <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</main>

<?php
// Include footer
include 'includes/footer.php';
?> 