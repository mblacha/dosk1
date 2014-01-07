<?php
$hasheading = ($PAGE->heading);
$hasnavbar = (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar() && has_capability('moodle/site:config', context_system::instance()));
$hasfooter = (empty($PAGE->layout_options['nofooter']));
$hassidepre = (empty($PAGE->layout_options['noblocks']) && $PAGE->blocks->region_has_content('side-pre', $OUTPUT));
$hassidepost = (empty($PAGE->layout_options['noblocks']) && $PAGE->blocks->region_has_content('side-post', $OUTPUT));
$haslogininfo = (empty($PAGE->layout_options['nologininfo']));
$hassidetop1 = $PAGE->blocks->region_has_content('top-1', $OUTPUT);
$hassidetop2 = $PAGE->blocks->region_has_content('top-2', $OUTPUT);
$hassidetop3 = $PAGE->blocks->region_has_content('top-3', $OUTPUT);
$hassidebot1 = $PAGE->blocks->region_has_content('bot-1', $OUTPUT);
$hassidebot2 = $PAGE->blocks->region_has_content('bot-2', $OUTPUT);
$hassidebot3 = $PAGE->blocks->region_has_content('bot-3', $OUTPUT);
$top_blocks = 0;
if($hassidetop1){
	$top_blocks++;
	if($hassidetop2){
		$top_blocks++;
		if($hassidetop3){
			$top_blocks++;
		}
	}
}

switch ($top_blocks){
case 1: $top_width = "980px"; break;
case 2: $top_width = "480px"; break;
case 3: $top_width = "320px"; break;
default: break;}

$bot_blocks = 0;
if($hassidebot1){
	$bot_blocks++;
	if($hassidebot2){
		$bot_blocks++;
		if($hassidebot3){
			$bot_blocks++;
		}
	}
}
switch ($bot_blocks){
case 1: $bot_width = "980px"; break;
case 2: $bot_width = "480px"; break;
case 3: $bot_width = "320px"; break;
default: break;}

$showsidepre = ($hassidepre && !$PAGE->blocks->region_completely_docked('side-pre', $OUTPUT));
$showsidepost = ($hassidepost && !$PAGE->blocks->region_completely_docked('side-post', $OUTPUT));

$custommenu = $OUTPUT->custom_menu();
$hascustommenu = (empty($PAGE->layout_options['nocustommenu']) && !empty($custommenu));

$courseheader = $coursecontentheader = $coursecontentfooter = $coursefooter = '';
if (empty($PAGE->layout_options['nocourseheaderfooter'])) {
    $courseheader = $OUTPUT->course_header();
    $coursecontentheader = $OUTPUT->course_content_header();
    if (empty($PAGE->layout_options['nocoursefooter'])) {
        $coursecontentfooter = $OUTPUT->course_content_footer();
        $coursefooter = $OUTPUT->course_footer();
    }
}

$bodyclasses = array();
if ($showsidepre && !$showsidepost) {
    if (!right_to_left()) {
        $bodyclasses[] = 'side-pre-only';
    }else{
        $bodyclasses[] = 'side-post-only';
    }
} else if ($showsidepost && !$showsidepre) {
    if (!right_to_left()) {
        $bodyclasses[] = 'side-post-only';
    }else{
        $bodyclasses[] = 'side-pre-only';
    }
} else if (!$showsidepost && !$showsidepre) {
    $bodyclasses[] = 'content-only';
}
if ($hascustommenu) {
    $bodyclasses[] = 'has_custom_menu';
}


echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
	 <script src="<?php echo $CFG->wwwroot; ?>/theme/dosk/javascript/jquery.colorbox-min.js"></script>
	 <script src="<?php echo $CFG->wwwroot; ?>/theme/dosk/javascript/jquery.colorbox-pl.js"></script>
</head>
<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses.' '.join(' ', $bodyclasses)) ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>
<div id="page">
<?php if ($hasheading || $hasnavbar || !empty($courseheader)) { ?>

<!-- START OF HEADER -->
    <div id="page-header" class="clearfix">
	 <a href="<?php echo $CFG->wwwroot; ?>" title="Strona główna">
        <h1 class="headermain"><img src="<?php echo $OUTPUT->pix_url('logo', 'theme'); ?>" alt="dobreOSK"></h1></a>
		  
		  <div class="mainlink" >
		  	  <a href="http://dobreosk.pl/" title="Strona główna">	<img src="<?php echo $OUTPUT->pix_url('home', 'theme'); ?>" alt="" >Platforma szkoleniowa dla OSK </a>
			</div>	
			
				<div id="menu_dosk">
				<a href="http://dobreosk.pl/pkk">PKK</a>
				<a href="http://dobreosk.pl/o-platformie">O platformie</a>
				<a href="http://dobreosk.pl/regulamin">Regulamin</a>
				<?php
				if(!$USER->id){
				echo '<a href="http://dobreosk.pl/auth/int_keygen/" class="log_in">Zaloguj</a>';
				} else {
				echo '<a href="'.$CFG->wwwroot.'/user/profile.php?id='.$USER->id.'">Twój profil</a>';
				echo '<a href="'.$CFG->wwwroot.'/login/logout.php?sesskey='.sesskey().'" class="log_out">Wyloguj</a>';
				}
				?>
				
			</div>
    </div>
<!-- END OF HEADER -->
<?php } ?>


 <?php if ($hasnavbar) { ?>
            <div class="navbar clearfix">
                <div class="breadcrumb"><?php echo $OUTPUT->navbar(); ?></div>
                <div class="navbutton"> <?php echo $PAGE->button; ?></div>
            </div>
    <?php } ?>


<!-- START OF TOP -->	 
<?php if ($hassidetop1 OR $hassidetop2 OR $hassidetop3) { 

?>

	<div class="clearfix" style="width: 100%; height: 10px; display: block; float: left;"></div>
	
                <div id="region-top" class="block-region">
                    <div class="region-content">
						  <?php
						  	if($hassidetop1){
							echo '<div id="region-top-1" style="width: '.$top_width.';">';
							echo $OUTPUT->blocks_for_region('top-1');
							echo '</div>';
							}	
							
							if($hassidetop1 && $hassidetop2) {
								echo '<div class="top-spacer"></div>';
							}
							
							if($hassidetop2){
							echo '<div id="region-top-2" style="width: '.$top_width.';">';
							echo $OUTPUT->blocks_for_region('top-2');
							echo '</div>';
							}
							
							if($hassidetop3 && ($hassidetop2 OR $hassidetop1)) {
								echo '<div class="top-spacer"></div>';
							}
							
							if($hassidetop3){
							echo '<div id="region-top-3" style="width: '.$top_width.';">';
							echo $OUTPUT->blocks_for_region('top-3');
							echo '</div>';
							}		

                    ?>
                    
							</div>
                </div>
					 
					 <br clear="all" />
					 
<?php } ?>	 
<!-- END OF TOP -->
 <!-- START OF CONTENT -->
   <div id="page-content">
        <div id="region-main-box">
            <div id="region-post-box">

                <div id="region-main-wrap">
                    <div id="region-main">
                        <div class="region-content">
                            <?php echo $OUTPUT->main_content() ?>
                        </div>
                    </div>
                </div>

                <?php if ($hassidepre OR (right_to_left() AND $hassidepost)) { ?>
                <div id="region-pre" class="block-region">
                    <div class="region-content">
                            <?php
                        if (!right_to_left()) {
                            echo $OUTPUT->blocks_for_region('side-pre');
                        } elseif ($hassidepost) {
                            echo $OUTPUT->blocks_for_region('side-post');
                    } ?>

                    </div>
                </div>
                <?php } ?>

                <?php if ($hassidepost OR (right_to_left() AND $hassidepre)) { ?>
                <div id="region-post" class="block-region">
                    <div class="region-content">
                           <?php
                       if (!right_to_left()) {
                           echo $OUTPUT->blocks_for_region('side-post');
                       } elseif ($hassidepre) {
                           echo $OUTPUT->blocks_for_region('side-pre');
                    } ?>
                    </div>
                </div>
                <?php } ?>

            </div>
        </div>
    </div>
<!-- END OF CONTENT -->
	  
<!-- START OF BOTTOM -->	 
<?php if ($hassidebot1 OR $hassidebot2 OR $hassidebot3) { 

?>
                <div id="region-bot" class="block-region">
                    <div class="region-content">
						  <?php
						  	if($hassidebot1){
							echo '<div id="region-bot-1" style="width: '.$bot_width.';">';
							echo $OUTPUT->blocks_for_region('bot-1');
							echo '</div>';
							}	
							
							if($hassidebot1 && $hassidebot2) {
								echo '<div class="bot-spacer"></div>';
							}
							
							if($hassidebot2){
							echo '<div id="region-bot-2" style="width: '.$bot_width.';">';
							echo $OUTPUT->blocks_for_region('bot-2');
							echo '</div>';
							}
							
							if($hassidebot3 && ($hassidebot2 OR $hassidebot1)) {
								echo '<div class="bot-spacer"></div>';
							}
							
							if($hassidebot3){
							echo '<div id="region-bot-3" style="width: '.$bot_width.';">';
							echo $OUTPUT->blocks_for_region('bot-3');
							echo '</div>';
							}		
							
			
                    ?>
                    
							</div>
                </div>
					 
					 <br clear="all" />
					 
<?php } ?>	 
<!-- END OF BOTTOM -->
	 
<!-- START OF FOOTER -->
    <?php if (!empty($coursefooter)) { ?>
        <div id="course-footer"><?php echo $coursefooter; ?></div>
    <?php } ?>
    <?php if ($hasfooter) { ?>
   <!-- START OF FOOTER -->
    <div id="page-footer" class="clearfix">
	
	
	&copy; Copyright dobreOSK.pl Wszelkie prawa zastrzeżone | Powered by SPH Credo
	
	
			 
	 <?php
	 echo $OUTPUT->login_info();
?>
	<div class="links">	
	 			<a href="http://dobreosk.pl/faq" class="faq">Faq</a>
				<a href="http://dobreosk.pl/kontakt" class="kontakt">Kontakt</a>
	 		</div>

        <?php echo $OUTPUT->standard_footer_html(); ?>
    
	
	 
	 </div>
    <div class="clearfix"></div>
</div>
<!-- END OF FOOTER -->
    <?php } ?>
    <div class="clearfix"></div>
</div>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>