# Future features — scoped for later

Two features the owner wants to add **after** the core theme ships. They are recorded here so the build leaves room for them and doesn't paint into a corner. **Do not build these in the first pass** unless asked — but read them, because a couple of early decisions make them painless later.

---

## 1. Private family site — invite + branded login

**Goal.** The whole site is private: only authenticated users (family) can see anything. A guest hitting any URL is sent to a calm, Glimmr-branded sign-in screen. New family members join by **invite**, not public registration.

### Feasibility (honest)
- **Gating the site private:** pure-core-adjacent. A small `template_redirect` guard in `functions.php` (or a tiny mu-plugin) redirects logged-out users to the login screen for every front-end request except the login route and assets. ~15 lines. No plugin required.
- **Login UI:** WordPress has `core/loginout` (a link) and the classic `wp-login.php`. A *branded* login **screen** is not a core block. Two routes:
  - **Lightweight:** style `wp-login.php` via `login_enqueue_scripts` + `login_headerurl`/`login_headertext` filters (swap the logo for the glimmr wordmark, set Ink/Mist/pink). Fastest; stays in core's form.
  - **Block-native:** a custom page template `templates/login.html` built from core blocks (`core/heading`, `core/paragraph`, and a custom `glimmr/login-form` block that posts to `wp_signon`). More work, fully on-brand, matches the rest of the theme. **Recommended** for the "most beautiful theme" goal.
- **Invite flow:** **needs custom code** (this is the one genuinely non-core piece). Minimal shape:
  - An admin screen (or a front-end "Invite" form for the owner) that creates a one-time, expiring invite token (stored in an option/CPT/usermeta) and emails a signup link.
  - A token-validated `templates/register.html` (or `glimmr/invite-accept` block) that lets the invitee set name + password, then creates the user at a low role (e.g. `subscriber`/a custom `family` role) and logs them in.
  - Roles: a custom `family` capability set so family can view (and maybe comment) but not edit.

### Design direction (so it matches)
- Reuse the **Link-in-bio / centered group** rhythm: narrow `core/group` (~420px), wordmark, one line of copy, the form, one pink button. Same quiet chrome, Mist background, square inputs (radius 3px), 2px pink focus ring.
- Empty/error states stay calm per the voice guide ("That invite has expired." — not "OOPS!").
- An **invite email** template in the same restrained voice.

### Build-now hooks (cheap insurance)
- Keep the **header "Follow" button** and the public **subscribe / link-in-bio** patterns *optional* (registered patterns, not hard-coded into templates) so a private site can simply not place them.
- Add a `family` role stub and a `glimmr_is_private()` helper now, even if it returns `false`, so wiring the gate later is a one-line flip.

---

## 2. Location data — map / place companion

**Goal.** Show where a photo was taken: a place name, and eventually a small map. The owner finds this "incredible" — it's a natural fit for travel/landscape work.

### Feasibility
- **Source of truth:** most photos carry **GPS EXIF**. The `x3p0/media-data` plugin already in the stack exposes EXIF fields — confirm whether it surfaces `GPSLatitude/GPSLongitude` (the documented field list focuses on camera/exposure; GPS may need a small reader). If not exposed, read it server-side with `wp_read_image_metadata()` / `exif_read_data()` on upload and store `lat`/`lng`/`place` as **post meta**.
- **Display, phased:**
  - **Phase A (no JS, no key):** a "Where" line in the single-photo sidebar — a place name + coordinates, formatted like EXIF (`46.0207° N, 7.7491° E`). Pure meta + a small block or a `core/paragraph` with a binding. Trivial.
  - **Phase B (static map):** a custom `glimmr/photo-map` block that renders a static map image (e.g. an `<img>` from a static-map endpoint) centered on the photo's lat/lng. One block, `apiVersion:3`, server-rendered. Needs a tiles/static-map provider (some need a key — keep it server-side).
  - **Phase C (interactive):** an optional Leaflet/MapLibre map; heavier, only if wanted. A **map archive** ("all photos on a map") is a fun stretch: a query of geo-tagged posts plotted as pins.

### Privacy (important for a family site)
- GPS on **family photos** can reveal home/school locations. Default to **off** or **coarsened**: strip or round coordinates on upload, and let the owner opt a photo in to precise location. Make "show location" a per-photo toggle (post meta), defaulting to hidden.
- Never print precise child-location data publicly; on a private site it's lower-risk but still worth the coarsening default.

### Design direction
- The "Where" line lives in the single-photo **metadata sidebar**, in the same `muted` meta type as EXIF, separated by middots. A static map sits square-cornered (radius 0, like all imagery) below the EXIF block.
- A map archive would reuse the **archive** template skeleton with the grid swapped for the map.

### Build-now hooks
- On the single template, leave the sidebar a flexible `core/group` so a "Where" line / map block drops in without restructuring.
- When writing the upload/meta handling for the term-image feature, it's cheap to **also** capture `lat`/`lng`/`place` post meta in the same pass — even if nothing renders them yet.

---

## Summary for the first build
Ship the core theme (templates, parts, the 12 patterns, the taxonomy featured-image feature, the six style variations). **Leave these seams:**
1. patterns stay optional/unplaced (not hard-wired) so a private site can omit public CTAs;
2. a `glimmr_is_private()` helper + `family` role stub;
3. capture `lat`/`lng`/`place` post meta at upload time alongside the term-image work;
4. keep the single-photo sidebar a flexible group.

Those four cost almost nothing now and turn both future features into additive work instead of refactors.
