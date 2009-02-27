<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Wishads for CafePress Search</title>
<style>
body {
	font-family:Arial, Helvetica, sans-serif;
	width: 600px;
	margin:20px;
	}
</style>
</head>

<body>
<h1>Wishads for CafePress Search Help File</h1>
<p>Though the plugin is relatively simple, this document will explain how to set up the plugin and use it in your blog.</p>
<h3>What you will need:</h3>
<ol>
  <li><strong>WordPress 2.7 or above</strong> (may work on previous versions)</li>
  <li><strong>Web hosting with php5</strong></li>
  <li>A free <strong>CafePress API key</strong> to access the product feed from CafePress. To get a key, visit the <a href="http://developer.cafepress.com/">CafePress API Developer Portal</a> and create an account. From the homepage after logging in, click the &quot;Apply for an API key&quot; and complete the documentation there. The answers you give won't have any effect on the plugin, but use these values for the following questions:  
    <ul>
      <li>What type of application are you building?: &quot;Web application with client side calls&quot;</li>
      <li>How many people do you anticipate will use your application?: &quot;1-10&quot;</li>
      <li>What is your preferred protocol? &quot;REST&quot;</li>
      <li>What is your preferred output format? &quot;XML&quot;<br /><br />
        If you just want to test this out, you can use our shared apikey <strong><font color="#FF0000">bebmn3qfxsvp4ah7z38rjrc5</font></strong>        but realize this is a shared key used for this purpose that should be replaced by your own key in order to properly function.<br />
      <br /></li>
    </ul>
  </li>

  <li>If you wish to use the plugin for affiliate sales, you will need <strong>an account with Commission Junction and be accepted into the CafePress program</strong>. <a href="https://signup.cj.com/member/brandedPublisherSignUp.do?air_refmerchantid=1956334">Click here to join.</a> Once accepted, you will need your website PID, found under the &quot;Account&quot; tab, then under the &quot;Web site Settings&quot; subtab. (DO NOT use your CJ account number found in the upper right of your account page.)</li>
  <li>If you are a CafePress shopkeeper and you wish to use this plugin for affiliate links, you will need your <strong>CafePress account number</strong> in order to not &quot;double dip&quot;. Double dipping is when you send traffic to CafePress as an affiliate and they end up purchasing one of your store's products. You cannot get affiliate credit in addition to your own markups and volume bonuses. The CafePress account number is found in the upper right of your CafePress account page. It is also known as the &quot;XID&quot; when using Commission Junction terminology.<br />
    Note that if you have affiliate redirection set up at CafePress from the previous affiliate program, you can leave your CJ PID blank, fill in your CafePress account number in, and the plugin will generate the &quot;?pid=xxxxxxxx&quot; link structure.<br /> 
  If you leave both the Commission Junction pid and the CafePress account number empty, the plugin will create plain  direct links to the CafePress products.</li>
</ol>
<h3>Plugin setup:</h3>
<ol>
  <li>Unzip the plugin zip file and copy the entire directory and subdirectory structure to your WordPress plugins directory.</li>
  <li>Log in to your WordPress blog and go to the &quot;Plugins&quot; section of your admin panel.</li>
  <li>Click &quot;Activate&quot; for the &quot;Wishads for CafePress Search&quot; plugin.</li>
  <li>Go to the &quot;Wishads for CafePress Search&quot; link under your &quot;Settings&quot; section of your admin panel.</li>
  <li>Complete the top part of the settings area with your account information.</li>
  <li>Complete the bottom part of the settings with the default information. This is entirely optional. This is what will be used if you use the shorter version of the shortcode explained on the settings page. These settings can be overwritten in any post or page.</li>
  <li>Note that there is a field to be able to block products by their design image. If you find a product displaying that is unacceptable to you, simply click the design link and note the number after &quot;/design/&quot; in the address. Add that to this field list with each number separated by a comma (no spaces) and they will no longer show.</li>
</ol>
<h3>Using the Plugin</h3>
<p>This plugin works on both posts and pages and uses a &quot;shortcode&quot;.  A shortcode is a snippet of text with some information that the plugin translates into a list of products with all of their information and links to the CafePress marketplace or to a CafePress shop. An example of the shortcode is:</p>
<blockquote>
  <p><font color="#FF0000"><em>[cpgrid preview=&quot;3&quot; return=&quot;21&quot; prodtypes=&quot;0,1,220&quot;]halloween[/cpgrid] </em></font></p>
</blockquote>
<p>The shortcode above would create a wishad that displays small mugs, large mugs and travel mugs from the CafePress product feed with designs that are tagged with &quot;halloween&quot;. On the front page or category pages of your blog, it will show only the first three products followed by a &quot;View All&quot; link that takes the visitor to the full page or post. There, the plugin will display 21 products fitting the request of halloween mugs. If you just want to use your default settings, simply use this type of shortcode:</p>
<blockquote>
  <p><em><font color="#FF0000">[cpgrid]halloween[/cpgrid]</font></em></p>
</blockquote>
<p>The shortcode above will pull products with designs tagged as &quot;halloween&quot; and filter/show them based on the default settings you've entered in the &quot;Settings&quot; panel.</p>
<p>The shortcode can be entered manually or use the form that the plugin creates under your posting text area when adding or editing posts or pages.</p>
<p>When you create a post or page, you'll see a form under the text area titled &quot;Wishads for CafePress Search Entry&quot;. TIP: In some cases, you will be able to drag this form higher up on the page, where it will remain in future posts!</p>
<p>Enter the information in the form, including the preview, show how many and product types if you want to override the default settings. Note that there is a link to a nifty tool that lets you select products then copy the list it generates for you.</p>
<p>Click the &quot;Create Wishad Shortcode&quot; button and the shortcode text will appear in your text editor. You can add any additional text above or below the shortcode, and it will appear in that place in the post or page. Note that on pages like the front page of you blog, only the number of products you designated in the &quot;preview&quot; field will show, followed by a &quot;View All&quot; link to get to the post.</p>
<h3>Appearance</h3>
<p>The entire results set is controlled with CSS, including the &quot;buy now&quot; button, and can be changed by editing the &quot;cpgrid.css&quot; file. If you want to change the appearance with CSS, I recommend keeping a backup copy as you go along.</p>
<p>There are two checkboxes in the settings panel to make it easier to display or hide the shop link and the design link.</p>
<p>If you are feeling adventurous, you can directly edit the cafepress_grid.php file to change the look. Look for the &quot;item block&quot; text to find the item block code.</p>
<h3>Practical Applications</h3>
<p>So what can you do with this plugin?</p>
<p>You can use it to enhance and monetize your existing blog by allowing you to feature topical merchandise. Blogging about politics? Drop in a shortcode about the recent budget debacle, or the latest politician to embarrass themselves.</p>
<p>Run an entire site based on a product or product niche. You can create an entire site based on mousepads or infant clothing for example. Each post or page can be a different topic. In this case, you could set the infant bodysuit as the default product! This is also a perfect example of how you would use the &quot;blocked designs&quot; feature. Not every kids product is actually &quot;kid safe&quot; in the product feed.</p>
<p>Run an entire site based on a topic niche. Create pages for different products or product groups. For example, if you're promoting disc golf, create pages each with the topic &quot;disc golf&quot; but with different ones for men's tees, women's tees, outerwear, dog products, mousepads, calendars, etc.</p>
<p>Those are just a few ideas, of course. Let your imagination run wild!</p>
<p><strong>What if I need help?</strong></p>
<p>Support can be found on our support forum at <a href="http://www.wishads.com/support">http://www.wishads.com/support</a> or feel free to email me at <a href="mailto:info@wishads.com">info@wishads.com</a>.</p>
<p><strong>What does this cost?</strong></p>
<p>In keeping with the spirit of WordPress, the plugin is free! There is no cost to use it, and there is no click-share or rev share involved. All commissions are yours. If you find that the plugin helps make you money and you like to see further development on this and other similar plugins, please consider making a donating via paypal at <a href="http://www.wishads.com">http://www.wishads.com</a></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
</html>
