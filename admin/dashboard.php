<?php
session_start();
require_once '../includes/db-connect.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get property counts
$totalProperties = countProperties($conn);
$residentialProperties = countPropertiesByType($conn, 'residential');
$commercialProperties = countPropertiesByType($conn, 'commercial');
$forSaleProperties = countPropertiesByPurpose($conn, 'buy');
$forRentProperties = countPropertiesByPurpose($conn, 'rent');

// Get recent properties
$recentProperties = getRecentProperties($conn, 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SELLAM Real Estate</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/admin-sidebar.php'; ?>
        
        <div class="admin-content">
            <?php include 'includes/admin-header.php'; ?>
            
            <main class="admin-main">
                <div class="dashboard-welcome">
                    <h2>Welcome, <?php echo $_SESSION['admin_username']; ?>!</h2>
                    <p>Here's an overview of your properties and activities.</p>
                </div>
                
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Properties</h3>
                            <p><?php echo $totalProperties; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Residential</h3>
                            <p><?php echo $residentialProperties; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Commercial</h3>
                            <p><?php echo $commercialProperties; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="stat-info">
                            <h3>For Sale</h3>
                            <p><?php echo $forSaleProperties; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="stat-info">
                            <h3>For Rent</h3>
                            <p><?php echo $forRentProperties; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-actions">
                    <a href="add-property.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div class="action-text">
                            <h3>Add New Property</h3>
                            <p>Create a new property listing</p>
                        </div>
                    </a>
                    <a href="manage-properties.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <div class="action-text">
                            <h3>Manage Properties</h3>
                            <p>View, edit or delete properties</p>
                        </div>
                    </a>
                    <a href="manage-inquiries.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="action-text">
                            <h3>Inquiries</h3>
                            <p>View and manage customer inquiries</p>
                        </div>
                    </a>
                </div>
                
                <div class="recent-properties">
                    <div class="section-header">
                        <h2>Recent Properties</h2>
                        <a href="manage-properties.php" class="view-all">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Purpose</th>
                                    <th>Price</th>
                                    <th>Date Added</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($recentProperties) > 0): ?>
                                    <?php foreach ($recentProperties as $property): ?>
                                        <tr>
                                            <td><?php echo $property['id']; ?></td>
                                            <td class="property-image-cell">
                                                <img src="<?php echo $property['main_image']; ?>" alt="<?php echo $property['title']; ?>">
                                            </td>
                                            <td><?php echo $property['title']; ?></td>
                                            <td><?php echo ucfirst($property['type']); ?></td>
                                            <td>For <?php echo ucfirst($property['purpose']); ?></td>
                                            <td>KSH <?php echo number_format($property['price']); ?></td>
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
                                        <td colspan="8" class="no-data">No properties found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
            
            <?php include 'includes/admin-footer.php'; ?>
        </div>
    </div>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>