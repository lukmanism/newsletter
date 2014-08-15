<form class="ajax" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

	<fieldset>
		<legend><?php echo _('SMTP Throttling'); ?></legend>
		<div>
			<label for="throttle_SMTP"><?php echo _('Throttle Sharing'); ?></label>
			<select name="throttle_SMTP" id="throttle_SMTP" class="onChange">
			<option value="individual"
			<?php
				if ('individual' == $this->throttle_SMTP)
				{
					echo 'selected="selected"';
				}
			?>
			><?php echo _('off'); ?></option>
			<option value="shared"
			<?php
				if ('shared' == $this->throttle_SMTP)
				{
					echo 'selected="selected"';
				}
			?>
			><?php echo _('on'); ?></option>
			</select>
			<div class="notes">
				<?php echo _('(ON; the throttler will be global. OFF; independent'
					.' throttler per relay.)'); ?>
			</div>
		</div>
		<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif"
				alt="loading..." class="hidden" name="loading" />
		<div class="output alert">
		<?php
			if ($this->output)
			{
				echo $this->output;
			}
		?>
		</div>
	</fieldset>

	<?php
		foreach ($this->smtpStatus as $id => $status)
		{
		?>
		<fieldset>
			<legend><?php echo sprintf(_('SMTP #%s'), $id); ?></legend>

			<div>
				<label><?php echo _('SMTP Status:'); ?></label>
				<?php
					if ($status)
					{
				?>
						<img src="<?php echo $this->url['theme']['shared'];
								?>images/icons/ok.png" alt="ok icon" />
						<?php echo _('Connected to SMTP server'); ?>
				<?php
					}
					else
					{
				?>
						<img src="<?php echo $this->url['theme']['shared'];
								?>images/icons/nok.png" alt="not ok icon" />
								<?php echo _('Unable to connect to SMTP server'); ?>
				<?php
					}
				?>
			</div>

			<div>
				<label for="host[<?php echo $id; ?>]">
					<?php echo _('SMTP Host:'); ?>
				</label>
				<input value="<?php echo $this->escape($this->smtp[$id]['host']); ?>"
						type="text" name="host[<?php echo $id; ?>]" />
				<div class="notes">
					<?php echo _('(IP Address or Name of SMTP server)'); ?>
				</div>
			</div>

            <div>
                <label for="security[<?php echo $id; ?>]">
                    <?php echo _('SMTP Security:'); ?>
                </label>
                <input type="radio" name="security[<?php echo $id; ?>]" value="none"
                <?php
                    if (
                        !$this->smtp[$id]['security']
                        || 'none' == $this->smtp[$id]['security']
                    ) {
                        echo 'checked="checked"';
                    }
                ?>
                /> None
                <input type="radio" name="security[<?php echo $id; ?>]" value="ssl"
                <?php
                    if ('ssl' === $this->smtp[$id]['security']) {
                        echo 'checked="checked"';
                    }
                ?>
                /> SSL
                <div class="notes">
                    <?php echo _('(If you are using gmail, SSL is required)'); ?>
                </div>
            </div>

			<div>
				<label for="port[<?php echo $id; ?>]">
					<?php echo _('Port Number:'); ?>
				</label>
				<input type="text" name="port[<?php echo $id; ?>]"
						value="<?php echo $this->escape($this->smtp[$id]['port']); ?>" />
				<div class="notes">
					<?php echo _('(Port # of SMTP server [usually 25])'); ?>
				</div>
			</div>

			<div>
				<label for="auth[<?php echo $id; ?>]">
					<?php echo _('SMTP Authentication:'); ?>
				</label>
				<input type="radio" name="auth[<?php echo $id; ?>]" value="on"
				<?php
					if ('on' == $this->smtp[$id]['auth'])
					{
						echo 'checked="checked"';
					}
				?>
				/> on
				<input type="radio" name="auth[<?php echo $id; ?>]" value="off"
				<?php
					if ('on' != $this->smtp[$id]['auth'])
					{
						echo 'checked="checked"';
					}
				?>
				/> off
				<div class="notes">
					<?php echo _('(Toggle SMTP Authentication [usually off])'); ?>
				</div>
			</div>

			<div>
				<label for="user[<?php echo $id; ?>]">
					<?php echo _('SMTP Username:'); ?>
				</label>
				<input type="text" name="user[<?php echo $id; ?>]"
						value="<?php echo $this->escape($this->smtp[$id]['user']);
						?>" />
				<div class="notes"><?php echo _('(optional)'); ?></div>
			</div>

			<div>
				<label for="pass[<?php echo $id; ?>]">
					<?php echo _('SMTP Password:'); ?>
				</label>
				<input type="password" name="pass[<?php echo $id; ?>]"
						value="<?php echo $this->escape($this->smtp[$id]['pass']);
						?>" />
				<div class="notes"><?php echo _('(optional)'); ?></div>
			</div>

			<div class="buttons">
				<input type="submit" name="updateSmtpServer[<?php echo $id; ?>]"
						id="updateSmtpServer<?php echo $id; ?>"
						value="<?php echo sprintf(_('Update Relay #%s'), $id);
						?>" />
				<img src="<?php echo $this->url['theme']['shared'];
						?>images/loader.gif" alt="loading..." class="hidden"
						name="loading" />
				<?php
					if (1 == $id)
					{
					?>
						- <?php echo _('This is your default relay'); ?>
					<?php
					}
					else
					{
					?>
						<input type="submit" name="deleteSmtpServer[<?php echo $id;
								?>]" id="deleteSmtpServer<?php echo $id; ?>"
								value="<?php echo sprintf(_('Remove Relay #%s'),
								$id); ?>" />
					<?php
					}
				?>
				<div class="output alert">
				<?php
					if ($this->output)
					{
						echo $this->output;
					}
				?>
				</div>
			</div>

		</fieldset>
		<?php
		}
	?>

	<?php
		if ($this->addServer)
		{
		?>
			<input type="submit" name="addSmtpServer[<?php echo $this->addServer;
					?>]" id="addSmtpServer<?php echo $this->addServer; ?>"
					value="<?php echo _('Add Another Relay'); ?>" />
		<?php
		}
	?>
</form>
