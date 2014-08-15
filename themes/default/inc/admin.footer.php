	<?php if (!isset($this->simpleTemplate)) { ?>
			<br class="clear" />
	</div> <!-- end content -->
	<div id="footer">
		<!-- <p>
			- <?php echo _('Page fueled by poMMo mailing management software'); ?> -
		</p> -->
	</div> <!-- end footer -->
	<?php } ?>
	<?php
		echo('<!-- Captured footer -->');
        echo $this->capturedFooter;
        echo('<!-- END Captured footer -->');
		echo('<!-- Captured Dialogs -->');
        echo $this->capturedDialogs;
		echo('<!-- END Captured Dialogs -->');
        ?>
</body>
</html>
