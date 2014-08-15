<form action="#" method="post" id='addUserForm'>

	<fieldset>
		<legend><?php echo _('Add User'); ?></legend>
		
		<div class="output alert">
			<?php echo _('User and password must be at least 5 characters'); ?>
		</div>

		<div>
			<label for="user">
				<strong class="required"><?php echo _('User:'); ?></strong>
			</label>
			<input type="text" size="32" maxlength="60"
					name="user" class='vfield' />
		</div>
		<div>
			<label for="password">
				<strong class="required"><?php echo _('Password:'); ?></strong>
			</label>
			<input type="password" size="32" maxlength="60" name="password"
					class='vfield'/>
		</div>
		<div>
			<label for="password2">
				<strong class="required"><?php echo _('Password:'); ?></strong>
			</label>
			<input type="password" size="32" maxlength="60" name="password2"
					class='vfield' />
		</div>
	</fieldset>

	<div class="buttons">
		<input id='addUserSubmit' disabled='disabled' type="submit"
				value="<?php echo _('Add User'); ?>" style='opacity: 0.5;' />
		<input type="reset" value="<?php echo _('Reset'); ?>" />
	</div>

	<p>
        <?php
        echo sprintf(_('Fields marked like %sthis%s are required.'),
            '<span class="required">', '</span>');
        ?>        
</form>

<script type="text/javascript">
$(function()
{
	$('.vfield', '#addUserForm').keyup(function()
	{
		//	Validate to activate submit button
		user 		= $('[name=user]').val();
		password	= $('[name=password]').val();
		password2	= $('[name=password2]').val();
		
		if (5 > user.length)
		{
			$('#addUserSubmit').attr('disabled', 'disabled').css('opacity', '0.5');
			return false;
		}
		
		if (5 > password.length)
		{
			$('#addUserSubmit').attr('disabled', 'disabled').css('opacity', '0.5');
			return false;
		}
		
		if (password != password2)
		{
			$('#addUserSubmit').attr('disabled', 'disabled').css('opacity', '0.5');
			return false;
		}
		
		$('#addUserSubmit').removeAttr('disabled').css('opacity', '1');
		return true;
	});
	
	$('#addUserSubmit').click(function()
	{
		$.ajax({
			'type':	'POST',
			'url':	'ajax/users.rpc.php',
			'data':
			{
				'call':		'add',
				'user':		user,
				'password':	password
			},
			'success': function(data)
			{
				poMMo.callback.addUser(data);
			}
		});
		return false;
	});

	poMMo.callback.addUser = function(user)
	{
		// refresh the page if no grid exists, else add new subscriber to grid
		if($('#grid').size() == 0)
		{
			history.go(0);
		}
        else
        {
        	poMMo.grid.addRow(user, { 'username':user });
        	$('#addUser').jqmHide();
        }
	};
});
</script>
