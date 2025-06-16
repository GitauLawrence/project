<?php
require_once 'includes/db-connect.php';
require_once 'includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>

<body>
    <!-- Page Banner -->
    <section class="page-banner">
        <div class="banner-overlay"></div>
        <div class="container">
            <h1>Contact Us</h1>
            <div class="breadcrumb">
                <a href="index.php">Home</a> / Contact
            </div>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="contact-info-section">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-form-container">
                    <div class="section-title">
                        <h2>Get In Touch</h2>
                        <p>We'd love to hear from you</p>
                    </div>
                    <form id="contact-form" class="contact-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Your Name</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Your Email</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Your Phone</label>
                                <input type="tel" id="phone" name="phone">
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" id="subject" name="subject">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message">Your Message</label>
                            <textarea id="message" name="message" rows="6" required></textarea>
                        </div>
                        <button type="submit" class="btn">Send Message</button>
                    </form>
                </div>
                
                <div class="contact-details">
                    <div class="section-title">
                        <h2>Contact Details</h2>
                        <p>Ways to reach us</p>
                    </div>
                    <div class="contact-info">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Our Office</h3>
                                <p>123 Westlands Road, Nairobi, Kenya</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Phone Number</h3>
                                <p>+254 708 600002</p>
                                
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Email Address</h3>
                                <p>office@sellamc.com </p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Working Hours</h3>
                                <p>Monday - Friday: 9:00 AM - 5:00 PM</p>
                                <p>Saturday: 10:00 AM - 2:00 PM</p>
                                <p>Sunday: Closed</p>
                            </div>
                        </div>
                    </div>
                    <div class="social-links">
                        <h3>Follow Us</h3>
                        <div class="social-icons">
                            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="container">
            <div class="section-title center">
                <h2>Our Location</h2>
                <p>Find us on the map</p>
            </div>
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.8176501045556!2d36.81227937493722!3d-1.2731856356164097!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f10a17046f66d%3A0xe5907c27ca57c34f!2sWestlands%2C%20Nairobi!5e0!3m2!1sen!2ske!4v1699345728830!5m2!1sen!2ske" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>