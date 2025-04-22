<?php
require_once 'config.php';

// Set page title
$page_title = "Actualités";

// Pagination setup
$items_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Get total number of news items
$count_query = "SELECT COUNT(*) as total FROM news";
$count_result = mysqli_query($conn, $count_query);
$total_items = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_items / $items_per_page);

// Ensure page is within valid range
if ($page < 1) $page = 1;
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

// Fetch news with pagination
$news_query = "SELECT * FROM news ORDER BY published_at DESC LIMIT $offset, $items_per_page";
$news_result = mysqli_query($conn, $news_query);

// Include header
include 'includes/header.php';
?>

<main>
    <div class="container">
        <div class="page-header">
            <h1>Actualités</h1>
            <p class="lead">Restez informé des dernières nouvelles et événements de l'ENSAF</p>
        </div>
        
        <div class="news-grid">
            <?php
            if (mysqli_num_rows($news_result) > 0) {
                while ($news_item = mysqli_fetch_assoc($news_result)) {
                    ?>
                    <div class="news-card">
                        <div class="news-image">
                            <?php if (!empty($news_item['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($news_item['image_url']); ?>" alt="<?php echo htmlspecialchars($news_item['title']); ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/600x400?text=ENSAF+News" alt="Image par défaut">
                            <?php endif; ?>
                            <div class="news-date">
                                <span><?php echo date('d', strtotime($news_item['published_at'])); ?></span>
                                <span><?php echo date('M', strtotime($news_item['published_at'])); ?></span>
                            </div>
                        </div>
                        <div class="news-content">
                            <h3><?php echo htmlspecialchars($news_item['title']); ?></h3>
                            <div class="news-meta">
                                <span><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($news_item['published_at'])); ?></span>
                                <?php if (!empty($news_item['category'])): ?>
                                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($news_item['category']); ?></span>
                                <?php endif; ?>
                            </div>
                            <p><?php echo substr(htmlspecialchars($news_item['content']), 0, 150) . '...'; ?></p>
                            <a href="news_detail.php?id=<?php echo $news_item['id']; ?>" class="read-more">Lire la suite <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                // Display placeholder news if no real news exists
                $placeholder_news = [
                    [
                        'title' => 'Journée portes ouvertes à l\'ENSAF',
                        'date' => date('d/m/Y', strtotime('-2 days')),
                        'content' => 'L\'École Nationale des Sciences Appliquées de Fès organise une journée portes ouvertes pour présenter ses formations et ses installations aux futurs étudiants.'
                    ],
                    [
                        'title' => 'Signature d\'une convention avec une entreprise internationale',
                        'date' => date('d/m/Y', strtotime('-5 days')),
                        'content' => 'L\'ENSAF a signé une convention de partenariat avec une entreprise internationale leader dans le domaine des technologies de l\'information.'
                    ],
                    [
                        'title' => 'Conférence sur l\'intelligence artificielle',
                        'date' => date('d/m/Y', strtotime('-10 days')),
                        'content' => 'Une conférence sur les avancées récentes en intelligence artificielle a été organisée à l\'ENSAF, avec la participation d\'experts nationaux et internationaux.'
                    ],
                    [
                        'title' => 'Hackathon national des écoles d\'ingénieurs',
                        'date' => date('d/m/Y', strtotime('-15 days')),
                        'content' => 'Les étudiants de l\'ENSAF ont brillé lors du hackathon national des écoles d\'ingénieurs, en remportant plusieurs prix dans différentes catégories.'
                    ],
                    [
                        'title' => 'Visite d\'une délégation internationale',
                        'date' => date('d/m/Y', strtotime('-20 days')),
                        'content' => 'Une délégation internationale a visité l\'ENSAF pour discuter des opportunités de collaboration dans le domaine de la recherche et de l\'innovation.'
                    ],
                    [
                        'title' => 'Lancement d\'un nouveau laboratoire de recherche',
                        'date' => date('d/m/Y', strtotime('-25 days')),
                        'content' => 'L\'ENSAF a inauguré un nouveau laboratoire de recherche dédié à l\'intelligence artificielle et aux systèmes embarqués, équipé des technologies les plus récentes.'
                    ]
                ];
                
                foreach ($placeholder_news as $news) {
                    $news_date = strtotime($news['date']);
                    ?>
                    <div class="news-card">
                        <div class="news-image">
                            <img src="https://via.placeholder.com/600x400?text=ENSAF+News" alt="Image par défaut">
                            <div class="news-date">
                                <span><?php echo date('d', $news_date); ?></span>
                                <span><?php echo date('M', $news_date); ?></span>
                            </div>
                        </div>
                        <div class="news-content">
                            <h3><?php echo $news['title']; ?></h3>
                            <div class="news-meta">
                                <span><i class="far fa-calendar-alt"></i> <?php echo $news['date']; ?></span>
                                <span><i class="fas fa-tag"></i> Événement</span>
                            </div>
                            <p><?php echo $news['content']; ?></p>
                            <a href="#" class="read-more">Lire la suite <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo ($page - 1); ?>" class="page-link"><i class="fas fa-chevron-left"></i> Précédent</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="page-link <?php echo ($page == $i) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo ($page + 1); ?>" class="page-link">Suivant <i class="fas fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
// Include footer
include 'includes/footer.php';
?> 