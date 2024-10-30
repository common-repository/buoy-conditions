<?php
/*
Plugin Name: BuoyAlerts WP Buoy Plugin
Plugin URI: http://www.buoyalerts.com/
Description: Display Swell Size, Swell Direction, Wind Speed, Wind Direction, Water Temperature, Air Temperature and more from any Buoy available on Buoyalerts.com on your wordpress menu 
Author: Buoy Alerts
Version: 1.0.3
Author URI: http://www.buoyalerts.com/wordpress_buoy_plugin/
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DECX4QFHCV8C8
Tags: navigation, buoys, sidebar, surf, surfing, weather, sports
Requires at least: 2.0.2
Tested up to: 3.3
Stable tag: 1.0.3



*/

define('BA_BUOY_PLUGIN_VERSION', '1.0.3');



// Specify Hooks/Filters
register_activation_hook(__FILE__, 'add_defaults_fn');
add_action('admin_init', 'buoyalerts_init_fn' );
add_action('admin_menu', 'buoyalerts_add_page_fn');


$default_ba_style_css = "

.ba_data, .ba_label {
	padding-top:.2em;
}

.ba_data {
	float:right;
	text-align:left;
	width:40%;
	font-size:.9em;
	font-weight:bolder;
}
.ba_label 
{
	margin-left:.5em;
	float: left;
	font-size:.9em
}
.ba_footer{
	clear:both;
	text-align:left;
	font-size:.8em;
	padding-top:.5em;
	padding-bottom:1em;
	visibility:visible;
	
}";





// Define default option settings
function add_defaults_fn() {
	$default_settings = get_option('plugin_options');

    if((!is_array($default_settings))) 
    {
		$default_settings = array(
									 "ba_style_css"=>$default_ba_style_css,
									 "buoy_id" => "46026",
									 "display_swell_height" => "on",
									 "swell_height_units" => "F",
									 "display_swell_interval" => "on", 
									 "display_wind_speed" => "on", 
									 "wind_speed_units" => "K", 
									 "display_wind_direction"=>"on",								 
									 "wind_direction_units" => "C", 
									 "display_water_temp" => "on", 
									 "water_temp_units"=>"F",								 
									 "display_air_temp" => "on", 
									 "display_water_temp" => "F", 

								 
								 );
		update_option('plugin_options', $default_settings);
	}




}

// Register our settings. Add the settings section, and settings fields


function buoyalerts_init_fn(){
	
	if(!function_exists('curl_init') || !function_exists('curl_exec')){
		add_settings_error('plugin_options', 'some_value', '<h3>This Plugin requires LibCurl support compiled into php. </h3><p>The function curl_init or curl_exec is not supported by your eversion of php</p>', 'error');
	}

	register_setting('plugin_options', 'plugin_options', 'plugin_options_validate' );


	add_settings_section('main_section', 'Main Settings', 'section_text_fn', __FILE__);

	add_settings_field('buoy_id', 'Buoy ID <br><font size="-2">(numeric identifier only, see  <a href="http://buoyalerts.com/buoys/" target="_blank">this</a> link. Valid Examples are 071, 46026, 028, etc.</font>)', 'setting_buoy_id_fn', __FILE__, 'main_section');


	//swell
	add_settings_field('display_swell_height', 'Display Swell Height', 'setting_display_swell_height_fn', __FILE__, 'main_section');
	add_settings_field('swell_height_units', 'Display Swell In', 'setting_display_swell_height_units_fn', __FILE__, 'main_section');
	add_settings_field('display_swell_interval', 'Display Swell Interval', 'setting_display_swell_interval_fn', __FILE__, 'main_section');

	//wind
	add_settings_field('display_wind_speed', 'Display Wind Speed', 'setting_display_wind_speed_fn', __FILE__, 'main_section');
	add_settings_field('wind_speed_units', 'Display Wind In', 'setting_display_wind_speed_units_fn', __FILE__, 'main_section');
	add_settings_field('display_wind_direction', 'Display Wind Direction', 'setting_display_wind_direction_fn', __FILE__, 'main_section');
	add_settings_field('wind_direction_units', 'Display Wind As', 'setting_display_wind_direction_units_fn', __FILE__, 'main_section');


	//water temp
	add_settings_field('display_water_temp', 'Display Water Temperature', 'setting_display_water_temp_fn', __FILE__, 'main_section');
	add_settings_field('water_temp_units', 'Display Water Temperature As', 'setting_display_water_temp_units_fn', __FILE__, 'main_section');

	//air temp
	add_settings_field('display_air_temp', 'Display Air Temperature', 'setting_display_air_temp_fn', __FILE__, 'main_section');
	add_settings_field('air_temp_units', 'Display Air Temperature As', 'setting_display_air_temp_units_fn', __FILE__, 'main_section');


	//	//add gusts ?
	//add_settings_field('display_swell_interval', 'Display Swell Interval', 'setting_display_swell_interval_fn', __FILE__, 'main_section');

	add_settings_field('display_swell_height', 'Display Swell Height', 'setting_display_swell_height_fn', __FILE__, 'main_section');

	//default style		
	add_settings_field('ba_style_css', 'CSS For Buoy Widget', 'setting_buoy_style_fn', __FILE__, 'main_section');



}

// Add sub page to the Settings Menu
function buoyalerts_add_page_fn() {
	add_options_page('BuoyAlerts Buoy Configuration Page', 'Buoy Conditions', 'administrator', __FILE__, 'options_page_fn');
}

// ************************************************************************************************************

// Callback functions

/***** new code */

// TEXTBOX - Name: plugin_options[buoy_id]
function setting_buoy_id_fn() {
	$options = get_option('plugin_options');
	
	echo "<input id='buoy_id' name='plugin_options[buoy_id]' size='5' type='text' value='{$options['buoy_id']}' />";
}

function setting_buoy_style_fn() {
	$options = get_option('plugin_options');
	echo "<textarea rows='10' cols='20' id='ba_style_css' name='plugin_options[ba_style_css]'>{$options['ba_style_css']}</textarea>";
}



// Display Swell Heigh - Name: plugin_options[display_swell_height]
function setting_display_swell_height_fn() {
	$options = get_option('plugin_options');
	if(!array_key_exists('display_swell_height', $options))
	{
		//echo '<pre>'.var_dump($options);
		//$options['display_swell_height'] = true;
	}
	if($options['display_swell_height']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='display_swell_height' name='plugin_options[display_swell_height]' type='checkbox' />";
}

// DROP-DOWN-BOX - Name: plugin_options[dropdown1]
function  setting_display_swell_height_units_fn() {
	$options = get_option('plugin_options');
	$items = array(
					"Meters" => "M", 
					"Feet"=>"F",

				);
	echo "<select id='swell_height_units' name='plugin_options[swell_height_units]'>";
	foreach($items as $item => $value) {
		$selected = ($options['swell_height_units']==$value) ? 'selected="selected"' : '';
		echo "<option value='$value' $selected>$item</option>";
	}
	echo "</select>";
}


// Display Swell Interval - Name: plugin_options[display_swell_interval]
function setting_display_swell_interval_fn() {
	$options = get_option('plugin_options');
	if($options['display_swell_interval']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='display_swell_interval' name='plugin_options[display_swell_interval]' type='checkbox' />";
}



/* wind functions */
// Display Wind Speed - Name: plugin_options[display_wind_speed]
function setting_display_wind_speed_fn() {
	$options = get_option('plugin_options');
	if(!array_key_exists('display_wind_speed', $options))
	{
		//echo '<pre>'.var_dump($options);
		//$options['display_swell_height'] = true;
	}
	if($options['display_wind_speed']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='display_wind_speed' name='plugin_options[display_wind_speed]' type='checkbox' />";
}


// DROP-DOWN-BOX - Name: plugin_options[dropdown1]
function  setting_display_wind_speed_units_fn() {
	$options = get_option('plugin_options');
	$items = array(
					"Knots"=>"K",
					"Meters per Second" => "M" 

				);
	echo "<select id='wind_speed_units' name='plugin_options[wind_speed_units]'>";
	foreach($items as $item => $value) {
		$selected = ($options['wind_speed_units']==$value) ? 'selected="selected"' : '';
		echo "<option value='$value' $selected>$item</option>";
	}
	echo "</select>";
}

// Display Wind Direction - Name: plugin_options[display_wind_direction]
function setting_display_wind_direction_fn() {
	$options = get_option('plugin_options');
	if(!array_key_exists('display_wind_direction', $options))
	{
		//echo '<pre>'.var_dump($options);
		//$options['display_swell_height'] = true;
	}
	if($options['display_wind_direction']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='display_wind_direction' name='plugin_options[display_wind_direction]' type='checkbox' />";
}


// DROP-DOWN-BOX - Name: plugin_options[dropdown1]
function  setting_display_wind_direction_units_fn() {
	$options = get_option('plugin_options');
	$items = array(
					"Degrees (Ex: 330°)"=>"D",
					"Compass Direction (Ex: NNW)" => "C" 

				);
	echo "<select id='wind_direction_units' name='plugin_options[wind_direction_units]'>";
	foreach($items as $item => $value) {
		$selected = ($options['wind_direction_units']==$value) ? 'selected="selected"' : '';
		echo "<option value='$value' $selected>$item</option>";
	}
	echo "</select>";
}

/* water temp */

// Display Wind Direction - Name: plugin_options[display_wind_direction]
function setting_display_water_temp_fn() {
	$options = get_option('plugin_options');
	if(!array_key_exists('display_water_temp', $options))
	{
		//echo '<pre>'.var_dump($options);
		//$options['display_swell_height'] = true;
	}
	if($options['display_water_temp']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='display_water_temp' name='plugin_options[display_water_temp]' type='checkbox' />";
}


// DROP-DOWN-BOX - Name: plugin_options[dropdown1]
function  setting_display_water_temp_units_fn() {
	$options = get_option('plugin_options');
	$items = array(
					"Fahrenheit (Ex: 65 °F)" =>"F",
					"Celcius  (Ex: 18.3 °C)" => "C" 

				);
	echo "<select id='water_temp_units' name='plugin_options[water_temp_units]'>";
	foreach($items as $item => $value) {
		$selected = ($options['water_temp_units']==$value) ? 'selected="selected"' : '';
		echo "<option value='$value' $selected>$item</option>";
	}
	echo "</select>";
}



/* air temp */

// Display Wind Direction - Name: plugin_options[display_wind_direction]
function setting_display_air_temp_fn() {
	$options = get_option('plugin_options');
	if(!array_key_exists('display_air_temp', $options))
	{
		//echo '<pre>'.var_dump($options);
		//$options['display_swell_height'] = true;
	}
	if($options['display_air_temp']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='display_air_temp' name='plugin_options[display_air_temp]' type='checkbox' />";
}


// DROP-DOWN-BOX - Name: plugin_options[dropdown1]
function  setting_display_air_temp_units_fn() {
	$options = get_option('plugin_options');
	$items = array(
					"Fahrenheit (Ex: 80 °F)" =>"F",
					"Celcius  (Ex: 26.6 °C)" => "C" 

				);
	echo "<select id='air_temp_units' name='plugin_options[air_temp_units]'>";
	foreach($items as $item => $value) {
		$selected = ($options['air_temp_units']==$value) ? 'selected="selected"' : '';
		echo "<option value='$value' $selected>$item</option>";
	}
	echo "</select>";
}





/***** end new code */


// Section HTML, displayed before the first option
function  section_text_fn() {
	add_settings_error('plugin_options', 'xxxx', 'my error message');

	//add_settings_error('buoy alerts required', '400', 'LIb Curl Support is required ', 'error'); 
	echo '<p>If you need assistance, please feel free to email us at <a href="mailto:wp-plugin-support@buoyalerts.com">wp-plugin-support@buoyalerts.com</a>. </p>';
}

// DROP-DOWN-BOX - Name: plugin_options[dropdown1]
function  wind_units_dropdown_fn() {
	$options = get_option('plugin_options');
	$items = array(
					"Meters" => "M", 
					"Knots"=>"K", 
					"Do Not Display"=>"DND"
				);
	echo "<select id='wind_units' name='plugin_options[wind_units]'>";
	foreach($items as $item => $value) {
		$selected = ($options['wind_units']==$value) ? 'selected="selected"' : '';
		echo "<option value='$value' $selected>$item</option>";
	}
	echo "</select>";
}


// DROP-DOWN-BOX - Name: plugin_options[dropdown1]
function  setting_dropdown_fn() {
	$options = get_option('plugin_options');
	$items = array("Red", "Green", "Blue", "Orange", "White", "Violet", "Yellow");
	echo "<select id='drop_down1' name='plugin_options[dropdown1]'>";
	foreach($items as $item) {
		$selected = ($options['dropdown1']==$item) ? 'selected="selected"' : '';
		echo "<option value='$item' $selected>$item</option>";
	}
	echo "</select>";
}

// TEXTAREA - Name: plugin_options[text_area]
function setting_textarea_fn() {
	$options = get_option('plugin_options');
	echo "<textarea id='plugin_textarea_string' name='plugin_options[text_area]' rows='7' cols='50' type='textarea'>{$options['text_area']}</textarea>";
}

// TEXTBOX - Name: plugin_options[text_string]
function setting_string_fn() {
	$options = get_option('plugin_options');
	echo "<input id='plugin_text_string' name='plugin_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
}

// PASSWORD-TEXTBOX - Name: plugin_options[pass_string]
function setting_pass_fn() {
	$options = get_option('plugin_options');
	echo "<input id='plugin_text_pass' name='plugin_options[pass_string]' size='40' type='password' value='{$options['pass_string']}' />";
}

// CHECKBOX - Name: plugin_options[chkbox1]
function setting_chk1_fn() {
	$options = get_option('plugin_options');
	if($options['chkbox1']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='plugin_chk1' name='plugin_options[chkbox1]' type='checkbox' />";
}

// CHECKBOX - Name: plugin_options[chkbox2]
function setting_chk2_fn() {
	$options = get_option('plugin_options');
	if($options['chkbox2']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='plugin_chk2' name='plugin_options[chkbox2]' type='checkbox' />";
}







// RADIO-BUTTON - Name: plugin_options[option_set1]
function setting_radio_fn() {
	$options = get_option('plugin_options');
	$items = array("Square", "Triangle", "Circle");
	foreach($items as $item) {
		$checked = ($options['option_set1']==$item) ? ' checked="checked" ' : '';
		echo "<label><input ".$checked." value='$item' name='plugin_options[option_set1]' type='radio' /> $item</label><br />";
	}
}

// Display the admin options page
function options_page_fn() {
?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Buoy Conditions Options</h2>
		Please configure your display options for the <a href="http://buoyalerts.com/" target="_blank">buoyalerts.com</a> WordPress Buoy Plugin below.
		<form action="options.php" method="post">
		<?php settings_fields('plugin_options'); ?>
		<?php do_settings_sections(__FILE__); ?>
		<p class="submit">
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
		</p>
		</form>
	</div>
<?php
}

// Validate user data for some/all of your input fields
function plugin_options_validate($input) {
	// Check our textbox option field contains no HTML tags - if so strip them out
	$input['text_string'] =  wp_filter_nohtml_kses($input['text_string']);	
	return $input; // return validated input
}


/* code to display widget + buoy info */
if(isset($_SERVER['SERVER_NAME'])){
	
	$ba_hostname = $_SERVER['SERVER_NAME'];
	
} else {
	$ba_hostname = 'unknown';

}


define('BA_PLUGIN_USER_AGENT', 'BA-WP-Buoy-Plugin/'.BA_BUOY_PLUGIN_VERSION.' (+'.$ba_hostname.')');
define('BA_BASE_REQUEST_URI', 'http://www.buoyalerts.com/wp_plugin/buoy/');
define('BA_BASE_LINK_URI', 'http://www.buoyalerts.com/buoy/');
//seconds to keep data cached (defaults to 15 minutes, do not use less than 15 minutes or 900 for this value)
define('BA_CACHE_INTERVAL', 900);


function buoy_print(){
	$ba_options =  get_option('plugin_options');


	$buoy_data = ba_get_data($ba_options['buoy_id'], false);


    $ba_template = '<div id="buoyalerts_report">';
	$ba_template .= '<h3 class="widget-title">Buoy Conditions</h3>';
    $ba_template .='<a href="'.BA_BASE_LINK_URI.$buoy_data->buoy_id.'" target="_blank">'.$buoy_data->buoy_id.' '.$buoy_data->name.'</a>';


	if($ba_options['display_swell_height'] =='on'){
    	
    	if($ba_options['swell_height_units']=="F"){
    		$ba_template .='<div class="wave_height ba_label">Wave Height: </div><div class="wave_height ba_data">'.$buoy_data->waveHeightFeet.' feet</div>';
    	} else {
    		$ba_template .='<div class="wave_height ba_label">Wave Height: </div><div class="wave_height ba_data">'.$buoy_data->waveHeightMeters.' meters</div>';
    	}
    }	
	if($ba_options['display_swell_interval'] =='on'){
    	$ba_template .='<div class="wave_period ba_label">Wave Period: </div><div class="wave_period ba_data">'.$buoy_data->dominantWavePeriod.' seconds</div>';
    }
	if($ba_options['display_wind_speed'] =='on'){	
    	if($ba_options['wind_speed_units']=="K"){
		    $ba_template .='<div class="wind_speed ba_label">Wind Speed: </div><div class="wind_speed ba_data">'.$buoy_data->windSpeedKnots.' Kts</div>';
    	} else {
		    $ba_template .='<div class="wind_speed ba_label">Wind Speed: </div><div class="wind_speed ba_data">'.$buoy_data->windSpeedMeters.' Meters/sec</div>';
    	}
	}

	if($ba_options['display_wind_direction'] =='on'){	
    	if($ba_options['wind_direction_units']=="D"){
		    $ba_template .='<div class="wind_direction ba_label">Wind Direction: </div><div class="wind_direction ba_data">'.$buoy_data->windDirectionDegrees.'°</div>';
    	} else {
   			 $ba_template .='<div class="wind_direction ba_label">Wind Direction: </div><div class="wind_direction ba_data">'.$buoy_data->windDirectionCompass.'°</div>';
    	}
	}



	if($ba_options['display_water_temp'] =='on'){	
    	if($ba_options['water_temp_units']=="F"){
		    $water_temp = $buoy_data->waterTemperatureF.' °F';
    	} else {
		    $water_temp = $buoy_data->waterTemperatureC.' °C';
    	}
	
		    $ba_template .='<div class="water_temp ba_label">Water Temperature: </div><div class="water_temp ba_data">'.$water_temp.'</div>';
	
	}


	if($ba_options['display_air_temp'] =='on'){	
    	if($ba_options['air_temp_units']=="F"){
    		$air_temp = $buoy_data->airTemperatureF.' °F';
    	} else {
	    	$air_temp = $buoy_data->airTemperatureC.' °C';
    	}
    		$ba_template .='<div class="air_temp ba_label">Air Temperature: </div><div class="air_temp ba_data">'.$air_temp.'</div>';

	}





    $ba_template .= '</div>';

    echo $ba_template;
    return $ba_template;

}


	function form( $instance ) {

			echo '<p>'. sprintf( __('No menus have been created yet. <a href="%s">Create some</a>.'), admin_url('nav-menus.php') ) .'</p>';
			return;
	}




function ba_display_widget($args){
	$ba_template = '';
	$ba_options =  get_option('plugin_options');


	$buoy_data = ba_get_data($ba_options['buoy_id'], false);


    extract($args); // gives υѕ thе defaulting settings οf widgets

    echo $before_widget; // echos thе container fοr thе widget || obtained frοm $args
        echo $before_title."Current Buoy Conditions".$after_title; // echoes thе title οf thе widget || $before_title/$after_title obtained frοm $args
		//* code from ba */
		//$ba_template .= '<h3 class="widget-title">Buoy Conditions</h3>';
		$ba_template .='<style>/* ba css */';
		$ba_template .= $ba_options['ba_style_css'];
		$ba_template .='</style>';
		
	
		$ba_template .='<H3 class="ba_buoy_title"><a href="'.BA_BASE_LINK_URI.$buoy_data->buoy_id.'" target="_blank">'.$buoy_data->buoy_id.' '.$buoy_data->name.'</a></H3>';

	if($ba_options['display_swell_height'] =='on'){
    	
    	if($ba_options['swell_height_units']=="F"){
    		$ba_template .='<div class="wave_height ba_label">Wave Height: </div><div class="wave_height ba_data">'.$buoy_data->waveHeightFeet.' feet</div>';
    	} else {
    		$ba_template .='<div class="wave_height ba_label">Wave Height: </div><div class="wave_height ba_data">'.$buoy_data->waveHeightMeters.' meters</div>';
    	}
    }	
	if($ba_options['display_swell_interval'] =='on'){
    	$ba_template .='<div class="wave_period ba_label">Wave Period: </div><div class="wave_period ba_data">'.$buoy_data->dominantWavePeriod.' seconds</div>';
    }
	if($ba_options['display_wind_speed'] =='on'){	
    	if($ba_options['wind_speed_units']=="K"){
		    $ba_template .='<div class="wind_speed ba_label">Wind Speed: </div><div class="wind_speed ba_data">'.$buoy_data->windSpeedKnots.' Kts</div>';
    	} else {
		    $ba_template .='<div class="wind_speed ba_label">Wind Speed: </div><div class="wind_speed ba_data">'.$buoy_data->windSpeedMeters.' Meters/sec</div>';
    	}
	}

	if($ba_options['display_wind_direction'] =='on'){	
    	if($ba_options['wind_direction_units']=="D"){
		    $ba_template .='<div class="wind_direction ba_label">Wind Direction: </div><div class="wind_direction ba_data">'.$buoy_data->windDirectionDegrees.'°</div>';
    	} else {
   			 $ba_template .='<div class="wind_direction ba_label">Wind Direction: </div><div class="wind_direction ba_data">'.$buoy_data->windDirectionCompass.'</div>';
    	}
	}



	if($ba_options['display_water_temp'] =='on'){	
    	if($ba_options['water_temp_units']=="F"){
		    $water_temp = $buoy_data->waterTemperatureF.' °F';
    	} else {
		    $water_temp = $buoy_data->waterTemperatureC.' °C';
    	}
	
		    $ba_template .='<div class="water_temp ba_label">Water Temperature: </div><div class="water_temp ba_data">'.$water_temp.'</div>';
	
	}


	if($ba_options['display_air_temp'] =='on'){	
    	if($ba_options['air_temp_units']=="F"){
    		$air_temp = $buoy_data->airTemperatureF.' °F';
    	} else {
	    	$air_temp = $buoy_data->airTemperatureC.' °C';
    	}
    		$ba_template .='<div class="air_temp ba_label">Air Temperature: </div><div class="air_temp ba_data">'.$air_temp.'</div>';

	}

		
		$ba_template .='<div class="ba_footer"><a href="'.BA_BASE_LINK_URI.$buoy_data->buoy_id.'" target="_blank">[ graph ]</a></div>';
		
		echo $ba_template;
		
		/* end code from BA */




    echo $after_widget; // close thе container || obtained frοm $args
}
wp_register_sidebar_widget(
							'ba_wp_buoys', 
							'Buoy Alerts Widget', 
							'ba_display_widget',
							array(                  // options
        						'description' => 'Displays Buoy Conditions in the Sidebar. See the Settings -> Buoy Plugin Options page to configure additional settings'
    							
    						)
							
							);



function ba_get_data($buoy_id, $cache_override=false){

	$identifier_key = $buoy_id.'_observation_data';
	$buoy_data = false;

	if($cache_override === true ){
		delete_transient( $identifier_key );
	} else {	
		$buoy_data = get_transient( $identifier_key );
	}
	
	if ( false === ( $buoy_data = get_transient( $identifier_key ) ) ) {
		$buoy_json = buoy_curl_request($buoy_id);
		$buoy_data = json_decode($buoy_json, false);		
		$set = set_transient( $identifier_key, $buoy_data, BA_CACHE_INTERVAL );

	}
	
	return $buoy_data;
}




function buoy_curl_request($buoy_id) {

	$url = BA_BASE_REQUEST_URI . $buoy_id;
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_USERAGENT, BA_PLUGIN_USER_AGENT);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$response = curl_exec($ch);
	return($response);
}





