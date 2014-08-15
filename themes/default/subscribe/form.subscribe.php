<div id="subscribeForm">
<form method="post" action="<?php echo $this->url['base']; ?>process.php">

	<fieldset>
		<legend><?php echo _('Join newsletter'); ?></legend>
		<input type="hidden" name="formSubmitted" value="1" />
		<?php
			if ($this->referer)
			{
				echo '<input type="hidden" name="bmReferer" value="'
						.$this->referer.'" />';
			}
		?>

		<div class="notes">
			<p>
			<?php
				echo sprintf('Fields marked like %s this %s are required.',
						'<span class="required">', '</span>');
			?>
			</p>
		</div>

		<div>
			<label class="required" for="email">
				<strong><?php echo _('Your Email:'); ?></strong>
			</label>
			<input type="text" size="32" maxlength="60" name="Email" id="email"
					value="<?php echo $this->escape($this->Email); ?>" />
		</div>

		<?php
            if (isset($this->fields) && is_array($this->fields)) {
                foreach ($this->fields as $key => $field) {
                ?>
                <div>
                    <label for="field<?php echo $key; ?>">
                        <?php
                            if ('on' == $field['required'])
                            {
                                echo '<strong class="required">';
                            }
                            echo $field['name'];
                            if ('on' == $field['required'])
                            {
                                echo '</strong>';
                            }
                        ?>
                    </label>

                    <?php
                        switch ($field['type'])
                        {
                            case 'text':
                            case 'number':
                            ?>
                                <input type="text" size="32" name="d[<?php echo $key; ?>]"
                                        id="field<?php echo $key; ?>"
                                        <?php
                                            if (isset($this->d[$key]))
                                            {
                                                echo 'value="'.$this->escape(
                                                        $this->d[$key]).'" ';
                                            }
                                            elseif ($field['normally'])
                                            {
                                                echo 'value="'.$this->escape(
                                                        $field['normally']).'" ';
                                            }
                                        ?>
                                        />
                            <?php
                                break;
                            case 'checkbox':
                            ?>
                                <input type="checkbox" name="d[<?php echo $key; ?>]"
                                        id="field<?php echo $key; ?>"
                                <?php
                                    if ('on' == $this->d['key'])
                                    {
                                        echo 'checked="checked" ';
                                    }
                                    elseif (!$this->formSubmitted
                                        && 'on' == $field['normally'])
                                    {
                                        echo 'checked="checked" ';

                                    }
                                ?>
                                />
                            <?php
                                break;
                            case 'multiple':
                            ?>
                                <select name="d[<?php echo $key; ?>]"
                                        id="field<?php echo $key; ?>">
                                    <option value=""><?php echo _('Choose Selection');
                                        ?></option>
                                <?php
                                    foreach ($field['array'] as $option)
                                    {
                                        echo '<option ';
                                        if ($option == $this->d['key'])
                                        {
                                            echo 'selected="selected" ';
                                        }
                                        elseif (!isset($this->d['key'])
                                                && $option == $field['normally'])
                                        {
                                            echo 'selected="selected" ';
                                        }
                                        echo '>'.$option.'</option>';

                                    }
                                ?>
                                </select>
                            <?php
                                break;
                            case 'date':
                            ?>
                                <input type="text" class="datepicker" size="12"
                                        name="d[<?php echo $key; ?>]"
                                        id="field<?php echo $key; ?>"
                                <?php
                                    if (isset($this->d['key']))
                                    {
                                        echo 'value="'.$this->escape($this->d['key'])
                                                .'" ';
                                    }
                                    elseif ($field['normally'])
                                    {
                                        echo 'value="'.$this->escape($field['normally'])
                                                .'" ';
                                    }
                                    else
                                    {
                                        echo 'value="'.$this->config['app']['dateformat']
                                                .'" ';
                                    }
                                ?>
                                />
                            <?php
                                break;
                            case 'comment':
                            ?>
                                <textarea name="comments" rows="3" cols="33"
                                        maxlength="255"><?php
                                if (isset($this->d['key']))
                                {
                                    echo $this->d['key'];
                                }
                                elseif ($field['normally'])
                                {
                                    echo $field['normally'];
                                }
                                ?></textarea>
                            <?php
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
		<input type="hidden" name="pommo_signup" value="true" />
		<input type="submit" name="pommo_signup" value="<?php echo _('Subscribe');
				?>" />
	</div>

</form>
</div>

