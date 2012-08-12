<?php /* @var $this Literally_WordPress */  ?>
<?php
$is_detail = isset($_GET["transaction_id"]) && is_numeric($_REQUEST["transaction_id"]) && ($transaction = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->transaction} WHERE ID = %d", $_REQUEST["transaction_id"])));
?>
<h2 class="nav-tab-wrapper">
	<a href="<?php echo admin_url('admin.php?page=lwp-management'); ?>" class="nav-tab<?php if(!$is_detail && !isset($_GET['view'])) echo ' nav-tab-active';?>">
		<?php $this->e('Transaction Dashboard'); ?>
	</a>
	<a href="<?php echo admin_url('admin.php?page=lwp-management&view=list'); ?>" class="nav-tab<?php if(isset($_GET['view']) && $_GET['view'] == 'list') echo ' nav-tab-active';?>">
		<?php $this->e('List'); ?>
	</a>
	<?php if($is_detail): ?>
	<a href="<?php echo admin_url('admin.php?page=lwp-management&transactin_id='.intval($_REQUEST['transaction_id'])); ?>" class="nav-tab<?php if($is_detail) echo ' nav-tab-active';?>">
		<?php printf($this->_('Detail No.%d'), $_REQUEST['transaction_id']); ?>
	</a>
	<?php endif; ?>
</h2>


<?php
/*---------------------------------
 * 個別表示
 */
if($is_detail):
		$book = wp_get_single_post($transaction->book_id);
		$user = get_userdata($transaction->user_id);
?>
<h3><?php $this->e('Transaction Detail'); ?></h3>
<table class="form-table detail-table">
	<thead>
		<tr>
			<th scope="row"><?php $this->e('Heading');?></th>
			<th scope="row"><?php $this->e('Value');?></th>
			<th scope="row"><?php $this->e('Action');?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th scope="row" valign="top"><?php $this->e('Item Name'); ?></th>
			<td><?php echo $book->post_title; ?></td>
			<td><a class="button" href="<?php echo admin_url("post.php?action=edit&post_type={$book->post_type}&post={$book->ID}"); ?>"><?php $this->e('Edit'); ?></a></td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php $this->e('User Name'); ?></th>
			<?php if($user): ?>
				<td><?php echo $user->display_name;  ?></td>
				<td><a class="button" href="<?php echo admin_url("user-edit.php?user_id={$user->ID}");?>"><?php $this->e('Profile'); ?></a></td>
			<?php else: ?>
				<td><?php $this->e('Deleted User'); ?></td>
				<td>---</td>
			<?php endif; ?>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php $this->e('Updated'); ?></th>
			<td>
				<?php echo mysql2date(get_option('date_format'), $transaction->updated); ?>
				<small>（<?php $this->e('Registered'); ?>: <?php echo mysql2date(get_option('date_format'), $transaction->registered); ?>）</small>
			</td>
			<td>---</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php $this->e("Purchased Price"); ?></th>
			<td>
				<strong><?php echo number_format($transaction->price)." ({$this->option['currency_code']})"; ?></strong>
				<p class="description"><?php $this->e('Original Price'); ?>: <?php echo number_format( lwp_original_price($book->ID))." ({$this->option['currency_code']})";?></p>
			</td>
			<td>---</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php $this->e('Quantity'); ?></th>
			<td><?php echo number_format_i18n($transaction->num); ?></td>
			<td>---</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php $this->e('Consumed'); ?></th>
			<td><?php echo number_format_i18n($transaction->consumed); ?></td>
			<td>---</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php $this->e('Purchase Method'); ?></th>
			<td>
				<?php $this->e($transaction->method); ?>
				<?php if($transaction->method == LWP_Payment_Methods::PAYPAL): ?>
				<br /><small><?php printf($this->_('Transaction ID: %s'), $transaction->transaction_id); ?></small>
				<?php endif; ?>
			</td>
			<td>---</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php $this->e('Invoice Num'); ?></th>
			<td><?php echo esc_html($transaction->transaction_key); ?></td>
			<td>---</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php $this->e("Status"); ?></th>
			<td>
				<?php $this->e($transaction->status);?>
			</td>
			<td>
				<form method="post">
					<?php wp_nonce_field('lwp_update_transaction'); ?>
					<input type="hidden" name="transaction_id" value="<?php echo $transaction->ID; ?>" />
					<p>
					<select name="status">
						<?php foreach(LWP_Payment_Status::get_all_status() as $s): ?>
						<?php if($s == LWP_Payment_Status::REFUND): ?>
							<?php if($transaction->status == LWP_Payment_Status::SUCCESS): ?>
								<?php $disabled = ($transaction->method == LWP_Payment_Methods::PAYPAL && 60 < ceil((time() - strtotime($transaction->updated)) / 60 / 60 / 24 )) ? ' disabled="disabled"' : '';  ?>
								<option value="<?php echo $s; ?>"<?php echo $disabled; ?>><?php $this->e($s); ?></option>
							<?php elseif($transaction->status == LWP_Payment_Status::REFUND): ?>
								<option value="<?php echo $s; ?>" checked="checked"><?php $this->e($s);?></option>
							<?php endif; ?>
						<?php else: ?>
							<option value="<?php echo $s; ?>"<?php if($s == $transaction->status) echo ' selected="selected"'; ?>><?php $this->e($s);?></option>
						<?php endif; ?>
						<?php endforeach; ?>
					</select>
					<?php submit_button($this->_('Update Status'), 'primary', 'update_transaction', false); ?>
					</p>
					<?php if($transaction->method == LWP_Payment_Methods::PAYPAL): ?>
					<p class="description"><?php $this->e('<strong>Note:</strong> PayPal accepts refund by 60 days.'); ?></p>
					<?php endif; ?>
				</form>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php $this->e("Expires"); ?></th>
			<td>
				<?php if($transaction->status == LWP_Payment_Status::SUCCESS): ?>
					<?php if($transaction->expires == '0000-00-00 00:00:00'): ?>
						<?php $this->e('No Limit.'); ?>
					<?php else:?>
						<strong><?php echo (strtotime($transaction->expires) < time()) ? $this->_('Expired'): $this->_('Valid');?></strong>
						<span class="description">(<?php echo mysql2date(get_option('date_format'), $transaction->expires); ?>)</span>
					<?php endif; ?>
				<?php else: ?>
					<?php $this->e('Not valid.'); ?>
				<?php endif; ?>
			</td>
			<td>
				<?php if($transaction->status == LWP_Payment_Status::SUCCESS || $transaction->expires != '0000-00-00 00:00:00'): ?>
				<form method="post">
					<?php wp_nonce_field('lwp_update_transaction'); ?>
					<input type="hidden" name="transaction_id" value="<?php echo $transaction->ID; ?>" />
					<p>
						<input class="date-picker" type="text" name="expires" value="<?php echo $transaction->expires; ?>" />
						<?php submit_button($this->_('Update Expiration'), 'primary', 'update_expires', false); ?>
					</p>
				</form>
				<?php else: ?>
				---
				<?php endif; ?>
			</td>
		</tr>
	</tbody>
</table>
<p>
	<a class="button" href="<?php echo admin_url('admin.php?page=lwp-management'); ?>">&laquo;<?php $this->e('Return to transaction list');?></a>
</p>


<?php
/*---------------------------------
 *  一覧表示
 */
elseif(isset($_GET['view']) && $_GET['view'] == 'list'):
?>
<iframe src="" name="lwp-csv-output" height="0" width="100%"></iframe>
<form id="lwp-csv-output-form" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>" target="lwp-csv-output">
	<input type="hidden" name="action" value="lwp_transaction_csv_output" />
	<?php wp_nonce_field('lwp_transaction_csv_output');?>
	<input type="hidden" name="status" value="" />
	<input type="hidden" name="post_type" value="" />
	<input type="hidden" name="from" value="" />
	<input type="hidden" name="to" value="" />
	<p class="description">
		<?php $this->e('You can get transaction list in status below.'); ?>
		<input type="submit" class="button-primary" value="<?php $this->e('Get CSV'); ?>" /><br />
	</p>
</form>

<form method="get" action="<?php echo admin_url('admin.php'); ?>">
	<input type="hidden" name="page" value="lwp-management" />
<?php
require_once $this->dir.DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."list-management.php";
$list_table = new LWP_List_Management();
$list_table->prepare_items();
do_action('admin_notice');
$list_table->search_box(__('Search'), 's');
$list_table->display();

?>
</form>

<?php
/*---------------------------------
 * ダッシュボード
 */
else: ?>




<?php
/*---------------------------------
 * 分岐終了
 */
endif;