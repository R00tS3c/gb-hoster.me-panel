<?php
//CIJENA SLOTA CS 1.6 jezik srpski din
$la_slot_cs_rs = "10";
//CIJENA SLOTA CS 1.6 jezik njemacki eur
$la_slot_cs_de = "0.5";
//CIJENA SLOTA CS 1.6 jezik engleski usd
$la_slot_cs_en = "0.8";

//CIJENA SLOTA CSGO jezik srpski din
$la_slot_csgo_rs = "10";
//CIJENA SLOTA CSGO  jezik njemacki eur
$la_slot_csgo_de = "0.5";
//CIJENA SLOTA CSGO jezik engleski usd
$la_slot_csgo_en = "0.8";

//CIJENA SLOTA samp jezik srpski din
$la_slot_sa_rs = "10";
//CIJENA SLOTA samp  jezik njemacki eur
$la_slot_sa_de = "0.5";
//CIJENA SLOTA samp jezik engleski usd
$la_slot_sa_en = "0.8";

//CIJENA SLOTA mc jezik srpski din
$la_slot_mc_rs = "10";
//CIJENA SLOTA mc  jezik njemacki eur
$la_slot_mc_de = "0.5";
//CIJENA SLOTA mc jezik engleski usd
$la_slot_mc_en = "0.8";

//CIJENA SLOTA cod2 jezik srpski din
$la_slot_cod2_rs = "10";
//CIJENA SLOTA cod2  jezik njemacki eur
$la_slot_cod2_de = "0.5";
//CIJENA SLOTA cod2 jezik engleski usd
$la_slot_cod2_en = "0.8";

//CIJENA SLOTA cod4 jezik srpski din
$la_slot_cod4_rs = "10";
//CIJENA SLOTA cod4 jezik njemacki eur
$la_slot_cod4_de = "0.5";
//CIJENA SLOTA cod4 jezik engleski usd
$la_slot_cod4_en = "0.8";

//CIJENA SLOTA ts jezik srpski din
$la_slot_ts_rs = "10";
//CIJENA SLOTA ts jezik njemacki eur
$la_slot_ts_de = "0.5";
//CIJENA SLOTA ts jezik engleski usd
$la_slot_ts_en = "0.8";

//CIJENA SLOTA vps jezik srpski din
$la_slot_vps_rs = "10";
//CIJENA SLOTA vps jezik njemacki eur
$la_slot_vps_de = "0.5";
//CIJENA SLOTA vps jezik engleski usd
$la_slot_vps_en = "0.8";

$lan = $_GET['lan'];
if($lan == "en") {
    $dodatni_link = "?lan=en";
} elseif($lan == "de") {
    $dodatni_link = "?lan=de";
} else {
    $dodatni_link = "";
}


//Varijable za jezik: srpski
$lan = $_GET['lan'];
if($lan == ""){

//Navigacija (gornja)
$li_pocetna = "Početna";
$li_gamepanel = "Game Panel";
$li_forum = "Forum";
$li_naruci = "Naruči";
$li_onama = "O nama";
$li_kontakt = "Kontakt";
$li_boostbalkan = "BoostBalkan.com";
$li_email_adresa = "email";
$li_password = "password";
$li_demo = "DEMO";
$li_login = "LOGIN";
$li_registruj = "REGISTRUJ SE";
$li_vesti = "VESTI";
$li_vest = "Dobrodosli na novi Portal sa integrisanim panelom, ovo je Beta verzija sajta i panela! Sve korisnike ukoliko imaju problema savjetujem da nas kontaktirate.";
$li_cs_download = "PREUZMI NAJNOVIJI COUNTER STRIKE";
$li_download = "DOWNLOAD NOW!";
$li_pocetna_igra = "Igra";
$li_pocetna_info = "Više info";
$li_pocetna_cijenaslota = "CIJENA SLOTA";
$li_pocetna_cijenaslotavaluta = "RSD";
$li_pocetna_naruci = "Naruči";
$li_bilten_naslov = "Bilten";
$li_bilten_opis = "prijavite se na naš bilten i dobijate najnovije informacije vezane za sam hosting...<br>Poruke od našeg Biltena možete onemogućiti u bilo kojem trenutku!";


//Navigacija (donja) - Ako si logiran
$li_mojprofil = "MOJ PROFIL";
$li_vesti_nav = "Vesti";
$li_serveri = "Serveri";
$li_billing = "Billing";
$li_podrska = "Podrška";
$li_podesavanja = "Podešavanja";
$li_iplog = "IP Log";
$li_logout = "Logout";

//vesti
$li_dobrodosao = "Dobrodošao u Gpanel";
//serveri
$li_naslov_serveri = "Serveri";
$li_opis_serveri = "Lista svih Vaših servera";
$li_ime_servera = "Ime servera";
$li_vazi_do = "Važi do";
$li_cena = "Cena";
$li_ip_adresa = "IP adresa";
$li_slotovi = "Slotovi";
$li_status =  "Status";
//billing
$li_naslov_billing = "Billing";
$li_opis_billing = "Lista svih Vaših narudžba";
$li_vrsta_placanja = "Vrsta plaćanja";
$li_datum_narudzbe = "Datum narudžbe";
$li_pregledava = "Pregledava: ";
$li_uplati_preko = "UPLATI PREKO";
$li_nova_narudzba = "Nova narudzba";
//podrska
$li_naslov_podrska = "Podrška";
$li_opis_podrska = "Dobrodosli u GameHoster.biz Support panel<br/>Ovde možete otvarati nove tikete ukoliko vam treba pomoć ili podrška oko servera.";
$li_idtiketa = "ID Tiketa";
$li_imetiketa = "Ime tiketa";
$li_datum = "Datum";
$li_server = "Server";
$li_broj_poruka = "Broj poruka";
$li_status = "Status";
$li_novitiket = "Novi tiket";
$li_arhiva = "Arhiva";
//podesavanja
$li_naslov_podesavanja = "Licni podaci";
$li_opis_podesavanja = "Ovde možete promeniti lične podatke!";
$li_poruka_podesavanja = "Kako bi pristupili opciji za editovanje vaših informacija potrebno je da ispravno unesete vaš pin kod!";
$li_otkljucaj = "Otključaj";
//iplog
$li_naslov_iplog = "Logovi";
$li_opis_logovi = "Lista svih logova ovog naloga";
$li_id_logovi = "ID";
$li_poruka_logovi = "Poruka";
$li_ip_logovi = "IP adresa";
$li_dav = "D&V";
//server
$li_server_server = "Server";
$li_server_podesavanje = "Podešavanje";
$li_admini_slotovi = "Admini i slotovi";
$li_server_webftp = "WebFTP";
$li_server_plugini = "Plugini";
$li_server_modovi = "Modovi";
$li_server_konzola ="Konzola";
$li_server_boost = "Boost";
$li_autorestart = "Autorestart";
$li_server_ime = "Ime servera";
$li_datum_isteka = "Datum isteka";
$li_server_igra = "Igra";
$li_server_lokacija = "Lokacija";
$li_podnozje = "GameHoster.biz se bavi hostovanjem game servera! Nastao je 2012 godine i mozemo se pohvaliti dosadasnjim uspehom! Nasi serveri se hostuju na Nemackim masinama! Trenutno u ponudi imamo CS,SAMP,MC servere,a takodje radimo na tome da prosirimo nase trziste! Ping se krece od 20-50ms/s sto zavisi od vasih internet provajdera.";
$li_podnozje_potpis = "Sva prava zadržana.";
$li_podnozje_dizajner = "Dizajner";
$li_podnozje_informacije = "Informacije";
$li_podnozje_prava = "Prava";
 // Error tekst
$li_error_nemaservera = "Taj server ne postoji ili nemate ovlaščenje za isti.";
$li_error_nistelog = "Niste logirani!";
 //Server status
$li_serverstatus_online = "Online";
$li_serverstatus_offline = "Server je offline.";
$li_serverstatus_stopiran = "Server je stopiran u panelu.";

}elseif($lan == "en"){

//jezik:engleski
//Navigacija (gornja)
$li_pocetna = "Home";
$li_gamepanel = "Game Panel";
$li_forum = "Forum";
$li_naruci = "Order";
$li_onama = "About Us";
$li_kontakt = "Contact";
$li_boostbalkan = "BoostBalkan.com";
$li_email_adresa = "email";
$li_password = "password";
$li_demo = "DEMO";
$li_login = "LOGIN";
$li_registruj = "REGISTER";
$li_vesti = "NEWS";
$li_vest = "Welcome to new Portal integrated gamepanel, this is Beta version of site and panel! If you have any problem, please contact us.";
$li_cs_download = "DOWNLOAD NEWEST COUNTER STRIKE";
$li_download = "DOWNLOAD NOW!";
$li_pocetna_igra = "Game";
$li_pocetna_info = "More info";
$li_pocetna_cijenaslota = "SLOT PRICE";
$li_pocetna_cijenaslotavaluta = "USD";
$li_pocetna_naruci = "Order";
$li_bilten_naslov = "Newsletter";
$li_bilten_opis = "if you sign up to our newsletter you can get newest notifications about hosting...<br>You can disable our newsletter when ever you want!";


//Navigacija (donja) - Ako si logiran
$li_mojprofil = "MY PROFILE";
$li_vesti_nav = "News";
$li_serveri = "Servers";
$li_billing = "Billing";
$li_podrska = "Support";
$li_podesavanja = "Settings";
$li_iplog = "IP Logs";
$li_logout = "Logout";

//vesti
$li_dobrodosao = "Welcome to Gpanel";
//serveri
$li_naslov_serveri = "Servers";
$li_opis_serveri = "Server list";
$li_ime_servera = "Server name";
$li_vazi_do = "Expire";
$li_cena = "Price";
$li_ip_adresa = "IP address";
$li_slotovi = "Slots";
$li_status =  "Status";
//billing
$li_naslov_billing = "Billing";
$li_opis_billing = "List of your orders";
$li_vrsta_placanja = "Payment type";
$li_datum_narudzbe = "Order date";
$li_pregledava = "Employer: ";
$li_uplati_preko = "Pay from";
$li_nova_narudzba = "New order";
//podrska
$li_naslov_podrska = "Support";
$li_opis_podrska = "Welcome to GameHoster.biz Support center<br/>Here you can opet tickets id you have problem about your server.";
$li_idtiketa = "Ticket ID";
$li_imetiketa = "Ticket name";
$li_datum = "Date";
$li_server = "Server";
$li_broj_poruka = "No. messages";
$li_status = "Status";
$li_novitiket = "New ticket";
$li_arhiva = "Archive";
//podesavanja
$li_naslov_podesavanja = "Licni podaci";
$li_opis_podesavanja = "Ovde možete promeniti lične podatke!";
$li_poruka_podesavanja = "Kako bi pristupili opciji za editovanje vaših informacija potrebno je da ispravno unesete vaš pin kod!";
$li_otkljucaj = "Unlock";
//iplog
$li_naslov_iplog = "Logs";
$li_opis_logovi = "List of logs on this account";
$li_id_logovi = "ID";
$li_poruka_logovi = "Message";
$li_ip_logovi = "IP address";
$li_dav = "D&V";
//server
$li_server_server = "Server";
$li_server_podesavanje = "Settings";
$li_admini_slotovi = "Admins and slots";
$li_server_webftp = "WebFTP";
$li_server_plugini = "Plugins";
$li_server_modovi = "Mods";
$li_server_konzola ="Console";
$li_server_boost = "Boost";
$li_autorestart = "Autorestart";
$li_server_ime = "Server name";
$li_datum_isteka = "Expire date";
$li_server_igra = "Game";
$li_server_lokacija = "Location";
$li_podnozje = "GameHoster.biz is company for hosting game servers! It is founded 2012, so far we can boast of our success! Our servers are located in Germany! Currently we offer CS,SAMP,MC servers,also we are working to expand our market! Ping is around 20-50ms/s it depends on your internet provider.";
$li_podnozje_potpis = "All rights reserved.";
$li_podnozje_dizajner = "Designer";
$li_podnozje_informacije = "Informations";
$li_podnozje_prava = "Copyrights";
}elseif($lan == "de"){
	$li_pocetna = "Test";
	$li_gamepanel = "Gamep";
}
?>