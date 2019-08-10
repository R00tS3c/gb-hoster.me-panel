var GAME_DATA = {
   "1":[
      {
         "id":"1",
         "name":"Counter-Strike 1.6",
         "min":"12",
         "max":"32",
         "loc":"1",
         "loc_name":"",
         "lite":0.375,
         "premium":0.5
      }
   ],
   "2":[
      {
         "id":"2",
         "name":"SAMP",
         "min":"50",
         "max":"500",
         "loc":"1",
         "loc_name":"",
         "lite":0.04,
         "premium":0.06
      }
   ],
   "3":[
      {
         "id":"3",
         "name":"Minecraft",
         "min":"1",
         "max":"15",
         "loc":"1",
         "loc_name":"",
         "lite":3,
         "premium":4
      }
   ],
   "4":[
      {
         "id":"4",
         "name":"TeamSpeak 3",
         "min":"5",
         "max":"500",
         "loc":"4",
         "loc_name":"",
         "lite":0.05,
         "premium":0.1
      }
   ],
   "5":[
      {
         "id":"5",
         "name":"FastDL",
         "min":"1",
         "max":"1",
         "loc":"4",
         "loc_name":"",
         "lite":2,
      }
   ],
   "6":[
      {
         "id":"6",
         "name":"Sinus Bot",
         "min":"2",
         "max":"18",
         "loc":"4",
         "loc_name":"",
         "lite":2,
         "premium":4
      }
   ]

}
jq = jQuery.noConflict();

function buildList(key){
	var game = GAME_DATA[key][0];
	var slotList = jq('.slotList');
	var locList = jq('.locList');
	slotList.empty();

	for(var i = parseInt(game['min'], 10); i <= parseInt(game['max'], 10); i++){
		slotList.append(jq('<option>').text(i).val(i));
	}
	
	locList.empty();
	for(var l = 0; l < GAME_DATA[key].length; l++){
		locList.append(jq('<option>').text(GAME_DATA[key][l]['loc_name']).val(l));
	}
	
	locationChange();
}

function slottobot() {
  	var str = document.getElementById("calc").innerHTML; 
  	var res = str.replace("Slotovi", "Botovi");
  	document.getElementById("calc").innerHTML = res;
}
function locationChange(){
	var gameList = jq('.gameList');
	var typeList = jq('.typeList');
	var locList = jq('.locList');
	
	var key = gameList.val();
	var game = GAME_DATA[key][locList.val()];		
	
	typeList.empty();	
	
	if(parseFloat(game['lite']) > 0.0){
		typeList.append(jq('<option>').text('Lite').val(2));	
	}	
	
	if(parseFloat(game['premium']) > 0.0){
		typeList.append(jq('<option>').text('Premium').val(1));		
	}

	if(gameList.val()==6)
	{
	slottobot();
	}
	updatePrice();
}

function updatePrice(){
	var gameList = jq('.gameList');
	var typeList = jq('.typeList');
	var locList = jq('.locList');
	var slotList = jq('.slotList');
	
	var key = gameList.val();	
	var game = GAME_DATA[key][locList.val()];
	
	var slot_price = (typeList.val() == 1)? parseFloat(game['premium']) : parseFloat(game['lite']);
	
	var price = Math.ceil(slot_price * slotList.val() * 100) / 100;
	
	jq('#calc a').text(price.toFixed(2) + ' EUR');
	
}

jq(document).ready(function(){
	var gameList = jq('.gameList');
	var typeList = jq('.typeList');
	var locList = jq('.locList');
	var slotList = jq('.slotList');
	var firstKey = null;
	for(var key in GAME_DATA){
		if(firstKey == null) firstKey = key;
		var value = GAME_DATA[key][0];
		gameList.append(jq('<option>').text(value['name']).val(key));
	}	
	

	gameList.change(function(){
		var key = this.value;
		buildList(key);
	});
	
	locList.change(locationChange);
	
	typeList.change(updatePrice);
	slotList.change(updatePrice);
	
	
	buildList(firstKey);
});