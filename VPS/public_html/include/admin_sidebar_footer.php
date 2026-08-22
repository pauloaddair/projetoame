    </div> <!-- #admin-main-content -->
</div> <!-- .admin-layout-wrapper -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('admin-sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('collapsed');
            const isCollapsed = sidebar.classList.contains('collapsed');
            
            // Salva a preferência no cookie para persistir entre recarregamentos
            document.cookie = "sidebar_collapsed=" + isCollapsed + "; path=/; max-age=" + (365 * 24 * 60 * 60);
        });
    }
});
</script>
