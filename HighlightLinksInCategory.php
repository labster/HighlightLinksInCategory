<?php
# Extension: Highlight Links in Category
# Copyright 2013, 2016, Brent Laabs and GethN7
# Released under a MIT-style license, as well as under the same terms
#    as Mediawiki.  See LICENSE for details


$wgExtensionCredits['other'][] = array(
        'name' => 'Highlight Links in Category',
        'author' => 'Brent Laabs, Arcane 21 (GethN7)',
        'version' => '0.9',
        'url' => 'https://github.com/labster/mediawiki-highlight-links-in-category',
        'descriptionmsg' => 'Highlights links in categories via customizable CSS classes',
);

$wgHooks['GetLinkColours'][] = 'HighlightLinksInCategory::onGetLinkColours';


# Configuration:
# $wgHighlightCategories is an array that configures which categories
# get an added CSS class.
# The keys are the Category names -- with underscores, without "Category:"
# The values are the CSS classes to add to links of that category
# If you want to make it so that the behavior of one category overrides
# the other, use CSS, like '.class1.class2 { only class2 stuff }'


$wgHighlightCategories = array(
    "Trope" => 'trope',
    "YMMV_Trope" => 'ymmv',
);

class HighlightLinksInCategory {

    public static function onGetLinkColours( $linkcolour_ids, &$colours ) {

        if ( ! count($wgHighlightCategories) ) {
            return true;
        }

        # linkcolour_ids only contains pages that exist, which does a lot
        # of our work for us
        $pageIDs  = array_keys($linkcolour_ids);
        $catNames = array_keys($wgHighlightCategories);

        # Get page ids with appropriate categories from the DB
        # There's an index on (cl_from, cl_to) so this should be fast
        $dbr = wfGetDB( DB_SLAVE );
        $res = $dbr->select( 'categorylinks',
            array('cl_from', 'cl_to'),
            $dbr->makeList( array(
                $dbr->makeList(
                    array( 'cl_from' => $pageIDs ), LIST_OR),
                $dbr->makeList(
                    array( 'cl_to'   => $catNames), LIST_OR)
                ),
                LIST_AND
            )
        );

        # Add the color classes to each page
        foreach ( $res as $s ) {
            $colours[ $linkcolour_ids[ $s->cl_from ] ]
                .= ' ' . $wgHighlightCategories[ $s->cl_to ];
        }
    }

}

