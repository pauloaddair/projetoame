<?php
// include/admin_sidebar.php - Menu lateral retrátil para a área administrativa do Projeto AME
$current_uri = $_SERVER['REQUEST_URI'];
$active_page = 'dashboard';

if (strpos($current_uri, 'admin/atividades') !== false || strpos($current_uri, 'admin/atividade') !== false || strpos($current_uri, 'admin/novoevento') !== false) {
    $active_page = 'atividades';
} elseif (strpos($current_uri, 'admin/candidatos') !== false || strpos($current_uri, 'casting') !== false || strpos($current_uri, 'editacandidato') !== false) {
    $active_page = 'candidatos';
} elseif (strpos($current_uri, 'admin/prospeccao') !== false) {
    $active_page = 'prospeccao';
} elseif (strpos($current_uri, 'admin/rodizio') !== false) {
    $active_page = 'rodizio';
} elseif (strpos($current_uri, 'admin/escala') !== false) {
    $active_page = 'escala';
} elseif (strpos($current_uri, 'admin/extrato') !== false || strpos($current_uri, 'admin/pagar') !== false || strpos($current_uri, 'admin/receber') !== false || strpos($current_uri, 'admin/saldo') !== false) {
    $active_page = 'financeiro';
} elseif (strpos($current_uri, 'admin/expositores') !== false) {
    $active_page = 'expositores';
}
?>
<style>
    /* Estilos do Layout Administrativo com Sidebar */
    .admin-layout-wrapper {
        margin-top: 56px;
        display: flex;
        width: 100%;
        min-height: calc(100vh - 56px);
        align-items: stretch;
        position: relative;
    }
    
    #admin-sidebar {
        min-width: 250px;
        max-width: 250px;
        background: #0f172a; /* Slate 900 */
        color: #e2e8f0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 99;
        position: relative;
    }
    
    #admin-sidebar.collapsed {
        min-width: 80px;
        max-width: 80px;
    }
    
    #admin-sidebar .sidebar-header {
        padding: 20px 15px;
        background: #1e293b; /* Slate 800 */
        border-bottom: 1px solid #334155;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    #admin-sidebar .sidebar-header h5 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #f8fafc;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: opacity 0.2s;
    }
    
    #admin-sidebar.collapsed .sidebar-header h5 {
        opacity: 0;
        width: 0;
        pointer-events: none;
    }
    
    #admin-sidebar .sidebar-header button.toggle-btn {
        background: transparent;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 5px;
        transition: color 0.2s;
    }
    
    #admin-sidebar .sidebar-header button.toggle-btn:hover {
        color: #f8fafc;
    }
    
    #admin-sidebar ul.components {
        padding: 15px 0;
        list-style: none;
        margin: 0;
    }
    
    #admin-sidebar ul li {
        padding: 4px 10px;
    }
    
    #admin-sidebar ul li a {
        padding: 12px 15px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        color: #94a3b8;
        border-radius: 8px;
        transition: all 0.2s;
        font-weight: 500;
        text-decoration: none;
        white-space: nowrap;
    }
    
    #admin-sidebar ul li a i {
        margin-right: 12px;
        font-size: 1.1rem;
        width: 20px;
        text-align: center;
        transition: margin 0.3s;
    }
    
    #admin-sidebar.collapsed ul li a i {
        margin-right: 0;
    }
    
    #admin-sidebar ul li a span {
        transition: opacity 0.2s;
    }
    
    #admin-sidebar.collapsed ul li a span {
        opacity: 0;
        width: 0;
        display: inline-block;
        pointer-events: none;
        overflow: hidden;
    }
    
    #admin-sidebar ul li a:hover {
        color: #f8fafc;
        background: #1e293b;
    }
    
    /* Cores personalizadas baseadas no branding AME */
    #admin-sidebar ul li.active a {
        color: #fff;
        background: #2563eb; /* Royal Blue */
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }
    
    #admin-sidebar ul li.item-atividades.active a {
        background: #10b981; /* Green - Atendentes */
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }
    
    #admin-sidebar ul li.item-crm.active a {
        background: #f59e0b; /* Orange/Yellow - Muito */
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
    }
    
    #admin-sidebar ul li.item-rodizio.active a {
        background: #ef4444; /* Red - Especiais */
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
    }
    
    #admin-sidebar ul li.item-financeiro.active a {
        background: #0284c7; /* Sky Blue */
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }
    
    #admin-sidebar ul li.item-expositores.active a {
        background: #8b5cf6; /* Purple */
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.25);
    }
    
    #admin-main-content {
        flex: 1;
        background: #f8fafc; /* Slate 50 */
        padding: 30px 20px;
        transition: all 0.3s;
        overflow-x: hidden;
    }
    
    /* Responsividade */
    @media (max-width: 768px) {
        #admin-sidebar {
            min-width: 80px;
            max-width: 80px;
        }
        #admin-sidebar .sidebar-header h5 {
            display: none;
        }
        #admin-sidebar ul li a span {
            display: none;
        }
        #admin-sidebar ul li a i {
            margin-right: 0;
        }
    }
</style>

<div class="admin-layout-wrapper">
    <!-- Sidebar -->
    <nav id="admin-sidebar" class="<?php echo isset($_COOKIE['sidebar_collapsed']) && $_COOKIE['sidebar_collapsed'] === 'true' ? 'collapsed' : ''; ?>">
        <div class="sidebar-header">
            <h5>Painel AME</h5>
            <button type="button" class="toggle-btn" id="sidebarToggle" title="Recolher/Expandir Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <ul class="components">
            <li class="<?php echo $active_page === 'dashboard' ? 'active' : ''; ?>">
                <a href="<?php echo $GLOBALS['app_web_root']; ?>admin">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard Geral</span>
                </a>
            </li>
            
            <li class="item-atividades <?php echo $active_page === 'atividades' ? 'active' : ''; ?>">
                <a href="<?php echo $GLOBALS['app_web_root']; ?>admin/atividades">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Atividades / Eventos</span>
                </a>
            </li>

            <li class="item-candidatos <?php echo $active_page === 'candidatos' ? 'active' : ''; ?>">
                <a href="<?php echo $GLOBALS['app_web_root']; ?>admin/candidatos">
                    <i class="fas fa-users"></i>
                    <span>Casting & Candidatos</span>
                </a>
            </li>

            <li class="item-crm <?php echo $active_page === 'prospeccao' ? 'active' : ''; ?>">
                <a href="<?php echo $GLOBALS['app_web_root']; ?>admin/prospeccao">
                    <i class="fas fa-search-dollar"></i>
                    <span>CRM Prospecção</span>
                </a>
            </li>
            
            <li class="item-rodizio <?php echo $active_page === 'rodizio' ? 'active' : ''; ?>">
                <a href="<?php echo $GLOBALS['app_web_root']; ?>admin/rodizio">
                    <i class="fas fa-sync-alt"></i>
                    <span>Fila & Rodízio</span>
                </a>
            </li>

            <li class="item-financeiro <?php echo $active_page === 'financeiro' ? 'active' : ''; ?>">
                <a href="<?php echo $GLOBALS['app_web_root']; ?>admin/extrato">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Extrato Contábil</span>
                </a>
            </li>

            <li class="item-expositores <?php echo $active_page === 'expositores' ? 'active' : ''; ?>">
                <a href="<?php echo $GLOBALS['app_web_root']; ?>admin/expositores">
                    <i class="fas fa-handshake"></i>
                    <span>Parceiros / Empresas</span>
                </a>
            </li>
            
            <hr class="border-secondary mx-3 my-2">
            
            <li>
                <a href="./" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    <span>Ver Site Público</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- Área de Conteúdo Principal -->
    <div id="admin-main-content">
