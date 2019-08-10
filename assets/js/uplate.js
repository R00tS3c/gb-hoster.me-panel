$('#drzava-uplatnica').change(function() {
	var val = $(this).val();
	
	if(val === "1") {
		$("#srbija").fadeIn("fast");
		$("#crnagora").hide();
		$("#bosna").hide();
		$("#hrvatska").hide();
		$("#makedonija").hide();
		$("#ostalezemlje").hide();
		$.colorbox.resize();
	} else if(val === "2") {
		$("#srbija").hide();
		$("#crnagora").fadeIn("fast");
		$("#bosna").hide();
		$("#hrvatska").hide();
		$("#makedonija").hide();
		$("#ostalezemlje").hide();
		$.colorbox.resize();
	} else if(val === "3") {
		$("#srbija").hide();
		$("#crnagora").hide();
		$("#bosna").fadeIn("fast");
		$("#hrvatska").hide();
		$("#makedonija").hide();
		$("#ostalezemlje").hide();
		$.colorbox.resize();
	} else if(val === "4") {
		$("#srbija").hide();
		$("#crnagora").hide();
		$("#bosna").hide();
		$("#hrvatska").fadeIn("fast");
		$("#makedonija").hide();
		$("#ostalezemlje").hide();
		$.colorbox.resize();
	} else if(val === "5") {
		$("#srbija").hide();
		$("#crnagora").hide();
		$("#bosna").hide();
		$("#hrvatska").hide();
		$("#makedonija").fadeIn("fast");
		$("#ostalezemlje").hide();
		$.colorbox.resize();
	} else if(val === "6") {
		$("#srbija").hide();
		$("#crnagora").hide();
		$("#bosna").hide();
		$("#hrvatska").hide();
		$("#makedonija").hide();
		$("#ostalezemlje").fadeIn("fast");
		$.colorbox.resize();
	} else {
		$("#srbija").fadeIn("fast");
		$("#crnagora").hide();
		$("#bosna").hide();
		$("#hrvatska").hide();
		$("#makedonija").hide();
		$("#ostalezemlje").hide();
		$.colorbox.resize();
	}
});

// Console print

var stop_css = "font-size:50px;color:red;text-shadow:-1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;";
console.log("%c %s",stop_css,'STOP!');

var msg_css = "font-size:15px;color:black;";
console.log("%c %s",msg_css,'This function browser is for developers, if you have a river that over conzola can hack or break into someone\'s GamePanel, you are so wrong this is just a lie!');