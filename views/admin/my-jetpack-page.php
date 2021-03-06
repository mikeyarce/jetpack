<div class="clouds-sm"></div>

<div class="page-content landing">
	<?php Jetpack::init()->load_view( 'admin/network-activated-notice.php' ); ?>

	<?php do_action( 'jetpack_notices' ) ?>

	<div id="my-jetpack-page-template"></div>

	<script id="tmpl-connection-page" type="text/html">
		<div class="content-container">
			<div id="my-jetpack-content" class="content">
				<h2><?php _e( 'My Jetpack', 'jetpack' ); ?></h2>

				<?php
				/*
				 * 3-column row shown to non-masters
				 */
				?>
				<# if ( ! data.currentUser.isMasterUser || ( ! data.currentUser.isMasterUser && data.masterUser ) ) { #>
					<div class="connection-details local-user j-row">
						<?php // left col ?>
						<div class="j-col j-lrg-4 j-md-6 j-sm-12 jp-user">
							<h3 title="<?php esc_attr_e( 'Username', 'jetpack' ); ?>"><?php _e( 'Site Username', 'jetpack' ); ?></h3>
							<div class="user-01">
								{{{ data.currentUser.gravatar }}} {{{ data.currentUser.adminUsername }}}
							</div>
						</div>

						<?php // middle col ?>
						<div class="j-col j-lrg-4 j-md-6 j-sm-12 wp-user">
							<h3 title="<?php esc_attr_e( 'WordPress.com Username', 'jetpack' ); ?>"><?php _e( 'WordPress.com Username', 'jetpack' ); ?></h3>
							<div class="wpuser-02">
								<# if ( data.currentUser.isUserConnected ) { #>
									{{{ data.currentUser.userComData.login }}}
								<# } else { #>
									<span><em><?php _e( 'Not connected', 'jetpack' ); ?></em></span>
								<# } #>
							</div> 
						</div>

						<?php // right col ( Link/Unlink my account ) ?>
						<div class="j-col j-lrg-4 j-md-12 j-sm-12 wp-action">
							<?php //@todo h3 tags here for styling purposes ?>
							<h3>&nbsp</h3>
							<div class="action-btns">
								<# if ( data.currentUser.isUserConnected ) { #>
									<a class="button" title="<?php esc_attr_e( 'Unlink your account from WordPress.com', 'jetpack' ); ?>" href="<?php echo wp_nonce_url( Jetpack::admin_url( 'action=unlink&redirect=my_jetpack' ), 'jetpack-unlink' ); ?>"><?php esc_html_e( 'Unlink my account ', 'jetpack' ); ?></a>
								<# } else { #>
									<a class="button button-primary" title="<?php esc_attr_e( 'Link your account to WordPress.com', 'jetpack' ); ?>" href="<?php echo Jetpack::init()->build_connect_url( false, Jetpack::admin_url( array( 'page' => 'my_jetpack' ) ) ); ?>" ><?php esc_html_e( 'Link your account', 'jetpack' ); ?></a>
								<# } #>
							</div>
						</div>
					</div>
				<# } #>


				<?php
				/*
				 * 2-column row shown to master users.
				 */
				?>
				<# if ( data.currentUser.isMasterUser ) { #>
					<div class="connection-details master-user j-row">
						<?php // Left Col ?>
						<div class="j-col j-lrg-6 j-md-6 j-sm-12 jp-user">
							<h3 title="<?php esc_attr_e( 'Primary User of the site', 'jetpack' ); ?>"><?php _e( 'Site Username (Primary)', 'jetpack' ); ?></h3>
							<div class="user-01">
								{{{ data.currentUser.gravatar }}} {{{ data.currentUser.adminUsername }}}
							</div>
						</div>

						<?php // Right Col ?>
						<div class="j-col j-lrg-6 j-md-6 j-sm-12 wp-user">
							<h3 title="<?php esc_attr_e( 'WordPress.com Username', 'jetpack' ); ?>"><?php _e( 'WordPress.com Username', 'jetpack' ); ?></h3>
							<div class="wpuser-02">
								<span>{{{ data.currentUser.userComData.login }}}</span>
							</div> 
						</div>
					</div>
				<# } #>

			</div><?php // my-jetpack-content ?>

			<?php
			/*
			 * User actions, only shown to admins
			 *
			 * Disconnect site, or change primary user
			 */
			?>
			<?php if ( current_user_can( 'jetpack_configure_modules' ) ) : ?>
				<div class="j-row my-jetpack-actions">
					<div class="j-col j-lrg-6 j-md-6 j-sm-12">
						<p><strong><?php _e( 'Jetpack Primary User', 'jetpack' ); ?></strong></p>
						<form action="" method="post">
							<select name="jetpack-new-master" id="user-list">
								<?php
								$all_users = get_users();
								$primary_text = __( '(primary)', 'jetpack' );

								foreach ( $all_users as $user ) {
									if ( Jetpack::is_user_connected( $user->ID ) && $user->caps['administrator'] ) {
										if ( $user->ID == Jetpack_Options::get_option( 'master_user' ) ) {
											$master_user_option = "<option selected value='{$user->ID}'>$user->user_login $primary_text</option>";
										} else {
											$user_options .= "<option value='{$user->ID}'>$user->user_login</option>";
										}
									}
								}
								// Show master first
								echo $master_user_option;

								// Show the rest of the linked admins
								$user_options = ! empty( $user_options ) ? $user_options : printf( __( '%sConnect more admins%s', 'jetpack' ), "<option disabled='disabled'>", "</option>" );
								echo $user_options;
								?>
							</select>
							<?php wp_nonce_field( 'jetpack_change_primary_user', '_my_jetpack_nonce' ); ?>
							<input type="submit" name="jetpack-set-master-user" id="save-primary-btn" class="button button-primary" value="Save" title="<?php esc_attr_e( 'Set the primary account holder', 'jetpack' ); ?>"/>
						</form>
					</div>
					<div class="j-col j-lrg-6 j-md-6 j-sm-12">
						<p><strong><?php _e( 'Disconnect Jetpack', 'jetpack' ); ?></strong></p>
						<a class="button" id="jetpack-disconnect" href="#"><?php esc_html_e( 'Disconnect site from WordPress.com', 'jetpack' ); ?></a>
					</div>
				</div>

				<div id="jetpack-disconnect-content">
					<div class="j-row">
						<div class="j-col j-lrg-10 j-md-10 j-sm-10">
							<h2><?php _e( 'Disconnecting Jetpack', 'jetpack' ); ?></h2>
							<p><?php _e( 'Before you completely disconnect Jetpack is there anything we can do to help?', 'jetpack' ); ?></p>
							<a class="button" id="confirm-disconnect" title="<?php esc_attr_e( 'Disconnect Jetpack', 'jetpack' ); ?>" href="<?php echo wp_nonce_url( Jetpack::admin_url( 'action=disconnect' ), 'jetpack-disconnect' ); ?>">Confirm Disconnect</a>
							<a class="button primary" id="support-no-disconnect" target="_blank" title="<?php esc_attr_e( 'Jetpack Support', 'jetpack' ); ?>" href="http://jetpack.me/contact-support/"><?php esc_html_e( 'I Need Support', 'jetpack' ); ?></a>
						</div>
						<div class="j-col j-lrg-2 j-md-2 j-sm-2">
							<a class="alignright" id="cancel-disconnect" target="_blank" title="<?php esc_attr_e( 'cancel', 'jetpack' ); ?>" href="#"><?php esc_html_e( 'cancel', 'jetpack' ); ?></a>
						</div>
					</div>
				</div>
			<?php endif;?>
		</div><?php // div.content-container ?>
	</script>
</div><?php // div.page-content ?>
