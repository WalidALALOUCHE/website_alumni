<?php
require_once 'config.php';

// Set page title
$page_title = "Formations";

// Get department filter from URL if present
$selected_dept = isset($_GET['dept']) ? sanitize_input($_GET['dept']) : '';

// Fetch all departments
$departments_query = "SELECT * FROM departments ORDER BY name ASC";
$departments_result = mysqli_query($conn, $departments_query);

// Store departments in an array for later use
$departments = [];
if ($departments_result && mysqli_num_rows($departments_result) > 0) {
    while ($row = mysqli_fetch_assoc($departments_result)) {
        $departments[] = $row;
    }
    // Reset the result pointer
    mysqli_data_seek($departments_result, 0);
}

// Include header
include 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Nos Formations</h1>
        <p class="lead">L'ENSAF offre une formation d'ingénieur en 5 ans, divisée en deux cycles : le Cycle Préparatoire Intégré (2 ans) et le Cycle Ingénieur (3 ans)</p>
    </div>
    
    <section class="formation-overview">
        <h2>Aperçu de la Formation</h2>
        <div class="cycle-cards">
            <div class="cycle-card">
                <div class="cycle-header">
                    <h3>Cycle Préparatoire Intégré</h3>
                    <span class="cycle-duration">2 ans</span>
                </div>
                <div class="cycle-content">
                    <p>Les deux premières années constituent un tronc commun où les étudiants acquièrent des bases solides en sciences fondamentales et appliquées.</p>
                    <h4>Matières principales :</h4>
                    <ul>
                        <li>Mathématiques (Analyse, Algèbre, Probabilités)</li>
                        <li>Physique générale et appliquée</li>
                        <li>Informatique et programmation</li>
                        <li>Électronique et circuits</li>
                        <li>Langues et communication</li>
                        <li>Économie et gestion</li>
                    </ul>
                    <p>À l'issue de ce cycle, les étudiants choisissent leur spécialisation pour le Cycle Ingénieur en fonction de leurs résultats et préférences.</p>
                </div>
            </div>
            
            <div class="cycle-card">
                <div class="cycle-header">
                    <h3>Cycle Ingénieur</h3>
                    <span class="cycle-duration">3 ans</span>
                </div>
                <div class="cycle-content">
                    <p>Le Cycle Ingénieur permet aux étudiants de se spécialiser dans l'une des filières proposées par l'ENSAF.</p>
                    <h4>Caractéristiques :</h4>
                    <ul>
                        <li>Formation spécialisée dans un domaine d'ingénierie</li>
                        <li>Projets pratiques et travaux en laboratoire</li>
                        <li>Stages en entreprise (2 mois en 4ème année, 6 mois en 5ème année)</li>
                        <li>Projet de fin d'études</li>
                        <li>Modules de développement personnel et professionnel</li>
                    </ul>
                    <p>Ce cycle comprend également des cours sur l'entrepreneuriat, l'innovation et la gestion de projets.</p>
                </div>
            </div>
        </div>
    </section>
    
    <section id="departments" class="departments-section">
        <h2>Nos Filières</h2>
        <p>L'ENSAF propose plusieurs filières d'ingénierie pour répondre aux besoins du marché du travail et aux défis technologiques actuels.</p>
        
        <?php if (empty($departments)): ?>
            <!-- Default departments if none found in database -->
            <div class="departments-grid">
                <div class="department-card">
                    <div class="department-header">
                        <span class="department-code">GDNC</span>
                        <h3>Génie du Développement Numérique et Cybersécurité</h3>
                    </div>
                    <p>Formation axée sur le développement d'applications sécurisées et la protection des systèmes d'information.</p>
                    <a href="formations.php?dept=GDNC" class="btn-details">En savoir plus</a>
                </div>
                
                <div class="department-card">
                    <div class="department-header">
                        <span class="department-code">ISDIA</span>
                        <h3>Ingénierie en Science de Données et Intelligence Artificielle</h3>
                    </div>
                    <p>Formation à l'analyse de données massives et aux algorithmes d'intelligence artificielle.</p>
                    <a href="formations.php?dept=ISDIA" class="btn-details">En savoir plus</a>
                </div>
                
                <div class="department-card">
                    <div class="department-header">
                        <span class="department-code">GI</span>
                        <h3>Génie Informatique</h3>
                    </div>
                    <p>Formation polyvalente en développement logiciel, bases de données et réseaux informatiques.</p>
                    <a href="formations.php?dept=GI" class="btn-details">En savoir plus</a>
                </div>
                
                <div class="department-card">
                    <div class="department-header">
                        <span class="department-code">GM</span>
                        <h3>Génie Mécanique</h3>
                    </div>
                    <p>Formation en conception mécanique, fabrication et simulation numérique.</p>
                    <a href="formations.php?dept=GM" class="btn-details">En savoir plus</a>
                </div>
            </div>
        <?php else: ?>
            <div class="departments-grid">
                <?php foreach ($departments as $dept): ?>
                    <div class="department-card <?php echo ($selected_dept == $dept['short_code']) ? 'active' : ''; ?>">
                        <div class="department-header">
                            <span class="department-code"><?php echo htmlspecialchars($dept['short_code']); ?></span>
                            <h3><?php echo htmlspecialchars($dept['name']); ?></h3>
                        </div>
                        <p><?php echo htmlspecialchars($dept['description'] ?? 'Formation d\'ingénieurs spécialisés'); ?></p>
                        <a href="formations.php?dept=<?php echo urlencode($dept['short_code']); ?>" class="btn-details">En savoir plus</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    
    <?php if (!empty($selected_dept)): ?>
        <section class="department-details">
            <?php
            // Find the selected department
            $found = false;
            $department = null;
            
            foreach ($departments as $dept) {
                if ($dept['short_code'] == $selected_dept) {
                    $department = $dept;
                    $found = true;
                    break;
                }
            }
            
            if ($found):
            ?>
                <h2><?php echo htmlspecialchars($department['name']); ?></h2>
                <div class="department-info">
                    <div class="department-description">
                        <h3>Présentation</h3>
                        <p><?php echo htmlspecialchars($department['description'] ?? 'Cette filière forme des ingénieurs hautement qualifiés capables de répondre aux défis technologiques dans leur domaine de spécialisation.'); ?></p>
                        
                        <h3>Objectifs de la formation</h3>
                        <ul>
                            <li>Former des ingénieurs polyvalents et opérationnels</li>
                            <li>Développer des compétences techniques avancées</li>
                            <li>Favoriser l'esprit d'innovation et d'entrepreneuriat</li>
                            <li>Préparer aux défis technologiques actuels et futurs</li>
                        </ul>
                        
                        <h3>Débouchés professionnels</h3>
                        <p>Les diplômés de cette filière peuvent accéder à divers postes dans des secteurs variés :</p>
                        <ul>
                            <li>Entreprises industrielles et de services</li>
                            <li>Bureaux d'études et de conseil</li>
                            <li>Startups et entreprises innovantes</li>
                            <li>Organismes publics et semi-publics</li>
                            <li>Poursuite d'études doctorales</li>
                        </ul>
                    </div>
                    
                    <div class="curriculum">
                        <h3>Programme d'études</h3>
                        <div class="year-tabs">
                            <div class="tab active" data-year="3">3ème année</div>
                            <div class="tab" data-year="4">4ème année</div>
                            <div class="tab" data-year="5">5ème année</div>
                        </div>
                        
                        <div class="year-content active" id="year-3">
                            <h4>Modules de la 3ème année</h4>
                            <ul class="modules-list">
                                <li>Module 1 : Fondamentaux de la spécialité</li>
                                <li>Module 2 : Mathématiques appliquées</li>
                                <li>Module 3 : Techniques de programmation</li>
                                <li>Module 4 : Gestion et économie</li>
                                <li>Module 5 : Langues et communication</li>
                                <li>Module 6 : Projet intégrateur</li>
                            </ul>
                        </div>
                        
                        <div class="year-content" id="year-4">
                            <h4>Modules de la 4ème année</h4>
                            <ul class="modules-list">
                                <li>Module 1 : Techniques avancées</li>
                                <li>Module 2 : Spécialisation I</li>
                                <li>Module 3 : Spécialisation II</li>
                                <li>Module 4 : Management de projets</li>
                                <li>Module 5 : Langues et Soft Skills</li>
                                <li>Module 6 : Stage d'application (2 mois)</li>
                            </ul>
                        </div>
                        
                        <div class="year-content" id="year-5">
                            <h4>Modules de la 5ème année</h4>
                            <ul class="modules-list">
                                <li>Module 1 : Spécialisation avancée</li>
                                <li>Module 2 : Innovation et Entrepreneuriat</li>
                                <li>Module 3 : Préparation à la vie professionnelle</li>
                                <li>Module 4 : Projet de Fin d'Études (6 mois)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <p>Les détails de cette filière ne sont pas disponibles pour le moment. Veuillez contacter l'administration pour plus d'informations.</p>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
    
    <section class="admission-section">
        <h2>Admission</h2>
        <div class="admission-cards">
            <div class="admission-card">
                <h3>Cycle Préparatoire</h3>
                <h4>Conditions d'accès :</h4>
                <ul>
                    <li>Baccalauréat scientifique ou technique</li>
                    <li>Sélection sur dossier (notes du baccalauréat)</li>
                    <li>Concours national commun pour les ENSA</li>
                </ul>
                <h4>Procédure :</h4>
                <ol>
                    <li>Pré-inscription en ligne sur le site de l'école</li>
                    <li>Dépôt du dossier de candidature</li>
                    <li>Étude des dossiers et sélection</li>
                    <li>Publication des résultats</li>
                    <li>Inscription définitive</li>
                </ol>
            </div>
            
            <div class="admission-card">
                <h3>Cycle Ingénieur</h3>
                <h4>Conditions d'accès :</h4>
                <ul>
                    <li>Réussite du Cycle Préparatoire Intégré</li>
                    <li>Admission sur concours pour les candidats externes</li>
                    <li>Passerelles possibles depuis d'autres établissements</li>
                </ul>
                <h4>Admission externe :</h4>
                <ol>
                    <li>Être titulaire d'un DEUG, DUT, BTS ou équivalent</li>
                    <li>Pré-sélection sur dossier</li>
                    <li>Épreuves écrites et entretien</li>
                    <li>Publication des résultats</li>
                </ol>
            </div>
        </div>
    </section>
    
    <section class="contact-section">
        <h2>Besoin de plus d'informations ?</h2>
        <p>Pour toute question concernant nos formations ou le processus d'admission, n'hésitez pas à nous contacter :</p>
        <div class="contact-buttons">
            <a href="contact.php" class="btn-primary"><i class="fas fa-envelope"></i> Nous contacter</a>
            <a href="tel:+212535600403" class="btn-secondary"><i class="fas fa-phone"></i> +212 535 600 403</a>
        </div>
    </section>
</div>

<script>
// Simple tab functionality for curriculum
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.year-tabs .tab');
    const contents = document.querySelectorAll('.year-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active class from all tabs and contents
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab
            tab.classList.add('active');
            
            // Show corresponding content
            const year = tab.getAttribute('data-year');
            document.getElementById('year-' + year).classList.add('active');
        });
    });
});
</script>

<?php
// Include footer
include 'includes/footer.php';
?> 