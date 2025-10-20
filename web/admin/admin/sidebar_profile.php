					<div class="user-panel">
						<div class="pull-left image">
							<img src="<?php echo lps_get_user_avatar() ?>" class="img-circle" alt="User Image">
						</div>
						<div class="pull-left info" style="width: 70%;">
							<div class="pull-right"><a href="<?php echo ADMIN_URL ?>/users/profile.php"><i class="fa fa-gear"></i></a></div>
							<div class="pull-left"><?php echo wps_get_current_user_name() ?></div>
							<div style="clear: both; padding-top: 10px;"><a href="<?php echo ADMIN_URL ?>/logout.php" class="label label-warning">로그아웃</a></div>
						</div>
					</div>
