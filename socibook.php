<?php
/*
 Plugin Name: SociBook
 Plugin URI: http://sozial-bookmark.phpwelt.net/wordpress-plugin.html
 Description: Adds sozial bookmarking links to your blog.
 Version: 0.85
 Author: Erik Sefkow
 Author URI: http://www.phpwelt.net/
 */
/*
 **		Copyright 2008 Erik Sefkow http://www.phpwelt.net
 **
 **    This program is free software; you can redistribute it and/or modify
 **    it under the terms of the GNU General Public License as published by
 **    the Free Software Foundation; either version 2 of the License, or
 **    (at your option) any later version.
 **
 **    This program is distributed in the hope that it will be useful,
 **    but WITHOUT ANY WARRANTY; without even the implied warranty of
 **    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 **    GNU General Public License for more details.
 **
 **    You should have received a copy of the GNU General Public License
 **    along with this program; if not, write to the Free Software
 **    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('Get it here: <a title="Socibook, socialbookmark wordpress plugin" href="http://sozial-bookmark.phpwelt.net/wordpress-plugin.html">Socibook, socialbookmark wordpress plugin</a>'); }
$phpweltsozial_version="0.85";
$phpweltsozial_langfile="socibook";
load_plugin_textdomain($phpweltsozial_langfile,$path='wp-content/plugins/'. basename(dirname(__FILE__)).'/');





function phpweltsozial_rohloader(){

	if(get_option("socibookcachetime")===false) {
		add_option('socibookcachetime',	time(),	'',	'yes');
		add_option('socibookcache',	phpweltsozial_get_url("http://sozial-bookmark.phpwelt.net/design_1.js"),	'',	'yes');
	}elseif(get_option("socibookcachetime")<time()-86400){
		update_option('socibookcachetime',	time());
		update_option('socibookcache',	phpweltsozial_get_url("http://sozial-bookmark.phpwelt.net/design_1.js"));
	}
	$ee=get_option("socibookcache");
	if($ee=="")$ee='<script language="JavaScript" type="text/javascript" src="http://sozial-bookmark.phpwelt.net/design_1.js"></script>';
	else $ee='<script type="text/javascript">
	<!--
	'.$ee.'
	-->
	 	</script>';

	return('
	 	<script type="text/javascript"><!--
	 	
	 	service="'.phpweltsozial_getopt("phpweltbookmarkservices").'";
	 	url= "'.get_permalink().'";
	 	titel="'.the_title('', '', false).'";
	 	siteopener="'.phpweltsozial_getopt("phpweltopenas").'";
	 	picpath  = "'.phpweltsozial_getopt("phpwelticonset").'";
	 	picendung = "'.phpweltsozial_getopt("phpwelticonsetendung").'";
	 	-->
	 	</script>
	 	'.$ee.'
	 	<noscript><a href="http://sozial-bookmark.phpwelt.net/bookmark.html"><img border="0" src="http://sozial-bookmark.phpwelt.net/alternativ-3.png" /></a></noscript>');
}


function phpweltsozial_loader($out){
	global $wong_buttons,$post;

	//Wenn Button aktiviert und man sich nicht im Adminbereich befindet
	if( !is_admin()){


		$out .= " | ".phpweltsozial_rohloader();
	}
	return $out;
}

add_filter('the_category', 'phpweltsozial_loader');
// Display social_bar in the_excerpt
add_filter('the_excerpt', 'phpweltsozial_loader');
// Display social_bar in the_actions
add_filter('comment_post', 'phpweltsozial_loader');


add_action('admin_menu',  'phpweltsozial_admin_menu');





function phpweltsozial_getopt($name){
	if(get_option($name)===false) {
		add_option('phpweltbookmarkservices',	"technorati.com icio.us mister-wong.de digg.com google yahoo bkmrk.de",	'PHPWelt.net Bookmark Services',	'yes');
		add_option('phpweltopenas',	"2",	'PHPWelt.net Bookmark Services2',	'yes');
		add_option('phpwelticonset',	"",	'PHPWelt.net Bookmark Services3',	'yes');
		add_option('phpwelticonsetendung',	"",	'PHPWelt.net Bookmark Services4',	'yes');
	}
	return get_option($name);
}

//get_option

//update_option('phpweltbookmarkservices',	$set_form['wong_button_design']);


// Manage Admin Options
function phpweltsozial_admin_menu()
{
	// Add admin page to the Options Tab of the admin section

	if ( function_exists('add_submenu_page') )
	add_submenu_page('plugins.php', __('SociBook'), __('SociBook'), 1, __FILE__, 'phpweltsozial_plugin_options');
	else  add_options_page('SociBook', 'SociBook', 8, __FILE__, 'phpweltsozial_plugin_options');
	// Check if the options exists on the database and add them if not
}

// Admin page
function phpweltsozial_plugin_options()
{
	global $phpweltsozial_langfile;

	if(isset($_POST['textarea'])){
		phpweltsozial_conf_save();
	}
	echo '<script type="text/javascript"><!--
	
	 	function set(name){
	 	service=document.getElementById("votet").value;
	 	service=service.split(" ");
	 	for (var i=0; i<service.length; i++)
		{
			if(service[i]==name){
				Check = confirm("'.__("The service \"+name+\" is already in your list. Add it again?",$phpweltsozial_langfile).'");
				if(Check == false)return;
				break;
			}
		}
	 	document.getElementById("votet").value=document.getElementById("votet").value+ " " +name;
	 	}
	 	-->
	 	</script>';


	print('<div class="wrap">');
	print('<h2>'.__("Sozial Bookmark Services",$phpweltsozial_langfile).'</h2>
	<b>'.__("General Settings",$phpweltsozial_langfile).'</b>');
	echo '<form action="" method="post" id="my_fieldset">'.__("Open...",$phpweltsozial_langfile).'<br />
<input name="openas" type="radio" value="1" '.phpweltsozial_desicion("phpweltbookmarkservices",1).' />'.__("as popup",$phpweltsozial_langfile).'
<input name="openas" type="radio" value="2" '.phpweltsozial_desicion("phpweltbookmarkservices",2).' />'.__("in the same window",$phpweltsozial_langfile).'
<input name="openas" type="radio" value="3" '.phpweltsozial_desicion("phpweltbookmarkservices",3).' />'.__("in a new window",$phpweltsozial_langfile).'<br><br>

<label>'.__("Path to spezific iconset:",$phpweltsozial_langfile).' <input name="phpwelticonset" type="text" value="'.phpweltsozial_getopt("phpwelticonset").'" /></label> '.__("(leave blank, use standarticons. End given path with \"/\" )",$phpweltsozial_langfile).'<br>
<label>'.__("(leave blank, use standarticons. End given path with \"/\" )",$phpweltsozial_langfile).' <input name="phpwelticonsetendung" size="5" type="text" value="'.phpweltsozial_getopt("phpwelticonsetendung").'" /></label> '.__("(input without leading dot, e.g.: \"gif\")",$phpweltsozial_langfile).'<br>
'.__("The images, in the specified directory must have the same name, like the selected services.",$phpweltsozial_langfile).'
';
	echo '<br><br><b>'.__("Choice of Services",$phpweltsozial_langfile).'</b><br><textarea id="votet" name="textarea" cols="90" rows="10">'.phpweltsozial_getopt("phpweltbookmarkservices").'</textarea><br><input name="submit" type="submit" value="'.__("save",$phpweltsozial_langfile).'"></form><br><br>';
	echo '<b>'.__("Bookmarkserices currently available (click to add):",$phpweltsozial_langfile).'</b><br>';

	echo '<table width="100%" border="0">';

	$i=0;

	foreach (phpweltsozial_listbookmarks() as $v){
		if($i%5==0)echo "<tr>";
		echo "<td width='20%'><img src=\"http://img.phpwelt.net/sozial/$v.gif\" \>&nbsp;<a href=\"#\" onclick=\"set('$v');return false;\">$v</a></td>";
		$i++;
		if($i%5==0)echo "</tr>";
	}
	for($ii=$i;($ii%5)!=0;$ii++)echo "<td>&nbsp</td>";
	if($i%5!=0)echo "</tr>";
	echo '</table>'.__("This list, keep it self up to date, send me an email to  <a href=\"mailto:sozialbookmarkservices@phpwelt.net\">sozialbookmarkservices@phpwelt.net</a>, if you know more sozical bookmarking service. Sorry, for my worse english :-(",$phpweltsozial_langfile).'';
	print('</div>');
}

function phpweltsozial_desicion($name,$id){
	if(phpweltsozial_getopt("phpweltopenas")==$id)return "checked";
	else return "";


}



function phpweltsozial_listbookmarks(){
	global $phpweltsozial_version;
	$text= phpweltsozial_get_url("http://sozial-bookmark.phpwelt.net/wordpress-plugin/verfuegbar.php?v=".$phpweltsozial_version."&justforstat=".$_SERVER["HTTP_HOST"]);
	$text=(explode(chr(10),$text));
	sort($text);
	if($text[0]=="")unset($text[0]);
	return $text;
}

function phpweltsozial_get_url($url)	{

	if (function_exists('file_get_contents')) $file = @file_get_contents($url);
	if($file==""){
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$file = curl_exec($curl);
		curl_close($curl);
	}

	return $file;
}
function phpweltsozial_conf_save(){
	update_option('phpweltbookmarkservices',	$_POST['textarea']);
	update_option('phpweltopenas',	$_POST['openas']);
	update_option('phpwelticonset',	$_POST['phpwelticonset']);
	update_option('phpwelticonsetendung',	$_POST['phpwelticonsetendung']);
	update_option('socibookcachetime',	1);

}

?>
