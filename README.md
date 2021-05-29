Highlight Links in Category
===========================

A Mediawiki Extension: If a link is a member of a category, it gets a custom CSS class.

You can add as many of these as you want, but from a UX perspective it's probably
not awesome to add 500 different style links.

Requirements
------------

Mediawiki 1.25 probably, but only tested on 1.26 and above.  Theoretically can
work on 1.12 but might require some changes.

Installation
------------

Make sure that this directory is installed in `mediawiki/extensions` (or wherever
your custom extension directory is.

Then, add this to `LocalSettings.php`:

		wfLoadExtension( 'HighlightLinksInCategory' );
		$wgHighlightLinksInCategory = array(
		    "Disambiguation_pages" => 'disambig',
		    "Templates" => 'templates',
		);

Configuration
-------------

The global variable `$wgHighlightLinksInCategory` is an array that configures which
categories get an added CSS class. The keys are the Category names, which must
include underscores instead of spaces. Do not include the `Category:` namespace.
The value for each key is the CSS class you would like to add to that
category. If you want add more than one class, separate the class names with
spaces, like:

	$wgHighlightLinksInCategory = array( "My_Cat" => "class1 class2 class3" );

If `$wgHighlightLinksInCategory` is not set or empty, this extension will do nothing.

Additionally, `$wgHighlightLinksInCategoryFollowRedirects` can be set to `true` in order to add classes based on a redirect's target's categories instead of its own categories. This defaults to `false` for backwards compatibility.

Styling
-------

Simply add the styles you need for these links to `Mediawiki:Common.css` on your wiki.
For example:

		a.disambig {
			color: rebeccapurple
		}
		a.templates {
			font-weight: bold;
			background-color: #efe;
		}

If you want one category to override another, you'll have to specify it with CSS.

		a.templates.disambig {
			font-weight: normal;
			background-color: transparent;
			color: rebeccapurple;
		}

Or alternatively, you could have defined the original `a.templates` rule as
`a.templates:not(.disambig)` instead.

There's also the link icon approach:

		a.superlink {
			background-image: url(super.png);
			padding-left: 16px;
		}

License
-------

MIT. See LICENSE for details.

Have the appropriate amount of fun!


