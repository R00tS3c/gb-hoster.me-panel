
function Chat_Refresh(){
	$('#chat_messages ul').load('chatprocess.php?chat_refresh');
}

function Online_Refresh(){
	$('#onlinea ul').load('chatprocess?online_refresh');
}

function Clanovi_Refresh(){
	$('#clanovi ul').load('chatprocess?clanovi_refresh');	
}

function chat(poruka) {
	alert("test");
	$("#chat_text").val(poruka);
	$("#chat_text").focus();
}

function konzola_refresh(id){
	$("#konzolaajax").animate({ scrollTop: $("#konzolaajax")[0].scrollHeight}, 400);
	$('#konzolaajax').load('srv-konzola.php?id='+id+'&log=view');	
}

function Refresh(){
	Chat_Refresh();
	Online_Refresh();
	Clanovi_Refresh();
}
			
function Chat_Send(){
	if( $('#chat_text').val().length == 0 ) {
		alert("Morate napisati neki tekst.");
	} else {
		$.ajax({
			type: 'POST',
			url: 'chatprocess?chat_send',
			data: "chat_text=" + $('#chat_text').val(),
			success: function(){
				Chat_Refresh();
			}
		});
		$('#chat_text').val('');
	}
}	
			
function Chat_IzbrisiSve(){	
	$('#atest').fadeIn(300);		
	$.ajax({
		url: 'chatprocess?chat_delete_all',
		success: function(){
			Chat_Refresh();
			setTimeout(function() {
			 $('#atest').fadeOut(300);
			}, 1000 );
		}
	});
}

function refresht(id){
	$('#asd123x').load('srvstatus.php?id='+id);
}

function Chat_Izbrisi(id){	
	$('#atest').fadeIn(300);		
	$.ajax({
		url: 'chatprocess?chat_delete=' + id,
		success: function(){
			Chat_Refresh();
		}
	});
}

setTimeout(function(){$('.alert').fadeOut('fast');},6000);

$('.tip').tipsy({fade:true,gravity:'s'});

var id = $("#konzolaajax").attr('serverid');

setInterval('Chat_Refresh()', 2000);
setInterval('Global_Timer()', 5000);

function Global_Timer() {
	Online_Refresh();
	Clanovi_Refresh();
	konzola_refresh(id);
	$("#pregledava").load('pregledava.php?id=' + tiket_id);
}


function dodajKomentar(){
    var komentar = $('textarea.komentar').val();
    var a_id = $('#adminid').val();
	var profil_id = $('#admin').val();
	var datum = $('#vreme').val();

    var dataString = 'task=komentar&komentar=' + komentar + '&admin_id=' + a_id + '&profil_id=' + profil_id + '&vreme=' + datum;
    $.ajax({
		type: "POST",
		url: "process.php",
		data: dataString,
		cache: false,
		success: function(result){
			var result=trim(result);
			$('textarea.komentar').val('');
			if(result=='uspesno'){
				$("#greskakoment").html("Uspesno ste napisali komentar.");
				window.location='admin_pregled.php?id=' + profil_id;
			} else {
				$("#greskakoment").html(result);
			}
		}
	});
}

function izbrisiKomentar(id){
	var dataString = 'task=delkomentar&id=' + id;
	var profil_id = $('#admin').val();
    $.ajax({
		type: "POST",
		url: "process.php",
		data: dataString,
		cache: false,
		success: function(result){
			var result=trim(result);
			$('textarea.komentar').val('');
			if(result=='uspesno'){
				$("li#" + id).fadeOut("fast");
				//window.location='admin_pregled.php?id=' + profil_id;
			} else {
				alert(result);
			}
		}
	});	
}

function izbrisiKomentarc(id){
	var dataString = 'task=delkomentarc&id=' + id;
	var profil_id = $('#admin').val();
    $.ajax({
		type: "POST",
		url: "process.php",
		data: dataString,
		cache: false,
		success: function(result){
			var result=trim(result);
			$('textarea.komentar').val('');
			if(result=='uspesno'){
				$("li#" + id).fadeOut("fast");
				//window.location='admin_pregled.php?id=' + profil_id;
			} else {
				alert(result);
			}
		}
	});	
}

function dodajKomentar_Tiket(){
    var komentar = $('textarea.tiketkoment').val();
    var a_id = $('#adminid').val();
	var tiketid = $('#tiketid').val();
	var datum = $('#vreme').val();

    var dataString = 'task=komentar_tiket&komentar=' + komentar + '&admin_id=' + a_id + '&tiket_id=' + tiketid + '&vreme=' + datum;
    $.ajax({
		type: "POST",
		url: "process.php",
		data: dataString,
		cache: false,
		success: function(result){
			var result=trim(result);
			$('textarea.tiketkoment').val('');
			if(result=='uspesno'){
				$("#greskakoment").html("Uspesno ste napisali komentar.");
				window.location='tiket.php?id=' + tiketid;
			} else {
				$("#greskakoment").html(result);
			}
		}
	});
}

function modal_show(id){
	$('#editobavestenje').modal('show');
}

function izbrisi_obavestenje(id){
	var dataString = 'task=delobavestenje&id=' + id;
	
	window.delkoment = id;
	
    $.ajax({
		type: "POST",
		url: "process.php",
		data: dataString,
		cache: false,
		success: function(result){
			var result=trim(result);
			if(result=='uspesno'){
				location.reload();
			} else {
				alert(result);
			}
		}
	});	
}

function izbrisi_slajd(id){
	var dataString = 'task=delslajd&id=' + id;
	
	window.delkoment = id;
	
    $.ajax({
		type: "POST",
		url: "process.php",
		data: dataString,
		cache: false,
		success: function(result){
			var result=trim(result);
			if(result=='uspesno'){
				location.reload();
			} else {
				alert(result);
			}
		}
	});	
}

function izbrisiKomentar_Tiket(id){
	var dataString = 'task=delkomentar_tiket&id=' + id;
    $.ajax({
		type: "POST",
		url: "process.php",
		data: dataString,
		cache: false,
		success: function(result){
			var result=trim(result);
			$('textarea.tiketkoment').val('');
			if(result=='uspesno'){
				$("li." + id).fadeOut("fast");
				//window.location='admin_pregled.php?id=' + profil_id;
			} else {
				alert(result);
			}
		}
	});	
}
function trim(str){
	var str=str.replace(/^\s+|\s+$/,'');
return str;
}

$('#chat_messages').tooltip({
      selector: "#cautor[data-toggle=tooltip]"
});	

$('.tipg').tooltip({
      selector: "a[data-toggle=tooltip]"
});	
			
var timeoutObj;
$('.autor').popover({
    offset: 10,
    trigger: 'manual',
	container: 'body',
    html: true,
    placement: 'right',
    template: '<div class="popover" onmouseover="clearTimeout(timeoutObj);$(this).mouseleave(function() {$(this).hide();});"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
}).mouseenter(function(e) {
    $(this).popover('show');
}).mouseleave(function(e) {
    var ref = $(this);
    timeoutObj = setTimeout(function(){
        ref.popover('hide');
    }, 50);
});		

$('.smajli').popover({
    offset: 10,
    trigger: 'manual',
	container: 'body',
    html: true,
    placement: 'top',
    template: '<div class="popover" onmouseover="clearTimeout(timeoutObj);$(this).mouseleave(function() {$(this).hide();});"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
}).mouseenter(function(e) {
    $(this).popover('show');
}).mouseleave(function(e) {
    var ref = $(this);
    timeoutObj = setTimeout(function(){
        ref.popover('hide');
    }, 50);
});



$(document).ready(function() {
	$("#fajledit").autosize({append: "\n"});
	
	$("#loadKomentarbr").load("komentar.php");
	
	$.ajaxSetup({ cache: false });
	
	var tiket_id = $("#tiket_id").val();
	$("#pregledava").load("pregledava.php?id=" + tiket_id);
	
	$.ajaxSetup({ cache: false });
	
	$("#t_ban").click(function(){
		$("#ban_klijenta").modal('show');
	});
	
	$("#gp_ban").click(function(){
		$("#ban_klijenta_gp").modal('show');
	});
	
	$("#t_prosl").click(function(){
		$("#prosl_tiket").modal('show');
	});
	
	$("#smajlici2").click(function() {
		var smajli = $(this).attr("kod");
		alert(smajli + "asd");
	});
	
	$("#trajni_ban").change(function() {
		var val = $(this).val();
		
		if(val === "2") {
			$("#datum_bana").prop('disabled', true);
		} else if(val === "1") {
			$("#datum_bana").prop('disabled', false).datepicker();
		}
	});
	
	$("#trajni_ban_gp").change(function() {
		var val = $(this).val();
		
		if(val === "2") {
			$("#datum_bana_gp").prop('disabled', true);
		} else if(val === "1") {
			$("#datum_bana_gp").prop('disabled', false).datepicker();
		}
	});
	
	$("#serveraddigra").change(function() {
		var val = $(this).val();
		
		if(val == "1") {
			$("#csad").show();
			$("#sampad").hide();
			$("#mcad").hide();
			$("#defad").hide();
			$("#csmod").show();
			$("#sampmod").hide();
			$("#mcmod").hide();
			$("#csdef").hide();
			$("#fivemad").hide();
			$("#fivemmod").hide();
			$("#tsmod").hide();
			$("#tsad").hide();
		} else if(val == "2") {
			$("#csad").hide();
			$("#mcad").hide();
			$("#sampad").show();
			$("#defad").hide();
			$("#csmod").hide();
			$("#sampmod").show();
			$("#csdef").hide();
			$("#fivemad").hide();
			$("#fivemmod").hide();
			$("#tsmod").hide();
			$("#tsad").hide();
		} else if(val == "3") {
			$("#csad").hide();
			$("#sampad").hide();
			$("#mcad").show();
			$("#defad").hide();
			$("#csmod").hide();
			$("#mcmod").show();
			$("#sampmod").hide();
			$("#csdef").hide();
			$("#fivemad").hide();
			$("#fivemmod").hide();
			$("#tsmod").hide();
			$("#tsad").hide();
		}
		else if(val == "9") {
			$("#csad").hide();
			$("#sampad").hide();
			$("#mcad").hide();
			$("#fivemad").show();
			$("#defad").hide();
			$("#csmod").hide();
			$("#fivemmod").show();
			$("#sampmod").hide();
			$("#mcmod").hide();
			$("#csdef").hide();
			$("#tsmod").hide();
			$("#tsad").hide();
		}
		 else if(val == "6") {
			$("#csad").hide();
			$("#sampad").hide();
			$("#tsad").show();
			$("#defad").hide();
			$("#csmod").hide();
			$("#tsmod").show();
			$("#sampmod").hide();
			$("#csdef").hide();
			$("#fivemad").hide();
			$("#fivemmod").hide();
			$("#mcad").hide();
			$("#mcmod").hide();
		}

	});
	
	$("#datum").datepicker();
	
	$(document).keydown(function(e) {
		var code = (e.keyCode) ? e.keyCode : e.which;
		if ($("#chat_text").is(":focus")) {
			if(code == 13) Chat_Send();
		}
	});
});

$(document).ready(function() {
	$("table").tablesorter();
});

function izracunajCenu(){
	var slot = $("option:selected","#slotovi").val();
	var flag = $("#flag").attr("title");
	var Izdvajanje = $("#drzava").val();
	var Izdvajanje = Izdvajanje .split("|")
	var CenaSlota = Izdvajanje[0];
	var Valuta = Izdvajanje[1];	
	var Skraceno = Izdvajanje[2];	
	var Mesec = $("#meseci").val();
	var Cena = slot;
	
 	var Popust = 0;
	if (Mesec==2) Popust=5/100;
	else 	
	if (Mesec==3) Popust=10/100;
	else 
	if (Mesec==6) Popust=15/100;
	else 
	if (Mesec==12) Popust=20/100;
 
	var CenaPopust = Math.round(Cena*Mesec*100)/100;
	Cena *= CenaSlota;
	Cena-=(Cena*Popust);
	
	CenaPopust *= CenaSlota;
	CenaPopust = Math.round(CenaPopust*100)/100;
	Cena*=Mesec;
	Cena = Cena.toFixed(2);
	Cena = Cena.replace(".00", "");

	var cena_valuta_zemlja = Cena+" "+Valuta+" <span style='float: right; margin-top: 8px; margin-right: 8px;'><img src='../assets/img/"+flag+".png' /></span>";
	var cena_valuta_zemljaa = Cena+" "+Valuta;

	if (!(slot>0)) cena_valuta_zemlja="Izaberite broj slotova";	
	$("#cena").html(cena_valuta_zemlja);
	$("#cenab").val(cena_valuta_zemljaa);
}

function imefoldera(folder)
{
	$("#ime-foldera").html(folder);
	$("#ime_foldera").val(folder);
}

function imefajla(folder)
{
	$("#ime-fajla").html(folder);
	$("#ime_fajla").val(folder);
}

function imeftpf(folder)
{
	$(".span5.sah").val(folder);
	$("#imeftps").val(folder);
}

function edit_obavestenje(id, naslov, text) {
	$("#naslovo").val(naslov);
	$("#texto").val(text);
	$("input.id_ob").val(id);
}

function edit_slajd(id, naslov, text, slika) {
	$("#naslovs").val(naslov);
	$("#texts").val(text);
	$("#slikas").val(slika);
	$("input.id_sl").val(id);
}

function plugin(id, ime, deskripcija, prikaz, text) {
	$("#pltext").val(text);
	$("#plime").val(ime);
	$("#pldesk").val(deskripcija);
	$("#plprikaz").val(prikaz);
	$(".id_pl").val(id);
}

function mod(id, ime, opis, putanja, igra, cena, mapa, sakriven, komanda, link, zipname, cena_premium) {
	$(".mod_id").val(id);
	$(".igra_id").val(igra);
	$("#modime").val(ime);
	$("#modopis").val(opis);
	$("#modputanja").val(putanja);
	$("#modigra").val(igra);
	$("#modmapa").val(mapa);
	$("#modsakriven").val(sakriven);
	$("#modkomanda").val(komanda);
	$("#modlink").val(link);
	$("#modzipname").val(zipname);
	
	var cena = cena .split("|");
	$("#modsrb").val(cena[0]);
	$("#modcg").val(cena[1]);
	$("#modmk").val(cena[2]);
	$("#modhr").val(cena[3]);
	$("#modbih").val(cena[4]);
	
	var cena_premium = cena_premium .split("|");
	$("#modsrb_premium").val(cena_premium[0]);
	$("#modcg_premium").val(cena_premium[1]);
	$("#modmk_premium").val(cena_premium[2]);
	$("#modhr_premium").val(cena_premium[3]);
	$("#modbih_premium").val(cena_premium[4]);
}

  //$('.autor').popover({ html:true, delay: { show: 100, hide: 1000 } })

    // popover demo
    $("a[data-toggle=popover]")
      .popover()
      .click(function(e) {
        e.preventDefault()
      })
	  
