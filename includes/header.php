<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SELLAM Real Estate</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<!-- Header -->
<header class="site-header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="index.php">
                    <img src="assets/images/logo.jpg" alt="SELLAM">
                </a>
            </div>
            
            <nav class="main-nav">
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li class="has-dropdown">
                        <a href="javascript:void(0)">Properties</a>
                        <ul class="dropdown-menu">
                            <li><a href="properties.php?type=residential">Residential</a>
                                <ul class="sub-dropdown">
                                    <li><a href="properties.php?type=residential&purpose=buy">Buy</a></li>
                                    <li><a href="properties.php?type=residential&purpose=rent">Rent</a></li>
                                </ul>
                            </li>
                            <li><a href="properties.php?type=commercial">Commercial</a>
                                <ul class="sub-dropdown">
                                    <li><a href="properties.php?type=commercial&purpose=buy">Buy</a></li>
                                    <li><a href="properties.php?type=commercial&purpose=rent">Rent</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li><a href="services.php">Services</a></li>
                    <li><a href="property-management.php">Property Management</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </nav>
            
            <div class="header-contact">
                <div class="phone-number">
                    <i class="fas fa-phone"></i>
                    <span>+254 708 600002</span>
                </div>
                <div class="mobile-menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Menu -->
<div class="mobile-menu">
    <div class="close-menu">
        <i class="fas fa-times"></i>
    </div>
    <ul class="mobile-nav">
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About</a></li>
        <li class="has-submenu">
            <a href="javascript:void(0)">Properties <i class="fas fa-chevron-down"></i></a>
            <ul class="submenu">
                <li class="has-submenu">
                    <a href="javascript:void(0)">Residential <i class="fas fa-chevron-down"></i></a>
                    <ul class="submenu">
                        <li><a href="properties.php?type=residential&purpose=buy">Buy</a></li>
                        <li><a href="properties.php?type=residential&purpose=rent">Rent</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="javascript:void(0)">Commercial <i class="fas fa-chevron-down"></i></a>
                    <ul class="submenu">
                        <li><a href="properties.php?type=commercial&purpose=buy">Buy</a></li>
                        <li><a href="properties.php?type=commercial&purpose=rent">Rent</a></li>
                    </ul>
                </li>
            </ul>
        </li>
        <li><a href="services.php">Services</a></li>
        <li><a href="property-management.php">Property Management</a></li>
        <li><a href="contact.php">Contact Us</a></li>
    </ul>
    <div class="mobile-contact">
        <a href="tel:+254 708 600002"><i class="fas fa-phone"></i> +254 708 600002</a>
        <a href="mailto:office@sellamc.com"><i class="fas fa-envelope"></i> office@sellamc.com</a>
    </div>
    <div class="mobile-social">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-linkedin-in"></i></a>
    </div>
</div>