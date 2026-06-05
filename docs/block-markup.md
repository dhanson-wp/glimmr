# Block markup — paste-ready

WordPress block markup for the parts, templates, and patterns. **Format rules** (validated the way `wp-blockmarkup-mcp` would — see CLAUDE.md):

- Color classes: `has-{slug}-color`, `has-{slug}-background-color` **+** `has-background`; font size `has-{slug}-font-size`. **Never** `has-background-color-{slug}`.
- Attribute values are **slugs/strings** (`"fontSize":"large"`), never numbers.
- **Dynamic** blocks (navigation, search, query, post-template, post-title/date/excerpt/terms/featured-image, query-pagination, comments, loginout) are **self-closing**: `<!-- wp:name {attrs} /-->`. Only static blocks have inner HTML + a closing comment.
- Use the **theme token slugs** from `starter-theme.json` (`ink`, `mist`, `pink`, `pink-action`, `blue`, spacing `30/40/50`, font sizes `base/large/x-large/xx-large`) — not WP default-palette values.
- Spacing uses preset vars: `var:preset|spacing|40` = 24px.
- Square corners on photos are enforced in `theme.json` (`core/image` + `core/post-featured-image` radius `0`); don't re-set per block.

Each screen's **full block inventory** is on the spec card in `Glimmr Theme Canvas.html` / `Glimmr Patterns.html`. Below are the load-bearing parts + worked examples in the house style; the rest follow the same shapes.

---

## Template part — `parts/header.html`

```html
<!-- wp:group {"tagName":"header","className":"hdr","backgroundColor":"ink","textColor":"on-ink","layout":{"type":"constrained","contentSize":"1400px"}} -->
<header class="wp-block-group hdr has-on-ink-color has-ink-background-color has-background">
  <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
  <div class="wp-block-group">
    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
    <div class="wp-block-group">
      <!-- wp:site-title {"level":0,"className":"glmr-wordmark"} /-->
      <!-- wp:navigation {"overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"left"}} /-->
    </div>
    <!-- /wp:group -->

    <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
    <div class="wp-block-group">
      <!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search photos","buttonText":"Search","buttonPosition":"no-button"} /-->
      <!-- wp:buttons -->
      <div class="wp-block-buttons"><!-- wp:button -->
        <div class="wp-block-button"><a class="wp-block-button__link wp-element-button">Follow</a></div>
      <!-- /wp:button --></div>
      <!-- /wp:buttons -->
    </div>
    <!-- /wp:group -->
  </div>
  <!-- /wp:group -->
</header>
<!-- /wp:group -->
```

**Notes.** The wordmark is `core/site-title` styled with the pink tittle (CSS in the theme stylesheet targets `.glmr-wordmark`; the dotless-i markup can't live in site-title, so style the title text and add the pink dot via a `::after` on the last glyph, OR ship the wordmark as a small synced pattern). The **Albums submenu** and the **mobile drawer** are native `core/navigation` features: build the menu in the Site Editor, give "Albums" submenu items (the categories), and set `overlayMenu:"mobile"`. Subnav panel styling (white, `shadow|md`, pink active term) is theme CSS targeting `.wp-block-navigation__submenu-container`.

---

## Template part — `parts/footer.html`

```html
<!-- wp:group {"tagName":"footer","className":"ftr","backgroundColor":"ink","textColor":"on-ink","layout":{"type":"constrained","contentSize":"1400px"}} -->
<footer class="wp-block-group ftr has-on-ink-color has-ink-background-color has-background">
  <!-- wp:columns -->
  <div class="wp-block-columns">
    <!-- wp:column {"width":"40%"} -->
    <div class="wp-block-column" style="flex-basis:40%">
      <!-- wp:site-title {"level":0} /-->
      <!-- wp:paragraph {"fontSize":"meta"} -->
      <p class="has-meta-font-size">A quiet place for photographs. The chrome steps back so the pictures can speak.</p>
      <!-- /wp:paragraph -->
    </div>
    <!-- /wp:column -->
    <!-- wp:column --><div class="wp-block-column">
      <!-- wp:heading {"level":5,"fontSize":"chip"} --><h5 class="wp-block-heading has-chip-font-size">Explore</h5><!-- /wp:heading -->
      <!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"}} /-->
    </div><!-- /wp:column -->
    <!-- repeat columns for Account / About -->
  </div>
  <!-- /wp:columns -->
</footer>
<!-- /wp:group -->
```

---

## Template — `templates/front-page.html`

```html
<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:cover {"useFeaturedImage":false,"url":"<pinned-photo>","dimRatio":40,"minHeight":74,"minHeightUnit":"vh","isDark":true,"align":"full"} -->
<div class="wp-block-cover alignfull is-dark" style="min-height:74vh">
  <span aria-hidden="true" class="wp-block-cover__background has-background-dim-40 has-background-dim"></span>
  <img class="wp-block-cover__image-background" src="<pinned-photo>" alt=""/>
  <div class="wp-block-cover__inner-container">
    <!-- wp:heading {"fontSize":"xx-large","textColor":"white"} -->
    <h2 class="wp-block-heading has-white-color has-text-color has-xx-large-font-size">The last hour of light.</h2>
    <!-- /wp:heading -->
    <!-- wp:paragraph {"textColor":"white"} -->
    <p class="has-white-color has-text-color">A running archive of light, weather, and the long way round.</p>
    <!-- /wp:paragraph -->
  </div>
</div>
<!-- /wp:cover -->

<!-- wp:query {"queryId":1,"query":{"perPage":24,"postType":"post","order":"desc","orderBy":"date"},"align":"wide"} -->
<div class="wp-block-query alignwide">
  <!-- wp:post-template {"layout":{"type":"grid","minimumColumnWidth":"240px"},"style":{"spacing":{"blockGap":"6px"}}} -->
    <!-- wp:post-featured-image {"isLink":true,"aspectRatio":"auto"} /-->
  <!-- /wp:post-template -->
  <!-- wp:query-pagination {"paginationArrow":"chevron","layout":{"type":"flex","justifyContent":"center"}} -->
    <!-- wp:query-pagination-previous /-->
    <!-- wp:query-pagination-numbers /-->
    <!-- wp:query-pagination-next /-->
  <!-- /wp:query-pagination -->
</div>
<!-- /wp:query -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->
```

The justified, aspect-aware grid + the hover scrim/title are theme CSS on `.wp-block-post-template` / `.wp-block-post-featured-image` (the post-template `grid` layout with `minimumColumnWidth:240px` + `blockGap:6px` is the core hook; column-count masonry is a CSS enhancement).

---

## Template — `templates/single.html` (photo)

```html
<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide">
  <!-- wp:column {"width":"68%"} -->
  <div class="wp-block-column" style="flex-basis:68%">
    <!-- wp:post-featured-image {"lightbox":{"enabled":true}} /-->
    <!-- wp:post-content {"layout":{"type":"constrained"}} /-->
    <!-- wp:comments --> … <!-- /wp:comments -->
  </div>
  <!-- /wp:column -->
  <!-- wp:column {"width":"32%"} -->
  <div class="wp-block-column" style="flex-basis:32%">
    <!-- wp:post-title {"fontSize":"x-large"} /-->
    <!-- wp:post-date {"fontSize":"meta","textColor":"muted"} /-->

    <!-- EXIF: the one allowed plugin, x3p0/media-data (mediaId bound to the post's featured image) -->
    <!-- wp:x3p0/media-data {"mediaId":0} -->
    <div class="wp-block-x3p0-media-data">
      <!-- wp:x3p0/media-data-field {"field":"camera"} /-->
      <!-- wp:x3p0/media-data-field {"field":"aperture"} /-->
      <!-- wp:x3p0/media-data-field {"field":"shutter_speed"} /-->
      <!-- wp:x3p0/media-data-field {"field":"iso"} /-->
      <!-- wp:x3p0/media-data-field {"field":"focal_length"} /-->
      <!-- wp:x3p0/media-data-field {"field":"created_timestamp"} /-->
    </div>
    <!-- /wp:x3p0/media-data -->

    <!-- wp:post-terms {"term":"post_tag"} /-->
  </div>
  <!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->
```

`lightbox.enabled` on the featured image is core's **native** lightbox. Bind `mediaId` to the featured image via Block Bindings (WP 6.9+): `"metadata":{"bindings":{"mediaId":{"source":"core/post-meta","args":{"key":"_thumbnail_id"}}}}` on the `x3p0/media-data` block.

---

## Archive cover from the custom term image — `templates/archive.html`

The custom feature (see `future-features.md` is front-end of this) stores a term-meta image id. Surface it in the cover via binding:

```html
<!-- wp:cover {"dimRatio":40,"minHeight":56,"minHeightUnit":"vh","align":"full","metadata":{"bindings":{"url":{"source":"glimmr/term-image"}}}} -->
<div class="wp-block-cover alignfull">
  <span aria-hidden="true" class="wp-block-cover__background has-background-dim-40 has-background-dim"></span>
  <div class="wp-block-cover__inner-container">
    <!-- wp:query-title {"type":"archive","fontSize":"xx-large","textColor":"white"} /-->
    <!-- wp:term-description {"textColor":"white"} /-->
  </div>
</div>
<!-- /wp:cover -->
```

Register a `glimmr/term-image` binding source in PHP that returns the term-meta image URL for the queried term.

---

## Patterns — `patterns/*.php`

Each pattern file is PHP header + block markup. Header example:

```php
<?php
/**
 * Title: Hero — Full-bleed cover
 * Slug: glimmr/hero-cover
 * Categories: glimmr-photo, banner
 * Description: One photo edge-to-edge with title, tagline, pill and a pink button.
 */
?>
<!-- wp:cover {"dimRatio":40,"minHeight":78,"minHeightUnit":"vh","isDark":true,"align":"full"} -->
<div class="wp-block-cover alignfull is-dark" style="min-height:78vh">
  <span aria-hidden="true" class="wp-block-cover__background has-background-dim-40 has-background-dim"></span>
  <img class="wp-block-cover__image-background" src="<?php echo esc_url( get_theme_file_uri( 'assets/img/hero.jpg' ) ); ?>" alt=""/>
  <div class="wp-block-cover__inner-container">
    <!-- wp:paragraph {"className":"pill","backgroundColor":"pink","textColor":"white","fontSize":"chip","style":{"border":{"radius":"9999px"}}} -->
    <p class="pill has-white-color has-pink-background-color has-background has-chip-font-size" style="border-radius:9999px">Sunsets · Tuscany</p>
    <!-- /wp:paragraph -->
    <!-- wp:heading {"fontSize":"xx-large","textColor":"white"} -->
    <h2 class="wp-block-heading has-white-color has-text-color has-xx-large-font-size">The last hour of light.</h2>
    <!-- /wp:heading -->
    <!-- wp:paragraph {"textColor":"white"} -->
    <p class="has-white-color has-text-color">Shot on available light, edited with a light hand.</p>
    <!-- /wp:paragraph -->
    <!-- wp:buttons -->
    <div class="wp-block-buttons"><!-- wp:button -->
      <div class="wp-block-button"><a class="wp-block-button__link wp-element-button">View album</a></div>
    <!-- /wp:button --></div>
    <!-- /wp:buttons -->
  </div>
</div>
<!-- /wp:cover -->
```

**Register two pattern categories** in `functions.php`: `glimmr-photo` and `glimmr-journal`. The 12 patterns map 1:1 to the spec cards in `Glimmr Patterns.html`:

| Slug | Pattern | Backbone blocks |
|---|---|---|
| `glimmr/hero-cover` | Hero — Full-bleed cover | cover + heading + paragraph + buttons |
| `glimmr/hero-split` | Hero — Split intro | columns → image / heading + paragraph + buttons |
| `glimmr/hero-mosaic` | Hero — Photo mosaic | gallery (or columns of images) + heading |
| `glimmr/album-showcase` | Album showcase | columns of cover cards |
| `glimmr/about-photographer` | About the photographer | columns → avatar / heading + paragraph + buttons |
| `glimmr/link-in-bio` | Link in bio | group → avatar + heading + paragraph + vertical buttons |
| `glimmr/colophon` | Colophon / Gear | columns → heading + list |
| `glimmr/journal-loop` | Journal query loop | query → post-template → columns(featured-image / title+date+excerpt) |
| `glimmr/journal-single` | Journal single | group(constrained) → post-title + post-date + post-content + post-terms |
| `glimmr/notes` | Notes | query → post-template → post-date + post-excerpt |
| `glimmr/subscribe` | Email subscribe CTA | group(bg) + heading + paragraph + buttons |
| `glimmr/closing-cta` | Closing CTA band | cover/group + heading + buttons |

**Journal rhythm** (patterns 8–10): a `core/group` constrained to ~680px, body `font-size` bumped a step with `lineHeight` ~1.75 — set on the group via `style.typography` so the whole track inherits the looser measure.

Pull exact copy, photo aspect ratios, spacing and per-pattern hover/focus notes straight from the spec cards.
