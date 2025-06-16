<?php
session_start();
require_once '../includes/db-connect.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Define pagination variables
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$propertiesPerPage = 10;
$offset = ($currentPage - 1) * $propertiesPerPage;

// Define filter variables
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';
$purposeFilter = isset($_GET['purpose']) ? $_GET['purpose'] : '';
$locationFilter = isset($_GET['location']) ? $_GET['location'] : '';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Get properties with filters and pagination
$properties = getPropertiesWithFilters($conn, $typeFilter, $purposeFilter, $locationFilter, $searchQuery, $propertiesPerPage, $offset);

// Get total count for pagination
$totalProperties = countPropertiesWithFilters($conn, $typeFilter, $purposeFilter, $locationFilter, $searchQuery);
$totalPages = ceil($totalProperties / $propertiesPerPage);

// Get locations for filter dropdown
$locations = getLocations($conn);

// Process bulk actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bulk_action']) && !empty($_POST['selected_properties'])) {
    $action = $_POST['bulk_action'];
    $selectedProperties = $_POST['selected_properties'];
    
    if ($action == 'delete') {
        foreach ($selectedProperties as $propertyId) {
            deleteProperty($conn, $propertyId);
        }
        // Redirect to refresh the page
        header("Location: manage-properties.php?success=bulk_delete");
        exit();
    } elseif ($action == 'feature') {
        foreach ($selectedProperties as $propertyId) {
            setPropertyFeatured($conn, $propertyId, 1);
        }
        // Redirect to refresh the page
        header("Location: manage-properties.php?success=bulk_feature");
        exit();
    } elseif ($action == 'unfeature') {
        foreach ($selectedProperties as $propertyId) {
            setPropertyFeatured($conn, $propertyId, 0);
        }
        // Redirect to refresh the page
        header("Location: manage-properties.php?success=bulk_unfeature");
        exit();
    }
}

// Handle success messages
$successMessage = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'delete':
            $successMessage = "Property deleted successfully!";
            break;
        case 'bulk_delete':
            $successMessage = "Selected properties deleted successfully!";
            break;
        case 'bulk_feature':
            $successMessage = "Selected properties marked as featured!";
            break;
        case 'bulk_unfeature':
            $successMessage = "Selected properties removed from featured!";
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Properties - SELLAM Real Estate</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/admin-sidebar.php'; ?>
        
        <div class="admin-content">
            <?php include 'includes/admin-header.php'; ?>
            
            <main class="admin-main">
                <div class="page-header">
                    <h2>Manage Properties</h2>
                    <a href="add-property.php" class="btn-primary"><i class="fas fa-plus"></i> Add New Property</a>
                </div>
                
                <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success">
                        <?php echo $successMessage; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Filters -->
                <div class="filters-container">
                    <form action="manage-properties.php" method="GET" class="filters-form">
                        <div class="filters-row">
                            <div class="filter-group">
                                <label for="type">Property Type</label>
                                <select id="type" name="type">
                                    <option value="">All Types</option>
                                    <option value="residential" <?php echo ($typeFilter == 'residential') ? 'selected' : ''; ?>>Residential</option>
                                    <option value="commercial" <?php echo ($typeFilter == 'commercial') ? 'selected' : ''; ?>>Commercial</option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="purpose">Purpose</label>
                                <select id="purpose" name="purpose">
                                    <option value="">All Purposes</option>
                                    <option value="buy" <?php echo ($purposeFilter == 'buy') ? 'selected' : ''; ?>>For Sale</option>
                                    <option value="rent" <?php echo ($purposeFilter == 'rent') ? 'selected' : ''; ?>>For Rent</option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="location">Location</label>
                                <select id="location" name="location">
                                    <option value="">All Locations</option>
                                    <?php foreach ($locations as $location): ?>
                                        <option value="<?php echo $location['id']; ?>" <?php echo ($locationFilter == $location['id']) ? 'selected' : ''; ?>><?php echo $location['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="filter-group search-group">
                                <label for="search">Search</label>
                                <input type="text" id="search" name="search" placeholder="Search by title or ID..." value="<?php echo $searchQuery; ?>">
                            </div>
                            
                            <div class="filter-actions">
                                <button type="submit" class="btn-primary">Apply Filters</button>
                                <a href="manage-properties.php" class="btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Properties Table -->
                <form method="POST" action="manage-properties.php" id="properties-form">
                    <div class="bulk-actions">
                        <select name="bulk_action" id="bulk-action">
                            <option value="">Bulk Actions</option>
                            <option value="delete">Delete Selected</option>
                            <option value="feature">Mark as Featured</option>
                            <option value="unfeature">Remove from Featured</option>
                        </select>
                        <button type="submit" class="btn-secondary" id="apply-bulk-action">Apply</button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="select-all">
                                    </th>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Purpose</th>
                                    <th>Price</th>
                                    <th>Location</th>
                                    <th>Featured</th>
                                    <th>Date Added</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($properties) > 0): ?>
                                    <?php foreach ($properties as $property): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="selected_properties[]" value="<?php echo $property['id']; ?>" class="property-checkbox">
                                            </td>
                                            <td><?php echo $property['id']; ?></td>
                                            <td class="property-image-cell">
                                                <img src="/<?php echo $property['main_image']; ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
                                            </td>
                                            <td><?php echo $property['title']; ?></td>
                                            <td><?php echo ucfirst($property['type']); ?></td>
                                            <td>For <?php echo ucfirst($property['purpose']); ?></td>
                                            <td>KSH <?php echo number_format($property['price']); ?></td>
                                            <td><?php echo $property['location']; ?></td>
                                            <td>
                                                <?php if ($property['featured'] == 1): ?>
                                                    <span class="badge featured"><i class="fas fa-star"></i> Featured</span>
                                                <?php else: ?>
                                                    <span class="badge not-featured">Not Featured</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($property['created_at'])); ?></td>
                                            <td class="actions-cell">
                                                <a href="edit-property.php?id=<?php echo $property['id']; ?>" class="action-btn edit-btn" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete-property.php?id=<?php echo $property['id']; ?>" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this property?');">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                                <a href="../property-details.php?id=<?php echo $property['id']; ?>" class="action-btn view-btn" title="View" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="11" class="no-data">No properties found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <a href="manage-properties.php?page=1<?php echo (!empty($typeFilter) ? '&type='.$typeFilter : '').(!empty($purposeFilter) ? '&purpose='.$purposeFilter : '').(!empty($locationFilter) ? '&location='.$locationFilter : '').(!empty($searchQuery) ? '&search='.$searchQuery : ''); ?>" class="page-link">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                            <a href="manage-properties.php?page=<?php echo $currentPage - 1; ?><?php echo (!empty($typeFilter) ? '&type='.$typeFilter : '').(!empty($purposeFilter) ? '&purpose='.$purposeFilter : '').(!empty($locationFilter) ? '&location='.$locationFilter : '').(!empty($searchQuery) ? '&search='.$searchQuery : ''); ?>" class="page-link">
                                <i class="fas fa-angle-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $startPage + 4);
                        if ($endPage - $startPage < 4 && $startPage > 1) {
                            $startPage = max(1, $endPage - 4);
                        }
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <a href="manage-properties.php?page=<?php echo $i; ?><?php echo (!empty($typeFilter) ? '&type='.$typeFilter : '').(!empty($purposeFilter) ? '&purpose='.$purposeFilter : '').(!empty($locationFilter) ? '&location='.$locationFilter : '').(!empty($searchQuery) ? '&search='.$searchQuery : ''); ?>" class="page-link <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="manage-properties.php?page=<?php echo $currentPage + 1; ?><?php echo (!empty($typeFilter) ? '&type='.$typeFilter : '').(!empty($purposeFilter) ? '&purpose='.$purposeFilter : '').(!empty($locationFilter) ? '&location='.$locationFilter : '').(!empty($searchQuery) ? '&search='.$searchQuery : ''); ?>" class="page-link">
                                <i class="fas fa-angle-right"></i>
                            </a>
                            <a href="manage-properties.php?page=<?php echo $totalPages; ?><?php echo (!empty($typeFilter) ? '&type='.$typeFilter : '').(!empty($purposeFilter) ? '&purpose='.$purposeFilter : '').(!empty($locationFilter) ? '&location='.$locationFilter : '').(!empty($searchQuery) ? '&search='.$searchQuery : ''); ?>" class="page-link">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </main>
            
            <?php include 'includes/admin-footer.php'; ?>
        </div>
    </div>
    
    <script src="../assets/js/admin.js"></script>
    <script>
        // Select all checkbox functionality
        const selectAllCheckbox = document.getElementById('select-all');
        const propertyCheckboxes = document.querySelectorAll('.property-checkbox');
        
        selectAllCheckbox.addEventListener('change', function() {
            propertyCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Bulk action confirmation
        document.getElementById('properties-form').addEventListener('submit', function(e) {
            const action = document.getElementById('bulk-action').value;
            const selectedCheckboxes = document.querySelectorAll('.property-checkbox:checked');
            
            if (action === '') {
                e.preventDefault();
                alert('Please select an action');
                return;
            }
            
            if (selectedCheckboxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one property');
                return;
            }
            
            if (action === 'delete' && !confirm('Are you sure you want to delete the selected properties? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>