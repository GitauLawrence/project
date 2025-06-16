document.addEventListener('DOMContentLoaded', function() {
    // Toggle Sidebar on Mobile
    const menuToggle = document.querySelector('.menu-toggle');
    const adminSidebar = document.querySelector('.admin-sidebar');
    const adminContent = document.querySelector('.admin-content');
    
    if (menuToggle && adminSidebar && adminContent) {
        menuToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('active');
        });
    }
    
    // Active Menu Item
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.sidebar-menu a');
    
    if (menuItems) {
        menuItems.forEach(function(item) {
            const href = item.getAttribute('href');
            if (currentPath.includes(href) && href !== '#') {
                item.classList.add('active');
            }
        });
    }
    
    // Auto Height for Textareas
    const textareas = document.querySelectorAll('textarea');
    
    if (textareas) {
        textareas.forEach(function(textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            
            // Trigger on load
            textarea.dispatchEvent(new Event('input'));
        });
    }
    
    // File Input Preview
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    if (fileInputs) {
        fileInputs.forEach(function(input) {
            input.addEventListener('change', function() {
                const previewId = this.getAttribute('data-preview');
                if (previewId) {
                    const preview = document.getElementById(previewId);
                    preview.innerHTML = '';
                    
                    if (this.files && this.files.length > 0) {
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
                }
            });
        });
    }
    
    // Confirm Delete
    const deleteButtons = document.querySelectorAll('.delete-btn');
    
    if (deleteButtons) {
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    }
    
    // Select All Checkboxes
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.property-checkbox');
    
    if (selectAll && checkboxes.length > 0) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = selectAll.checked;
            });
        });
        
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(checkboxes).every(function(c) {
                    return c.checked;
                });
                
                const anyChecked = Array.from(checkboxes).some(function(c) {
                    return c.checked;
                });
                
                selectAll.checked = allChecked;
                selectAll.indeterminate = anyChecked && !allChecked;
            });
        });
    }
    
    // Apply Bulk Action Button
    const applyBulkAction = document.getElementById('apply-bulk-action');
    
    if (applyBulkAction) {
        applyBulkAction.addEventListener('click', function(e) {
            const bulkAction = document.getElementById('bulk-action').value;
            const selectedCount = document.querySelectorAll('.property-checkbox:checked').length;
            
            if (!bulkAction) {
                e.preventDefault();
                alert('Please select an action');
                return;
            }
            
            if (selectedCount === 0) {
                e.preventDefault();
                alert('Please select at least one property');
                return;
            }
            
            if (bulkAction === 'delete') {
                if (!confirm(`Are you sure you want to delete ${selectedCount} selected properties? This action cannot be undone.`)) {
                    e.preventDefault();
                }
            }
        });
    }
    
    // Auto-dismiss alerts
    const alerts = document.querySelectorAll('.alert');
    
    if (alerts.length > 0) {
        setTimeout(function() {
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 5000);
    }
});