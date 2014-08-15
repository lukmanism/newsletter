<div id="subscribeForm">
	<form method="post" action="<?php echo $this->url['base']; ?>subscribe.php">
		<fieldset>
			<legend>Join newsletter</legend>

			<?php
				if ($this->referer)
				{
				?>
					<input type="hidden" value="<?php echo $this->referer; ?>"
							name="bmReferer"/>
				<?php
				}
			?>

			<div>
				<label for="email"><?php echo _('Your Email:'); ?></label>
				<input type="text" size="20" maxlength="60" name="Email" id="email" />
			</div>
		</fieldset>

		<div class="buttons">
			<input type="submit" value="<?php echo _('Subscribe'); ?>" />
		</div>
	</form>
</div>
