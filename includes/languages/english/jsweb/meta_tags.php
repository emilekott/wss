<?php
/**
 * @package languageDefines
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: meta_tags.php 6668 2007-08-16 10:05:09Z drbyte $
 */

// page title
define('TITLE', 'Surf Shop');

// Site Tagline
define('SITE_TAGLINE', 'Surfboards, Xcel Wetsuits, Surfing Hardware Online.');

// Custom Keywords
define('CUSTOM_KEYWORDS', ' surfboard, wetsuit, winter wetsuit, summer wetsuit, xcel wetsuit, xcel wetsuits, surf forecast, surf webcam, surf cam, surfboards, cheap surfboards, boardbags, leashes, surfing leash, surfboard leash, chichester surf, west sussex surfing, sussex surf, bracklesham surf,');

// Home Page Only:
  define('HOME_PAGE_META_DESCRIPTION', 'UK Surf Shop selling Surfboards, Xcel Wetsuits, Boardbags, Leashes, Surfing Hardware & Surf Clothing. With FREE UK Delivery!');
  define('HOME_PAGE_META_KEYWORDS', 'wittering, bargain surfboard, surfboard, wetsuit, winter wetsuit, summer wetsuit, xcel wetsuit, xcel wetsuits, surf forecast, surf webcam, surf cam, surfboards, cheap surfboards, boardbags, leashes, surfing leash, surfboard leash, chichester surf, west sussex surfing, sussex surf, bracklesham surf,');

  // NOTE: If HOME_PAGE_TITLE is left blank (default) then TITLE and SITE_TAGLINE will be used instead.
  define('HOME_PAGE_TITLE', 'Surfboards, Xcel Wetsuits, Surfing Hardware & Surf Clothing Online.'); // usually best left blank


// EZ-Pages meta-tags.  Follow this pattern for all ez-pages for which you desire custom metatags. Replace the # with ezpage id.
// If you wish to use defaults for any of the 3 items for a given page, simply do not define it. 
// (ie: the Title tag is best not set, so that site-wide defaults can be used.)
// repeat pattern as necessary
  define('META_TAG_DESCRIPTION_EZPAGE_17','Local Surf Forecast and Up-To-Date Reports for East, West Wittering and Bracklesham. Full HD Surf Cam with Auto Pan and Zoom.');
  define('META_TAG_KEYWORDS_EZPAGE_17','wittering surf forecast, bracklesham surf, wittering surf, east wittering, west wittering, wittering webcam, wittering surf report, surf webcam, shore road webcam, shore road surf, west wittering surfing,');
  define('META_TAG_TITLE_EZPAGE_17', 'Surf Forecast and Reports for East, West Wittering and Bracklesham. Full HD Surf Cam with Auto Pan and Zoom.');

// Per-Page meta-tags. Follow this pattern for individual pages you wish to override. This is useful mainly for additional pages.
// replace "page_name" with the UPPERCASE name of your main_page= value, such as ABOUT_US or SHIPPINGINFO etc.
// repeat pattern as necessary
  define('META_TAG_DESCRIPTION_SURF_FORECAST','Local Surf Forecast and Up-To-Date Reports for East, West Wittering and Bracklesham. Full HD Surf Cam with Auto Pan and Zoom.');
  define('META_TAG_KEYWORDS_PAGE_SURF_FORECAST','wittering surf forecast, bracklesham surf, wittering surf, east wittering, west wittering, wittering webcam, wittering surf report, surf webcam, shore road webcam, shore road surf, west wittering surfing,');
  define('META_TAG_TITLE_PAGE_SURF_FORECAST', 'Surf Forecast for East Wittering & Bracklesham. HD Surf Cam.');

// Review Page can have a lead in:
  define('META_TAGS_REVIEW', 'Reviews: ');

// separators for meta tag definitions
// Define Primary Section Output
  define('PRIMARY_SECTION', ' : ');

// Define Secondary Section Output
  define('SECONDARY_SECTION', ' - ');

// Define Tertiary Section Output
  define('TERTIARY_SECTION', ', ');

// Define divider ... usually just a space or a comma plus a space
  define('METATAGS_DIVIDER', ' ');

// Define which pages to tell robots/spiders not to index
// This is generally used for account-management pages or typical SSL pages, and usually doesn't need to be touched.
  define('ROBOTS_PAGES_TO_SKIP','login,logoff,create_account,account,account_edit,account_history,account_history_info,account_newsletters,account_notifications,account_password,address_book,advanced_search,advanced_search_result,checkout_success,checkout_process,checkout_shipping,checkout_payment,checkout_confirmation,cookie_usage,create_account_success,contact_us,download,download_timeout,customers_authorization,down_for_maintenance,password_forgotten,time_out,unsubscribe,info_shopping_cart,popup_image,popup_image_additional,product_reviews_write,ssl_check');


// favicon setting
// There is usually NO need to enable this unless you need to specify a path and/or a different filename
//  define('FAVICON','favicon.ico');

?>