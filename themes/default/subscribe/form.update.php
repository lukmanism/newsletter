<div id="subscribeForm">

	<form method="post" action="">
		<input type="hidden" name="formSubmitted" value="1" />
		<input type="hidden" name="code" value="<?php echo $this->code; ?>" />

		<fieldset>
			<legend><?php echo _('Your Information'); ?></legend>
			<input type="hidden" name="updateForm" value="true" />

			<div class="notes">
				<p>
				<?php
					echo sprintf(_('Fields marked like %s this %s are required.'),
							'<span class="required">', '</span>');
				?>
				</p>
			</div>

			<div>
				<label class="required" for="email">
					<strong><?php echo _('Your Email:'); ?></strong>
				</label>
				<input type="text" size="32" maxlength="60" name="email" id="email"
						value="<?php echo $this->escape($this->email); ?>"
						readonly="readonly" />
			</div>

			<div>
				<label for="email"><?php echo _('New Email:'); ?></label>
				<input type="text" size="32" maxlength="60" name="newemail"
						id="newemail" value="<?php echo $this->newemail; ?>" />
			</div>

			<div>
				<label for="email"><?php echo _('Verify New Email:'); ?></label>
				<input type="text" size="32" maxlength="60" name="newemail2"
						id="newemail2" value="<?php echo $this->newemail2; ?>" />
			</div>

			<?php
                if (!empty($this->fields) && is_array($this->fields))
                {
                    foreach ($this->fields as $key => $field)
                    {
                    ?>
                    <div>
                        <!-- DON'T DISPLAY COMMENT FIELDS ON UPDATE FORM. A COMMENT FIELD
                                IS PROVIDED @ user/update.php FOR UNSUBSCRIBE -->
                        <?php
                        if ('comment' != $field['type'])
                        {
                        ?>
                            <label
                            <?php
                                if ('on' == $field['required'])
                                {
                                    echo 'class="required"';
                                }
                            ?> for="field<?php echo $key; ?>">
                                <?php echo $field['name']; ?>:
                            </label>
                        <?php
                        }

                        switch ($field['type'])
                        {
                            case 'checkbox':
                                echo '<input type="checkbox" name="d['.$key.']"
                                        id="field'.$key.'"'.('on' == $this->d[$key] ?
                                        ' checked="checked" ' : '').' />';
                                break;
                            case 'multiple':
                                echo '<select name="d['.$key.']" id="field'.$key.'">';
                                echo
                                    '<option value="">'
                                        ._('Choose Selection')
                                    .'</option>';
                                foreach ($field['array'] as $option)
                                {
                                    echo
                                        '<option '.($option == $this->d[$key] ?
                                                ' selected="selected" ' : '').'>'
                                            .$option
                                        .'</option>';
                                }
                                echo '</select>';
                                break;
                            default:
                                echo '<input type="text" '.('date' == $field['type'] ?
                                        'class="text datepicker" size="12" ' :
                                        'size="32" ').'name="d['.$key.']"
                                        id="field'.$key.'" '.(isset($this->d[$key]) ?
                                        ' value="'.$this->escape($this->d[$key]).'" ' :
                                        '').' />';
                                break;
                        }
                        ?>
                </div>
                <?php
                    }
                }
			?>
		</fieldset>

		<div class="buttons">
			<input type="submit" value="<?php echo _('Update Records'); ?>"
					name="update" />
		</div>
	</form>
</div>
