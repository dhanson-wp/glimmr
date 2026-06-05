=== Glimmr ===
Contributors: derekhanson
Requires at least: 6.9
Tested up to: 7.0
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A fast, image-first photo-gallery block theme. Quiet near-monochrome chrome so the only saturated colour on the page is the photography.

== Description ==

Glimmr is a full-site-editing block theme for personal photo galleries. Near-monochrome chrome (Ink, white, Mist) keeps the photographs the only saturated thing on the page, with a hot Glimmr Pink reserved for action and focus. Square-cornered photos, a justified photostream, native lightbox on single photos, a calmer writing-forward Journal track, and a custom taxonomy featured-image control that drives each category/tag's cover hero.

Ships with six switchable style variations: Daylight (default), Midnight (dark), Silver, Sepia and Cyanotype (each a global photo duotone), and Gallery (museum white).

The theme is content-agnostic: it ships templates, parts, patterns and styles, never content. No category or tag slugs are hardcoded; archive heroes read the queried term, and the front-page pin is a sticky post.

== Plugins ==

Recommended (optional):

* **X3P0: Media Data** (`x3p0/media-data`) by Justin Tadlock — provides the EXIF/ID3 metadata blocks used by the single-photo camera readout (camera, aperture, shutter, ISO, focal length, date). The gallery, archives and all other screens work without it; only the single-photo EXIF block needs it. Install from the plugin directory: search "X3P0 Media Data".

== Style variations ==

Switch in Appearance → Editor → Styles:

* Daylight — the default light theme.
* Midnight — dark room.
* Silver — black-and-white photo duotone.
* Sepia — warm analogue photo duotone.
* Cyanotype — blueprint-blue photo duotone.
* Gallery — museum white, no duotone.

== Custom feature: taxonomy featured image ==

Edit Category / Edit Tag gains a "Featured image" control (a media picker storing an attachment id as term meta). That image becomes the term archive's cover hero, surfaced through the `glimmr/term-image` binding source and a scoped cover render filter.

== Changelog ==

= 1.0.0 =
* Initial release.
