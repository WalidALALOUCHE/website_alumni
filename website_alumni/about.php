<?php
require_once 'config.php';

// Set page title
$page_title = "À propos";

// Include header
include 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>À propos de l'ENSAF</h1>
    </div>
    
    <section class="about-section">
        <div class="history-section">
            <h2>Histoire et Mission</h2>
            <p>L'École Nationale des Sciences Appliquées de Fès (ENSAF) est un établissement public de formation d'ingénieurs créé en 2005, relevant de l'Université Sidi Mohamed Ben Abdellah de Fès.</p>
            <p>Notre mission est de former des ingénieurs de haut niveau, dotés de compétences techniques solides et de qualités humaines essentielles pour contribuer au développement technologique et économique du Maroc.</p>
        </div>
        
        <div class="values-section">
            <h2>Nos Valeurs</h2>
            <div class="values-grid">
                <div class="value-card">
                    <i class="fas fa-brain"></i>
                    <h3>Excellence</h3>
                    <p>Nous visons l'excellence dans tous les aspects de notre enseignement et recherche.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-lightbulb"></i>
                    <h3>Innovation</h3>
                    <p>Nous encourageons l'innovation et la créativité dans la résolution des problèmes.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-hands-helping"></i>
                    <h3>Collaboration</h3>
                    <p>Nous valorisons le travail d'équipe et les partenariats productifs.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-globe-africa"></i>
                    <h3>Responsabilité</h3>
                    <p>Nous sommes engagés pour un développement durable et responsable.</p>
                </div>
            </div>
        </div>
    </section>
    
    <section class="stats-section">
        <h2>ENSAF en Chiffres</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">1354</div>
                <div class="stat-label">Étudiants</div>
                <div class="stat-description">dont 773 élèves ingénieurs en 2023</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">224</div>
                <div class="stat-label">Diplômés</div>
                <div class="stat-description">pour l'année 2022</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">64</div>
                <div class="stat-label">Enseignants Chercheurs</div>
                <div class="stat-description">et 2 ingénieurs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">23</div>
                <div class="stat-label">Personnel Administratif</div>
                <div class="stat-description">et 3 techniciens</div>
            </div>
        </div>
    </section>
    
    <section class="team-section">
        <h2>Notre Direction</h2>
        <div class="team-members">
            <div class="team-member">
                <img src="https://via.placeholder.com/200x200?text=Directeur" alt="Directeur" class="team-image">
                <h3>Prof. Mohammed Hassani</h3>
                <p class="member-title">Directeur de l'ENSAF</p>
                <p class="member-bio">Professeur en informatique avec plus de 20 ans d'expérience dans l'enseignement supérieur et la recherche.</p>
            </div>
            <div class="team-member">
                <img src="https://via.placeholder.com/200x200?text=Directeur+Adjoint" alt="Directeur Adjoint" class="team-image">
                <h3>Prof. Amina Tazi</h3>
                <p class="member-title">Directrice Adjointe</p>
                <p class="member-bio">Spécialiste en génie électrique et responsable des relations internationales de l'école.</p>
            </div>
            <div class="team-member">
                <img src="https://via.placeholder.com/200x200?text=Secrétaire+Général" alt="Secrétaire Général" class="team-image">
                <h3>M. Karim Bensouda</h3>
                <p class="member-title">Secrétaire Général</p>
                <p class="member-bio">En charge de la gestion administrative et financière de l'établissement.</p>
            </div>
        </div>
    </section>
    
    <section class="contact-section">
        <h2>Nous Contacter</h2>
        <div class="contact-info">
            <div class="contact-card">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Adresse</h3>
                <p>Route d'Imouzzer, Fès 30000, Maroc</p>
            </div>
            <div class="contact-card">
                <i class="fas fa-phone"></i>
                <h3>Téléphone</h3>
                <p>+212 535 600 403</p>
            </div>
            <div class="contact-card">
                <i class="fas fa-envelope"></i>
                <h3>Email</h3>
                <p>contact@ensaf.ac.ma</p>
            </div>
            <div class="contact-card">
                <i class="fas fa-clock"></i>
                <h3>Horaires</h3>
                <p>Lundi - Vendredi: 8:30 - 16:30</p>
            </div>
        </div>
    </section>
</div>

<?php
// Include footer
include 'includes/footer.php';
?> 