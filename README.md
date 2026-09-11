# Glimmr

A fast, image-first WordPress block theme for personal photo galleries. The chrome stays quiet and near-monochrome (Ink, white, and Mist) so the only saturated colour on the page is your photography, with a single hot Glimmr Pink reserved for action and focus.

## Overview

Glimmr is built from core WordPress blocks wherever possible, configured through `theme.json` and a small amount of theme CSS. It pairs a photo-first front end (a pinned hero, a justified square photostream, a native lightbox on single photos) with a calmer, text-forward Journal track for writing. Six switchable style variations re-skin the whole site, and three of them apply a global duotone to every photo.

The theme is content-agnostic: it ships templates, parts, patterns, and styles, never content. No category or tag slugs are hardcoded anywhere; archive heroes read the queried term, and the front-page pin is simply a sticky post.

## Features

- Pinned front-page hero driven by a sticky post's featured image.
- Masonry-style, square-cornered photostream with a hover scrim and title fade.
- Single-photo screen with a native core lightbox and a camera-style EXIF readout.
- A writing-forward Journal track with a looser reading measure.
- Custom taxonomy featured-image control that drives each category or tag's cover hero.
- Twelve filesystem block patterns plus a dynamic Albums index pattern.
- Six style variations: Daylight, Midnight, Silver, Sepia, Cyanotype, and Gallery.
- Self-hosted Figtree (variable woff2), so no third-party font requests.
- Built for accessibility: visible focus rings, reduced-motion support, and AA-corrected action colours.

## Requirements

- WordPress 6.9 or newer (tested up to 7.0).
- PHP 8.0 or newer.
- `x3p0/media-data` (`x3p0-media-data`) for the single-photo camera and exposure readout.

## WordPress.com notes

Glimmr is built around core blocks and theme-owned code. The only extra plugin needed for full design fidelity is `x3p0-media-data`, which powers the camera and exposure panel on single photo pages.

On WordPress.com, plugin installation requires a paid plan. If the plugin isn't installed, the rest of the theme still works, but the EXIF panel won't match the design.

## Installation

1. Download or clone this repository into `wp-content/themes/glimmr`.
2. Install and activate `x3p0-media-data` if you want the EXIF panel on single photos to match the design.
3. In the admin, go to Appearance, Themes, and activate Glimmr.
4. For the best front page, set Settings, Reading to show your latest posts, and mark one post as sticky to feature it in the hero.

## Style variations

Switch in Appearance, Editor, Styles:

| Variation | Treatment |
| --- | --- |
| Daylight | The default light theme. |
| Midnight | A dark room: near-black chrome and surfaces, a brighter pink. |
| Silver | Black and white; a grayscale duotone over every photo. |
| Sepia | Warm and analogue; a sepia duotone and a toasted paper page. |
| Cyanotype | Blueprint blue; a cyanotype duotone and a cool paper page. |
| Gallery | Museum white; generous air, no duotone. |

Each variation changes only the tokens, so the same templates and blocks re-skin with no markup change.

## Taxonomy featured image

Editing a category or tag adds a Featured image control (a media picker that stores an attachment id as term meta). That image becomes the term archive's cover hero. It is surfaced through a `glimmr/term-image` binding source and a scoped cover render filter, so the archive template stays pure block markup.

## Templates and patterns

Templates: `front-page`, `index`, `archive`, `single`, `page`, `search`, `404`, plus slug-specific page templates for About, Albums, Contact, Link in bio, Newsletter, Popular, Prints, and Tags. Parts: `header` and `footer`.

Patterns are grouped under two categories, "Glimmr: Photo" (heroes, album showcase, link in bio, about, colophon, subscribe, closing CTA) and "Glimmr: Journal" (query loop, single, notes). The Albums page also registers a dynamic Albums index pattern from live category data. Insert filesystem patterns from the block inserter.

## Development

Everything is plain files; there is no build step. Design configuration lives in `theme.json`, with `assets/css/glimmr.css` covering what `theme.json` cannot express (the photostream grid, hover scrim, nav submenu, chip bar, EXIF readout, and lightbox polish). PHP is kept minimal: asset loading, pattern categories, dynamic patterns, and small theme blocks in `functions.php`, the taxonomy featured-image feature in `inc/featured-term-image.php`, and block-binding sources in `inc/block-bindings.php`.

```
glimmr/
├── theme.json            Design tokens, presets, and base styles
├── style.css             Theme header
├── functions.php         Asset loading, pattern categories, feature includes
├── blocks/               Breadcrumbs, heart button, and photo actions blocks
├── inc/                  Taxonomy featured image + block-binding sources
├── templates/            Block templates
├── parts/                Header and footer
├── patterns/             Twelve filesystem block patterns
├── styles/               Six style variations
└── assets/               Fonts, CSS, JS, admin CSS, and pattern placeholder images
```

## Credits and licence

- Typeface: [Figtree](https://github.com/erikdkennedy/figtree) by Erik Kennedy (SIL Open Font License 1.1).

Glimmr is released under the GPL-2.0-or-later licence. See [LICENSE](LICENSE) for details.
