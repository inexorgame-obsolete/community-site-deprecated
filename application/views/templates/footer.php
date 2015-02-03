		<footer class="footer">
			<div class="container-fluid">
				<a href="#menu-toggle" class="btn btn-default" id="menu-toggle">Toggle Menu</a>
				<p class="text-muted">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
     		</div>
    	</footer>
    	
		</div> <!-- #main -->
    </div> <!-- #main-container -->
	
    </div> <!-- #wrapper -->

	<?php echo script_tag("asset/js/jquery-2.1.3.min.js") . PHP_EOL;?>
	<?php echo script_tag("asset/js/bootstrap.min.js") . PHP_EOL;?>
	<?php echo script_tag("asset/js/tinymce/tinymce.min.js") . PHP_EOL;?>
	
	<script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    </script>
</body>
</html>
