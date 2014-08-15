<?php
ob_start();
echo '<link type="text/css" rel="stylesheet" href="'.$this->url['theme']['shared']
		.'css/table.css" />';
include $this->template_dir.'/inc/ui.dialog.php';
include $this->template_dir.'/inc/ui.form.php';
$this->capturedHead = ob_get_clean();

include $this->template_dir.'/inc/admin.header.php';
?>

<ul class="inpage_menu">
	<li>
		<a href="<?php echo $this->url['base']; ?>subscribers_groups.php"><?php
				echo sprintf(_('Return to %s'), $this->returnStr); ?></a>
	</li>
</ul>

<h2><?php echo _('Edit Group'); ?></h2>

<p>
	<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/groups.png"
			alt="groups icon" class="navimage right" />
	<?php
		echo sprintf(_('To add subscribers to a group you must create matching'
			.' rules. Subscribers are automatically added to a group if their'
			.' %ssubscriber field%s values "match" a Group\'s rules. For'
			.' example, if you collect "AGE" and "COUNTRY" as %ssubscriber'
			.' fields%s, you can match those who are 21+ and living in Japan'
			.' by creating two rules; one which matches "AGE" to greater than'
			.' 20, and another which matches "Japan" to "COUNTRY". Including'
			.' or excluding members of other groups is possible.'),
			'<a href="'.$this->url['base'].'setup_fields.php">', '</a>',
			'<a href="'.$this->url['base'].'setup_fields.php">', '</a>');
	?>
</p>

<?php

include $this->template_dir.'/inc/messages.php';

?>

<form class="json validate" action="ajax/group.rpc.php?call=renameGroup"
		method="post">
	<fieldset>
		<legend><?php echo _('Change Name'); ?></legend>
		<div>
			<label for="group_name"><?php echo _('Group name:'); ?></label>
			<input class="pvEmpty" type="text" title="<?php
					echo _('type new group name'); ?>" maxlength="60" size="30"
					name="group_name" id="group_name" value="<?php
					echo $this->escape($this->group['name']); ?>" />
			<input type="submit" name="rename" value="<?php echo _('Rename'); ?>" />
			<div class="output"></div>
		</div>
	</fieldset>
</form>

<form action="" id="addRule" method="post">
	<fieldset>
		<legend><?php echo _('Add Rule'); ?></legend>

		<div>
			<label for="field">
			<?php
				echo sprintf(_('Select a %sfield%s to filter'),
						'<strong><a href="'.$this->url['base'].'setup_fields.php">',
						'</a></strong>');
			?>
			</label>
			<select name="field">
				<option value="">
					-- <?php echo _('Choose Subscriber Field'); ?> --
				</option>
				<?php
					foreach ($this->fields as $id => $field)
					{
					?>
					<option value="<?php echo $id; ?>">
						<?php echo $field['name']; ?>
					</option>
					<?php
					}
				?>
			</select>
		</div>

		<div>
			<label for="group">
			<?php
				echo sprintf(_('or, Select a %s group %s to include or exclude'),
						'<strong><a href="'.$this->url['base'].'subscribers_groups.php">',
						'</a></strong>');
			?>
			</label>
			<select name="group">
			<option value="">-- <?php echo _('Choose Group'); ?> --</option>
			<?php
					foreach ($this->legalGroups as $id => $name)
					{
					?>
					<option value="<?php echo $id; ?>">
						<?php echo $name; ?>
					</option>
					<?php
					}
				?>
			</select>
		</div>

	</fieldset>
</form>

<form id="rules" class="json" action="ajax/group.rpc.php?call=updateRule"
		method="post">
	<input type="hidden" name="fieldID" value=''>
	<input type="hidden" name="logic" value=''>
	<input type="hidden" name="type" value=''>
	<input type="hidden" name="request" value=''>

	<div class="output alert"></div>

	<fieldset>
		<legend><?php echo _('Group Rules'); ?></legend>

		<table>
			<thead>
				<tr>
					<th><?php echo _('Delete'); ?></th>
					<th><?php echo _('Edit'); ?></th>
					<th><?php echo _('Field'); ?></th>
					<th><?php echo _('Logic'); ?></th>
					<th><?php echo _('Value'); ?></th>
					<th><?php echo _('Type'); ?></th>
				</tr>
			</thead>
			<tr class="alert">
				<td colspan="6" style="padding: 5px;">
					<center>
					<?php
						echo _('"AND" RULES').'<br />';
						echo sprintf(_('MATCH %sALL%s OF THE FOLLOWING'),
								'<strong>', '</strong>');
					?>
					</center>
				</td>
			</tr>

			<?php
				if (!empty($this->rules['and']) && is_array($this->rules['and']))
				{
					foreach ($this->rules['and'] as $field_id => $rule)
					{
						$cycle = 0;
						foreach ($rule as $logic_id => $values)
						{
							$cycle++;
							if (3 < $cycle)
							{
								$cycle = 1;
							}
							?>
							<tr class="r<?php echo $cycle; ?>">
								<td>
									<img src="<?php echo $this->url['theme']['shared'];
											?>images/icons/delete.png"
											alt="<?php echo _('Delete'); ?>"
											onClick="poMMo.callback.updateRule({fieldID:'<?php
											echo $this->escape($field_id);
											?>',logic:'<?php
											echo $this->escape($logic_id);
											?>',request:'delete'});" />
								</td>
								<td>
								<?php
									// Do not allow editing of checkboxes
									if ('true' != $logic_id && 'false' != $logic_id)
									{
									?>
										<img src="<?php echo
												$this->url['theme']['shared'];
												?>images/icons/edit.png"
												alt="<?php echo _('Edit'); ?>"
												onClick="poMMo.callback.editRule({fieldID:'<?php
												echo $this->escape($field_id);
												?>',logic:'<?php echo
												$this->escape($logic_id);
												?>', type: 'and'});" />
									<?php
									}
								?>
								</td>

								<td>
									<?php
										echo $this->fields[$field_id]['name'];
									?>
								</td>

								<td>
									<?php echo $this->logicNames[$logic_id]; ?>
								</td>

								<td>
									<ul>
									<?php
									$first = true;
									foreach ($values as $v)
									{
										if ($v)
										{
											if (!$first)
											{
												echo '<br />('._('or').')';
											}

											if ('date' ==
													$this->fields[$field_id]['type'])
											{
												echo Pommo_Helper::timeToStr($v);
											}
											else
											{
												echo $v;
											}
										}
										$first = false;
									}
									?>
									</ul>
								</td>

								<td>
									<select onChange="poMMo.callback.updateRule({fieldID:'<?php
											echo $this->escape($field_id); ?>',logic:'<?php
											echo $this->escape($logic_id);
											?>',type:'or',request:'update'});">
										<option selected><?php echo _('AND'); ?></option>
										<option><?php echo _('OR'); ?></option>
									</select>
								</td>
							</tr>
						<?php
						}
					}
				}
				else
				{
				?>
				<tr class="r1">
					<td colspan="5">
						<?php echo _('No rules have been added'); ?>
					</td>
				</tr>
				<?php
				}
			?>

			<tr class="alert">
				<td colspan="6" style="padding: 5px; position: relative;">
					<center>
						<?php
							echo _('"OR" RULES');
							echo '<br />';
							echo sprintf(_('<strong>OR</strong>, MATCH %sANY%s'
								.' OF THE FOLLOWING'), '<strong>', '</strong>');
						?>
					</center>
				</td>
			</tr>

			<?php
				if (!empty($this->rules['or']) && is_array($this->rules['or']))
				{
					foreach ($this->rules['or'] as $field_id => $rule)
					{
						$cycle = 0;
						foreach ($rule as $logic_id => $values)
						{
							$cycle++;
							if (3 < $cycle)
							{
								$cycle = 1;
							}
							?>
							<tr class="r<?php echo $cycle; ?>">
								<td>
									<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/delete.png" alt="<?php echo _('Delete'); ?>" onClick="poMMo.callback.updateRule({fieldID:'<?php echo $this->escape($field_id); ?>',logic:'<?php echo $this->escape($logic_id); ?>',request:'delete'});" />
								</td>

								<td>
								<?php
									// Do not allow editing of checkboxes
									if ('true' != $logic_id && 'false' != $logic_id)
									{
									?>
										<img src="<?php echo $this->url['theme']['shared']; ?>images/icons/edit.png" alt="<?php echo _('Edit'); ?>" onClick="poMMo.callback.editRule({fieldID:'<?php echo $this->escape($field_id); ?>',logic:'<?php echo $this->escape($logic_id); ?>', type: 'or'});" />
									<?php
									}
								?>
								</td>

								<td><?php echo $this->fields[$field_id]['name']; ?></td>

								<td><?php echo $this->logicNames[$logic_id]; ?></td>

								<td>
									<ul>
									<?php
										$first = true;
										foreach ($values as $v)
										{
											if ($v)
											{
												if (!$first)
												{
													echo '<br />('._('or').')';
												}

												if ('date' ==
														$this->fields[$field_id]['type'])
												{
													echo Pommo_Helper::timeToStr($v);
												}
												else
												{
													echo $v;
												}
											}
											$first = false;
										}
									?>
									</ul>
								</td>

								<td>
									<select onChange="poMMo.callback.updateRule({fieldID:'<?php echo $this->escape($field_id); ?>',logic:'<?php echo $this->escape($logic_id); ?>',type:'and',request:'update'});">
										<option><?php echo _('AND'); ?></option>
										<option selected=true><?php echo _('OR'); ?></option>
									</select>
								</td>
							</tr>
						<?php
						}
					}
				}
				else
				{
				?>
				<tr class="r1">
					<td colspan="5">
						<?php echo _('No rules have been added'); ?>
					</td>
				</tr>
				<?php
				}
			?>

			<tr class="alert">
				<td colspan="6" style="padding: 5px;">
					<center>
						<?php
							echo _('<strong>AND</strong>, ADD OR SUBTRACT'
								.' MEMBERS IN OTHER GROUPS');
						?>
						<br />
					</center>
				</td>
			</tr>

			<?php
				if (!empty($this->rules['include'])
						&& is_array($this->rules['include']))
				{
					$cycle = 1;
					foreach ($this->rules['include'] as $field_id => $rule)
					{
						$cycle++;
						if (3 < $cycle)
						{
							$cycle = 1;
						}
						?>
						<tr class="r<?php echo $cycle; ?>">

						<td colspan="2">
							<img src="<?php echo $this->url['theme']['shared'];
									?>images/icons/delete.png"
									alt="<?php echo _('Delete'); ?>"
									onClick="poMMo.callback.updateRule({fieldID:'<?php
									echo $this->escape($field_id);
									?>',logic:'is_in',request:'delete'});" />
							</td>

							<td colspan="4">
							<?php
								echo sprintf(_('%sAdd%s members matching %s'),
										'<strong>', '</strong>', $rule);
							?>
							</td>
						</tr>
						<?php
					}
				}
				else
				{
				?>
					<tr class="r1">
						<td colspan="5">
							<?php echo _('No rules have been added'); ?>
						</td>
					</tr>
				<?php
				}

				if (!empty($this->rules['exclude'])
						&& is_array($this->rules['exclude']))
				{
					$cycle = 1;
					foreach ($this->rules['exclude'] as $field_id => $rule)
					{
						$cycle++;
						if (3 < $cycle)
						{
							$cycle = 1;
						}
						?>
						<tr class="r<?php echo $cycle; ?>">
							<td colspan="2">
								<img src="<?php echo $this->url['theme']['shared'];
										?>images/icons/delete.png"
										alt="<?php echo _('Delete'); ?>"
										onClick="poMMo.callback.updateRule({fieldID:'<?php
										echo $this->escape($field_id);
										?>',logic:'not_in',request:'delete'});" />
							</td>

							<td colspan="4">
							<?php
								echo sprintf(_('%sSubtract%s members matching %s'),
										'<strong>', '</strong>', $rule);
							?>
							</td>
						</tr>
						<?php
					}
				}
			?>
		</table>
	</fieldset>
</form>

<p>
<?php
	echo sprintf(_('%s rules match a total of %s active subscribers'),
			'<em>'.$this->ruleCount.'</em>', '<strong>'.$this->tally.'</strong>');
?>
</p>

<script type="text/javascript">
$().ready(function(){
	// assign ajax + json forms
	poMMo.form.assign();

	// Setup Modal Dialogs
	PommoDialog.init('#dialog',{modal: true});

	$('#addRule select').change(function(){
		var type = this.name, fieldID = $(this).val();
		if($.trim(fieldID) != '')
			$('#dialog')
				.jqm({ajax: 'ajax/group.rpc.php?call=displayRule&ruleType='+type+'&fieldID='+fieldID})
				.jqmShow();
	});

});

poMMo.callback.updateRule = function(p) {
	$('#rules input[name=fieldID]').val(p.fieldID);
	$('#rules input[name=logic]').val(p.logic);
	$('#rules input[name=type]').val(p.type);
	$('#rules input[name=request]').val(p.request);

	poMMo.callback.pause();
	$('#rules').submit();
	return false;
};

poMMo.callback.editRule = function(p) {
	console.log(p.logic);
	$('#dialog')
		.jqm({ajax: 'ajax/group.rpc.php?call=displayRule&ruleType=field&fieldID='+p.fieldID+'&logic='+p.logic+'&type='+p.type})
		.jqmShow();
	return false;
};


</script>

<?php

ob_start();

$this->dialogId = 'dialog';
$this->dialogWide = true;
include $this->template_dir.'/inc/dialog.php';

$this->capturedDialogs = ob_get_clean();

include $this->template_dir.'/inc/admin.footer.php';
