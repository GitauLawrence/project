<?php
// Function to get featured properties
function getFeaturedProperties($conn, $limit = 6) {
    $sql = "SELECT p.*, l.name as location FROM properties p 
            JOIN locations l ON p.location_id = l.id 
            WHERE p.featured = 1 
            ORDER BY p.created_at DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $properties = [];
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
    
    $stmt->close();
    return $properties;
}

// Function to get property by ID
function getPropertyById($conn, $id) {
    $sql = "SELECT p.*, l.name as location FROM properties p 
            JOIN locations l ON p.location_id = l.id 
            WHERE p.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $property = $result->fetch_assoc();
        $stmt->close();
        return $property;
    }
    
    $stmt->close();
    return null;
}

// Function to get property images
function getPropertyImages($conn, $propertyId) {
    $sql = "SELECT * FROM property_images WHERE property_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $propertyId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
    
    $stmt->close();
    return $images;
}

// Function to get property amenities
function getPropertyAmenities($conn, $propertyId) {
    $sql = "SELECT a.* FROM amenities a 
            JOIN property_amenities pa ON a.id = pa.amenity_id 
            WHERE pa.property_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $propertyId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $amenities = [];
    while ($row = $result->fetch_assoc()) {
        $amenities[] = $row;
    }
    
    $stmt->close();
    return $amenities;
}

// Function to get similar properties
function getSimilarProperties($conn, $propertyId, $type, $purpose, $limit = 3) {
    $sql = "SELECT p.*, l.name as location FROM properties p 
            JOIN locations l ON p.location_id = l.id 
            WHERE p.id != ? AND p.type = ? AND p.purpose = ? 
            ORDER BY p.created_at DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $propertyId, $type, $purpose, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $properties = [];
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
    
    $stmt->close();
    return $properties;
}

// Function to get all locations
function getLocations($conn) {
    $sql = "SELECT * FROM locations ORDER BY name";
    
    $result = $conn->query($sql);
    
    $locations = [];
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }
    
    return $locations;
}

// Function to get all amenities
function getAmenities($conn) {
    $sql = "SELECT * FROM amenities ORDER BY name";
    
    $result = $conn->query($sql);
    
    $amenities = [];
    while ($row = $result->fetch_assoc()) {
        $amenities[] = $row;
    }
    
    return $amenities;
}
function uploadImage($file, $targetDir) {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        error_log("Upload error: " . $file['error']);
        return false;
    }

    // Verify file is an image
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($mime, $allowedTypes)) {
        error_log("Invalid file type: " . $mime);
        return false;
    }

    // Generate unique filename
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $uniqueName = uniqid('prop_', true) . '.' . $fileExt;
    
    // Create absolute path
    $absolutePath = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($targetDir, '/');
    
    // Ensure directory exists
    if (!file_exists($absolutePath)) {
        mkdir($absolutePath, 0755, true);
    }

    // Move uploaded file
    $targetPath = $absolutePath . $uniqueName;
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Return path relative to website root
        return $targetDir . $uniqueName;
    }

    return false;
}

// Function to count all properties
function countProperties($conn) {
    $sql = "SELECT COUNT(*) as count FROM properties";
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    
    return $row['count'];
}

// Function to count properties by type
function countPropertiesByType($conn, $type) {
    $sql = "SELECT COUNT(*) as count FROM properties WHERE type = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $stmt->close();
    return $row['count'];
}

// Function to count properties by purpose
function countPropertiesByPurpose($conn, $purpose) {
    $sql = "SELECT COUNT(*) as count FROM properties WHERE purpose = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $purpose);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $stmt->close();
    return $row['count'];
}

// Function to get recent properties
function getRecentProperties($conn, $limit = 5) {
    $sql = "SELECT p.*, l.name as location FROM properties p 
            JOIN locations l ON p.location_id = l.id 
            ORDER BY p.created_at DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $properties = [];
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
    
    $stmt->close();
    return $properties;
}

// Function to get properties with filters and pagination
function getPropertiesWithFilters($conn, $type = '', $purpose = '', $location = '', $search = '', $limit = 10, $offset = 0) {
    $sql = "SELECT p.*, l.name as location FROM properties p 
            JOIN locations l ON p.location_id = l.id 
            WHERE 1=1";
    
    $params = [];
    $types = "";
    
    if (!empty($type)) {
        $sql .= " AND p.type = ?";
        $params[] = $type;
        $types .= "s";
    }
    
    if (!empty($purpose)) {
        $sql .= " AND p.purpose = ?";
        $params[] = $purpose;
        $types .= "s";
    }
    
    if (!empty($location)) {
        $sql .= " AND p.location_id = ?";
        $params[] = $location;
        $types .= "i";
    }
    
    if (!empty($search)) {
        $sql .= " AND (p.title LIKE ? OR p.id LIKE ?)";
        $searchParam = "%" . $search . "%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= "ss";
    }
    
    $sql .= " ORDER BY p.created_at DESC LIMIT ?, ?";
    $params[] = $offset;
    $params[] = $limit;
    $types .= "ii";
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $properties = [];
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
    
    $stmt->close();
    return $properties;
}

// Function to count properties with filters
function countPropertiesWithFilters($conn, $type = '', $purpose = '', $location = '', $search = '') {
    $sql = "SELECT COUNT(*) as count FROM properties p 
            JOIN locations l ON p.location_id = l.id 
            WHERE 1=1";
    
    $params = [];
    $types = "";
    
    if (!empty($type)) {
        $sql .= " AND p.type = ?";
        $params[] = $type;
        $types .= "s";
    }
    
    if (!empty($purpose)) {
        $sql .= " AND p.purpose = ?";
        $params[] = $purpose;
        $types .= "s";
    }
    
    if (!empty($location)) {
        $sql .= " AND p.location_id = ?";
        $params[] = $location;
        $types .= "i";
    }
    
    if (!empty($search)) {
        $sql .= " AND (p.title LIKE ? OR p.id LIKE ?)";
        $searchParam = "%" . $search . "%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= "ss";
    }
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $stmt->close();
    return $row['count'];
}

// Function to delete property
function deleteProperty($conn, $propertyId) {
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete property images
        $stmt = $conn->prepare("DELETE FROM property_images WHERE property_id = ?");
        $stmt->bind_param("i", $propertyId);
        $stmt->execute();
        $stmt->close();
        
        // Delete property amenities
        $stmt = $conn->prepare("DELETE FROM property_amenities WHERE property_id = ?");
        $stmt->bind_param("i", $propertyId);
        $stmt->execute();
        $stmt->close();
        
        // Delete property
        $stmt = $conn->prepare("DELETE FROM properties WHERE id = ?");
        $stmt->bind_param("i", $propertyId);
        $stmt->execute();
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        return true;
    } catch (Exception $e) {
        // Rollback in case of error
        $conn->rollback();
        return false;
    }
}

// Function to set property featured status
function setPropertyFeatured($conn, $propertyId, $featured) {
    $stmt = $conn->prepare("UPDATE properties SET featured = ? WHERE id = ?");
    $stmt->bind_param("ii", $featured, $propertyId);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}
?>