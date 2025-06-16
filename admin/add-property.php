<?php
session_start();
require_once '../includes/db-connect.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$success = '';
$error = '';

// Get locations for dropdown
$locations = getLocations($conn);

// Get amenities for checkboxes
$amenities = getAmenities($conn);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = trim($_POST['title']);
    $type = $_POST['type'];
    $purpose = $_POST['purpose'];
    $price = $_POST['price'];
    $bedrooms = isset($_POST['bedrooms']) ? $_POST['bedrooms'] : 0;
    $bathrooms = isset($_POST['bathrooms']) ? $_POST['bathrooms'] : 0;
    $area = $_POST['area'];
    $garages = isset($_POST['garages']) ? $_POST['garages'] : 0;
    $location_id = $_POST['location'];
    $year_built = isset($_POST['year_built']) ? $_POST['year_built'] : null;
    $description = trim($_POST['description']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Validate form data
    if (empty($title) || empty($price) || empty($area) || empty($description)) {
        $error = "Please fill all required fields";
    } else {
        // Upload main image
        $mainImagePath = '';
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
            // Changed target directory to not use ../
            $mainImagePath = uploadImage($_FILES['main_image'], 'assets/images/properties/');
            if (!$mainImagePath) {
                $error = "Error uploading main image";
            }
        } else {
            $error = "Main image is required";
        }
        
        if (empty($error)) {
            // Insert property
            $stmt = $conn->prepare("INSERT INTO properties (title, type, purpose, price, bedrooms, bathrooms, area, garages, location_id, year_built, description, main_image, featured, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssdiidiisssi", $title, $type, $purpose, $price, $bedrooms, $bathrooms, $area, $garages, $location_id, $year_built, $description, $mainImagePath, $featured);

            if ($stmt->execute()) {
                $property_id = $conn->insert_id;
                
                // Upload additional images
                if (isset($_FILES['additional_images'])) {
                    $fileCount = count($_FILES['additional_images']['name']);
                    
                    for ($i = 0; $i < $fileCount; $i++) {
                        if ($_FILES['additional_images']['error'][$i] == 0) {
                            $tempFile = array(
                                'name' => $_FILES['additional_images']['name'][$i],
                                'type' => $_FILES['additional_images']['type'][$i],
                                'tmp_name' => $_FILES['additional_images']['tmp_name'][$i],
                                'error' => $_FILES['additional_images']['error'][$i],
                                'size' => $_FILES['additional_images']['size'][$i]
                            );
                            
                            // Changed target directory to not use ../
                            $imagePath = uploadImage($tempFile, 'assets/images/properties/');
                            if ($imagePath) {
                                // Insert image path to property_images table
                                $imgStmt = $conn->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                                $imgStmt->bind_param("is", $property_id, $imagePath);
                                $imgStmt->execute();
                                $imgStmt->close();
                            }
                        }
                    }
                }
                
                // Insert amenities
                if (isset($_POST['amenities']) && is_array($_POST['amenities'])) {
                    foreach ($_POST['amenities'] as $amenity_id) {
                        $amenStmt = $conn->prepare("INSERT INTO property_amenities (property_id, amenity_id) VALUES (?, ?)");
                        $amenStmt->bind_param("ii", $property_id, $amenity_id);
                        $amenStmt->execute();
                        $amenStmt->close();
                    }
                }
                
                $success = "Property added successfully!";
                
                // Redirect to property list after a delay
                header("Refresh: 2; URL=manage-properties.php");
            } else {
                $error = "Error adding property: " . $conn->error;
            }
            
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property - SELLAM Real Estate</title>
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
                    <h2>Add New Property</h2>
                    <a href="manage-properties.php" class="btn-secondary"><i class="fas fa-arrow-left"></i> Back to Properties</a>
                </div>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form class="admin-form" action="add-property.php" method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-section">
                            <h3>Basic Information</h3>
                            
                            <div class="form-group">
                                <label for="title">Property Title *</label>
                                <input type="text" id="title" name="title" required>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="type">Property Type *</label>
                                    <select id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="residential">Residential</option>
                                        <option value="commercial">Commercial</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="purpose">Purpose *</label>
                                    <select id="purpose" name="purpose" required>
                                        <option value="">Select Purpose</option>
                                        <option value="buy">For Sale</option>
                                        <option value="rent">For Rent</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="price">Price (KSH) *</label>
                                <input type="number" id="price" name="price" min="0" required>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group" id="bedrooms-group">
                                    <label for="bedrooms">Bedrooms</label>
                                    <input type="number" id="bedrooms" name="bedrooms" min="0">
                                </div>
                                
                                <div class="form-group" id="bathrooms-group">
                                    <label for="bathrooms">Bathrooms</label>
                                    <input type="number" id="bathrooms" name="bathrooms" min="0">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="area">Area (sq ft) *</label>
                                    <input type="number" id="area" name="area" min="0" required>
                                </div>
                                
                                <div class="form-group" id="garages-group">
                                    <label for="garages">Garages</label>
                                    <input type="number" id="garages" name="garages" min="0">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="location">Location *</label>
                                    <select id="location" name="location" required>
                                        <option value="">Select Location</option>
                                        <?php foreach ($locations as $location): ?>
                                            <option value="<?php echo $location['id']; ?>"><?php echo $location['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="year_built">Year Built</label>
                                    <input type="number" id="year_built" name="year_built" min="1900" max="<?php echo date('Y'); ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description *</label>
                                <textarea id="description" name="description" rows="6" required></textarea>
                            </div>
                            
                            <div class="form-group checkbox-group">
                                <input type="checkbox" id="featured" name="featured" value="1">
                                <label for="featured" class="checkbox-label">Mark as Featured Property</label>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h3>Images</h3>
                            
                            <div class="form-group">
                                <label for="main_image">Main Image *</label>
                                <div class="file-input-container">
                                    <input type="file" id="main_image" name="main_image" accept="image/*" required>
                                    <div class="file-preview" id="main-image-preview"></div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="additional_images">Additional Images</label>
                                <div class="file-input-container">
                                    <input type="file" id="additional_images" name="additional_images[]" accept="image/*" multiple>
                                    <div class="file-preview" id="additional-images-preview"></div>
                                </div>
                                <div class="help-text">You can select multiple images at once</div>
                            </div>
                            
                            <h3>Amenities</h3>
                            <div class="amenities-list">
                                <?php foreach ($amenities as $amenity): ?>
                                    <div class="amenity-checkbox">
                                        <input type="checkbox" id="amenity-<?php echo $amenity['id']; ?>" name="amenities[]" value="<?php echo $amenity['id']; ?>">
                                        <label for="amenity-<?php echo $amenity['id']; ?>"><?php echo $amenity['name']; ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Add Property</button>
                        <button type="reset" class="btn-secondary">Reset Form</button>
                    </div>
                </form>
            </main>
            
            <?php include 'includes/admin-footer.php'; ?>
        </div>
    </div>
    
    <script src="../assets/js/admin.js"></script>
    <script>
        // Show/hide residential-specific fields based on property type
        document.getElementById('type').addEventListener('change', function() {
            const bedroomsGroup = document.getElementById('bedrooms-group');
            const bathroomsGroup = document.getElementById('bathrooms-group');
            const garagesGroup = document.getElementById('garages-group');
            
            if (this.value === 'residential') {
                bedroomsGroup.style.display = 'block';
                bathroomsGroup.style.display = 'block';
                garagesGroup.style.display = 'block';
            } else if (this.value === 'commercial') {
                bedroomsGroup.style.display = 'none';
                bathroomsGroup.style.display = 'none';
                garagesGroup.style.display = 'none';
            }
        });
        
        // Preview uploaded images
        document.getElementById('main_image').addEventListener('change', function() {
            const preview = document.getElementById('main-image-preview');
            preview.innerHTML = '';
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        document.getElementById('additional_images').addEventListener('change', function() {
            const preview = document.getElementById('additional-images-preview');
            preview.innerHTML = '';
            
            if (this.files) {
                for (let i = 0; i < this.files.length; i++) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        preview.appendChild(img);
                    }
                    reader.readAsDataURL(this.files[i]);
                }
            }
        });
    </script>
</body>
</html>