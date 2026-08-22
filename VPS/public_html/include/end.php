<!-- VLibras Widget -->
<div vlibras-widget class="vlibras-widget">
	<div class="vlibras-widget-wrapper">
		<div class="vlibras-widget-content">
			<div class="vlibras-widget-content-buttons">
				<div class="vlibras-widget-content-buttons-button vlibras-widget-content-buttons-button-close"></div>
			</div>
		</div>
	</div>
</div>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		if (window.VLibras && window.VLibras.Widget) {
			new window.VLibras.Widget('https://vlibras.gov.br/app');
		}
	});
</script>
<!-- END -->
</body>
</html>