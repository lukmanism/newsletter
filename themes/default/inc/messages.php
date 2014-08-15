<?php

if ($this->messages)
{
?>
	<div id="alertmsg" class="warn">
		<ul>
		<?php
			foreach ($this->messages as $m)
			{
				echo '<li><strong>'.$m.'</strong></li>';
			}
		?>
		</ul>
	</div>
<?php
}

if ($this->errors)
{
?>
	<div id="alertmsg" class="error">
	<?php
		if ($this->fatalMsg)
		{
			echo '<img src="'.$this->url['theme']['shared']
					.'images/icons/alert.png" alt="fatal error icon" />';
		}
	?>
	<ul>
	<?php
		foreach ($this->errors as $e)
		{
			echo '<li>'.$e.'</li>';
		}
	?>
	</ul>

	</div>
<?php
}

