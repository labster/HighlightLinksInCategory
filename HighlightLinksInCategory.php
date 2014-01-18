<?php
# Extension: Highlight Links in Category
# Copyright 2013, Brent Laabs and GethN7
# Released under a MIT-style license, as well as under the same terms
#    as Mediawiki.  See LICENSE for details


$wgExtensionCredits['other'][] = array( 
        'name' => 'Highlight Link in Category', 
        'author' => 'Brent Laabs, Arcane 21 (GethN7)',
        'version' => '0.5',
        'url' => 'https://github.com/labster/mediawiki-highlight-links-in-category',
        'descriptionmsg' => 'Highlights links in categories via customizable CSS classes',
); 

$wgHooks['LinkEnd'][] = 'HighlightCategoryLinks::HCLExtensionLinkEnd';


class HighlightCategoryLinks {

	private static $CategoryTrope;
	private static $CategoryYMMV;

	public static function HCLExtensionLinkEnd( $dummy, Title $target, array $options, &$html, array &$attribs, &$ret ) {
		if (isset($attribs['class'])) {
			return true; # don't mess with it if we have interwiki/broken/redirect
		} elseif ( self::pageInCategory("YMMV",  self::$CategoryYMMV,  $target) ) {
			$attribs['class'] = "ymmvlink";
		} elseif ( self::pageInCategory("Trope", self::$CategoryTrope, $target) ) {
			$attribs['class'] = "tropelink";
		}
		return true;
	}

	private static function pageInCategory ($category, &$categoryArray, $target) {
		if (! $categoryArray) {
			self::getCatHash($category, $categoryArray);
		}
		return isset($categoryArray[$target->getArticleID()]);
	}

	private static function getCatHash ($category, &$categoryArray) {
		global $wgMemc;
		# Check memcached first (can be commented out if the absence of memcached)
		$categoryArray = $wgMemc->get("HLCategoryList:$category");
		if ($categoryArray) { return; }

		# We need to look this up in the DB
		$underscored_category = $category;
		preg_replace( '/ /', '_', $underscored_category);
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'categorylinks', 'cl_from', array( 'cl_to' => $underscored_category ) );
		foreach ( $res as $row ) {
			$categoryArray[$row->cl_from] = True;  # map page ids to true for O(1) lookups
		}

		# Cache result for a day, and return (can be commented out if the absence of memcached)
		$wgMemc->set("HLCategoryList:$category", $categoryArray, 86400 );
		return;
	}

}

