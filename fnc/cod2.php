<?php if($game == "cod2") { ?>

<script>

function izracunajCenu(){
	var slot=$("option:selected","#slotovi").val();
	var flag=$("#flag").attr("title");
	var Izdvajanje=$("#odaberiigru").val();
	
	if(Izdvajanje == 1) {
		var Izdvajanjep = '0.375|&euro;';
		$('.premium').css('display', 'none');
		$('.lite').css('display', 'block');
		var slot=$("option:selected",".lite").val();
	} else {
		if(Izdvajanje == 2) {
			var Izdvajanjep = '0.575|&euro;';
			$('.premium').css('display', 'block');
			$('.lite').css('display', 'none');						
			var slot=$("option:selected",".premium").val();
		}
	}

	var Izdvajanje=Izdvajanjep.split("|");
	var CenaSlota=Izdvajanje[0];
	var Valuta=Izdvajanje[1];
	var Mesec=$("#meseci").val();
	var Cena=slot;
	var Popust=0;
	
	if(Mesec==2)Popust=5/100;else
	if(Mesec==3)Popust=10/100;else
	if(Mesec==6)Popust=15/100;else
	if(Mesec==12)Popust=20/100;

	var CenaPopust=Math.round(Cena*Mesec*100)/100;
	Cena*=CenaSlota;
	Cena-=(Cena*Popust);
	CenaPopust*=CenaSlota;
	CenaPopust=Math.round(CenaPopust*100)/100;
	Cena*=Mesec;Cena=Cena.toFixed(2);
	Cena=Cena.replace(".00","");
	var cena_valuta_zemlja=Cena+" "+Valuta;
	var cena_valuta_zemljaa=Cena;

	if(!(slot>0))cena_valuta_zemlja="Izaberite broj slotova";
	$("#cena").html(cena_valuta_zemlja);
	$("#cijenaserverainput").val(cena_valuta_zemljaa);
}

</script>	

<form action="/fnc/buy.php?task=buy_cod2" method="POST" class="form-horizontal">

	<div class="col-md-6">
		<div class="NarServerPrviDeo">
			
			<li style="display:block;">
				<span class="inline" id="span_for_name">
					<div class="none">
						<img src="/img/icon/katanac-overlay.png" style="width:46px;position:absolute;margin:3px -30px;">
						<img src="/img/icon/user-icon-username.png" style="width:15px;margin:13px -16px;position:absolute;">
					</div>
				</span>
				<input type="text" required="required" placeholder="ime i prezime" name="ime" class="NarServer" />	
			</li>

			<li style="display:block;">
				<span class="inline" id="span_for_name">
					<div class="none">
						<img src="/img/icon/katanac-overlay.png" style="width:46px;position:absolute;margin:3px -30px;">
						<img src="/img/icon/user-icon-username.png" style="width:15px;margin:13px -16px;position:absolute;">
					</div>
				</span>
				<input type="email" required="required" placeholder="email" name="email" class="NarServer" />	
			</li>

			<li style="display:block;">
				<span class="inline" id="span_for_name">
					<div class="none">
						<img src="/img/icon/katanac-overlay.png" style="width:46px;position:absolute;margin:3px -30px;">
						<img src="/img/icon/user-icon-username.png" style="width:15px;margin:13px -16px;position:absolute;">
					</div>
				</span>
				<select name='odaberiteigru' class="NarServer">
					<option value='cod2'>Call of Duty 2</option>
				</select>	
			</li>

			<li style="display:block;">
				<span class="inline" id="span_for_name">
					<div class="none">
						<img src="/img/icon/katanac-overlay.png" style="width:46px;position:absolute;margin:3px -30px;">
						<img src="/img/icon/user-icon-username.png" style="width:15px;margin:13px -16px;position:absolute;">
					</div>
				</span>
				<select name='odaberiteslotove' class='NarServer lite' id='slotovi'  onChange='izracunajCenu();'>
					<option value='0'>broj slotova</option>
					<option value='12'>12 slotova</option>
					<option value='14'>14 slotova</option>
					<option value='16'>16 slotova</option>
					<option value='18'>18 slotova</option>
					<option value='20'>20 slotova</option>
					<option value='22'>22 slota</option>
					<option value='24'>24 slota</option>
					<option value='26'>26 slota</option>
					<option value='28'>28 slotova</option>
					<option value='30'>30 slotova</option>
					<option value='32'>32 slota</option>
				</select>
				
				<select name='odaberiteslotove2' class='NarServer premium' id='slotovi' onChange='izracunajCenu();' style="display: none;">
					<option value='0'>broj slotova</option>
					<option value='12'>12 slotova</option>
					<option value='14'>14 slotova</option>
					<option value='16'>16 slotova</option>
					<option value='18'>18 slotova</option>
					<option value='20'>20 slotova</option>
					<option value='22'>22 slota</option>
					<option value='24'>24 slota</option>
					<option value='26'>26 slota</option>
					<option value='28'>28 slotova</option>
					<option value='30'>30 slotova</option>
					<option value='32'>32 slota</option>
				</select>
			</li>

			<li style="display:block;">
				<span class="inline" id="span_for_name">
					<div class="none">
						<img src="/img/icon/katanac-overlay.png" style="width:46px;position:absolute;margin:3px -30px;">
						<img src="/img/icon/user-icon-username.png" style="width:15px;margin:13px -16px;position:absolute;">
					</div>
				</span>
				<input type="text" required="required" placeholder="naziv servera" name="naziv" class="NarServer" />		
			</li>

		</div>
	</div> <!-- PRVI DEO -->

	<div class="col-md-6">
		<div class="NarServerDrugiDeo">
		
			<li style="display:block;">
				<span class="inline" id="span_for_name">
					<div class="none">
						<img src="/img/icon/katanac-overlay.png" style="width:46px;position:absolute;margin:3px -30px;">
						<img src="/img/icon/user-icon-username.png" style="width:15px;margin:13px -16px;position:absolute;">
					</div>
				</span>
				<select name='lokacija' class="NarServer" id='odaberiigru' onchange='izracunajCenu();'>
					<option value=''>izaberite lokaciju</option>
					<option value='1'>Lite - Njemacka</option>
					<option value='2'>Premium - Srbija</option>
				</select>		
			</li>			

			<li style="display:block;">
				<span class="inline" id="span_for_name">
					<div class="none">
						<img src="/img/icon/katanac-overlay.png" style="width:46px;position:absolute;margin:3px -30px;">
						<img src="/img/icon/user-icon-username.png" style="width:15px;margin:13px -16px;position:absolute;">
					</div>
				</span>
				<select name='drzava' class="NarServer">
					<option value=''>izaberite drzavu</option>
					<option value='ME'>Crna Gora</option>
					<option value='RS'>Srbija</option>
					<option value='MK'>Makedonija</option>
					<option value='HR'>Hrvatska</option>
					<option value='BA'>Bosna i Hercegovina</option>
				</select>			
			</li>	

			<li style="display:block;">
				<span class="inline" id="span_for_name">
					<div class="none">
						<img src="/img/icon/katanac-overlay.png" style="width:46px;position:absolute;margin:3px -30px;">
						<img src="/img/icon/user-icon-username.png" style="width:15px;margin:13px -16px;position:absolute;">
					</div>
				</span>
				<select name='nacinplacanja' class="NarServer">
					<option value=''>nacin placanja</option>
					<option value='Bank/Posta'>Banka/Posta</option>
				</select>		
			</li>						

			<li style="display:block;">
				<span class="inline" id="span_for_name">
					<div class="none">
						<img src="/img/icon/katanac-overlay.png" style="width:46px;position:absolute;margin:3px -30px;">
						<img src="/img/icon/user-icon-username.png" style="width:15px;margin:13px -16px;position:absolute;">
					</div>
				</span>
				<select name='mjeseci' class="NarServer" id='meseci' onChange='izracunajCenu();'>
					<option value='1'>1 Mesec</option>
					<option value='2'>2 Meseca ( 5 % popusta )</option>
					<option value='3'>3 Meseca ( 10 % popusta )</option>
					<option value='6'>6 Meseci ( 15 % popusta )</option>
					<option value='12'>12 Meseci ( 20 % popusta )</option>
				</select>		
			</li>						

			<li style="display:block;">
				<span class="inline" id="span_for_name">
					<div class="none">
						<img src="/img/icon/katanac-overlay.png" style="width:46px;position:absolute;margin:3px -30px;">
						<img src="/img/icon/user-icon-username.png" style="width:15px;margin:13px -16px;position:absolute;">
					</div>
				</span>
				<div readonly='readonly' class="NarServer" id='cena'>
					<span style="position:absolute;margin-top:6px;left:45px;">0.00 &euro;</span>
				</div>	
				<input type='hidden' id='cijenaserverainput' name='cijenaserverainput'>	
			</li>

		</div>
	</div>

	<div class="NarServerButton">
		
		<input type='submit' name='naruciserver' class='btn order' value='Naruci'>

	</div>

</form>


<?php } ?>