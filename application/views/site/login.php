<div class="container">
	<div class="row">
		<div class="span12">
			<ul class="breadcrumb">
				<li>
					<i class="icon-off"></i> <?php echo $this->lang->line('please_login'); ?>
				</li>
			</ul>
		</div>
	</div>

	<?php if ($this->session->flashdata('error')) : ?>
		<div class="row">
			<div class="span4">
				<div class="alert alert-error">
					<?php echo $this->session->flashdata('error'); ?>
			    </div>
			</div>
		</div>
	<?php endif; ?>

	<div class="row" >
		<div class="span12">
			<form action="" method="post">
				<div class="control-group<?php echo form_error('form[username]') ? ' error' : ''; ?>">
					<!-- <label><?php echo $this->lang->line('username'); ?>:</label> -->
					<input name="form[username]" type="text" placeholder="Username" class="span4" maxlength="30" value="<?php echo set_value('form[username]'); ?>"/>
					<span class="help-inline"><?php echo form_error('form[username]'); ?></span>
				</div>

				<div class="control-group<?php echo form_error('form[password]') ? ' error' : ''; ?>">
					<!-- <label><?php echo $this->lang->line('password'); ?>:</label> -->
					<input name="form[password]" type="password"  placeholder="Password" class="span4" maxlength="30"/>
					<span class="help-inline"><?php echo form_error('form[password]'); ?></span>
				</div>
				<div class="form-actions">	
					<button type="submit" class="btn btn-danger span3"><i class="icon-user icon-white"></i> <?php echo $this->lang->line('login'); ?></button>
					<button type="button" class="btn btn-success span3" onclick="window.location.href='http://www.tjhsst.edu/studentlife/activ/ict/login/newuser.php'"><i class="icon-user icon-white"></i> <?php echo $this->lang->line('register'); ?></button>
				</div>
			</form>
		</div>
	</div>
</div>
