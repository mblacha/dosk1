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
   <div id="page-header" class="clearfix">
	 <a href="<?php echo $CFG->wwwroot; ?>" title="Strona główna">
        <h1 class="headermain"><img src="<?php echo $OUTPUT->pix_url('logo', 'theme'); ?>" alt="dobreOSK"></h1></a>
		  
		  <div class="mainlink" >
		  	  <a href="<?php echo $CFG->wwwroot; ?>" title="Strona główna">	<img src="<?php echo $OUTPUT->pix_url('home', 'theme'); ?>" alt="" >Platforma szkoleniowa dla OSK </a>
			</div>	
			
			<div id="menu_dosk">
				<a href="<?php echo $CFG->wwwroot."/mod/page/view.php?id=2"; ?>">PKK</a>
				<a href="<?php echo $CFG->wwwroot."/mod/page/view.php?id=7"; ?>">O platformie</a>
				<a href="<?php echo $CFG->wwwroot."/mod/page/view.php?id=11"; ?>">Regulamin</a>
				<?php
				if(!$USER->id){
				echo '<a href="'.$CFG->wwwroot.'/login/index.php">Logowanie</a>';
				} else {
				echo '<a href="'.$CFG->wwwroot.'/user/profile.php?id='.$USER->id.'">Twój profil</a>';
				}
				?>
				
			</div>
    </div>
<!-- END OF CONTENT -->
	 

<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>