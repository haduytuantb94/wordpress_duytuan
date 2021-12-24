<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

$settings	=	$doing_data['settings'];

$max_round 	=	$doing_data['adaptive_max_round'];

?>

<div id="qm-page-doing" class="reset-vuetify doing-stage" data-pages="<?php echo count($doing_data['questions']); ?>">
	
	<v-app>
	<?php do_action('quizmaker_before_doing', $doing_data); ?>

	<?php if(isset($settings['is_sidebar_tracking']) && !$settings['is_sidebar_tracking']){ ?>
	<div class="stage-timer"></div>
	<?php } ?>

	<div class="doing-stage-adaptive-info">
		<div id="adaptive_max_round">
			<div id="adaptive_max_round_container"></div>
			<h2 id="adaptive_max_round_footer"><?php _e('Round:', 'quizmaker'); ?> <?php echo $doing_data['adaptive_round']; ?><?php echo $max_round > 0 ? '/' . $max_round : ''; ?></h2>
		</div>

		<div id="adaptive_percent">
			<div id="adaptive_percent_container"></div>
			<h2 id="adaptive_percent_footer"><?php _e('Required Correct:', 'quizmaker'); ?> <?php echo $doing_data['adaptive_times']; ?></h2>
		</div>
	</div>

	<form name="quizmaker_doing_test" action="<?php echo esc_url(qm_get_result_test_url()); ?>" method="post" enctype="multipart/form-data">

		<div class="stage-questions"></div>

		<?php if($doing_data['questions']): ?>
		<div class="quizmaker-direction" style="<?php echo !$settings['is_pagination'] ? 'display:none;' : ''; ?>">
			<div v-show="false" class="stage-navigation">

				<?php if($settings['is_backward']): ?>
				<a href="#" class="qm_prev_page"><span class="ion-android-arrow-back"></span>Prev</a>
				<?php endif; ?>
				<a href="#" class="qm_next_page"><span class="ion-android-arrow-forward"></span>Next</a>
				
			</div>
			
			<div class="stage-pagination">
				<ul>
				<?php foreach($doing_data['questions'] as $index => $q): ?>
					<li data-index="<?php echo $index; ?>"><span><?php echo $index + 1; ?></span></li>
				<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<?php endif; ?>
		
		<button type="submit" name="quizmaker_submit_test" value="1" class="qm-btn-submit-test"><?php _e('SUBMIT', 'quizmaker'); ?></button>

	</form>

	<?php do_action('quizmaker_after_doing', $doing_data ); ?>
	</v-app>
</div>