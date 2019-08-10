<?php
$_mysql_connect = NULL;
if(!function_exists('mysql_connect'))
{
	function mysql_connect($server, $user, $password){
		global $_mysql_connect;
		$_mysql_connect = mysqli_connect($server, $user, $password);
		return $_mysql_connect;
	}
}
if(!function_exists('mysql_select_db'))
{
	function mysql_select_db($db){
		global $_mysql_connect;
		return mysqli_select_db($_mysql_connect, $db);
	}
}
if(!function_exists('mysql_query'))
{
	function mysql_query($query){
		global $_mysql_connect;
		return mysqli_query($_mysql_connect, $query);
	}
}
if(!function_exists('mysql_fetch_object'))
{
	function mysql_fetch_object($result){
		return mysqli_fetch_object($result);
	}
}
if(!function_exists('mysql_fetch_array'))
{
	function mysql_fetch_array($result){
		return mysqli_fetch_array($result);
	}
}
if(!function_exists('mysql_fetch_row'))
{
	function mysql_fetch_row($result){
		return mysqli_fetch_row($result);
	}
}
if(!function_exists('mysql_num_rows'))
{
	function mysql_num_rows($result){
		return mysqli_num_rows($result);
	}
}
if(!function_exists('mysql_set_charset'))
{
	function mysql_set_charset($charset){
		global $_mysql_connect;
		return mysqli_set_charset($_mysql_connect, $charset);
	}
}
if(!function_exists('mysql_real_escape_string'))
{
	function mysql_real_escape_string($string){
		global $_mysql_connect;
		return mysqli_real_escape_string($_mysql_connect, $string);
	}
}
if(!function_exists('mysql_insert_id'))
{
	function mysql_insert_id() {
		global $_mysql_connect;
		return mysqli_insert_id($_mysql_connect);
	}
}
if(!function_exists('mysql_data_seek'))
{
	function ($db_query, $row_number=0) {
		return mysqli_data_seek($db_query, $row_number);
	}
}
if(!function_exists('mysql_fetch_field'))
{
	function mysql_fetch_field($results) {
		return mysqli_fetch_field($results);
	}
}
if(!function_exists('mysql_free_result'))
{
	function mysql_free_result($results) {
		return mysqli_free_result($results);
	}
}
if(!function_exists('mysql_affected_rows'))
{
	function mysql_affected_rows() {
		global $_mysql_connect;
		return mysqli_affected_rows($_mysql_connect);
	}
}
if(!function_exists('mysql_get_server_info'))
{
	function mysql_get_server_info() {
		global $_mysql_connect;
		return mysqli_get_server_info($_mysql_connect);
	}
}
if(!function_exists('mysql_close'))
{
	function mysql_close($_mysql_connect) {
		return mysqli_close($_mysql_connect);
	}
}