<?php
$konekcija = mysql_connect(HOST, DBUSER, DBPASS);
if (!$konekcija)
{
	exit("<html><head></head><body><b>Greska!!!</b><br />Ne mogu se spojiti sa bazom!</body></html>");
}

$db_konekcija = mysql_select_db(DBNAME);
if (!$db_konekcija)
{
	exit("<html><head></head><body><b>Greska!!!</b><br />Ne mogu se spojiti sa bazom!</body></html>");
}

query_basic("SET names UTF8");																																																																																																																															

/**
 * query_basic -- mysql_query
 *
 * Uzima se za INSERT INTO i UPDATE.
 *
 * Nema return.
 */
 
function query_basic($query)
{
	$result = mysql_query($query);
	if ($result == FALSE)
	{	
		$greska = mysql_real_escape_string(mysql_error());
		mysql_query("INSERT INTO error_log (number, string, file, line, datum, vrsta) 
					VALUES ('1', 
							'{$greska}', 
							'mysql_greska', 
							'mysql_greska',
							'".time()."',
							'2')
					") or die(mysql_error());
	}
}

/**
 * query_basic -- mysql_query + mysql_num_rows
 *
 * Return broj kolona.
 */
function query_numrows($query)
{
	$result = mysql_query($query);
	if ($result == FALSE)
	{
		$greska = mysql_real_escape_string(mysql_error());
		mysql_query("INSERT INTO error_log (number, string, file, line, datum, vrsta) 
					VALUES ('1', 
							'{$greska}', 
							'mysql_greska', 
							'mysql_greska',
							'".time()."',
							'2')
					") or die(mysql_error());
	}
	return (mysql_num_rows($result));
}

/**
 * query_fetch_assoc -- mysql_query + mysql_fetch_assoc
 */
function query_fetch_assoc($query)
{
	$result = mysql_query($query);
	if ($result == FALSE)
	{
		$greska = mysql_real_escape_string(mysql_error());
		mysql_query("INSERT INTO error_log (number, string, file, line, datum, vrsta) 
					VALUES ('1', 
							'{$greska}', 
							'mysql_greska', 
							'mysql_greska',
							'".time()."',
							'2')
					") or die(mysql_error());
	}
	return (mysql_fetch_assoc($result));
}
?>
