<?php /* @var $this Literally_WordPress */ ?>
<h2 class="nav-tab-wrapper">
	<a href="<?php echo admin_url('admin.php?page=lwp-reward'); ?>" class="nav-tab<?php if(!isset($_GET['tab'])) echo ' nav-tab-active';?>">
		<?php $this->e('Reward Dashboard'); ?>
	</a>
	<a href="<?php echo admin_url('admin.php?page=lwp-reward&tab=history'); ?>" class="nav-tab<?php if(isset($_GET['tab']) && $_GET['tab'] == 'history') echo ' nav-tab-active';?>">
		<?php $this->e('Reward History'); ?>
	</a>
	<a href="<?php echo admin_url('admin.php?page=lwp-reward&tab=request'); ?>" class="nav-tab<?php if(isset($_GET['tab']) && $_GET['tab'] == 'request') echo ' nav-tab-active';?>">
		<?php $this->e('Reward Request'); ?>
	</a>
</h2>

<?php do_action('admin_notice'); ?>

<?php if(!isset($_GET['tab'])): ?>
ダッシュボード

<?php elseif($_GET['tab'] == 'history'): ?>
<p class="description">
	<?php $this->e('Users contribution list is below.'); ?>
</p>

<?php elseif($_GET['tab'] == 'request'): ?>
<p class="description">
	<?php $this->e('User payment requests are below.'); ?>
</p>


<?php endif; ?>
