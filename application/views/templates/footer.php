   		</div> <!-- #main -->
    </div> <!-- #main-container -->

    <div>
      	<footer class="footer">
			<div class="container-fluid">
				<a href="#menu-toggle" class="btn btn-default" id="menu-toggle">Toggle Menu</a>
				<p class="text-muted">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
			</div>
			
			<ul class="pager" hidden>
				<!-- Previous, but not needed with AJAX -->
    			<li><a href="<?php echo current_url()?>">Next</a></li>
 			</ul>
    	</footer>
    </div> <!-- #wrapper -->

	<?php echo script_tag("asset/js/jquery-2.1.3.min.js") . PHP_EOL;?>
	<?php echo script_tag("asset/js/bootstrap.min.js") . PHP_EOL;?>
	<?php echo script_tag("asset/js/jquery-ias.min.js") . PHP_EOL;?>
	<?php echo script_tag("asset/js/tinymce/tinymce.min.js") . PHP_EOL;?>
	
	<script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });

    var ias = jQuery.ias({
   		container:  '#page-content-wrapper',
    	item:       '.post',
    	pagination: '.pager',
    	next:       '.next'
    });
    </script>
</body>
</html>
