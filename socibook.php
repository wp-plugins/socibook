<?php
/*
Plugin Name: SociBook
Plugin URI: http://sozial-bookmark.phpwelt.net/wordpress-plugin.html
Description: Adds sozial bookmarking links to your blog.
Version: 0.8
Author: Erik Sefkow
Author URI: http://www.phpwelt.net/
*/
 
switch( WPLANG )
{
	case 'de_DE': 	$phpweltsozial_l = 0; $domain = 'de'; 	break;
	default: 	$phpweltsozial_l = 1; $domain = 'com'; 	break;
}

$phpweltsozial_lang = Array(

'u1' => Array(
			'Sozial Bookmark Services',
			'Social bookmarking service',
		),

		
		
't1' => Array(
			'Allgmeine Einstellungen',
			'main settings',
		),
't2_1' => Array(
			'&Ouml;ffnen ...',
			'Open ...',
		),		
't2_2' => Array(
			'als Popup',
			'as popup',
		),		 
't2_3' => Array(
			'im selben Fenster',
			'in same window',
		),		
't2_4' => Array(
			'in einem neuen Fenster',
			'as new window',
		),
't3' => Array(
			'Wahl der Services',
			'Service selection',
		),
't4' => Array(
			'Aktuell verf&uuml;gbare Bookmarkserices (draufklicken um sie hinzuzuf&uuml;gen):',
			'available bookmarkingservices (klick to add)',
		),		
't5' => Array(
			'Diese Liste h&auml;lt sich selbst st&auml;ndig aktuell. Du kennst noch weitere Social Bookmarking Services, dann schicke mir eine Email an <a href="mailto:sozialbookmarkservices@phpwelt.net">sozialbookmarkservices@phpwelt.net</a>! F&uuml;r Fehlermeldungen und Ideen an die selbige Emailaddrese bin ich dankbar!',
			'This list, keep it self aktuell, Send me an email to  <a href="mailto:sozialbookmarkservices@phpwelt.net">sozialbookmarkservices@phpwelt.net</a>, if you know more sozical bookmarking service. Sorry, for my worse english :-(',
		),	
't6' => Array(
			'Speichern',
			'save',
		),	
't7' => Array(
			'(frei lassen, f&uuml;r Standarticons. Angegeben Pfad mit "/" abschliessen)',
			'(leave blank, use standarticons. End given path with "/" )',
		),	
't8' => Array(
			'Pfad zu eigenen Iconset:',
			'Path to spezific iconset:',
		),	
				
't9' => Array(
			'Wenn du ein eigenes Iconset nutzt, welche Endung haben die Bilddateien: ',
			'(leave blank, use standarticons. End given path with "/" )',
		),	
't10' => Array(
			'(Eingabe ohne f&uuml;hrenden Punkt, also z.B.: "gif")',
			'(input without leading dot, e.g.: "gif")',
		),	
't11' => Array(
		'Die Bilddateien, in dem angegeben Verzeichniss müssen den selben Namen, wie die ausgew&auml;hlen Services tragen.',
		'If you want to use your own, icons, they must named like the names of the selected bookmarkingservices',
),						
);

		
		
		
function phpweltsozial_rohloader(){
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
	 	<script language="JavaScript" type="text/javascript" src="http://sozial-bookmark.phpwelt.net/design_1.js"></script>
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
		add_option('phpweltbookmarkservices',	"technorati.com icio.us mister-wong.de digg.com google yahoo yigg.de",	'PHPWelt.net Bookmark Services',	'yes');
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
	global $phpweltsozial_lang, $phpweltsozial_l;
	
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
				Check = confirm("Der Service "+name+" ist schon in der Liste enthalten. Nochmal hinzufuegen?");
				if(Check == false)return;
				break;
			}
		}
	 	document.getElementById("votet").value=document.getElementById("votet").value+ " " +name;
	 	}
	 	-->
	 	</script>';


	print('<div class="wrap">');
	print('<h2>'.$phpweltsozial_lang['u1'][$phpweltsozial_l].'</h2>
	<b>'.$phpweltsozial_lang['t1'][$phpweltsozial_l].'</b>');
	echo '<form action="" method="post" id="my_fieldset">'.$phpweltsozial_lang['t2_1'][$phpweltsozial_l].'<br />
<input name="openas" type="radio" value="1" '.phpweltsozial_desicion("phpweltbookmarkservices",1).' />'.$phpweltsozial_lang['t2_2'][$phpweltsozial_l].'
<input name="openas" type="radio" value="2" '.phpweltsozial_desicion("phpweltbookmarkservices",2).' />'.$phpweltsozial_lang['t2_3'][$phpweltsozial_l].'
<input name="openas" type="radio" value="3" '.phpweltsozial_desicion("phpweltbookmarkservices",3).' />'.$phpweltsozial_lang['t2_4'][$phpweltsozial_l].'<br><br>

<label>'.$phpweltsozial_lang['t8'][$phpweltsozial_l].' <input name="phpwelticonset" type="text" value="'.phpweltsozial_getopt("phpwelticonset").'" /></label> '.$phpweltsozial_lang['t7'][$phpweltsozial_l].'<br>
<label>'.$phpweltsozial_lang['t9'][$phpweltsozial_l].' <input name="phpwelticonsetendung" size="5" type="text" value="'.phpweltsozial_getopt("phpwelticonsetendung").'" /></label> '.$phpweltsozial_lang['t10'][$phpweltsozial_l].'<br>
'.$phpweltsozial_lang['t11'][$phpweltsozial_l].'
';
	echo '<br><br><b>'.$phpweltsozial_lang['t3'][$phpweltsozial_l].'</b><br><textarea id="votet" name="textarea" cols="90" rows="10">'.phpweltsozial_getopt("phpweltbookmarkservices").'</textarea><br><input name="submit" type="submit" value="'.$phpweltsozial_lang['t6'][$phpweltsozial_l].'"></form><br><br>';
	echo '<b>'.$phpweltsozial_lang['t4'][$phpweltsozial_l].'</b><br>';

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
	 echo '</table>'.$phpweltsozial_lang['t5'][$phpweltsozial_l].'';
	print('</div>');
}

function phpweltsozial_desicion($name,$id){
	if(phpweltsozial_getopt("phpweltopenas")==$id)return "checked";
	else return "";
	
	
}



function phpweltsozial_listbookmarks(){
	$text= phpweltsozial_get_url("http://sozial-bookmark.phpwelt.net/wordpress-plugin/verfuegbar.php");

	$text=(explode(chr(10),$text));
	sort($text);

	return $text;

}

function phpweltsozial_get_url($url)	{

	if (function_exists('file_get_contents')) {
		$file = file_get_contents($url);
	} else {
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
	
}

?>
