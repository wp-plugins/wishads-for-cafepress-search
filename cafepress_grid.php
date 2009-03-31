<?
/* 
Plugin Name: Wishads for CafePress Search
Plugin URI: http://www.wishads.com/wordpress-plugins/cafepress_grid/
Description: A plugin that creates a display grid of products available from CafePress.com based on search terms and creates affiliate links to said products. 
Author: Wishads.com
Version: 1.1.2
Author URI: http://www.wishads.com/
*/ 

/*	Copyright 2009  Wishads.com  (email : info@wishads.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


function wpCPGrid ($attr, $content) {

    $attr = shortcode_atts(array('return'   => get_option('wishads_cpgrid_numtoshow'),
                                 'preview'   => get_option('wishads_cpgrid_numtopreview'),
                                 'prodtypes' => get_option('wishads_cpgrid_prodtypes')), $attr);


		
	// check on the cp api and the cj pid
	$cpApiKey = trim(get_option('wishads_cpgrid_cpapikey'));
	if ($cpApiKey == '') {
		return '<div>wishad alert: no apikey</div>';
	} else {
		$cpgrid_size = trim(get_option('wishads_cpgrid_imgSize'));
		if ($cpgrid_size == '')
			$cpgrid_size = 150;
		
		$cpgrid_preview = $attr['preview'];
		$cpgrid_return = $attr['return'];
		$cpgrid_prodTypes = $attr['prodtypes'];
		$cpgrid_startPage = (isset($_GET['startpage']) && $_GET['startpage']) ? $_GET['startpage'] : "1";
		$cpgrid_search = $content;

		$cpgrid_permalink = get_permalink($post->ID);
		// build the file name
		$cpgrid_cachedir = WP_PLUGIN_DIR . "/wishads-for-cafepress-search/cache/";
		cpgrid_cleancache($cpgrid_cachedir);
		$cpgrid_fileName = 	$cpgrid_cachedir . "$cpgrid_search" . "_" . $cpgrid_startPage . "_" . $cpgrid_prodTypes . "_" . $cpgrid_return  . ".xml";	
	
		
		if (file_exists ( $cpgrid_fileName)) { // check for existing cached file
			$cpgrid_xml = $cpgrid_fileName;
		} else { // make the api request and cache it
		if (empty($cpgrid_prodTypes) || $cpgrid_prodTypes == "")
			$cpgrid_prodTypes = "-999";
			$cpApiRequest = "http://open-api.cafepress.com/product.advancedSearch.cp?v=3&appKey=$cpApiKey&query=$cpgrid_search&merchandiseIds=$cpgrid_prodTypes&startResults=" . ($cpgrid_startPage-1) . "&maxResults=$cpgrid_return&maxProductsPerDesign=1&sort=by_score_desc";	
			$cpgrid_getxml = simplexml_load_file($cpApiRequest) or die("feed not loading");
		
		
			foreach ($cpgrid_getxml->children() as $mainResult) {
			
				foreach ($mainResult->children() as $SearchResultItem) {
					
					$productnumber = $SearchResultItem->products->product['productNumber'];
					$designthumb = $SearchResultItem['mediaUrl'];
					$designid = $SearchResultItem['mediaId'];
					$producttype = $SearchResultItem->products->product['productTypeNumber'];
					$mplink = $SearchResultItem->products->product['marketplaceUrl'];
					$storename = $SearchResultItem->products->product['storeName'];
					$storeurl = $SearchResultItem->products->product['storeUrl'];
					$shoplink = $storeurl . "." . $productnumber;
					$thumbnail = "http://images.cafepress.com/nocache/product/".$productnumber."_150x150_Front.jpg";
					$description = $SearchResultItem->products->product['sellerDescription'];
					$title = $SearchResultItem->products->product['caption'];
					$price = $SearchResultItem->products->product['price'];
							
			$xmlInside .= 	"
  <item>
	<productnumber>$productnumber</productnumber>
	<designid>$designid</designid>
	<producttype>$producttype</producttype>
	<mplink>$mplink</mplink>
	<shoplink>$shoplink</shoplink>
	<storename>$storename</storename>
	<storeurl>$storeurl</storeurl>
	<price>$price</price>
	<thumbnail>$thumbnail</thumbnail>
	<designthumb>$designthumb</designthumb>
	<description>$description</description>
	<title>$title</title>
  </item>";			
				}
			break;
			}
$xmltowrite = "<?xml version=\"1.0\" standalone=\"yes\"?>
<items>$xmlInside
</items>";
				
		
		$fh = fopen($cpgrid_fileName, 'w') or die("can't open file");
		fwrite($fh, $xmltowrite);
		fclose($fh);
		$cpgrid_xml = $cpgrid_fileName;
		} // end of caching the xml file
		
		// create the page based on the cached file
		$cpgrid_getxml = simplexml_load_file($cpgrid_xml) or die("feed not loading");
		$cpgrid_counter=0;
		$cpgrid_numresults = count($cpgrid_getxml->children());

		// if is_page or is_single then do pagination

		if (is_single() || is_page()) { // show the paging
			if ($cpgrid_startPage>1) {
				// do previous
				if (strpos($cpgrid_permalink,"?"))
				{
					$cpgrid_paging = "<a href=\"" . $cpgrid_permalink . "&startpage=" . ($cpgrid_startPage-1) . "\">View previous page</a>";
					$cpgrid_isprevious = true;
				}
				else 
				{
					$cpgrid_paging = "<a href=\"" . $cpgrid_permalink . "?startpage=" . ($cpgrid_startPage-1) . "\">View previous page</a>";
					$cpgrid_isprevious = true;
				}

		
			}
			if ($cpgrid_numresults>1) {
				// do next
				if ($cpgrid_isprevious)
					$cpgrid_paging .= "&nbsp;&nbsp;&nbsp;";

				if (strpos($cpgrid_permalink,"?"))
				{
					$cpgrid_paging .= "<a href=\"" . $cpgrid_permalink . "&startpage=" . ($cpgrid_startPage+1) . "\">View next page</a>";
				}
				else 
				{
					$cpgrid_paging .= "<a href=\"" . $cpgrid_permalink . "?startpage=" . ($cpgrid_startPage+1) . "\">View next page</a>";
				}

		
			}

			$cpgrid .= "<div class=\"cpgrid_paging\">$cpgrid_paging</div>";
			
		}		

		// set productTypeNumbers that just have a front
		$cpgrid_onlyFront = array(3,15,18,26,37,42,49,50,51,52,53,54,58,59,65,72,73,74,75,77,78,82,86,90,92,96,100,101,105,110,118,119,120,121,122,123,124,125,137,138,139,140,141,142,143,147,152,155,161,162,163,164,167,181,184,186,190,192,193,194,195,203,204,205,207,209,210,211,212,213,214,215,216,220,223,224,232,233,234,236,244,246,249);
		$cpgrid_doubleFront = array(206); // yard sign 206 is special case. Show front for both sides.

		// check for the target, shop or mp
		$cpgrid_target = get_option('wishads_cpgrid_target');
			
		$cpgrid_cjpid = get_option('wishads_cpgrid_cjpid');
		$cpgrid_cjsid = get_option('wishads_cpgrid_cjsid');
		$cpgrid_cjxid = get_option('wishads_cpgrid_cjxid');
		if ($cpgrid_cjpid == '') {
			$cpgrid_prefix = '';
			if ($cpgrid_cjxid == '') {
				$cpgrid_suffix = '';
			} else {
				$cpgrid_suffix = "?pid=$cpgrid_cjxid";
			}
		} else {
			$cpgrid_prefix = "http://www.anrdoezrs.net/click-".$cpgrid_cjpid."-10463747?XID=".$cpgrid_cjxid."&SID=".str_replace(" ","_",$cpgrid_cjsid)."&URL=";
		}



		$cpgrid_showStoreLink = get_option('wishads_cpgrid_showStoreLink');
		$cpgrid_showDesignLink = get_option('wishads_cpgrid_showDesignLink');
		$cpgrid_blockedDesigns = explode(",",get_option('wishads_cpgrid_blockedDesigns'));
		$cpgrid_isfluid = get_option('wishads_cpgrid_isfluid');
		
		foreach ($cpgrid_getxml->children() as $mainResult) {
			if ($cpgrid_target == "mp") 
				$cpgrid_link = $mainResult->mplink;
			else
				$cpgrid_link = $mainResult->shoplink;
			
			$thisDesignId = $mainResult->designid;
			if(!in_array($thisDesignId,$cpgrid_blockedDesigns)){

				// check for page type. if is_page or is_single then show all of it. If not, just loop to the number in "preview" field.
				if (!is_single() && !is_page() && $cpgrid_counter == $cpgrid_preview) { 
					$cpgrid .= "<div class=\"cpgrid_viewall\"><a href=\"" . $cpgrid_permalink . "\">View all</div>";
					break;
				}
				$thisTitle = $mainResult->title;
				$thisStoreUrl = $mainResult->storeurl;
				$thisStoreName = $mainResult->storename;
				$thisDesignThumb = $mainResult->designthumb;
				$thisDesignZoom = str_replace("125x125","400x400",$thisDesignThumb);
				$thisFrontThumb = "http://images.cafepress.com/product/" . $mainResult->productnumber . "_150x150_Front.jpg";
				$thisFrontZoom = "http://images.cafepress.com/product/" . $mainResult->productnumber . "_350x350_Front.jpg";
				$thisBackThumb = "http://images.cafepress.com/product/" . $mainResult->productnumber . "_150x150_Back.jpg";
				$thisBackZoom = "http://images.cafepress.com/product/" . $mainResult->productnumber . "_350x350_Back.jpg";
	
				// this is the item block where you can rearrange the layout if you dare
				if ($cpgrid_isfluid == "Y") {
				
					$cpgrid .= '<div class="cpgrid_fluiditemblock" onmouseover="this.className=\'cpgrid_fluiditemblockover\'" onmouseout="this.className=\'cpgrid_fluiditemblock\'">';
					$cpgrid .= '<div class="cpgrid_fluidthumbs"><a href="' . $cpgrid_prefix . $cpgrid_link . $cpgrid_suffix . '"><img width=150 height=150 src="' . $thisFrontThumb . '"></a></div>';
					$cpgrid .= '<div class="cpgrid_title">' . $thisTitle . '</div><div class="cpgrid_buylink"><a href="' . $cpgrid_prefix . $cpgrid_link . $cpgrid_suffix . '">Buy Now! - ' . $mainResult->price . '</a></div>';
	
			/* remove if you want the description on the thumbnails but you'll need to increase the height of the itemblock divs
					$cpgrid .= '<div class="cpgrid_description">' . $description. '</div>';
			*/
					$cpgrid .= '</div>';

				
				
				
				} else {
					
					
					$cpgrid .= "<div class=\"cpgrid_itemblock\">";
					$cpgrid .= "<div class=\"cpgrid_title\">$thisTitle</div>";
					if ($cpgrid_showStoreLink)			
						$cpgrid .= "<div class=\"cpgrid_shoplink\">from <a href=\"" . $cpgrid_prefix . $thisStoreUrl . $cpgrid_suffix . "\">$thisStoreName</a></div>\n";
					$cpgrid .= "<div class=\"cpgrid_thumbnailblock\">";
					$cpgrid .= "<div class=\"cpgrid_design\"><div class=\"cpgrid_designtext\">Design</div>";
					$cpgrid .= "<div class=\"cpgrid_designimg\" style=\"background-image: url('" . $thisDesignThumb . "'); background-repeat: no-repeat; background-position: center;\">";
					$cpgrid .= "<a href=\"" . $thisDesignZoom . "\" title=\"$thisTitle design zoom\" class=\"thickbox\"><img src=\"" . WP_PLUGIN_URL ."/wishads-for-cafepress-search/images/trans.gif\" /></a></div></div>\n";
					$cpgrid .= "<div class=\"cpgrid_front\"><div class=\"cpgrid_fronttext\">Front</div><div class=\"cpgrid_frontimg\">";
					$cpgrid .= "<a href=\"" . $thisFrontZoom . "\" title=\"$thisTitle product front\" class=\"thickbox\"><img src=\"$thisFrontThumb\" /></a></div></div>\n";
					if (in_array($mainResult->producttype,$cpgrid_doubleFront)) {
						$cpgrid .= "<div class=\"cpgrid_back\"><div class=\"cpgrid_backtext\">Back</div><div class=\"cpgrid_backimg\">";
						$cpgrid .= "<a href=\"" . $thisFrontZoom . "\" title=\"$thisTitle product back\" class=\"thickbox\"><img src=\"$thisFrontThumb\" /></a></div></div>\n";
					}
					elseif (!in_array($mainResult->producttype,$cpgrid_onlyFront))
					{
						$cpgrid .= "<div class=\"cpgrid_back\"><div class=\"cpgrid_backtext\">Back</div><div class=\"cpgrid_backimg\">";
						$cpgrid .= "<a href=\"" . $thisBackZoom . "\" title=\"$thisTitle product back\" class=\"thickbox\"><img src=\"$thisBackThumb\" /></a></div></div>\n";
					}
					
					$cpgrid .= "</div><div style=\"clear:both;\"></div>";
					$cpgrid .= "<div class=\"cpgrid_description\">" . $mainResult->description . "</div>\n";
					if ($cpgrid_showDesignLink)			
						$cpgrid .= "<div class=\"cpgrid_designlink\"><a href=\"" . $cpgrid_prefix . "http://shop.cafepress.com/design/" . $mainResult->designid . $cpgrid_suffix . "\">View more products with this design</a></div>";
					$cpgrid .= "<div class=\"cpgrid_buylink\"><a href=\"" . $cpgrid_prefix . $cpgrid_link . $cpgrid_suffix . "\">Buy Now! - " . $mainResult->price . "</a></div>";
					$cpgrid .= "</div>\n";
					$cpgrid .= "<div class=\"cpgrid_separator\"></div>\n\n";
				}	
				// end of the item block
				
				$cpgrid_counter++;
			
			}
		}
		$cpgrid .= '<div style="clear:both;"></div>';
		if (is_single() || is_page()) { // show the paging
			$cpgrid .= "<div class=\"cpgrid_paging\">$cpgrid_paging</div>";
		}		


		return $cpgrid;
	}
	
}



function cpgrid_add_css() {

	wp_enqueue_script('jquery');
	wp_enqueue_script('thickbox');
	echo "<script type=\"text/javascript\" src=\"/wp-includes/js/jquery/jquery.js\"></script>";
	echo "<script type=\"text/javascript\" src=\"/wp-includes/js/thickbox/thickbox.js\"></script>";
	echo "<link rel=\"stylesheet\" href=\"/wp-includes/js/thickbox/thickbox.css\" type=\"text/css\" media=\"screen\" />\n";
	echo "<link rel=\"stylesheet\" href=\"" . WP_PLUGIN_URL ."/wishads-for-cafepress-search/cpgrid.css\" type=\"text/css\" media=\"screen\" />\n";
	
}


function handleAdminMenu() {
    add_meta_box('cpGridMB', 'Wishads for CafePress Search Entry', 'insertForm', 'post', 'normal');
    add_meta_box('cpGridMB', 'Wishads for CafePress Search Entry', 'insertForm', 'page', 'normal');
}

function cpgridwarning() {
	echo "<div id='wpCPGrid_warning' class='updated fade-ff0000'><p><strong>"
		.__('Wishads for Cafepress Search is almost ready.')."</strong> "
		.sprintf(__('You must <a href="options-general.php?page=wishads-for-cafepress-search/cafepress_grid.php">enter your CafePress API key and your Commission Junction PID</a> for it to work.'), "options-general.php?page=wishads-for-cafepress-search/cafepress_grid.php")
		."</p></div>";
}

function insertForm() {
?>
        <table class="form-table">
            <tr valign="top">
                <th align="right" scope="row"><label for="wpCPGrid_search"><?php _e('Search Keyword(s):')?></label></th>
                <td>
                    <input type="text" size="40" style="width:95%;" name="wpCPGrid_search" id="wpCPGrid_search" />
                </td>
            </tr>
            <tr valign="top">
                <th align="right" scope="row"><label for="wpCPGrid_preview"><?php _e('Preview how many?:')?></label></th>
                <td>
                    <input type="text" size="40" style="width:95%;" name="wpCPGrid_preview" id="wpCPGrid_preview" />
                </td>
            </tr>
            <tr valign="top">
                <th align="right" scope="row"><label for="wpCPGrid_return"><?php _e('Show how many?:')?></label></th>
                <td>
                    <input type="text" size="40" style="width:95%;" name="wpCPGrid_return" id="wpCPGrid_return" />
                </td>
            </tr>
            <tr valign="top">
                <th align="right" scope="row"><label for="wpCPGrid_prodtypes"><?php _e('Product Types (<a href="http://www.cafewish.com/cpgrid_products.asp" target="_blank">Comma separated list</a>):')?></label></th>
                <td>
                    <input type="text" size="40" style="width:95%;" name="wpCPGrid_prodtypes" id="wpCPGrid_prodtypes" />
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="button" onclick="return this_wpCPGridAdmin.sendToEditor(this.form);" value="<?php _e('Create Wishad Shortcode &raquo;'); ?>" />
        </p>
<?php
}


function adminHead () {
    if ($GLOBALS['editing']) {
        wp_enqueue_script('wpCPGridAdmin', WP_PLUGIN_URL .'/wishads-for-cafepress-search/js/cpgrid.js', array('jquery'), '1.0.0');
    }
}

// admin menus
function wishads_cpgrid_plugin_menu() {
  	add_options_page('Wishads for CafePress Search Settings', 'Wishads for CafePress Search', 8, __FILE__, 'wishads_cpgrid_plugin_options');
}


function wishads_cpgrid_plugin_options() {
	echo "<h2>Wishads for CafePress Search</h2>";

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST['get_cpgrid_submit'] == 'Y' ) {

		// let the browser know it's been updated
		echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';

        // Read their posted value
        $cpgrid_cpapikey = $_POST['cpgrid_cpapikey'];
        $cpgrid_cjpid = $_POST['cpgrid_cjpid'];
        $cpgrid_cjsid = $_POST['cpgrid_cjsid'];
        $cpgrid_cjxid = $_POST['cpgrid_cjxid'];
        $cpgrid_numtopreview = $_POST['cpgrid_numtopreview'];
        $cpgrid_numtoshow = $_POST['cpgrid_numtoshow'];
        $cpgrid_target = $_POST['cpgrid_target'];
		$cpgrid_prodTypes = $_POST['cpgrid_prodTypes'];
		$cpgrid_showStoreLink = $_POST['cpgrid_showStoreLink'];
		$cpgrid_showDesignLink = $_POST['cpgrid_showDesignLink'];
		$cpgrid_blockedDesigns = $_POST['cpgrid_blockedDesigns'];
		$cpgrid_isfluid = $_POST['cpgrid_isfluid'];
		
		

        // Save the posted value in the database
        update_option( 'wishads_cpgrid_cpapikey', $cpgrid_cpapikey );
        update_option( 'wishads_cpgrid_cjpid', $cpgrid_cjpid );
        update_option( 'wishads_cpgrid_cjsid', $cpgrid_cjsid );
        update_option( 'wishads_cpgrid_cjxid', $cpgrid_cjxid );
        update_option( 'wishads_cpgrid_numtoshow', $cpgrid_numtoshow );
        update_option( 'wishads_cpgrid_numtopreview', $cpgrid_numtopreview );
        update_option( 'wishads_cpgrid_target', $cpgrid_target );
        update_option( 'wishads_cpgrid_prodTypes', $cpgrid_prodTypes );
        update_option( 'wishads_cpgrid_showStoreLink', $cpgrid_showStoreLink );
        update_option( 'wishads_cpgrid_showDesignLink', $cpgrid_showDesignLink );
        update_option( 'wishads_cpgrid_blockedDesigns', $cpgrid_blockedDesigns );
        update_option( 'wishads_cpgrid_isfluid', $cpgrid_isfluid );

	}

	$cpgrid_cpapikey = get_option('wishads_cpgrid_cpapikey');
	$cpgrid_cjpid = get_option('wishads_cpgrid_cjpid');
	$cpgrid_cjsid = get_option('wishads_cpgrid_cjsid');
	$cpgrid_cjxid = get_option('wishads_cpgrid_cjxid');
	$cpgrid_numtoshow = get_option('wishads_cpgrid_numtoshow');
	$cpgrid_numtopreview = get_option('wishads_cpgrid_numtopreview');
	$cpgrid_target = get_option('wishads_cpgrid_target');
	$cpgrid_prodTypes = get_option('wishads_cpgrid_prodTypes');
	$cpgrid_showStoreLink = get_option('wishads_cpgrid_showStoreLink');
	$cpgrid_showDesignLink = get_option('wishads_cpgrid_showDesignLink');
	$cpgrid_blockedDesigns = get_option('wishads_cpgrid_blockedDesigns');
	$cpgrid_isfluid = get_option('wishads_cpgrid_isfluid');

	?>
    
	<div class="wrap">
    <h3>For a complete explanation of the setup and use, see the <a href="<? echo WP_PLUGIN_URL; ?>/wishads-for-cafepress-search/cafepress_grid_help.php" target="_blank">help file</a>.	</h3>

	<form name="myform" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    <input type="hidden" name="get_cpgrid_submit" value="Y" />
    <table style="border 2px solid black;" border=0 cellspacing=5>
    <tr valign="top"><td align="left" colspan="2"><h3>Wishads for CafePress Search Configuration: </h3>
    <p>Complete the settings below to configure your wishad.</p></td></tr>
    <tr valign="top"><td width="400px" align="right" >Enter your CafePress API Key:</td>
    	<td><input type=text name="cpgrid_cpapikey" value="<?php echo $cpgrid_cpapikey ?>" /> </td></tr>
    <tr valign="top"><td width="400px" align="right" >Enter your Commission Junction PID:</td>
    	<td><input type=text name="cpgrid_cjpid" value="<?php echo $cpgrid_cjpid ?>" /> </td></tr>
    <tr valign="top"><td width="400px" align="right" >Optional - Commission Junction SID</a>:</td>
    	<td><input type=text name="cpgrid_cjsid" value="<?php echo $cpgrid_cjsid ?>" /> </td></tr>
    <tr valign="top"><td width="400px" align="right" >Optional - <b>CafePress</b> Account Number</a>:</td>
    	<td><input type=text name="cpgrid_cjxid" value="<?php echo $cpgrid_cjxid ?>" /> </td></tr>
    <tr valign="top">
      <td width="400px" align="right" >Link to Marketplace or Shop version of product?</td>
      <td>
      <select name="cpgrid_target">
      <option <? if ($cpgrid_target == "mp") echo "selected "; ?>value="mp">Marketplace</option>
      <option <? if ($cpgrid_target == "shop") echo "selected "; ?>value="shop">Shop</option>
      </select></td></tr>
    <tr valign="top"><td align="right" nowrap>Show Shop Link (to shop)?:</td>
      <td nowrap><input type=checkbox name="cpgrid_showStoreLink" value="Y" <? if ($cpgrid_showStoreLink == "Y") echo "checked "; ?>/></td></tr>
    <tr valign="top"><td align="right" nowrap>Show Design Link (to mp)?:</td>
      <td nowrap><input type=checkbox name="cpgrid_showDesignLink" value="Y" <? if ($cpgrid_showDesignLink == "Y") echo "checked "; ?>/></td></tr>
      <tr valign="top"><td align="left" colspan="2"><p>By default, the plugin displays a row for each product, showing the design image and the front and back thumbnail if applicable. You can change this below to display a product thumbnail grid instead. It's easy to switch them back and forth as you wish, and the layout can be controlled with CSS.</p></td></tr>
    <tr valign="top">
        <td width="400px" align="right" >Switch to grid layout?</td>
      <td nowrap><input type=checkbox name="cpgrid_isfluid" value="Y" <? if ($cpgrid_isfluid == "Y") echo "checked "; ?>/></td></tr>
      <tr valign="top"><td align="left" colspan="2"><h3>Wishads for CafePress Search Default Settings: </h3>
    <p>These settings can be changed per individual wishad post</p></td></tr>
    <tr valign="top">
        <td width="400px" align="right" >Default # of products to preview on the main page/archive pages:</td>
        <td><input name="cpgrid_numtopreview" type=text value="<?php echo $cpgrid_numtopreview ?>" size="4"></td></tr>
      <tr valign="top">
        <td width="400px" align="right" >Default # of products to show on single post pages:</td>
        <td><input name="cpgrid_numtoshow" type=text value="<?php echo $cpgrid_numtoshow ?>" size="4"></td></tr>
      <tr valign="top">
        <td width="400px" align="right" >Comma separated list of product types:</td>
        <td><input name="cpgrid_prodTypes" type=text value="<?php echo $cpgrid_prodTypes ?>" size="6"></td></tr>
      <tr valign="top">
        <td width="400px" align="right" >Comma separated list of blocked design ids:</td>
        <td><input name="cpgrid_blockedDesigns" type=text value="<?php echo $cpgrid_blockedDesigns ?>" size="20"></td></tr>
      <tr valign="top">
        <td width="400px" align="right" colspan="2">To find the design id number, click the &quot;shop by design&quot; link. Look at the url of this page and the design number will be right after &quot;http://shop.cafepress.com/design/&quot; in the url. Add that number to the list above, and this design will no longer show on your pages or posts. Note that this check can only be done after receiving the product list from CafePress. If a request for 30 products contains a blocked design, only 29 products will show on your page.</td></tr>
    </table>
    <input type="submit" value="Update" />
    </form></p>
	</div>
  <?
}


function cpgrid_cleancache($directory)
{
	$seconds_old = 84600; // 
	if( !$dirhandle = @opendir($directory) )
			return;

	while( false !== ($filename = readdir($dirhandle)) ) {
			if( $filename != "." && $filename != ".." ) {
					$filename = $directory. "/". $filename;

					if( @filemtime($filename) < (time()-$seconds_old) )
							@unlink($filename);
			}
	}

}

// get going!

add_shortcode('cpgrid', 'wpCPGrid');  
add_action('admin_menu', 'wishads_cpgrid_plugin_menu');
add_action('admin_menu', 'handleAdminMenu');
add_action('wp_head', 'cpgrid_add_css');
add_filter('admin_print_scripts', 'adminHead');


?>