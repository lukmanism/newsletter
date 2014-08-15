<?php
ob_start();
include $this->template_dir.'/inc/ui.form.php';
include $this->template_dir.'/inc/ui.cssTable.php';
$this->capturedHead = ob_get_clean();

$this->sidebar = false;
include $this->template_dir.'/inc/admin.header.php';
?>

<ul class="inpage_menu">
	<li>
		<a href="subscribers_import.php">
		<?php
			echo sprintf(_('Return to %s'), $this->returnStr);
		?>
		</a>
	</li>
</ul>

<h2><?php echo _('Import Subscribers'); ?></h2>

<form action="" method="post" id="assign">
	<fieldset>
		<legend><?php echo _('Assign Fields'); ?></legend>

		<div>
			<?php echo _('Below is a preview of your CSV data. You can assign'
				.' subscriber fields to columns. At the very least, you must assign'
				.' an email address.'); ?>
		</div>

		<?php
			if ($this->excludeUnsubscribed)
			{
			?>
			<input type="hidden" name="excludeUnsubscribed" value="true" />
			<?php
			}
		?>
		<table summary="<?php echo _('Assign Fields'); ?>">
			<thead>
				<tr>
				<?php
					for ($n = 0; $n < $this->colNum; $n++)
					{
					?>
					<th>
						&nbsp;<select name="f[<?php echo $n; ?>]">
							<option value=""><?php echo _('Ignore Column');
									?></option>
							<option value="">-----------</option>
							<option value="email"><?php echo _('Email');
									?></option>
							<option value="registered"><?php
									echo _('Date Registered'); ?></option>
							<option value="ip"><?php echo _('IP Address');
									?></option>
							<option value="">-----------</option>
							<?php
								foreach ($this->fields as $id => $f)
								{
								?>
									<option value="<?php echo $id; ?>"><?php
											echo $f['name']; ?></option>
								<?php
								}
							?>
						</select>&nbsp;
					</th>
					<?php
					}
				?>
				</tr>
			</thead>

			<tbody>
			<?php
				foreach ($this->preview as $row)
				{
				?>
				<tr>
				<?php
				for ($n = 0; $n < $this->colNum; $n++)
				{
				?>
					<td>
					<?php
						if ($row[$n])
						{
							echo $row[$n];
						}
					?>
					</td>
				<?php
				}
				?>
				</tr>
				<?php
				}
			?>
			</tbody>
		</table>


		<div class="buttons" id="buttons">
		<a href="#" id="import"><button><?php echo _('Import'); ?></button></a>
		</div>
	</fieldset>
</form>

<div id="ajax" class="warn hidden">
	<img src="<?php echo $this->url['theme']['shared']; ?>images/loader.gif"
			alt="Importing..." />... <?php echo _('Processing'); ?>
</div>

<script type="text/javascript">
$().ready(function(){

	// stripe table body rows
	$('table tbody').jqStripe();

	$('#import').click(function() {

		var input = $('#assign').formToArray();
		var c = false;

		for (i in input) {
			if(input[i].value == 'email')
				c = true;
		}

		if(!c) {
			alert('<?php echo _('You must assign an email column!'); ?>');
			return false;
		}

		$('#buttons').hide();

		$('#ajax').show().load('import_csv2.php',input, function() {
			$('#ajax').removeClass('warn').addClass('error');
		});

		return false;
	
	});
});
</script>

<?php

include $this->template_dir.'/inc/admin.footer.php';

