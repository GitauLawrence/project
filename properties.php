<?php
require_once 'includes/db-connect.php';
require_once 'includes/functions.php';

// Get filter parameters
$type = isset($_GET['type']) ? $_GET['type'] : '';
$purpose = isset($_GET['purpose']) ? $_GET['purpose'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$minPrice = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$maxPrice = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$bedrooms = isset($_GET['bedrooms']) ? $_GET['bedrooms'] : '';

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$propertiesPerPage = 9;
$offset = ($page - 1) * $propertiesPerPage;

// Get properties with filters
$properties = getPropertiesWithFilters($conn, $type, $purpose, $location, '', $propertiesPerPage, $offset);
$totalProperties = countPropertiesWithFilters($conn, $type, $purpose, $location, '');
$totalPages = ceil($totalProperties / $propertiesPerPage);

// Get locations for filter
$locations = getLocations($conn);
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>

<body>
    <!-- Page Banner -->
    <section class="page-banner">
        <div class="banner-overlay"></div>
        <div class="container">
            <h1>Properties</h1>
            <div class="breadcrumb">
                <a href="index.php">Home</a> / Properties
                <?php if (!empty($type)): ?>
                    / <?php echo ucfirst($type); ?>
                <?php endif; ?>
                <?php if (!empty($purpose)): ?>
                    / For <?php echo ucfirst($purpose); ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Properties Section -->
    <section class="properties-section">
        <div class="container">
            <div class="properties-wrapper">
                <!-- Filters Sidebar -->
                <div class="filters-sidebar">
                    <div class="filter-box">
                        <h3>Find Your Property</h3>
                        <form action="properties.php" method="GET" class="filter-form">
                            <div class="filter-group">
                                <label for="type">Property Type</label>
                                <select id="type" name="type">
                                    <option value="">All Types</option>
                                    <option value="residential" <?php echo ($type == 'residential') ? 'selected' : ''; ?>>Residential</option>
                                    <option value="commercial" <?php echo ($type == 'commercial') ? 'selected' : ''; ?>>Commercial</option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="purpose">Purpose</label>
                                <select id="purpose" name="purpose">
                                    <option value="">All Purposes</option>
                                    <option value="buy" <?php echo ($purpose == 'buy') ? 'selected' : ''; ?>>For Sale</option>
                                    <option value="rent" <?php echo ($purpose == 'rent') ? 'selected' : ''; ?>>For Rent</option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="location">Location</label>
                                <select id="location" name="location">
                                    <option value="">All Locations</option>
                                    <?php foreach ($locations as $loc): ?>
                                        <option value="<?php echo $loc['id']; ?>" <?php echo ($location == $loc['id']) ? 'selected' : ''; ?>><?php echo $loc['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="min_price">Min Price</label>
                                <input type="number" id="min_price" name="min_price" placeholder="Min Price" value="<?php echo $minPrice; ?>">
                            </div>
                            
                            <div class="filter-group">
                                <label for="max_price">Max Price</label>
                                <input type="number" id="max_price" name="max_price" placeholder="Max Price" value="<?php echo $maxPrice; ?>">
                            </div>
                            
                            <div class="filter-group">
                                <label for="bedrooms">Bedrooms</label>
                                <select id="bedrooms" name="bedrooms">
                                    <option value="">Any</option>
                                    <option value="1" <?php echo ($bedrooms == '1') ? 'selected' : ''; ?>>1+</option>
                                    <option value="2" <?php echo ($bedrooms == '2') ? 'selected' : ''; ?>>2+</option>
                                    <option value="3" <?php echo ($bedrooms == '3') ? 'selected' : ''; ?>>3+</option>
                                    <option value="4" <?php echo ($bedrooms == '4') ? 'selected' : ''; ?>>4+</option>
                                    <option value="5" <?php echo ($bedrooms == '5') ? 'selected' : ''; ?>>5+</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-full">Apply Filters</button>
                            <a href="properties.php" class="btn-secondary btn-full">Reset Filters</a>
                        </form>
                    </div>
                    
                    <div class="filter-box">
                        <h3>Featured Properties</h3>
                        <div class="featured-sidebar">
                            <?php 
                            $featuredSidebar = getFeaturedProperties($conn, 3);
                            foreach ($featuredSidebar as $featured): 
                            ?>
                                <div class="sidebar-property">
                                    <div class="sidebar-property-image">
                                        <img src="<?php echo $featured['main_image']; ?>" alt="<?php echo $featured['title']; ?>">
                                        <div class="property-tag <?php echo $featured['purpose'] == 'rent' ? 'rent' : 'sale'; ?>">
                                            For <?php echo ucfirst($featured['purpose']); ?>
                                        </div>
                                    </div>
                                    <div class="sidebar-property-info">
                                        <h4><a href="property-details.php?id=<?php echo $featured['id']; ?>"><?php echo $featured['title']; ?></a></h4>
                                        <div class="sidebar-property-price">KSH <?php echo number_format($featured['price']); ?></div>
                                        <a href="property-details.php?id=<?php echo $featured['id']; ?>" class="view-more-sm">View Details</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Properties Content -->
                <div class="properties-content">
                    <div class="properties-header">
                        <div class="properties-found">
                            <h2><?php echo $totalProperties; ?> Properties Found</h2>
                            <?php if (!empty($type) || !empty($purpose) || !empty($location)): ?>
                                <p>Filters: 
                                    <?php if (!empty($type)): ?>
                                        <span class="filter-tag"><?php echo ucfirst($type); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($purpose)): ?>
                                        <span class="filter-tag">For <?php echo ucfirst($purpose); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($location)): ?>
                                        <?php foreach ($locations as $loc): ?>
                                            <?php if ($loc['id'] == $location): ?>
                                                <span class="filter-tag"><?php echo $loc['name']; ?></span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="properties-sort">
                            <label for="sort">Sort By:</label>
                            <select id="sort" name="sort">
                                <option value="latest">Latest</option>
                                <option value="price_asc">Price: Low to High</option>
                                <option value="price_desc">Price: High to Low</option>
                            </select>
                        </div>
                    </div>
                    
                    <?php if (count($properties) > 0): ?>
                        <div class="properties-grid">
                            <?php foreach ($properties as $property): ?>
                                <div class="property-card">
                                    <div class="property-image">
                                        <img src="<?php echo $property['main_image']; ?>" alt="<?php echo $property['title']; ?>">
                                        <div class="property-tag <?php echo $property['purpose'] == 'rent' ? 'rent' : 'sale'; ?>">
                                            For <?php echo ucfirst($property['purpose']); ?>
                                        </div>
                                    </div>
                                    <div class="property-info">
                                        <h3><?php echo $property['title']; ?></h3>
                                        <div class="property-location">
                                            <i class="fas fa-map-marker-alt"></i> <?php echo $property['location']; ?>
                                        </div>
                                        <div class="property-details">
                                            <?php if ($property['type'] == 'residential'): ?>
                                                <span><i class="fas fa-bed"></i> <?php echo $property['bedrooms']; ?> Beds</span>
                                                <span><i class="fas fa-bath"></i> <?php echo $property['bathrooms']; ?> Baths</span>
                                            <?php endif; ?>
                                            <span><i class="fas fa-vector-square"></i> <?php echo $property['area']; ?> sq ft</span>
                                        </div>
                                        <div class="property-price">
                                            KSH <?php echo number_format($property['price']); ?>
                                        </div>
                                        <a href="property-details.php?id=<?php echo $property['id']; ?>" class="view-more">View More</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div class="pagination">
                                <?php if ($page > 1): ?>
                                    <a href="properties.php?page=1<?php echo (!empty($type) ? '&type='.$type : '').(!empty($purpose) ? '&purpose='.$purpose : '').(!empty($location) ? '&location='.$location : '').(!empty($minPrice) ? '&min_price='.$minPrice : '').(!empty($maxPrice) ? '&max_price='.$maxPrice : '').(!empty($bedrooms) ? '&bedrooms='.$bedrooms : ''); ?>" class="page-link">
                                        <i class="fas fa-angle-double-left"></i>
                                    </a>
                                    <a href="properties.php?page=<?php echo $page - 1; ?><?php echo (!empty($type) ? '&type='.$type : '').(!empty($purpose) ? '&purpose='.$purpose : '').(!empty($location) ? '&location='.$location : '').(!empty($minPrice) ? '&min_price='.$minPrice : '').(!empty($maxPrice) ? '&max_price='.$maxPrice : '').(!empty($bedrooms) ? '&bedrooms='.$bedrooms : ''); ?>" class="page-link">
                                        <i class="fas fa-angle-left"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $startPage + 4);
                                if ($endPage - $startPage < 4 && $startPage > 1) {
                                    $startPage = max(1, $endPage - 4);
                                }
                                
                                for ($i = $startPage; $i <= $endPage; $i++):
                                ?>
                                    <a href="properties.php?page=<?php echo $i; ?><?php echo (!empty($type) ? '&type='.$type : '').(!empty($purpose) ? '&purpose='.$purpose : '').(!empty($location) ? '&location='.$location : '').(!empty($minPrice) ? '&min_price='.$minPrice : '').(!empty($maxPrice) ? '&max_price='.$maxPrice : '').(!empty($bedrooms) ? '&bedrooms='.$bedrooms : ''); ?>" class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <a href="properties.php?page=<?php echo $page + 1; ?><?php echo (!empty($type) ? '&type='.$type : '').(!empty($purpose) ? '&purpose='.$purpose : '').(!empty($location) ? '&location='.$location : '').(!empty($minPrice) ? '&min_price='.$minPrice : '').(!empty($maxPrice) ? '&max_price='.$maxPrice : '').(!empty($bedrooms) ? '&bedrooms='.$bedrooms : ''); ?>" class="page-link">
                                        <i class="fas fa-angle-right"></i>
                                    </a>
                                    <a href="properties.php?page=<?php echo $totalPages; ?><?php echo (!empty($type) ? '&type='.$type : '').(!empty($purpose) ? '&purpose='.$purpose : '').(!empty($location) ? '&location='.$location : '').(!empty($minPrice) ? '&min_price='.$minPrice : '').(!empty($maxPrice) ? '&max_price='.$maxPrice : '').(!empty($bedrooms) ? '&bedrooms='.$bedrooms : ''); ?>" class="page-link">
                                        <i class="fas fa-angle-double-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="no-properties">
                            <p>No properties found matching your criteria. Please try different filters.</p>
                            <a href="properties.php" class="btn">View All Properties</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>