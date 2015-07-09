<?php
/*
Plugin Name: LogMyTrip
Plugin URI: http://www.LogMyTrip.co.uk
Description: Display posts as map icons linked by a route on a Google map.
Version: 1.9
Author: John Waters
Author URI: http://www.LogMyTrip.co.uk
License: GPL2
Installation:
Place this file in your /wp-content/plugins/ directory, then activate through the administration panel. 
*/

/*  Copyright 2011 John Waters (email : info@johnwaters.co.uk)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

add_shortcode( 'logmytrip', 'logtrips' );
add_shortcode( 'logmytripmap', 'logtripmap' );
add_action('wp_head', 'add_lmt_support');
add_action('wp_footer', 'add_lmt_div');
add_filter('the_content', 'displaylocation', 5);
add_action('admin_head-post-new.php', 'adminhead');
add_action('admin_head-post.php', 'adminhead');
add_action('admin_menu', 'location_add_custom_box');
add_action('save_post', 'location_save_postdata');

wp_enqueue_script("jquery");

// Gets images for a post
function get_images($iPostID) {
 
    // Get images for this post
    $arrImages =& get_children('post_type=attachment&post_mime_type=image&post_parent=' . $iPostID );
 
    // If images exist for this page
    if($arrImages) {
 
	// Get array keys representing attached image numbers
	$arrKeys = array_keys($arrImages);
 
	/******BEGIN BUBBLE SORT BY MENU ORDER************
	// Put all image objects into new array with standard numeric keys (new array only needed while we sort the keys)
	foreach($arrImages as $oImage) {
		$arrNewImages[] = $oImage;
	}
 
	// Bubble sort image object array by menu_order TODO: Turn this into std "sort-by" function in functions.php
	for($i = 0; $i < sizeof($arrNewImages) - 1; $i++) {
		for($j = 0; $j < sizeof($arrNewImages) - 1; $j++) {
			if((int)$arrNewImages[$j]->menu_order > (int)$arrNewImages[$j + 1]->menu_order) {
				$oTemp = $arrNewImages[$j];
				$arrNewImages[$j] = $arrNewImages[$j + 1];
				$arrNewImages[$j + 1] = $oTemp;
			}
		}
	}
 
	// Reset arrKeys array
	$arrKeys = array();
 
	// Replace arrKeys with newly sorted object ids
	foreach($arrNewImages as $oNewImage) {
		$arrKeys[] = $oNewImage->ID;
	}
	******END BUBBLE SORT BY MENU ORDER**************/
 
	// Get the first image attachment
	$iNum = $arrKeys[0];
 
	// Get the thumbnail url for the attachment
	$sThumbUrl = wp_get_attachment_thumb_url($iNum);
 
	// Print the image
	return $sThumbUrl;
    }
}

function get_latlngs($tid) {
   // Get the lat/long and other data for the point and return as a javascript string
   $lls = '';
   $myquery = '&showposts=500&author='.$tid.'&orderby=date&order=DESC';
   $queryObject = new WP_Query($myquery);
   if ($queryObject->have_posts()) {
        while ($queryObject->have_posts()) {
                $queryObject->the_post();
		$ID = get_the_ID();
		if( get_post_meta($ID, 'geo_latitude', true) ) {
                $lls .= "var Address = '".esc_js(get_post_meta($ID, 'geo_address', true))."';\n";
                $lls .= "if(Address != '') {
                          var place = Address;
                        } else {
                          var place = '".esc_js(get_the_title($ID))."';
                        }\n";
                $lls .= "var date = '".esc_js(get_the_date())."';\n";
                $lls .= "var pc = '".esc_js(get_images($ID))."';\n";
                $lls .= "var caption = '".esc_js(get_the_title($ID))."';\n";
		$lls .= "if (!pc) {
			var html = '<div style=\"width:360px;height:100px;word-wrap:break-word;padding-top:2px;\"><b>' + place + '</b> <br />' + date + ' - ' + caption + '</div>';
		} else {
			var html = '<div style=\"width:360px;height:200px;word-wrap:break-word;padding-top:2px;\"><b>' + place + '</b><img align=\"left\" height=\"150\" width=\"150\" alt=\"' + place + '\" src=\"' + pc + '\"><br /><a href=?p=".$ID." title=\"Click to read more...\">' + date + ' - ' + caption + '</a></div>';
		}\n";
                $lls .= "var Latitude = '".esc_js(get_post_meta($ID, 'geo_latitude', true))."';\n";
                $lls .= "var Longitude = '".esc_js(get_post_meta($ID, 'geo_longitude', true))."';\n";
                $lls .= "if((Latitude != '') && (Longitude != '')) {
                        var point = new google.maps.LatLng(parseFloat(Latitude),parseFloat(Longitude));
                        route.push( point );
                        latlngbounds.extend( point );
                        var marker = new google.maps.Marker({
                                map: map,
                                icon: image,
                                title: place,
                                position: point
                        });
                        bindInfoWindow(marker, map, infoWindow, html);
                        }\n";
		}
        }
	wp_reset_query();
   }
   return $lls;
}

function logtripmap() {
   if($_COOKIE["tid"]) {
	$tid = $_COOKIE["tid"];
   } else {
	$tid = 1;
   }
   echo '<div id="maptitle"></div>';
   if (function_exists('simple_social_bookmarks')) {
	$linkurl = "http://www.LogMyTrip.co.uk/?tid=".$tid."&page_id=6";
	echo simple_social_bookmarks($linkurl,'','','default=off&Email=on&Facebook=on&LinkedIn=on&StumbleUpon=on&Twitter=on','Here\'s a link to the LogMyTrip map of my travels');
   }
   echo '<div id="mainmap"></div>
   <script type="text/javascript">
   function bindInfoWindow(marker, mainmap, infoWindow, html) {
        google.maps.event.addListener(marker, "click", function() {
        infoWindow.setContent(html);
        infoWindow.open(mainmap, marker);
        });
   }';
   $lmtmeta = get_lmtmeta($tid);
   if ($lmtmeta) {
	echo $lmtmeta;
	echo 'document.getElementById("maptitle").innerHTML = lmt_name;
	var image = new google.maps.MarkerImage("/ico/"+icon+".png",
	// This marker is 26 pixels wide by 30 pixels tall.
	new google.maps.Size(26, 30),
	// The origin for this image is 0,0.
	new google.maps.Point(0,0),
	// The anchor for this image is the centre base at 13,28.
	new google.maps.Point(13, 28));
   ';
   } else {
	echo 'var image = "http://maps.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png";';
   }
   echo 'var myOptions = {mapTypeId: google.maps.MapTypeId.HYBRID,
streetViewControl: false,
scaleControl: true,
scaleControlOptions: { position: google.maps.ControlPosition.LEFT_BOTTOM }}
   var map = new google.maps.Map(document.getElementById("mainmap"),myOptions);
   var latlngbounds = new google.maps.LatLngBounds( );
   var route = new Array();
   var infoWindow = new google.maps.InfoWindow;
   ';

   $latlngs = get_latlngs($tid);
   echo $latlngs;

   echo 'map.fitBounds( latlngbounds );
         map.setCenter( latlngbounds.getCenter( ) );
         var Path = new google.maps.Polyline({
                        path: route,
                        strokeColor: "#FF0000",
                        strokeOpacity: 0.9,
                        strokeWeight: 2
         });
         Path.setMap(map);
   </script>';
}

function get_lmtmeta($tid) {

        global $wpdb;

        $output = '';

	// Select data for this user from the usermeta table
        $sql = "SELECT meta_key,meta_value FROM $wpdb->usermeta WHERE user_id = $tid and meta_key in ('flickr','icon','lmt_name') ORDER BY meta_key";

	$metadata = $wpdb->get_results($sql);

   if ($metadata) {

	foreach ($metadata as $data) {

		$output .= "var ".$data->meta_key." = '".esc_js($data->meta_value)."';\n";
	}

        return $output;
   } else {
        return false;
   }
}

function logmytrips() {

        $pre_HTML='<div class="textwidget"><table>';
        $post_HTML='</table></div>';

        global $wpdb;

        $sql = "SELECT user_id,meta_value FROM $wpdb->usermeta WHERE meta_key = 'lmt_name' ORDER BY meta_value";

        $trips = $wpdb->get_results($sql);

        $output = $pre_HTML;
        $output .= "\n<tr><th>Trip Title</th></tr>";
        foreach ($trips as $trip) {
		$output .= "\n\t<tr><td style='vertical-align:top;'> <a href=\"/settrip.php?tid=" . $trip->user_id . "&rdm=" . rand() . "\" title=\"" . $trip->meta_value . "\"> ". $trip->meta_value ."</a> </td></tr>\n";
        }
        $output .= $post_HTML;

	return $output;
}

function logtrips() {
	return logmytrips();
}

function location_add_custom_box() {
		if(function_exists('add_meta_box')) {
			add_meta_box('location_sectionid', __( 'LogMyTrip Location', 'myplugin_textdomain' ), 'location_inner_custom_box', 'post', 'advanced' );
		} 
		else {
			add_action('dbx_post_advanced', 'location_old_custom_box' );
		}
}

function location_inner_custom_box() {
	echo '<input type="hidden" id="location_nonce" name="location_nonce" value="' . 
	wp_create_nonce(plugin_basename(__FILE__) ) . '" />';
	echo '
		<label class="screen-reader-text" for="location-address">Location</label>
		<div class="taghint">Enter your address</div>
		<input type="text" id="location-address" name="location-address" class="newtag form-input-tip" size="25" autocomplete="off" value="" />
		<input id="location-load" type="button" class="button locationadd" value="Search" tabindex="3" />
		<input type="hidden" id="location-latitude" name="location-latitude" />
		<input type="hidden" id="location-longitude" name="location-longitude" />
                <div id="location-map" style="border:solid 1px #c6c6c6;width:600px;height:380px;margin-top:5px;"></div>
	';
}

/* edit form for pre-WordPress 2.5 post/page */
function location_old_custom_box() {
  echo '<div class="dbx-b-ox-wrapper">' . "\n";
  echo '<fieldset id="location_fieldsetid" class="dbx-box">' . "\n";
  echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">' . 
        __( 'LogMyTrip Location', 'location_textdomain' ) . "</h3></div>";   
   
  echo '<div class="dbx-c-ontent-wrapper"><div class="dbx-content">';

  location_inner_custom_box();

  echo "</div></div></fieldset></div>\n";
}

function location_save_postdata($post_id) {
 if('page' != $_POST['post_type'] ) {
  // Check authorization, permissions, autosave, etc
  if (!wp_verify_nonce($_POST['location_nonce'], plugin_basename(__FILE__)))
    return $post_id;
  
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return $post_id;
  
  if('page' == $_POST['post_type'] ) {
    if(!current_user_can('edit_page', $post_id))
		return $post_id;
  } else {
    if(!current_user_can('edit_post', $post_id)) 
		return $post_id;
  }

  $latitude = cleancoordinate($_POST['location-latitude']);
  $longitude = cleancoordinate($_POST['location-longitude']);
  $address = reverseGeocode($latitude, $longitude);
 
  if( (cleancoordinate($latitude) != '') && (cleancoordinate($longitude) != '') ) {
	if(get_post_meta($post_id, 'fromPic', true)) {
		delete_post_meta($post_id, 'fromPic');
	} else {
		update_post_meta($post_id, 'geo_latitude', $latitude);
		update_post_meta($post_id, 'geo_longitude', $longitude);

		if(esc_html($address) != '') {
			update_post_meta($post_id, 'geo_address', $address);
		} else {
			update_post_meta($post_id, 'geo_address', 'Address not found');
		}
	}
  }
 } 
 return $post_id;
}

function adminhead() {
	global $post;
	$post_id = $post->ID;
	$post_type = $post->post_type;
	$lmtzoom = 11;
	?>
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript">
		 	var $j = jQuery.noConflict();
			$j(function() {
				$j(document).ready(function() {
				    var hasLocation = false;
					var center = new google.maps.LatLng(0.0,0.0);
					var postLatitude =  '<?php echo esc_js(get_post_meta($post_id, 'geo_latitude', true)); ?>';
					var postLongitude =  '<?php echo esc_js(get_post_meta($post_id, 'geo_longitude', true)); ?>';
					
					enableGeo();
					
					if((postLatitude != '') && (postLongitude != '')) {
						center = new google.maps.LatLng(postLatitude, postLongitude);
						hasLocation = true;
						$j("#location-latitude").val(center.lat());
						$j("#location-longitude").val(center.lng());
						reverseGeocode(center);
					}
						
				 	var myOptions = {
				      'zoom': <?php echo $lmtzoom; ?>,
				      'center': center,
				      'mapTypeId': google.maps.MapTypeId.HYBRID
				    };
						
				    var map = new google.maps.Map(document.getElementById('location-map'), myOptions);	
					var marker = new google.maps.Marker({
						position: center, 
						map: map });
					
					if((!hasLocation) && (google.loader.ClientLocation)) {
				      center = new google.maps.LatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);
				      reverseGeocode(center);
				    }
				    else if(!hasLocation) {
				    	map.setZoom(1);
				    }
					
					google.maps.event.addListener(map, 'click', function(event) {
						placeMarker(event.latLng);
					});
					
					var currentAddress;
					var customAddress = false;
					$j("#location-address").click(function(){
						currentAddress = $j(this).val();
						if(currentAddress != '')
							$j("#location-address").val('');
					});
					
					$j("#location-load").click(function(){
						if($j("#location-address").val() != '') {
							customAddress = true;
							currentAddress = $j("#location-address").val();
							geocode(currentAddress);
						}
					});
					
					$j("#location-address").keyup(function(e) {
						if(e.keyCode == 13)
							$j("#location-load").click();
					});

                                        $j("#location-enabled").click(function(){
                                                enableGeo();
                                        });
					
					function placeMarker(location) {
						marker.setPosition(location);
						map.setCenter(location);
						if((location.lat() != '') && (location.lng() != '')) {
							$j("#location-latitude").val(location.lat());
							$j("#location-longitude").val(location.lng());
						}
						
						if(!customAddress)
							reverseGeocode(location);
					}
					
					function geocode(address) {
						var geocoder = new google.maps.Geocoder();
					    if (geocoder) {
							geocoder.geocode({"address": address}, function(results, status) {
								if (status == google.maps.GeocoderStatus.OK) {
									placeMarker(results[0].geometry.location);
									if(!hasLocation) {
								    	map.setZoom(16);
								    	hasLocation = true;
									}
								}
							});
						}
						$j("#geodata").html(latitude + ', ' + longitude);
					}
					
					function reverseGeocode(location) {
						var geocoder = new google.maps.Geocoder();
					    if (geocoder) {
							geocoder.geocode({"latLng": location}, function(results, status) {
							if (status == google.maps.GeocoderStatus.OK) {
							  if(results[1]) {
							  	var address = results[1].formatted_address;
							  	if(address == "")
							  		address = results[7].formatted_address;
							  	else {
									$j("#location-address").val(address);
									placeMarker(location);
							  	}
							  }
							}
							});
						}
					}
					
					function enableGeo() {
						$j("#location-address").removeAttr('disabled');
						$j("#location-load").removeAttr('disabled');
						$j("#location-map").css('filter', '');
						$j("#location-map").css('opacity', '');
						$j("#location-map").css('-moz-opacity', '');
						$j("#location-map").removeAttr('readonly');
					}
					
				});
			});
		</script>
	<?php
}

function add_lmt_div() {
	$width = 460;
	$height = 320;
	echo '<div id="map" class="location-map" style="width:'.$width.'px;height:'.$height.'px;"></div>';
}

function add_lmt_support() {
	global $location_options, $posts;
	echo google_maps($posts);
	echo '<link type="text/css" rel="stylesheet" href="'.esc_url(plugins_url('style.css', __FILE__)).'" />';
}

function google_maps($posts) {
	$lmtzoom = 11;
	global $post_count;
	$post_count = count($posts);
	
	echo '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript">
		var $j = jQuery.noConflict();
		$j(function(){
			var center = new google.maps.LatLng(0.0, 0.0);
			var myOptions = {
		      zoom: '.$lmtzoom.',
		      center: center,
                        streetViewControl: false,
		      mapTypeId: google.maps.MapTypeId.HYBRID
		    };
		    var map = new google.maps.Map(document.getElementById("map"), myOptions);
		    var marker = new google.maps.Marker({
					position: center, 
					map: map});

                        setTimeout(function() {
                                allowDisappear = true; 
                                cancelDisappear = false;
                                $j("#map").css("display", "none");
                        },10);

			$j(".location-link").mouseover(function(){
				$j("#map").stop(true, true);
				var lat = $j(this).attr("name").split(",")[0];
				var lng = $j(this).attr("name").split(",")[1];
				var latlng = new google.maps.LatLng(lat, lng);
				placeMarker(latlng);
				
				var offset = $j(this).offset();
				$j("#map").fadeTo(250, 1);
				$j("#map").css("z-index", "99");
				$j("#map").css("visibility", "visible");
				$j("#map").css("top", offset.top + 20);
				$j("#map").css("left", offset.left);
				
				allowDisappear = false;
				$j("#map").css("visibility", "visible");
			});
			
			$j(".location-link").mouseover(function(){
			});
			
			$j(".location-link").mouseout(function(){
				allowDisappear = true;
				cancelDisappear = false;
				setTimeout(function() {
					if((allowDisappear) && (!cancelDisappear))
					{
						$j("#map").fadeTo(500, 0, function() {
							$j("#map").css("z-index", "-1");
							allowDisappear = true;
							cancelDisappear = false;
						});
					}
			    },800);
			});
			
			$j("#map").mouseover(function(){
				allowDisappear = false;
				cancelDisappear = true;
				$j("#map").css("visibility", "visible");
			});
			
			$j("#map").mouseout(function(){
				allowDisappear = true;
				cancelDisappear = false;
				$j(".location-link").mouseout();
			});
			
			function placeMarker(location) {
				map.setZoom('.$lmtzoom.');
				marker.setPosition(location);
				map.setCenter(location);
			}
			
			google.maps.event.addListener(map, "click", function() {
				window.location = "http://maps.google.com/maps?q=" + map.center.lat() + ",+" + map.center.lng();
			});
		});
	</script>';
}

function displaylocation($content)  {
	global $post, $post_count;
	$post_id = $post->ID;
	$latitude = cleancoordinate(get_post_meta($post->ID, 'geo_latitude', true));
	$longitude = cleancoordinate(get_post_meta($post->ID, 'geo_longitude', true));
	$address = get_post_meta($post->ID, 'geo_address', true);
	
	if(empty($address))
		$address = reverseGeocode($latitude, $longitude);
	
	if((!empty($latitude)) && (!empty($longitude) )) {
		$html = '<a class="location-link" href="#" id="location'.$post->ID.'" name="'.$latitude.','.$longitude.'" onclick="return false;">Posted from '.esc_html($address).'.</a>';
		$content = $html.'<br/><br/>'.$content;
	}

    return $content;
}

function reverseGeocode($latitude, $longitude) {
	$url = "http://maps.google.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&sensor=false";
	$result = wp_remote_get($url);
	$json = json_decode($result['body']);
	foreach ($json->results as $result)
	{
		foreach($result->address_components as $addressPart) {
			if((in_array('locality', $addressPart->types)) && (in_array('political', $addressPart->types)))
	    		$city = $addressPart->long_name;
	    	else if((in_array('administrative_area_level_1', $addressPart->types)) && (in_array('political', $addressPart->types)))
	    		$state = $addressPart->long_name;
	    	else if((in_array('country', $addressPart->types)) && (in_array('political', $addressPart->types)))
	    		$country = $addressPart->long_name;
		}
	}
	
	if(($city != '') && ($state != '') && ($country != ''))
		$address = $city.', '.$state.', '.$country;
	else if(($city != '') && ($state != ''))
		$address = $city.', '.$state;
	else if(($state != '') && ($country != ''))
		$address = $state.', '.$country;
	else if($country != '')
		$address = $country;
		
	return $address;
}

function cleancoordinate($coordinate) {
	$pattern = '/^(\-)?(\d{1,3})\.(\d{1,15})/';
	preg_match($pattern, $coordinate, $matches);
	return $matches[0];
}

?>
