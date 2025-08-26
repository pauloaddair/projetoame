<?php
$current_page = basename($_SERVER['PHP_SELF']);
$js_file = '';

switch($current_page) {
    case 'index.php':
        if (strpos($_SERVER['REQUEST_URI'], '/promoters/') !== false) {
            $js_file = '/assets/js/promoters.js';
        } else if (strpos($_SERVER['REQUEST_URI'], '/exhibitors/') !== false) {
            $js_file = '/assets/js/exhibitors.js';
        } else if (strpos($_SERVER['REQUEST_URI'], '/events/') !== false) {
            $js_file = '/assets/js/events.js';
        }
        break;
}

if ($js_file) {
    echo "<script src=\"$js_file\"></script>";
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/kanban.js"></script>
</body>
</html>