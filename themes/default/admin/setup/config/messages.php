<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="json">
	<fieldset>
		<legend><?php echo _('notifications'); ?></legend>
		<?php echo _('Administrators can be sent a notification of subscription'
			.' list changes.'); ?>

		<div>
			<label for="notify_email">
				<?php echo _('Notification email(s):'); ?>
			</label>
			<input value="<?php echo $this->escape($this->notify_email); ?>"
					type="text" name="notify_email" />
			<span class="notes">
				<?php echo _('(Notifications will be sent to the above'
					.' address(es). Multiple addresses can be entered --'
					.' seperate with a comma.)'); ?>
			</span>
		</div>

		<div>
			<label for="notify_subject"><?php echo _('Subject Prefix:'); ?></label>
			<input value="<?php echo $this->escape($this->notify_subject); ?>"
					type="text" name="notify_subject" />
			<span class="notes">
				<?php echo _('(The subject of Notification Mails will begin with'
					.' this)'); ?>
			</span>
		</div>

		<div>
			<label for="notify_subscribe">
			<?php
				echo sprintf(_('Notify on %s'),
						'<strong>'.$this->t_subscribe.'</strong>');
			?>
			</label>
			<input type="radio" name="notify_subscribe" value="on"
			<?php
				if ('on' == $this->notify_subscribe)
				{
					echo 'checked="checked"';
				}
			?>
			/> <?php echo _('on'); ?>
			<input type="radio" name="notify_subscribe" value="off"
			<?php
				if ('on' != $this->notify_subscribe)
				{
					echo 'checked="checked"';
				}
			?> /> <?php echo _('off'); ?>
			<span class="notes">
				<?php echo _('(sent upon successful subscription)'); ?>
			</span>
		</div>

		<div>
			<label for="notify_unsubscribe">
			<?php
				echo sprintf(_('Notify on %s'),
						'<strong>'.$this->t_unsubscribe.'</strong>');
			?>
			</label>
			<input type="radio" name="notify_unsubscribe" value="on"
			<?php
				if ('on' == $this->notify_unsubscribe)
				{
					echo 'checked="checked"';
				}
			?> /> <?php echo _('on'); ?>
			<input type="radio" name="notify_unsubscribe" value="off"
			<?php
				if ('on' != $this->notify_unsubscribe)
				{
					echo 'checked="checked"';
				}
			?> /> <?php echo _('off'); ?>
			<span class="notes">
				<?php echo _('(sent upon successful unsubscription)'); ?>
			</span>
		</div>

		<div>
			<label for="notify_update">
			<?php
				echo sprintf(_('Notify on %s'),
						'<strong>'.$this->t_update.'</strong>');
			?>
			</label>
			<input type="radio" name="notify_update" value="on"
			<?php
				if ('on' == $this->notify_update)
				{
					echo 'checked="checked"';
				}
			?> /> <?php echo _('on'); ?>
			<input type="radio" name="notify_update" value="off"
			<?php
				if ('on' != $this->notify_update)
				{
					echo 'checked="checked"';
				}
			?> /> <?php echo _('off'); ?>
			<span class="notes">
				<?php echo _('(sent upon subscriber update)'); ?>
			</span>
		</div>

		<div>
			<label for="notify_pending">
			<?php
				echo sprintf(_('Notify on %s'),
						'<strong>'.$this->t_pending.'</strong>');
			?>
			</label>
			<input type="radio" name="notify_pending" value="on"
			<?php
				if ('on' == $this->notify_pending)
				{
					echo 'checked="checked"';
				}
			?> /> <?php echo _('on'); ?>
			<input type="radio" name="notify_pending" value="off"
			<?php
				if ('on' != $this->notify_pending)
				{
					echo 'checked="checked"';
				}
			?> /> <?php echo _('off'); ?>
			<span class="notes">
				<?php echo _('(sent upon subscription attempt)'); ?>
			</span>
		</div>

		<div class="buttons">
			<input type="submit" value="<?php echo _('Update'); ?>" />
			<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif"
					alt="loading..." class="hidden" name="loading" />
		</div>

		<div class="output alert">
		<?php
			if ($this->output)
			{
				echo $this->output;
			}
		?>
		</div>

	</fieldset>

	<fieldset>
		<legend><?php echo _('Messages'); ?></legend>

		<?php echo _('Customize the messages sent to your users when they'
			.' subscribe, unsubscribe, attempt to subscribe, or request to'
			.' update their records.'); ?>

		<h2><?php echo _('Subscription'); ?></h2>

		<h3><?php echo _('Email'); ?></h3>

		<input type="checkbox" name="subscribe_email"
		<?php
			if ($this->subscribe_email)
			{
				echo 'checked="checked"';
			}
		?> /><?php echo _('(Check to Enable)'); ?>

		<div>
			<label for="subscribe_sub">
				<strong class="required"><?php echo _('Subject:'); ?></strong>
			</label>
			<input value="<?php echo $this->escape($this->subscribe_sub); ?>"
					type="text" name="subscribe_sub" />
		</div>

		<div>
			<label for="subscribe_msg">
				<strong class="required"><?php echo _('Message:'); ?></strong>
			</label>
			<textarea name="subscribe_msg" cols="70" rows="10"><?php
					echo $this->escape($this->subscribe_msg); ?></textarea>
		</div>

		<h3><?php echo _('Website'); ?></h3>

		<div>
			<label for="subscribe_web">
				<strong class="required"><?php echo _('Message:'); ?></strong>
			</label>
			<textarea name="subscribe_web" cols="70" rows="5"><?php
					echo $this->escape($this->subscribe_web); ?></textarea>
			<div class="notes"><?php echo '(displayed on webpage)'; ?></div>
		</div>

		<div class="buttons">
			<input type="submit" value="<?php echo _('Update'); ?>" />
			<input type="submit" name="restore[subscribe]"
					value="<?php echo _('Restore to Defaults'); ?>" />
			<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif"
					alt="loading..." class="hidden" name="loading" />
		</div>

		<div class="output alert">
		<?php
			if ($this->output)
			{
				echo $this->output;
			}
		?>
		</div>

		<hr />
		<h2><?php echo _('Unsubscription'); ?></h2>

		<h3><?php echo _('Email'); ?></h3>

		<input type="checkbox" name="unsubscribe_email"
		<?php
			if ($this->unsubscribe_email)
			{
				echo 'checked="checked"';
			}
		?> /><?php echo _('(Check to Enable)'); ?>

		<div>
			<label for="unsubscribe_sub">
				<strong class="required"><?php echo _('Subject:'); ?></strong>
			</label>
			<input value="<?php echo $this->escape($this->unsubscribe_sub); ?>"
					type="text" name="unsubscribe_sub" />
		</div>

		<div>
			<label for="unsubscribe_msg">
				<strong class="required"><?php echo _('Message:'); ?></strong>
			</label>
			<textarea name="unsubscribe_msg" cols="70" rows="10"><?php
					echo $this->escape($this->unsubscribe_msg); ?></textarea>
		</div>

		<h3><?php echo _('Website'); ?></h3>

		<div>
			<label for="unsubscribe_web">
				<strong class="required"><?php echo _('Message:'); ?></strong>
			</label>
			<textarea name="unsubscribe_web" cols="70" rows="5"><?php
					echo $this->escape($this->unsubscribe_web); ?></textarea>
			<div class="notes"><?php echo '(displayed on webpage)'; ?></div>
		</div>

		<div class="buttons">
			<input type="submit" value="<?php echo _('Update'); ?>" />
			<input type="submit" name="restore[unsubscribe]"
					value="<?php echo _('Restore to Defaults'); ?>" />
			<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif"
					alt="loading..." class="hidden" name="loading" />
		</div>

		<div class="output alert">
		<?php
			if ($this->output)
			{
				echo $this->output;
			}
		?>
		</div>

		<hr />
		<h2><?php echo _('Subscription Confirmation'); ?></h2>

		<h3><?php echo _('Email'); ?></h3>

		<div>
			<label for="confirm_sub">
				<strong class="required"><?php echo _('Subject:'); ?></strong>
			</label>
			<input value="<?php echo $this->escape($this->confirm_sub); ?>"
					type="text" name="confirm_sub" />
		</div>

		<div>
			<label for="confirm_msg">
				<strong class="required"><?php echo _('Message:'); ?></strong>
			</label>
			<textarea name="confirm_msg" cols="70" rows="10"><?php
					echo $this->escape($this->confirm_msg); ?></textarea>
			<div class="notes">
			<?php
				echo sprintf(_('(Use %s[[url]]%s for the confirm link at least once)'),
						'<tt>', '</tt>');
			?>
			</div>
		</div>

		<div class="buttons">
			<input type="submit" value="<?php echo _('Update'); ?>" />
			<input value="<?php echo _('Restore to Defaults'); ?>"
					type="submit" name="restore[confirm]" />
			<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif"
						alt="loading..." class="hidden" name="loading" />
		</div>

		<div class="output alert">
		<?php
			if ($this->output)
			{
				echo $this->output;
			}
		?>
		</div>

		<hr />
		<h2><?php echo _('Account Access'); ?></h2>

		<h3><?php echo _('Email'); ?></h3>
		<div>
			<label for="activate_sub">
				<strong class="required"><?php echo _('Subject:'); ?></strong>
			</label>
			<input value="<?php echo $this->escape($this->activate_sub); ?>"
					type="text" name="activate_sub" />
		</div>

		<div>
			<label for="activate_msg">
				<strong class="required"><?php echo _('Message:'); ?></strong>
			</label>
			<textarea name="activate_msg" cols="70" rows="10"><?php
					echo $this->escape($this->activate_msg); ?></textarea>
			<div class="notes">
			<?php
				echo sprintf(_('(Use %s[[url]]%s for the confirm link at least once)'),
						'<tt>', '</tt>');
			?>
			</div>
		</div>

		<div class="buttons">
			<input type="submit" value="<?php echo _('Update'); ?>" />
			<input type="submit" name="restore[activate]"
					value="<?php echo _('Restore to Defaults'); ?>" />
			<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif"
					alt="loading..." class="hidden" name="loading" />
		</div>

		<div class="output alert">
		<?php
			if ($this->output)
			{
				echo $this->output;
			}
		?>
		</div>

		<hr />
		<h2><?php echo _('Update Validation'); ?></h2>

		<h3><?php echo _('Email'); ?></h3>

		<div>
			<label for="update_sub">
				<strong class="required"><?php echo _('Subject:'); ?></strong>
			</label>
			<input value="<?php echo $this->escape($this->update_sub); ?>"
					type="text" name="update_sub" />
		</div>

		<div>
			<label for="update_msg">
				<strong class="required"><?php echo _('Message:'); ?></strong>
			</label>
			<textarea name="update_msg" cols="70" rows="10"><?php
					echo $this->escape($this->update_msg); ?></textarea>
			<div class="notes">
			<?php
				echo sprintf(_('(Use %s[[url]]%s for the confirm link at least once)'),
						'<tt>', '</tt>');
			?>
			</div>
		</div>

		<div class="buttons">
			<input type="submit" value="<?php echo _('Update'); ?>" />
			<input type="submit" name="restore[update]"
					value="<?php echo _('Restore to Defaults'); ?>" />
			<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif"
					alt="loading..." class="hidden" name="loading" />
		</div>

		<div class="output">
		<?php
			if ($this->output)
			{
				echo $this->output;
			}
		?>
		</div>

	</fieldset>

</form>
