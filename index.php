<?php
require_once 'includes/db-connect.php';
require_once 'includes/functions.php';

// Get featured properties
$featuredProperties = getFeaturedProperties($conn, 6);
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>

<body>
    <!-- Hero Section with Featured Properties -->
<section class="hero-slider">
  <div class="slides-wrapper">
    <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1570129477492-45c003edd2be');"></div>
    <div class="slide" style="background-image: url('https://i.pinimg.com/736x/e6/de/4e/e6de4ead0efaefce730ce4175c9c9102.jpg');"></div>
    <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1560448204-e02f11c3d0e2');"></div>
  </div>
   <div class="hero-overlay"></div>
     <div class="hero-content">
                <h1>PREMIUM PROPERTIES IN THE BEST LOCATIONS</h1>
            </div>
            
            <!-- Property Search Form -->
            <div class="search-container">
                <form id="property-search-form" action="properties.php" method="GET">
                    <div class="search-wrapper">
                        <div class="search-box">
                            <div class="property-type-tabs">
                                <div class="tab active" data-type="residential">Residential</div>
                                <div class="tab" data-type="commercial">Commercial</div>
                            </div>
                            <input type="hidden" name="property_type" id="property_type" value="residential">
                            
                            <div class="search-options">
                                <div class="search-field">
                                    <select name="purpose" id="purpose">
                                        <option value="">Buy or Rent</option>
                                        <option value="buy">Buy</option>
                                        <option value="rent">Rent</option>
                                    </select>
                                </div>
                                <div class="search-field">
                                    <select name="location" id="location">
                                        <option value="">Location</option>
                                        <?php
                                        $locations = getLocations($conn);
                                        foreach ($locations as $location) {
                                            echo "<option value='" . $location['id'] . "'>" . $location['name'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="search-field">
                                    <button type="submit" class="search-btn">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Featured Properties Section -->
    <section class="featured-properties">
        <div class="container">
            <div class="section-title">
                <h2>FEATURED PROPERTIES</h2>
            </div>
            
            <div class="property-slider" id="property-slider">
                <?php if (count($featuredProperties) > 0): ?>
                    <?php foreach ($featuredProperties as $property): ?>
                        <div class="property-card">
                            <div class="property-image">
                                <img src="/<?php echo $property['main_image']; ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
                                <div class="property-tag <?php echo $property['purpose'] == 'rent' ? 'rent' : 'sale'; ?>">
                                     <?php echo ucfirst($property['purpose']); ?>
                                </div>
                            </div>
                            <div class="property-info">
                                <h3><?php echo $property['title']; ?></h3>
                                <div class="property-location">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo $property['location']; ?>
                                </div>
                                <div class="property-details">
                                    <span><i class="fas fa-bed"></i> <?php echo $property['bedrooms']; ?> Beds</span>
                                    <span><i class="fas fa-bath"></i> <?php echo $property['bathrooms']; ?> Baths</span>
                                    <span><i class="fas fa-vector-square"></i> <?php echo $property['area']; ?> sq ft</span>
                                </div>
                                <div class="property-price">
                                    KSH <?php echo number_format($property['price']); ?>
                                </div>
                                <a href="property-details.php?id=<?php echo $property['id']; ?>" class="view-more">View More</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-properties">
                        <p>No featured properties available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- About Section Preview -->
    <section class="about-preview">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2>About SELLAM</h2>
                    <p>SELLAM is a real estate agency that utilizes world class technology and innovative marketing strategies to assist clients in achieving their real estate goals.</p>
                    <p>SELLAM's reputation is built on trust, transparency, and dedication, and we focus on creating lasting relationships with clients from around the world.</p>
                    <a href="about.php" class="btn">Learn More About Us</a>
                </div>
                <div class="about-image">
                    <img src="https://i.pinimg.com/736x/8d/24/ee/8d24ee59ab4b2b9cd9cd7cbfe7ce87db.jpg" alt="SELLAM Real Estate">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Preview -->
    <section class="services-preview">
        <div class="container">
            <div class="section-title">
                <h2>Our Services</h2>
                <p>We redefine the standard of client service by curating elevated experiences</p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h3>Residential and commercial sales</h3>
                    <p>We help clients buy and sell residential and commercial properties, ensuring a smooth, professional transaction every step of the way.</p>
                    <a href="services.php#buying" class="read-more">Read More</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <h3>Property Management</h3>
                    <p>We manage your property with care, handling tenants, maintenance, and maximizing your rental income.</p>
                    <a href="services.php#selling" class="read-more">Read More</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3>Real Estate Investment Advisory</h3>
                    <p>We offer expert advice to help you make smart, profitable real estate investments.</p>
                    <a href="property-management.php" class="read-more">Read More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Communities -->
    <section class="featured-communities">
        <div class="container">
            <div class="section-title">
                <h2>FEATURED COMMUNITIES</h2>
                <p>Explore our handpicked selection of premium neighborhoods</p>
            </div>
            <div class="communities-grid">
                <div class="community-card">
                    <img src="https://i.pinimg.com/736x/70/72/ed/7072edcc744de8c86ea081a4ee5c6fb7.jpg" alt="Karen">
                    <div class="community-overlay">
                        <h3>Karen</h3>
                        <a href="properties.php?location=1" class="btn">View Properties</a>
                    </div>
                </div>
                <div class="community-card">
                    <img src="https://i.pinimg.com/736x/49/17/03/491703f17113b60804bea5d296458b2b.jpg" alt="Westlands">
                    <div class="community-overlay">
                        <h3>Westlands</h3>
                        <a href="properties.php?location=2" class="btn">View Properties</a>
                    </div>
                </div>
                <div class="community-card">
                    <img src="https://i.pinimg.com/736x/c2/dd/64/c2dd64314973895ab64f773dd6ce1e15.jpg
" alt="Kilimani">
                    <div class="community-overlay">
                        <h3>Kilimani</h3>
                        <a href="properties.php?location=3" class="btn">View Properties</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Customer Reviews -->
    <section class="section testimonials">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">What Our Clients Say</h2>
                <div class="section-line"></div>
            </div>
            <div class="testimonial-slider">
                <div class="testimonial-slide active">
                    <div class="testimonial-content">
                        <div class="testimonial-quote">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p>SELLAM's attention to detail and understanding of our needs was exceptional. They found us our dream home in half the time we expected.</p>
                        <div class="testimonial-author">
                            <h4>David & Sarah Kimani</h4>
                            <p>Nairobi, Kenya</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-slide">
                    <div class="testimonial-content">
                        <div class="testimonial-quote">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p>As international investors, we relied completely on SELLAM's expertise. Their property management services have made our investment completely hands-off and profitable.</p>
                        <div class="testimonial-author">
                            <h4>John Williams</h4>
                            <p>London, UK</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-slide">
                    <div class="testimonial-content">
                        <div class="testimonial-quote">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p>The marketing strategy SELLAM created for our property resulted in a sale price significantly above our expectations and in record time.</p>
                        <div class="testimonial-author">
                            <h4>Elizabeth Aroko</h4>
                            <p>Mombasa, Kenya</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-controls">
                    <button class="testimonial-prev"><i class="fas fa-chevron-left"></i></button>
                    <div class="testimonial-indicators">
                        <span class="indicator active"></span>
                        <span class="indicator"></span>
                        <span class="indicator"></span>
                    </div>
                    <button class="testimonial-next"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/property-slider.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
    const slides = document.querySelectorAll('.testimonial-slide');
    const indicators = document.querySelectorAll('.indicator');
    const prevBtn = document.querySelector('.testimonial-prev');
    const nextBtn = document.querySelector('.testimonial-next');
    let currentSlide = 0;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
            indicators[i].classList.toggle('active', i === index);
        });
    }

    prevBtn.addEventListener('click', () => {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(currentSlide);
    });

    nextBtn.addEventListener('click', () => {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    });

    indicators.forEach((indicator, i) => {
        indicator.addEventListener('click', () => {
            currentSlide = i;
            showSlide(currentSlide);
        });
    });
</script>
</body>
</html>