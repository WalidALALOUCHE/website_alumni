</main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>ENSAF</h3>
                <div class="footer-logo">
                    <img src="<?php echo ROOT_URL; ?>/assets/img/logo-ensaf.png" alt="ENSAF Logo">
                </div>
                <p>École Nationale des Sciences Appliquées de Fès</p>
            </div>
            
            <div class="footer-section">
                <h3>Contact</h3>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>Route d'Imouzzer, Fès 30000, Maroc</p>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <p>+212 535 600 403</p>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <p>contact@ensaf.ac.ma</p>
                    </div>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Liens Rapides</h3>
                <ul class="footer-links">
                    <li><a href="<?php echo ROOT_URL; ?>/formations.php">Formations</a></li>
                    <li><a href="<?php echo ROOT_URL; ?>/opportunities.php">Opportunités</a></li>
                    <li><a href="<?php echo ROOT_URL; ?>/events.php">Événements</a></li>
                    <li><a href="<?php echo ROOT_URL; ?>/alumni.php">Annuaire</a></li>
                    <li><a href="<?php echo ROOT_URL; ?>/contact.php">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Newsletter</h3>
                <div class="newsletter">
                    <h4>Restez informé</h4>
                    <form class="newsletter-form" action="subscribe.php" method="POST">
                        <input type="email" name="email" placeholder="Votre email" required>
                        <button type="submit"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> ENSAF. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>