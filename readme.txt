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

Glimmr is a block theme for personal photo galleries. Near-monochrome chrome (Ink, white, Mist) keeps the photographs the only saturated thing on the page, with a hot Glimmr Pink reserved for action and focus. Square-cornered photos, a justified photostream, native lightbox on single photos, a calmer writing-forward Journal track, and a custom taxonomy featured-image control that drives each category/tag's cover hero.

Ships with six switchable style variations: Daylight (default), Midnight (dark), Silver, Sepia and Cyanotype (each a global photo duotone), and Gallery (museum white).

The theme is content-agnostic: it ships templates, parts, patterns and styles, never content. No category or tag slugs are hardcoded; archive heroes read the queried term, and the front-page pin is a sticky post.

== Required plugin ==

For full single-photo fidelity, install and activate `x3p0/media-data` (`x3p0-media-data`). Glimmr uses that block plugin for the camera and exposure readout on `templates/single.html`. The rest of the theme is theme-owned and core-block based.

On WordPress.com, plugin installation requires a paid plan. If the plugin is not installed, the theme still works, but the single-photo EXIF panel will not match the design.

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
