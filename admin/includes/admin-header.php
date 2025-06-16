<header class="admin-header">
    <div class="menu-toggle">
        <i class="fas fa-bars"></i>
    </div>
    
    <div class="header-actions">
        <div class="header-action">
            <a href="#" title="Notifications">
                <i class="fas fa-bell"></i>
                <span class="badge">3</span>
            </a>
        </div>
        
        <div class="header-action">
            <a href="../index.php" target="_blank" title="View Website">
                <i class="fas fa-globe"></i>
            </a>
        </div>
        
        <div class="admin-user">
            <div class="user-avatar">
                <img src="../assets/images/admin-avatar.jpg" alt="Admin User">
            </div>
            <div class="user-info">
                <h4><?php echo $_SESSION['admin_username']; ?></h4>
                <div class="user-role">Administrator</div>
            </div>
        </div>
    </div>
</header>