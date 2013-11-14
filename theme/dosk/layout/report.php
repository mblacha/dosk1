<?php

$hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);
$showsidepre = $hassidepre && !$PAGE->blocks->region_completely_docked('side-pre', $OUTPUT);
$showsidepost = $hassidepost && !$PAGE->blocks->region_completely_docked('side-post', $OUTPUT);

$custommenu = $OUTPUT->custom_menu();
$hascustommenu = (empty($PAGE->layout_options['nocustommenu']) && !empty($custommenu));

$bodyclasses = array();
$bodyclasses[] = 'content-only';

if ($hascustommenu) {
    $bodyclasses[] = 'has_custom_menu';
}
//echo $PAGE->heading 
echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <meta name="description" content="<?php p(strip_tags(format_text($SITE->summary, FORMAT_HTML))) ?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
</head>
<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses.' '.join(' ', $bodyclasses)) ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>

<div id="page">


<!-- START OF HEADER -->
    <div id="page-header" class="clearfix">
        <h1 class="headermain"><img src="<?php echo $OUTPUT->pix_url('logo', 'theme'); ?>" alt="dobreOSK"></h1>
		  <div class="logotxt">
		  	Profesjonalne kursy dla kierowców	
			</div>	
		  <div class="mainlink">
		  	  <a href="<?php echo $CFG->wwwroot; ?>">	<img src="<?php echo $OUTPUT->pix_url('home', 'theme'); ?>" alt="">Platforma szkoleniowa dla OSK </a>
			</div>	
        <div class="headermenu"><?php
            echo $OUTPUT->login_info();
            //echo $OUTPUT->lang_menu();
            echo $PAGE->headingmenu;
        ?></div>
        

			
    </div>
	 
 
			
<!-- END OF HEADER -->




<!-- START OF CONTENT -->
    <div id="page-content" class="clearfix">
	 
	 	

		<div class="main-region">
       	<div class="region-content">
	         <?php echo $OUTPUT->main_content(); ?>
	       </div>
		 </div>
							
	
    </div>
<!-- END OF CONTENT -->
	 

<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>