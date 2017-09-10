<div id="page_user_settings" data-apply-page-scope="logged_in" class="container" style="display: none">
	<div class="jumbotron">
		<div class="row">
			<div class="col-xs-12">
				<h2 class="text-center">User Settings</h2>
				<p>Your account needs to be secure! Change your password, add two-step-verifications or change your
					email.</p>
			</div>
		</div>
		<div class="row row-margin">
			<div class="col-xs-12">
				<h3>Login</h3>
				<h4>Password</h4>
				<div>
					<div id="page_user_settings_alert_change_password_disabled" class="alert alert-info"
					     style="display: none">
						Changing password is disabled! Please disable two factor authentication first.
					</div>
					<form id="page_user_settings_form_change_password" class="clearfix" action="action.php"
					      method="post">
						<input type="hidden" name="a" value="user/changePassword">
						<div class="text">
							<input type="password" class="form-control" title="Current Password" name="password"
							       required
							       autocomplete="off"/>
							<label>Current Password</label>
						</div>
						<div class="text">
							<input type="password" class="form-control" title="New Password" name="new_password"
							       required
							       autocomplete="off"/>
							<label>New Password</label>
						</div>
						<div class="text">
							<input type="password" class="form-control" title="New Password (again)"
							       name="new_password2" required autocomplete="off"/>
							<label>New Password (again)</label>
						</div>
						<p>This process could take a while, because all your passwords have to be decrypted and
							encrypted
							again!</p>
						<button type="submit" class="btn btn-primary pull-right">Apply</button>
					</form>
				</div>
				<div>
					<h4>Two-Factor-Authentication</h4>
					<p>With two-factor-authentication (often shortened by 2FA) you can add an extra layer of security to
						your account. After you login, PASSY will ask for a 6-digit 2FA code, which will be
						generated by a secondary app (likely on your phone).</p>
					<p>
						Status: <span id="text_2fa_status"></span>
					</p>
					<button id="btn2faSetupModalToggle" class="btn btn-primary" data-toggle="modal"
					        data-target="#page_user_settings_modal_2fa_setup">Enable
					</button>
					<button data-toggle="modal" data-target="#page_user_settings_modal_2fa_disable" id="btn2faDisableModalToggle" class="btn btn-danger">Disable</button>
				</div>
			</div>
			<div class="col-xs-12">
				<h3>Username</h3>
				<form id="page_user_settings_form_change_username" class="clearfix" action="action.php" method="post">
					<input type="hidden" name="a" value="user/changeUsername">
					<div class="text">
						<input type="password" class="form-control" title="Current Password" name="password"
						       required
						       autocomplete="off"/>
						<label>Current Password</label>
					</div>
					<div class="text">
						<input type="text" class="form-control" title="New username" name="new_username" required
						       autocomplete="off"/>
						<label>New username</label>
					</div>
					<button type="submit" class="btn btn-primary pull-right">Apply</button>
				</form>
			</div>
			<div class="col-xs-12">
				<h3>Export</h3>
				<p>This will put all your passwords into a file, so you can import them later in any PASSY server.</p>
				<form id="page_user_settings_form_export" class="clearfix" action="action.php" method="post">
					<input type="hidden" name="a" value="misc/export">
					<select name="format">
						<option value="passy">PASSY</option>
						<option value="keepass" disabled>KeePass (not implemented)</option>
						<option value="csv">CSV</option>
						<option value="text" disabled>Plaintext</option>
					</select>
					<br/>
					<input type="checkbox" title="Export with Password" name="with-pass">
					<label>Export with Password</label>
					<div class="text">
						<input type="password" class="form-control" title="Password, by default you master password"
						       name="pass"
						       autocomplete="off"/>
						<label>Password, by default you Master Password</label>
					</div>
					<p>This process could take a while, because all your passwords have to be decrypted!</p>
					<button type="submit" class="btn btn-primary pull-right">Export</button>
				</form>

			</div>
			<div class="col-xs-12">
				<h3>Import</h3>
				<p>Import your previously exported passwords.</p>
				<form id="page_user_settings_form_import" class="clearfix" action="action.php"
				      enctype="multipart/form-data"
				      method="post">
					<input type="hidden" name="a" value="misc/import">
					<input type="file" id="import-file" name="parse-file">
					<br/>
					<input type="checkbox" title="Import with Password" name="with-pass">
					<label>Import with Password</label>
					<div class="text">
						<input type="password" class="form-control" title="Password, by default you master password"
						       name="pass"
						       autocomplete="off"/>
						<label>Password, by default you Master Password</label>
					</div>
					<button type="submit" class="btn btn-primary pull-right">Import</button>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="page_user_settings_modal_2fa_setup" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content depth-5">
			<div class="modal-header">
				<h4 class="modal-title">Two-Factor-Authentication Setup</h4>
			</div>
			<div class="modal-body">
				<div>
					<ul class="nav nav-tabs hidden" id="tab_2fa" role="tablist">
						<li role="presentation" class="active">
							<a href="#tab_2fa_step_1" aria-controls="tab_2fa_step_1" role="tab" data-toggle="tab">
								Step 1
							</a>
						</li>
						<li role="presentation">
							<a href="#tab_2fa_step_2" aria-controls="tab_2fa_step_2" role="tab" data-toggle="tab">
								Step 2
							</a>
						</li>
						<li role="presentation">
							<a href="#tab_2fa_step_3" data-hide="#btn_2fa_next" data-show="#btn_2fa_enable_submit"
							   aria-controls="tab_2fa_step_3" role="tab" data-toggle="tab">
								Step 3
							</a>
						</li>
						<li role="presentation">
							<a href="#tab_2fa_finish" data-hide="#btn_2fa_enable_submit" data-show="#btn_2fa_finish"
							   aria-controls="tab_2fa_finish" role="tab" data-toggle="tab">
								Finish
							</a>
						</li>
					</ul>
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane fade in active" id="tab_2fa_step_1">
							<p>
								First of all you will need a client program, which will generate the 2FA codes for
								you. <br>There are several options available:
							</p>
							<ul>
								<li>
									Google Authenticator (Mobile) &middot;
									<a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2"
									   target="_blank">Google Play Store</a>,
									<a href="https://itunes.apple.com/app/google-authenticator/id388497605"
									   target="_blank">Apple AppStore</a>
								</li>
								<li>Authy (Mobile, Desktop, Browser) &middot;
									<a href="https://play.google.com/store/apps/details?id=com.authy.authy"
									   target="_blank">Google
										Play Store</a>,
									<a href="https://itunes.apple.com/app/authy/id494168017" target="_blank">Apple
										AppStore</a>,
									<a href="https://authy.com/download/" target="_blank">Desktop / Browser</a>
								</li>
								<li>Thenticate (Mobile) &middot;
									<a href="https://play.google.com/store/apps/details?id=eu.overmorrow.thenticate"
									   target="_blank">Google Play Store</a>
								</li>
							</ul>
							<p>
								After you selected an option click <i>Next</i>.
							</p>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="tab_2fa_step_2">
							<p>
								Scan the following QR code with your desired 2FA app. If you can't scan it use the code
								below.
							</p>
							<img id="img_2fa_key" src="" class="img-responsive margin-center">
							<pre id="pre_2fa_key" class="selectable no-contextmenu text-center"></pre>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="tab_2fa_step_3">
							<form id="page_user_settings_form_2fa_setup" action="action.php" method="post"
							      class="clearfix">
								<input type="hidden" name="a" value="user/2faEnable">
								<p>After your app has started to generate codes, enter the code that is currently shown,
									below</p>
								<div class="form-group">
									<div class="text">
										<input type="text" class="form-control" title="Private Key" name="2faPrivateKey"
										       autocomplete="off" readonly/>
										<label>Private Key</label>
									</div>

									<div class="text">
										<input type="text" class="form-control" title="Code" name="2faCode"
										       autocomplete="off"/>
										<label>Code</label>
									</div>
								</div>
							</form>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="tab_2fa_finish">
							<p>
								<span class="text-success">Congratulations!</span> Two-Factor-Authentication has been
								enabled! <br>
								The following text is your back-up code, which you can use to disable 2FA for your
								account, if you can't use your 2FA-generator for some reason.
							</p>
							<pre id="pre_2fa_key2" class="selectable no-contextmenu text-center"></pre>
						</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btn_2fa_cancel" class="btn btn-flat btn-danger" data-dismiss="modal">Cancel
				</button>
				<button type="submit" id="btn_2fa_next" data-target="#tab_2fa" data-next="tab"
				        class="btn btn-flat btn-primary">Next
				</button>
				<button type="submit" id="btn_2fa_enable_submit" data-submit="#page_user_settings_form_2fa_setup" class="btn btn-flat btn-primary">Submit
				</button>
				<button type="submit" id="btn_2fa_finish" class="btn btn-flat btn-primary" data-dismiss="modal">Finish
				</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="page_user_settings_modal_2fa_disable" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content depth-5">
			<div class="modal-header">
				<h4 class="modal-title">Two-Factor-Authentication</h4>
			</div>
			<div class="modal-body">
				<form method="post" action="action.php" id="page_user_settings_form_2fa_disable" autocomplete="off">
					<input type="hidden" name="a" value="user/2faDisable" readonly style="display: none"/>
					<p>
						To disable two factor authentication, please enter you generated 6-digit code below.
						<br>
						You may enter your recovery code, if you can't use your generator.
					</p>
					<div class="form-group clearfix">
						<div class="text">
							<input type="text" class="form-control" title="Authentication Code" name="2faCode" autocomplete="off"/>
							<label>Authentication Code</label>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-flat btn-primary" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-flat btn-danger" data-submit="#page_user_settings_form_2fa_disable">Disable</button>
			</div>
		</div>
	</div>
</div>