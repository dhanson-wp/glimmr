# Handoff: Glimmr — WordPress block theme

## Overview
**Glimmr** is a fast, image-first personal photo gallery delivered as a **WordPress block theme (FSE)**, inspired by Flickr but original. The whole system rests on one idea: **near-monochrome chrome (Ink `#212124`, white, Mist `#f3f5f6`) so the only saturated color on the page is the photography**, with a hot **Glimmr Pink `#ff0084`** reserved for primary action and focus. Square corners on photos. A real dark theme ships alongside light. The theme stays unmistakably photo-first but also supports a writing/**Journal** track with a deliberately calmer, text-forward rhythm.

This package is everything needed to build the theme to spec.

## About the design files
The files in `design-references/` are **design references created in HTML** — interactive prototypes on a pan/zoom design canvas that show the intended look, copy, spacing, and interaction states. **They are not production code to copy.** The task is to **build the actual WordPress block theme** (the `theme.json` + `templates/*.html` + `parts/*.html` + `patterns/*.php` + `styles/*.json` structure) so that, rendered in WordPress, it matches these references.

Open them in a browser:
- **`design-references/Glimmr Theme Canvas.html`** — every template, template part, the custom admin feature, and the six style variations. Each section has a **Spec card** (class `.spec`) listing that screen's file, the **template parts** + **core blocks** it uses, its interaction states, and a note. **The spec cards are your per-screen block inventory — read them first.**
- **`design-references/Glimmr Patterns.html`** — the 12 reusable block patterns, each shown Desktop + Mobile (dropped into a page) with its own Spec card.

To pan: drag the background. To zoom: ctrl/⌘ + scroll (or pinch). Click an artboard's expand icon to focus it; ←/→ to step through.

## Fidelity
**High-fidelity.** Final colors, typography, spacing, copy, and interaction states. Recreate pixel-faithfully using **core WordPress blocks** and the tokens in `starter-theme.json`. The visual style is **binding** to the Glimmr design system — do not invent colors, type, or components not grounded here.

## Hard constraints (confirmed with the owner)
- **Target: latest WordPress (7.0).** `theme.json` **version 3**; any custom block is **`apiVersion: 3`**.
- **Full block theme / FSE.** No page builders.
- **Core blocks as far as possible.** Allowed beyond core:
  - **One plugin dependency:** `x3p0/media-data` (EXIF/ID3 metadata blocks) for the single-photo EXIF readout. See `block-markup.md`.
  - **One or two small custom theme elements** (registered in the theme, `apiVersion: 3`). The taxonomy featured-image feature is the first; keep additions minimal.
- **No social-icon blocks.** The social row is replaced by the **Link-in-bio** pattern (full-width link buttons).
- **Zero gradients except the photo scrim**, no glassmorphism, no `backdrop-filter`, no resting card shadows (hover only).

## What to build

### 1. `theme.json` (start from `starter-theme.json`)
A complete, valid v3 `theme.json` with the Glimmr palette, Figtree type scale, the 7-step spacing preset, shadows, and the three photo **duotones**. Default styles set Mist background, Figtree body, pink buttons/links, 2px pink focus, and **square corners on images**. Drop it in and refine.

### 2. Templates — `templates/*.html`
From the **Theme Canvas** spec cards:
| File | Screen | Spec card |
|---|---|---|
| `front-page.html` | Pinned hero cover + justified photostream + pagination | "Front page" |
| `index.html` | Title band + stream (fallback) | "Index" |
| `archive.html` | Term cover hero + filtered grid (+ empty state) | "Archive" |
| `single.html` | Photo + EXIF sidebar + native lightbox + comments | "Single photo" |
| `page.html` | Constrained content column (About) | "Page" |
| `404.html` | Message + search + recovery grid | "404" |
| `search.html` | Query-title header + grid (+ empty state) | "Search results" |

### 3. Template parts — `parts/*.html`
- `header.html` — ink chrome: wordmark + `core/navigation` (with **Albums submenu** + **mobile overlay drawer**) + `core/search` + Follow button.
- `footer.html` — ink footer: wordmark + blurb + three nav columns + base row.

Both have worked markup in `block-markup.md`.

### 4. Patterns — `patterns/*.php`
The **12 patterns** from the Patterns Canvas, in two registered categories (`glimmr-photo`, `glimmr-journal`). Mapping table + a worked example in `block-markup.md`. Journal patterns use a constrained ~680px group with a bumped body size and ~1.75 line-height.

### 5. Style variations — `styles/*.json`
Six one-file `theme.json` variations: **Daylight** (default), **Midnight** (dark), **Silver**, **Sepia**, **Cyanotype**, **Gallery**. Silver/Sepia/Cyanotype apply a global photo **duotone** (pairs are in `starter-theme.json` → `settings.color.duotone`; apply per-variation via `styles.blocks.core/post-featured-image.filter.duotone` + `core/image` + `core/cover`). Midnight inverts to the dark palette in the design system. See the two "Style variations" spec cards.

### 6. Custom feature — Taxonomy featured image
A theme element that adds a **Featured image** control to the **Edit Category / Edit Tag** admin forms (with a `wp.media()` picker), storing an image id as **term meta**. That image becomes the **archive cover hero** (surfaced via a `glimmr/term-image` block binding — see `block-markup.md` → archive). The Theme Canvas "Custom feature" section shows the exact admin UI (set + empty states + the Media Library modal). Spec card: "Taxonomy featured image".

## Design tokens
Full set in `starter-theme.json`. Quick reference:

**Color** — `mist #f3f5f6` (bg) · `white #ffffff` (surface) · `ink #212124` (chrome) · `fg #101012` (text) · `muted #5c5c5c` (meta) · `faint #898989` · `on-ink #f3f5f6` · `hairline #dddddd` · `pink #ff0084` (accent/focus/active) · `pink-action #d1006e` (button bg, AA) · `blue #0063a8` (link, hover `#004f86`). Dark theme: bg `#212124`, surface `#2b2b2e`, pink `#ff3da0`, blue `#5cb8ff`, border `#38383a`.

**Type** — Figtree (Google Fonts, OFL; self-host the variable woff2). Weights 300 (heroes) / 400 (body) / 500 / 600 (buttons, active). Sizes: chip 11–12 · meta 12–14 · base 14–16 · large 16–20 · x-large 24–36 · xx-large 36–52. Line-height 1.5 body, 1.1 heroes. Journal body bumps a step at ~1.75.

**Spacing** — 4 / 8 / 16 / 24 / 48 / 96 / 160 (slugs 10–70). Photostream gap **6px**. Content 1060px, wide 1400px.

**Radii** — inputs/buttons **3px**, large cards 16px, avatars/pills/chips full. **Images always 0.**

**Shadows** — `sm` hover lift `rgba(0,0,0,.2) 0 1px 4px` · `md` dropdown `0 3px 6px -1px` · `lg` modal `rgba(0,0,0,.5) 0 2px 8px` · `inset` media edge. No resting shadows.

**Motion** — 100–500ms, `ease-out cubic-bezier(0.22,0.61,0.36,1)`. Signature: photo hover = scrim + title fade (opacity 0.25s). No bounce/spring. Respect `prefers-reduced-motion`.

**Scrim** (only gradient in the system) — vertical black, transparent top → ~70% bottom; heroes add a 40–45% dim under it. In blocks: `core/cover` `dimRatio:40`.

## Iconography
**Material Symbols Rounded, Fill weight** (Apache 2.0). Ship the **real SVG paths**, not the webfont (exporters can't rasterize icon-font ligatures). The reference `ICON_PATHS` map (real paths) is in `design-references/gl-atoms.jsx`. Essentials: search, favorite, chat_bubble, share, upload, slideshow, more_horiz, photo_camera, download, star, grid_view, menu, close, check, chevron_left/right. **No emoji.**

## Content voice
Playful but restrained, third-person, sentence case everywhere, **wordmark "glimmr" always lowercase**. CTAs are single verbs ("Follow," "View"). Meta reads like a camera readout (`ƒ2.8 · 1/250 s · ISO 400`, middot separators, lowercase units). Empty states are calm ("No photos here yet."), never cute-error. Examples and full guidance are in the design system; copy on every screen is final — lift it verbatim from the references.

## Future features (do NOT build in pass 1)
See **`future-features.md`** for two planned additions and the cheap "seams" to leave for them now:
1. **Private family site** — invite + branded login (the gate is near-core; invite flow needs custom code).
2. **Location data** — a "Where" line + static map from photo GPS EXIF (privacy: coarsen/opt-in by default).

## Files in this package
- `README.md` — this document (self-sufficient build brief).
- `starter-theme.json` — drop-in `theme.json` v3 with all tokens + duotones.
- `block-markup.md` — paste-ready block markup for parts, templates, the EXIF block, the archive cover binding, and patterns (+ the 12-pattern mapping table and the format rules).
- `future-features.md` — private/login/invite + location, scoped for later.
- `design-references/` — the two HTML design canvases and all their component files. Open the two `.html` files; read the on-canvas **Spec cards** for per-screen block inventories and interaction notes.

## Source repos (for reference)
- Block-theme + block-development skills: `github.com/WordPress/agent-skills` (trunk).
- Block-markup validator (rules internalized in `block-markup.md`): `github.com/pluginslab/wp-blockmarkup-mcp` (main).
- EXIF plugin dependency: `github.com/x3p0-dev/x3p0-media-data` (master).
