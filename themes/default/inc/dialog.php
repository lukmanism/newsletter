<?php

if (!$this->dialogId)
{
	$this->dialogId = 'dialog';
}

$dialogClass = 'jqmDialog';
if ($this->dialogWide)
{
	$dialogClass .= ' jqmdWide';
}

$internalClass = 'jqmdBC';
if ($this->dialogTall)
{
	$internalClass .= ' jqmdTall';
}
if ($this->dialogShort)
{
	$internalClass .= ' jqmdShort';
}

?>

<div id="<?php echo $this->dialogId; ?>" class="<?php echo $dialogClass; ?>">
	<div class="jqmdTL">
		<div class="jqmdTR">
			<div class="jqmdTC">
				<?php
					if ($this->dialogTitle)
					{
						echo $this->dialogTitle;
					}
					else
					{
						echo 'poMMo';
					}
				?>
			</div>
		</div>
	</div>
	<div class="jqmdBL">
		<div class="jqmdBR">
			<div class="<?php echo $internalClass; ?>">
				<div class="jqmdMSG">
					<?php
						if ($this->dialogContent)
						{
							echo $this->dialogContent;
						}
						else
						{
						?>
						<img src="<?php echo $this->url['theme']['shared'];
								?>images/loader.gif"
								alt="Loading Icon" title="Please Wait" border="0" />
						<?php
							echo _('Please Wait').'...';
						}
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
        //If it is not a wait dialog we add the close X to it.
		if (!$this->dialogWait || isset($this->dialogWait))
		{
		?>
		<input type="image" src="<?php echo $this->url['theme']['shared'];
				?>images/dialog/close.gif"
				class="jqmdX jqmClose" />	
		<?php
		}
	?>
</div>
