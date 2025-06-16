<?php
require_once 'includes/db-connect.php';
require_once 'includes/functions.php';

// Get property ID from URL
if (isset($_GET['id'])) {
    $propertyId = $_GET['id'];
    $property = getPropertyById($conn, $propertyId);
    $propertyImages = getPropertyImages($conn, $propertyId);
    
    if (!$property) {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>

<body>
    <!-- Property Details -->
    <section class="property-details-section">
        <div class="container">
            <!-- Property Title -->
            <div class="property-title">
                <h1><?php echo $property['title']; ?></h1>
                <div class="property-location">
                    <i class="fas fa-map-marker-alt"></i> <?php echo $property['location']; ?>
                </div>
                <div class="property-price">
                    KSH <?php echo number_format($property['price']); ?>
                    <span class="property-tag <?php echo $property['purpose'] == 'rent' ? 'rent' : 'sale'; ?>">
                        For <?php echo ucfirst($property['purpose']); ?>
                    </span>
                </div>
            </div>
            
            <!-- Property Images Slider -->
          <div class="property-slider-main" id="property-slider-main">
    <?php foreach ($propertyImages as $image): ?>
        <div class="property-slide">
            <img src="/<?= htmlspecialchars($image['image_path']) ?>" 
                 alt="Property Image <?= htmlspecialchars($image['id']) ?>"
                 loading="lazy">
        </div>
    <?php endforeach; ?>
</div>

<div class="property-slider-nav" id="property-slider-nav">
    <?php foreach ($propertyImages as $index => $image): ?>
        <div class="property-slide-nav" data-index="<?= $index ?>">
            <img src="/<?= htmlspecialchars($image['image_path']) ?>" 
                 alt="Thumbnail <?= htmlspecialchars($image['id']) ?>"
                 loading="lazy">
        </div>
    <?php endforeach; ?>
</div>
            
            <!-- Property Information -->
            <div class="property-info-grid">
                <div class="property-main-info">
                    <!-- Property Details -->
                    <div class="property-details-card">
                        <h2>Property Details</h2>
                        <div class="property-features">
                            <div class="feature">
                                <i class="fas fa-bed"></i>
                                <span><?php echo $property['bedrooms']; ?></span>
                                <p>Bedrooms</p>
                            </div>
                            <div class="feature">
                                <i class="fas fa-bath"></i>
                                <span><?php echo $property['bathrooms']; ?></span>
                                <p>Bathrooms</p>
                            </div>
                            <div class="feature">
                                <i class="fas fa-vector-square"></i>
                                <span><?php echo $property['area']; ?></span>
                                <p>Sq Ft</p>
                            </div>
                            <div class="feature">
                                <i class="fas fa-car"></i>
                                <span><?php echo $property['garages']; ?></span>
                                <p>Garages</p>
                            </div>
                        </div>
                        
                        <div class="property-details-list">
                            <div class="detail-item">
                                <span class="detail-label">Property Type:</span>
                                <span class="detail-value"><?php echo ucfirst($property['type']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Purpose:</span>
                                <span class="detail-value">For <?php echo ucfirst($property['purpose']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Year Built:</span>
                                <span class="detail-value"><?php echo $property['year_built']; ?></span>
                            </div>
                            <?php if($property['purpose'] == 'rent'): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Rental Period:</span>
                                    <span class="detail-value">Monthly</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Property Description -->
                    <div class="property-description-card">
                        <h2>Description</h2>
                        <div class="description-content">
                            <?php echo $property['description']; ?>
                        </div>
                    </div>
                    
                    <!-- Property Amenities -->
                    <div class="property-amenities-card">
                        <h2>Amenities</h2>
                        <div class="amenities-list">
                            <?php 
                            $amenities = getPropertyAmenities($conn, $propertyId);
                            foreach ($amenities as $amenity): 
                            ?>
                                <div class="amenity-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span><?php echo $amenity['name']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar - Contact Agent -->
                <div class="property-sidebar">
                    <div class="agent-contact-card">
                        <h3>Contact Agent</h3>
                        <div class="agent-info">
                            <div class="agent-image">
                                <img src="assets/images/agent.jpg" alt="Agent">
                            </div>
                            <div class="agent-details">
                                <h4>John Smith</h4>
                                <p>Sales Agent</p>
                                <p><i class="fas fa-phone"></i> +254 123 456 789</p>
                                <p><i class="fas fa-envelope"></i> john@sellam.co.ke</p>
                            </div>
                        </div>
                        
                        <form id="property-inquiry-form" class="agent-contact-form">
                            <input type="hidden" name="property_id" value="<?php echo $propertyId; ?>">
                            <div class="form-group">
                                <input type="text" name="name" placeholder="Your Name" required>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" placeholder="Your Email" required>
                            </div>
                            <div class="form-group">
                                <input type="tel" name="phone" placeholder="Your Phone">
                            </div>
                            <div class="form-group">
                                <textarea name="message" placeholder="Your Message" required>I'm interested in this property (<?php echo $property['title']; ?>).</textarea>
                            </div>
                            <button type="submit" class="btn btn-full">Send Message</button>
                        </form>
                    </div>
                    
                    <!-- Share Property -->
                    <div class="share-property-card">
                        <h3>Share Property</h3>
                        <div class="share-buttons">
                            <a href="#" class="share-btn facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="share-btn twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="share-btn whatsapp"><i class="fab fa-whatsapp"></i></a>
                            <a href="#" class="share-btn email"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Similar Properties -->
            <div class="similar-properties">
                <h2>Similar Properties</h2>
                <div class="properties-grid">
                    <?php 
                    $similarProperties = getSimilarProperties($conn, $propertyId, $property['type'], $property['purpose'], 3);
                    foreach ($similarProperties as $similarProperty): 
                    ?>
                        <div class="property-card">
                            <div class="property-image">
                                <img src="<?php echo $similarProperty['main_image']; ?>" alt="<?php echo $similarProperty['title']; ?>">
                                <div class="property-tag <?php echo $similarProperty['purpose'] == 'rent' ? 'rent' : 'sale'; ?>">
                                    For <?php echo ucfirst($similarProperty['purpose']); ?>
                                </div>
                            </div>
                            <div class="property-info">
                                <h3><?php echo $similarProperty['title']; ?></h3>
                                <div class="property-location">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo $similarProperty['location']; ?>
                                </div>
                                <div class="property-details">
                                    <span><i class="fas fa-bed"></i> <?php echo $similarProperty['bedrooms']; ?> Beds</span>
                                    <span><i class="fas fa-bath"></i> <?php echo $similarProperty['bathrooms']; ?> Baths</span>
                                    <span><i class="fas fa-vector-square"></i> <?php echo $similarProperty['area']; ?> sq ft</span>
                                </div>
                                <div class="property-price">
                                    KSH <?php echo number_format($similarProperty['price']); ?>
                                </div>
                                <a href="property-details.php?id=<?php echo $similarProperty['id']; ?>" class="view-more">View More</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/property-slider.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
