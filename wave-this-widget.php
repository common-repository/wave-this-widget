<?php
/*
Plugin Name: Wave This! Widget
Plugin URI: http://nunnone.com/wave-this-widget
Description: A sidebar widget that adds a button to posts that lets readers share it in Google Wave. Uses the exerpt of the page or creates one.
Version: 0.3
Author: Joshua Nunn
Author URI: http://www.joshnunn.com.au/
License: GPL2


	Copyright 2010  Joshua Nunn  (email : josh@nunnone.com)
	
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
*/

$wavethisplugindir = get_settings('siteurl').'/wp-content/plugins/'.dirname(plugin_basename(__FILE__));

add_action("widgets_init", array('jn_wave_this_widget', 'register'));
register_activation_hook( __FILE__, array('jn_wave_this_widget', 'activate'));
register_deactivation_hook( __FILE__, array('jn_wave_this_widget', 'deactivate'));
class jn_wave_this_widget {
	function activate(){
		$data = array( 'title','img_url','alt_img_url','img_width',);
		if ( ! get_option('jn_wave_this_widget')){
			add_option('jn_wave_this_widget' , $data);
		} else {
			update_option('jn_wave_this_widget' , $data);
		}
	}

	function deactivate(){
		delete_option('jn_wave_this_widget');
	}

	function control(){
		$data = get_option('jn_wave_this_widget');
		?>
		<p><label>Title<br/><input name="jn_wave_this_widget_title" type="text" value="<?php echo $data['title']; ?>" /></label></p>
		<p><label>Choose a WaveThis image<br/>Or add an alternate image below<br/>
		<input type="radio" name="jn_wave_this_widget_img" value="icon16" <?php checked('icon16', $data['img_url']); ?> ><img src="http://wave.google.com/wavethis/icon16.png" alt="16x16 icon"/></input>
		<input type="radio" name="jn_wave_this_widget_img" value="button16" <?php checked('button16', $data['img_url']); ?> ><img src="http://wave.google.com/wavethis/button16.png" alt="16x16 button with text"/><br/>
		<input type="radio" name="jn_wave_this_widget_img" value="icon24" <?php checked('icon24', $data['img_url']); ?> ><img src="http://wave.google.com/wavethis/icon24.png" alt="24x24 icon"/></input>
		<input type="radio" name="jn_wave_this_widget_img" value="button24" <?php checked('button24', $data['img_url']); ?> ><img src="http://wave.google.com/wavethis/button24.png" alt="24x24 button with text"/><br/>
		<input type="radio" name="jn_wave_this_widget_img" value="icon32" <?php checked('icon32', $data['img_url']); ?> ><img src="http://wave.google.com/wavethis/icon32.png" alt="32x32 icon"/></input>
		<input type="radio" name="jn_wave_this_widget_img" value="button32" <?php checked('button32', $data['img_url']); ?> ><img src="http://wave.google.com/wavethis/button32.png" alt="32x32 button with text"/></input></label></p>
		<p><label>Alternate Image URL<br/>Leave blank to use buttons above<br/><input name="jn_wave_this_widget_alt_img_url" type="text" value="<?php echo $data['alt_img_url']; ?>" /></label></p>
		<p><label>Image Width<br/><input name="jn_wave_this_widget_img_width" type="text" value="<?php echo $data['img_width']; ?>" /></label></p>
		<?php
		if (isset($_POST['jn_wave_this_widget_title'])){
			$data['title'] = attribute_escape($_POST['jn_wave_this_widget_title']);
			$data['img_url'] = attribute_escape($_POST['jn_wave_this_widget_img']);
			$data['alt_img_url'] = attribute_escape($_POST['jn_wave_this_widget_alt_img_url']);
			$data['img_width'] = attribute_escape($_POST['jn_wave_this_widget_img_width']);
			update_option('jn_wave_this_widget', $data);
		}
	}

	function widget($args){
		if ( is_single($post)) {
			$data = get_option('jn_wave_this_widget');
			$excerpt = urlencode(get_the_excerpt());  // encode the title and excerpt with +'s for spaces etc. to be included in the URL
			$posttitle = urlencode(get_the_title());
			
			echo $args['before_widget'];
			if ( $data['title']) { echo $args['before_title'] . $data['title'] . $args['after_title'];}; // check if the user has specified a title for the widget and include if necessary
			
			echo '<a href="https://wave.google.com/wave/wavethis?t=';
			echo $posttitle;
			echo '&c=%22';
			echo $excerpt;
			echo '%22"><img src="';
			
			if ( $data['alt_img_url'] ) { // check if the user has included an alternate image URL
				echo $data['alt_img_url'] . '"';
			} else {
				echo 'http://wave.google.com/wavethis/' . $data['img_url'] .'.png"';
			}
			
			echo 'alt="Wave This!" style="display:block;margin-left:auto;margin-right:auto;';
			
			if ( $data['img_width'] ){ echo 'width:' . $data['img_width'];};

			echo '" /></a>';
			echo $args['after_widget'];
		}
	}

	function register(){
		register_sidebar_widget('Wave This!', array('jn_wave_this_widget', 'widget'));
		register_widget_control('Wave This!', array('jn_wave_this_widget', 'control'));
	}
}
?>