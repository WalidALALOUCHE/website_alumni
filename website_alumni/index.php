<?php
require_once 'config.php';

// Set page title
$page_title = SITE_NAME;

// Fetch the latest news
$news_query = "SELECT * FROM news ORDER BY published_at DESC LIMIT 3";
$news_result = mysqli_query($conn, $news_query);

// Fetch upcoming events
$events_query = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3";
$events_result = mysqli_query($conn, $events_query);

// Fetch all departments
$departments_query = "SELECT * FROM departments ORDER BY name ASC";
$departments_result = mysqli_query($conn, $departments_query);

// Add direct CSS link for fallback
$extra_css = '<link rel="stylesheet" href="' . ROOT_URL . '/styles.css">';

// Include header
include 'includes/header.php';
?>

<!-- Start main content -->
    <section id="home" class="hero">
        <div class="overlay">
            <h2>Bienvenue à ENSA FES</h2>
        <p>École Nationale des Sciences Appliquées de Fès - Formant les leaders de demain</p>
        <div class="hero-buttons">
            <a href="#" class="btn-primary" onclick="showSection('contact')">Nous Contacter</a>
            <a href="#" class="btn-primary" onclick="showSection('formations')">Nos Formations</a>
        </div>
        </div>
    </section>

<section id="about">
        <h2>ENSAF : CREATION & OBJECTIFS</h2>
    <div class="about-grid">
        <div class="about-card">
            <i class="fas fa-history"></i>
        <h3>Création & chiffres</h3>
        <ul>
            <li>L'ENSA de Fès est une école d'Ingénieurs créée en 2005</li>
            <li>1354 étudiants dont 773 élèves ingénieurs en 2023</li>
            <li>224 diplômés en 2022</li>
        </ul>
        </div>
        <div class="about-card">
            <i class="fas fa-bullseye"></i>
        <h3>Objectifs</h3>
        <ul>
            <li>Formation des ingénieurs d'état (avec un cursus de Bac+5)</li>
            <li>Recherche Scientifique et Technique et R&D.</li>
        </ul>
        </div>
        <div class="about-card">
            <i class="fas fa-users"></i>
        <h3>Personnels enseignants</h3>
        <ul>
            <li>64 Enseignants Chercheurs</li>
            <li>2 Ingénieurs</li>
        </ul>
        </div>
        <div class="about-card">
            <i class="fas fa-user-tie"></i>
        <h3>Personnels techniques et administratif</h3>
        <ul>
            <li>23 Personnels administratifs</li>
            <li>3 Personnels techniques</li>
        </ul>
        </div>
    </div>
    </section>

<section id="formations">
    <h2>Notre Formation</h2>
    <p class="section-intro">L'École Nationale des Sciences Appliquées de Fès (ENSAF) propose une formation
             d'ingénieur en 5 ans, structurée en deux cycles :</p>
    
    <div class="formation-timeline">
        <div class="timeline-item">
            <div class="timeline-content">
                <h3>Cycle Préparatoire Intégré (2 ans)</h3>
        <p>Les deux premières années constituent un tronc commun où les étudiants acquièrent des bases solides en :</p>
        <ul>
            <li>Mathématiques appliquées</li>
            <li>Physique et électronique</li>
            <li>Informatique et algorithmique</li>
            <li>Langues et communication</li>
        </ul>
        <p>À l'issue de ce cycle, les étudiants passent en Cycle Ingénieur, selon leur choix de spécialisation et leurs résultats.</p>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-content">
                <h3>Cycle Ingénieur (3 ans)</h3>
        <p>Le Cycle Ingénieur permet aux étudiants de se spécialiser dans l'une des filières proposées par l'ENSAF :</p>
                <div class="departments-grid">
            <?php
            if (mysqli_num_rows($departments_result) > 0) {
                while ($row = mysqli_fetch_assoc($departments_result)) {
                            echo '<div class="department-card">';
                            echo '<h4>' . htmlspecialchars($row['short_code']) . '</h4>';
                            echo '<p>' . htmlspecialchars($row['name']) . '</p>';
                            echo '</div>';
                }
            } else {
                        $default_departments = [
                            ['name' => 'Génie du Développement Numérique et Cybersécurité', 'short_code' => 'GDNC'],
                            ['name' => 'Ingénierie en Science de Données et Intelligence Artificielle', 'short_code' => 'ISDIA'],
                            ['name' => 'Génie Informatique', 'short_code' => 'GI'],
                            ['name' => 'Génie Mécanique', 'short_code' => 'GM'],
                            ['name' => 'Génie Énergétique et Systèmes Intelligents', 'short_code' => 'GESI'],
                            ['name' => 'Génie Mécatronique', 'short_code' => 'GMeca'],
                            ['name' => 'Génie Industriel', 'short_code' => 'GIn'],
                            ['name' => 'Ingénierie Informatique, Intelligence Artificielle et Confiance Numérique', 'short_code' => '3IACN'],
                            ['name' => 'Ingénierie des Systèmes Embarqués et Intelligence Artificielle', 'short_code' => 'ISEIA'],
                            ['name' => 'Ingénierie Logicielle et Intelligence Artificielle', 'short_code' => 'ILIA'],
                            ['name' => 'Ingénierie des Systèmes Communicants et Sécurité Informatique', 'short_code' => 'ISCSI']
                        ];
                        
                        foreach ($default_departments as $dept) {
                            echo '<div class="department-card">';
                            echo '<h4>' . $dept['short_code'] . '</h4>';
                            echo '<p>' . $dept['name'] . '</p>';
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="formation-goal">
        <h3>Objectif de la formation</h3>
        <p>Cette formation complète vise à former des ingénieurs compétents et polyvalents, capables d'innover et de répondre aux défis technologiques actuels.</p>
    </div>
    </section>

<section id="espace">
    <h2>Espaces Utilisateurs</h2>
    <p class="section-intro">Accédez à votre espace personnel en fonction de votre profil</p>
        <div class="cards">
            <div class="card">
                <i class="fas fa-briefcase"></i>
                <h3>Espace Enseignants</h3>
            <p>Gérez vos cours, emplois du temps et notes</p>
            <a href="login.php?type=professor">Connexion <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="card">
                <i class="fas fa-user-graduate"></i>
                <h3>Espace Étudiants</h3>
            <p>Consultez vos cours, notes et documents</p>
            <a href="login.php?type=student">Connexion <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="card">
                <i class="fas fa-school"></i>
                <h3>Espace Scolarité</h3>
                <p>Administration et gestion des étudiants</p>
                <a href="login.php?type=admin">Connexion <i class="fas fa-arrow-right"></i></a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="admin/verify_users.php" class="admin-action">
                        <i class="fas fa-user-check"></i> Vérifier les utilisateurs
                        <?php 
                        $pending_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE verification_status = 'pending'");
                        $count = mysqli_fetch_assoc($pending_count);
                        if ($count['count'] > 0): 
                        ?>
                            <span class="badge"><?php echo $count['count']; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            </div>
            <div class="card">
                <i class="fas fa-graduation-cap"></i>
                <h3>Espace Lauréats</h3>
            <p>Réseau des anciens et opportunités professionnelles</p>
            <a href="login.php?type=alumni">Connexion <i class="fas fa-arrow-right"></i></a>
        </div>
        </div>
    </section>

<!-- News Section -->
<section id="news">
        <h2>Actualités</h2>
    <p class="section-intro">Restez informé des dernières nouvelles et événements de l'ENSAF</p>
        <div class="news-container">
            <?php
            if (mysqli_num_rows($news_result) > 0) {
                while ($news_item = mysqli_fetch_assoc($news_result)) {
                    ?>
                    <div class="news-item">
                        <?php if (!empty($news_item['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($news_item['image_url']); ?>" alt="Image actualité" class="news-image">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/600x400?text=ENSAF+News" alt="Image par défaut" class="news-image">
                        <?php endif; ?>
                        <div class="news-content">
                            <h3><?php echo htmlspecialchars($news_item['title']); ?></h3>
                        <p class="news-date"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($news_item['published_at'])); ?></p>
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
                ]
            ];
            
            foreach ($placeholder_news as $news) {
                ?>
                <div class="news-item">
                    <img src="https://via.placeholder.com/600x400?text=ENSAF+News" alt="Image par défaut" class="news-image">
                    <div class="news-content">
                        <h3><?php echo $news['title']; ?></h3>
                        <p class="news-date"><i class="far fa-calendar-alt"></i> <?php echo $news['date']; ?></p>
                        <p><?php echo $news['content']; ?></p>
                        <a href="#" class="read-more">Lire la suite <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <?php
            }
            }
            ?>
        </div>
    <div class="center-button">
        <a href="news.php" class="btn-primary">Voir toutes les actualités</a>
        </div>
    </section>

<!-- Events Section -->
<section id="events">
        <h2>Événements à venir</h2>
    <p class="section-intro">Participez aux événements organisés par l'ENSAF et ses partenaires</p>
        <div class="events-container">
            <?php
            if (mysqli_num_rows($events_result) > 0) {
                while ($event = mysqli_fetch_assoc($events_result)) {
                    ?>
                    <div class="event-card">
                        <div class="event-date">
                            <span class="event-day"><?php echo date('d', strtotime($event['event_date'])); ?></span>
                            <span class="event-month"><?php echo date('M', strtotime($event['event_date'])); ?></span>
                        </div>
                        <div class="event-details">
                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p class="event-time"><i class="far fa-clock"></i> <?php echo date('H:i', strtotime($event['event_time'])); ?></p>
                            <p class="event-location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                            <p><?php echo substr(htmlspecialchars($event['description']), 0, 100) . '...'; ?></p>
                        <a href="event_detail.php?id=<?php echo $event['id']; ?>" class="read-more">Plus d'infos <i class="fas fa-arrow-right"></i></a>
                    </div>
                    </div>
                    <?php
                }
            } else {
            // Display placeholder events if no real events exist
            $placeholder_events = [
                [
                    'title' => 'Forum des entreprises ENSAF',
                    'date' => date('Y-m-d', strtotime('+7 days')),
                    'time' => '09:00',
                    'location' => 'Amphi A, ENSAF',
                    'description' => 'Forum annuel permettant aux étudiants de rencontrer des entreprises qui recrutent dans les domaines de l\'ingénierie.'
                ],
                [
                    'title' => 'Hackathon ENSAF Innovation',
                    'date' => date('Y-m-d', strtotime('+14 days')),
                    'time' => '10:00',
                    'location' => 'Salle Informatique, ENSAF',
                    'description' => 'Compétition de 48 heures où les étudiants développent des solutions innovantes à des problèmes réels.'
                ],
                [
                    'title' => 'Cérémonie de remise des diplômes',
                    'date' => date('Y-m-d', strtotime('+21 days')),
                    'time' => '15:00',
                    'location' => 'Théâtre Mohammed El Mennouni, Fès',
                    'description' => 'Cérémonie officielle de remise des diplômes aux lauréats de la promotion 2023.'
                ]
            ];
            
            foreach ($placeholder_events as $event) {
                ?>
                <div class="event-card">
                    <div class="event-date">
                        <span class="event-day"><?php echo date('d', strtotime($event['date'])); ?></span>
                        <span class="event-month"><?php echo date('M', strtotime($event['date'])); ?></span>
                </div>
                    <div class="event-details">
                        <h3><?php echo $event['title']; ?></h3>
                        <p class="event-time"><i class="far fa-clock"></i> <?php echo $event['time']; ?></p>
                        <p class="event-location"><i class="fas fa-map-marker-alt"></i> <?php echo $event['location']; ?></p>
                        <p><?php echo $event['description']; ?></p>
                        <a href="#" class="read-more">Plus d'infos <i class="fas fa-arrow-right"></i></a>
                </div>
                </div>
            <?php
            }
            }
            ?>
        </div>
    <div class="center-button">
        <a href="events.php" class="btn-primary">Voir tous les événements</a>
        </div>
    </section>

<!-- Contact Section -->
<section id="contact" class="section">
    <div class="container">
        <h2>Contactez-nous</h2>
        <p class="section-intro">Nous sommes là pour répondre à vos questions. N'hésitez pas à nous contacter.</p>
        
        <div class="contact-preview">
            <div class="contact-cards">
        <div class="contact-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Adresse</h3>
                    <p>Route d'Imouzzer, Fès 30000, Maroc</p>
                </div>
                
                <div class="contact-card">
                    <i class="fas fa-phone-alt"></i>
                    <h3>Téléphone</h3>
                    <p>+212 535 600 403</p>
                </div>
                
                <div class="contact-card">
                    <i class="fas fa-envelope"></i>
                    <h3>Email</h3>
                    <p>contact@ensaf.ac.ma</p>
                </div>
            </div>
            
            <div class="contact-action">
                <p>Pour plus d'informations ou pour nous envoyer un message :</p>
                <a href="contact.php" class="btn-primary">Page de contact</a>
            </div>
        </div>
    </div>
</section>

<!-- Alumni Section -->
<section id="alumini">
    <h2>Nos Alumni</h2>
    <p class="section-intro">Découvrez le réseau des anciens diplômés de l'ENSAF</p>
    
    <div class="alumni-filters">
        <select class="filter-select" id="year-filter">
            <option value="">Toutes les promotions</option>
            <?php
            for ($year = date('Y'); $year >= 2010; $year--) {
                echo "<option value=\"$year\">Promotion $year</option>";
            }
            ?>
        </select>
        
        <select class="filter-select" id="department-filter">
            <option value="">Toutes les filières</option>
            <?php
            if (mysqli_num_rows($departments_result) > 0) {
                mysqli_data_seek($departments_result, 0);
                while ($row = mysqli_fetch_assoc($departments_result)) {
                    echo "<option value=\"" . htmlspecialchars($row['short_code']) . "\">" . htmlspecialchars($row['name']) . "</option>";
                }
            } else {
                echo "<option value=\"GI\">Génie Informatique</option>";
                echo "<option value=\"GM\">Génie Mécanique</option>";
                echo "<option value=\"GESI\">Génie Énergétique et Systèmes Intelligents</option>";
                echo "<option value=\"GMeca\">Génie Mécatronique</option>";
            }
            ?>
        </select>
        
        <button id="filter-button" class="btn-primary">Filtrer</button>
    </div>
    
    <div class="alumni-grid">
        <?php
        // Placeholder alumni for display
        $placeholder_alumni = [
            [
                'name' => 'Mohammed Alaoui',
                'photo' => 'https://randomuser.me/api/portraits/men/1.jpg',
                'year' => '2020',
                'department' => 'Génie Informatique',
                'linkedin' => '#',
                'github' => '#',
                'twitter' => '#'
            ],
            [
                'name' => 'Fatima Zahra Benjelloun',
                'photo' => 'https://randomuser.me/api/portraits/women/2.jpg',
                'year' => '2021',
                'department' => 'Génie Mécanique',
                'linkedin' => '#',
                'github' => '#',
                'twitter' => '#'
            ],
            [
                'name' => 'Ahmed Tazi',
                'photo' => 'https://randomuser.me/api/portraits/men/3.jpg',
                'year' => '2019',
                'department' => 'Génie Énergétique',
                'linkedin' => '#',
                'github' => '#',
                'twitter' => '#'
            ],
            [
                'name' => 'Amina El Fassi',
                'photo' => 'https://randomuser.me/api/portraits/women/4.jpg',
                'year' => '2022',
                'department' => 'Génie Informatique',
                'linkedin' => '#',
                'github' => '#',
                'twitter' => '#'
            ],
            [
                'name' => 'Karim Ouazzani',
                'photo' => 'https://randomuser.me/api/portraits/men/5.jpg',
                'year' => '2020',
                'department' => 'Génie Mécatronique',
                'linkedin' => '#',
                'github' => '#',
                'twitter' => '#'
            ],
            [
                'name' => 'Laila Benkirane',
                'photo' => 'https://randomuser.me/api/portraits/women/6.jpg',
                'year' => '2018',
                'department' => 'Génie Industriel',
                'linkedin' => '#',
                'github' => '#',
                'twitter' => '#'
            ]
        ];
        
        foreach ($placeholder_alumni as $alumni) {
            ?>
            <div class="alumni-card">
                <img src="<?php echo $alumni['photo']; ?>" alt="<?php echo $alumni['name']; ?>" class="alumni-photo">
                <div class="alumni-info">
                    <h3 class="alumni-name"><?php echo $alumni['name']; ?></h3>
                    <p class="alumni-year">Promotion <?php echo $alumni['year']; ?></p>
                    <p class="alumni-department"><?php echo $alumni['department']; ?></p>
                <div class="social-links">
                        <a href="<?php echo $alumni['linkedin']; ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                        <a href="<?php echo $alumni['github']; ?>" target="_blank"><i class="fab fa-github"></i></a>
                        <a href="<?php echo $alumni['twitter']; ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        </div>
    
    <div class="center-button">
        <a href="alumni.php" class="btn-primary">Voir tous les alumni</a>
    </div>
</section>

<!-- Include footer -->
<?php include 'includes/footer.php'; ?>