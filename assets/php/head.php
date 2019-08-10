<?php    
header('Content-type: text/html; charset=utf-8');
//require($_SERVER['DOCUMENT_ROOT'].'/anti_ddos/start.php'); ?>
<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
		<title>GB-HOSTER | Game | VPS | Dedicated</title>
		<meta name="description" content="Buy your Game/VPS/Dedicated server at a very low price with very high quality!">
		<meta name="keywords" content="GB-Hoster, gb-hoster.me, GameHosting, game, cs, mc, css, csgo, gameserver, vps,dedicated,boost cs 1.6,reseller, servers">
		<meta name="author" content="RootSec">
        <link rel="stylesheet" type="text/css" href="szablon/theme/css/all.css?v4" media="screen">
        <link rel="stylesheet" href="szablon/theme/font-awesome/css/fontawesome.css" type="text/css">
		<link rel="stylesheet" href="szablon/adminpanel/css/icons.css" type="text/css" media="screen" title="default"/>
		        <?php if($page != "sms")
        {?>
        <script src="ajax/libs/jquery/1.11.1/jquery.min.js"></script>
             <?php } ?>
        <script type="text/javascript" src="szablon/theme/js/stepcarousel.js"></script>
        <script type="text/javascript" src="szablon/theme/js/calculator.php.js?<?php echo time(); ?>"></script>
        <?php if($page != "sms")
        {?>
		<script type="text/javascript" src="ajax/libs/jquery/1.4/jquery.min.js"></script> 
        <?php } ?>
		<!--  checkbox styling script -->

		<script src="szablon/js/jquery/ui.core.js" type="text/javascript"></script>
		<script src="szablon/js/jquery/ui.checkbox.js" type="text/javascript"></script>
		<script src="szablon/js/jquery/jquery.bind.js" type="text/javascript"></script>
		<!-- Tooltips -->
		<script src="szablon/js/jquery/jquery.tooltip.js" type="text/javascript"></script>
		<script src="szablon/js/jquery/jquery.dimensions.js" type="text/javascript"></script>	
		<!--- Fancybox  --->
		<script type="text/javascript" src="szablon/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
		<link rel="stylesheet" href="szablon/js/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen">		
		<script src="szablon/js/jquery/jquery.pngFix.pack.js" type="text/javascript"></script>
		<script type="text/javascript">
		function showme(num) {
			var showdiv = document.getElementById("showme"+num);
			showdiv.style.display = showdiv.style.display == "none" ? "" : "none";
		}	
		</script>
<script type="text/javascript">
stepcarousel.setup({
    galleryid: 'bgrot', //id of carousel DIV
    beltclass: 'belt', //class of inner "belt" DIV containing all the panel DIVs
    panelclass: 'panel', //class of panel DIVs each holding content
    autostep: {enable:true, moveby:1, pause:3000},
    panelbehavior: {speed:450, wraparound:true, wrapbehavior:'slide', persist:true},
    defaultbuttons: {enable: true, moveby: 1, leftnav: ['/szablon/theme/img/left.png', 0, 80], rightnav: ['/szablon/theme/img/right.png', -24, 80]},
    statusvars: ['statusA', 'statusB', 'statusC'], //register 3 variables that contain current panel (start), current panel (last), and total panels
    contenttype: ['inline'] //content setting ['inline'] or ['ajax', 'path_to_external_file']
})
</script>

</head>