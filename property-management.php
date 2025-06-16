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
            <h1>Property Management</h1>
            <div class="breadcrumb">
                <a href="index.php">Home</a> / Property Management
            </div>
        </div>
    </section>

    <!-- Property Management Intro -->
    <section class="pm-intro">
        <div class="container">
            <div class="section-title center">
                <h2>Professional Property Management</h2>
                <p>Let us handle the complexities of property management</p>
            </div>
            <div class="pm-intro-content">
                <p>Managing tenants and properties can be both challenging and time-consuming for landlords. It's also easy to get things wrong in an ever-changing legal landscape. Our professional property management services allow you to enjoy the benefits of your investment without the stress.</p>
            </div>
        </div>
    </section>

    <!-- Property Management Services -->
    <section class="pm-services">
        <div class="container">
            <div class="pm-services-grid">
                <div class="pm-service-card">
                    <div class="service-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h3>Tenant Placement</h3>
                    <p>Finding You High-Quality Tenants</p>
                    <div class="service-description">
                        <p>Embarking on the journey to becoming a landlord can feel exciting as you learn all about your new responsibilities. When it comes to finding tenants, we will help you with all the marketing costs and our years of expertise will be shared with you.</p>
                        <p>Our focus is always to source top-quality tenants who are keen to remain in your property and take care of it. Not only do we use the standard property portals, as you would expect, we also work hard on constantly monitoring and adapting our own website's SEO.</p>
                        <p>This ensures your property's net is cast as far as digitally possible. With more enquiries comes more choice. This means we can help you to select the people that will be in your best interests.</p>
                    </div>
                </div>
                
                <div class="pm-service-card">
                    <div class="service-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3>Property Maintenance</h3>
                    <p>24/7 Support for Your Property</p>
                    <div class="service-description">
                        <p>When your new tenant is fully ensconced in their new home, SELLAM are on hand 24 hours a day, 365 days a year to support them. This means that you, the landlord, can stay snuggled up warm in bed when they call because of a burst pipe at 3am.</p>
                        <p>Our management response service handles all tenant contact and ensures the day-to-day maintenance is dealt with quickly and efficiently. The key to maintaining your property in good order is the tenant's goodwill.</p>
                        <p>Our focus on strong, regular communication between all parties supports this. It also keeps your expenditure to an absolute minimum by dealing with problems that arise before they become a mountain from a molehill.</p>
                    </div>
                </div>
                
                <div class="pm-service-card">
                    <div class="service-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3>Rent Collection</h3>
                    <p>Timely and Reliable Financial Management</p>
                    <div class="service-description">
                        <p>Rent collection is included as part of our management service. Collecting rent is one of the most important parts of owning an investment property.</p>
                        <p>Our state-of-the-art accounts system provides you with monthly statements so you, and we, can monitor everything closely. Monthly and annual statements, that include full income and expenditure breakdowns, are also produced for you to take away some of the stress from your tax returns.</p>
                    </div>
                </div>
                
                <div class="pm-service-card">
                    <div class="service-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h3>Legal Compliance</h3>
                    <p>Stay Updated with Changing Regulations</p>
                    <div class="service-description">
                        <p>The legal landscape for landlords is constantly evolving. Our property management service ensures your property remains compliant with all current regulations and requirements.</p>
                        <p>From safety certificates to tenancy agreements, we handle all the documentation and ensure everything is up to date and legally sound.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Property Management Benefits -->
    <section class="pm-benefits">
        <div class="container">
            <div class="section-title">
                <h2>Benefits of Our Management Services</h2>
                <p>Why choose SELLAM for your property management needs</p>
            </div>
            <div class="benefits-content">
                <div class="benefits-list">
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <p>Maximized rental income with market analysis and competitive pricing</p>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <p>Reduced vacancy periods with efficient tenant placement</p>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <p>Quality tenants through rigorous screening processes</p>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <p>Lower maintenance costs with preventive maintenance programs</p>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <p>Legal protection with up-to-date compliance knowledge</p>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <p>Peace of mind with 24/7 emergency response</p>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-check-circle"></i>
                        <p>Time savings with professional management of all property-related matters</p>
                    </div>
                </div>
                <div class="benefits-image">
                    <img src="https://i.pinimg.com/736x/80/49/36/8049361af45caab7731eee0aee67a50b.jpg" alt="Property Management Benefits">
                </div>
            </div>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="contact-cta">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Experience Hassle-Free Property Ownership?</h2>
                <p>Contact us today to learn more about our property management services.</p>
                <a href="contact.php" class="btn">Contact Us Now</a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>