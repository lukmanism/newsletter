<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="json">
    <div>
        <label for="bounces_address">
            <strong class="required"><?php echo _('Bounce Address:'); ?></strong>
        </label>
        <input value="<?php echo $this->escape($this->bounces_address); ?>"
                type="text" name="bounces_address" id="bounces_address"/>
        <span class="notes">
            <?php echo _('(Returned emails will be sent to this address)'); ?>
        </span>
    </div>

    <div>
        <label for="bounces_server">
            <strong class="required"><?php echo _('Server:'); ?></strong>
        </label>
        <input value="<?php echo $this->escape($this->bounces_server); ?>"
                type="text" name="bounces_server" id="bounces_server"/>
        <span class="notes">
            <?php echo _('(POP3 server for bounce emails)'); ?>
        </span>
    </div>

    <div>
        <label for="bounces_port">
            <strong class="required"><?php echo _('Port:'); ?></strong>
        </label>
        <input value="<?php echo $this->escape($this->bounces_port); ?>"
                type="text" name="bounces_port" id="bounces_port"/>
        <span class="notes">
            <?php echo _('(POP3 port for bounce emails)'); ?>
        </span>
    </div>

    <div>
        <label for="bounces_user">
            <strong class="required"><?php echo _('User:'); ?></strong>
        </label>
        <input value="<?php echo $this->escape($this->bounces_user); ?>"
                type="text" name="bounces_user" id="bounces_user"/>
        <span class="notes">
            <?php echo _('(POP3 user for bounce address)'); ?>
        </span>
    </div>

    <div>
        <label for="bounces_password">
            <strong class="required"><?php echo _('Password:'); ?></strong>
        </label>
        <input value="<?php echo $this->escape($this->bounces_password); ?>"
                type="text" name="bounces_password"/>
        <span class="notes">
            <?php echo _('(POP3 password for bounce address)'); ?>
        </span>
    </div>

    <input type="submit" value="<?php echo _('Save'); ?>" />
</form>
