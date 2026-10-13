## Toposel Assignment – WordPress Plugin

This repository contains a WordPress **plugin** implementation for the assignment requirements:

- **No** Elementor / page builders
- Custom **PHP + HTML + CSS + JS**
- **Fully responsive** (desktop / tablet / mobile)
- **Dynamic content** managed via WordPress admin UI

### What this plugin provides

- A custom post type: `Landing Pages` (`tp_landing`)
- A metabox with CMS-manageable fields:
  - Sidebar (menu, quick actions, last orders)
  - Header stats / tabs
  - Promo cards + product cards + bottom banner (with image uploaders)
  - Link (URL) fields for menu items, quick actions, last orders and every card button
    (empty links fall back to `#`; values are saved with `esc_url_raw`)
- Bundled default photos in `assets/images/`, shown until an image is picked from the
  Media Library ("Use Default" in the admin restores them)
- A shortcode that turns any WordPress page into the full-screen interface (the theme's header, footer and page title are not rendered):
  - `[tp_landing id="123"]`
- A direct route to the same full-screen interface:
  - `/tp-landing/123`
- Front-end assets:
  - `assets/css/frontend.css`
  - `assets/js/frontend.js` (UI tab/switch interactions)

### Run locally with wp-env (Docker)

Requires Docker Desktop running and Node.js.

```bash
npx @wordpress/env start
```

- Site: http://localhost:8888
- Admin: http://localhost:8888/wp-admin (user `admin`, password `password`)
- Stop: `npx @wordpress/env stop`

The plugin is mounted and activated automatically.

### Install on an existing WordPress site

1. Copy this plugin folder into `wp-content/plugins/`
2. Activate **Toposel Assignment – Landing Page**
3. On activation it creates:
   - A sample `Landing Page` entry
   - A WordPress page titled **“Toposel Landing Demo”** with the shortcode embedded

### Note about the reference image

Use the provided reference image (the “BuyMore / Explore” UI). The main layout lives in `templates/landing.php` and its styling in `assets/css/frontend.css`.

### Photo credits

The default photos in `assets/images/` are from [Unsplash](https://unsplash.com) and are used under the [Unsplash License](https://unsplash.com/license) (free to use, no permission needed):

| File | Photographer / source image |
| --- | --- |
| `promo-left.jpg` | [Aleksandar Andreev](https://images.unsplash.com/photo-1715340430606-54a97728ad29) |
| `promo-right.jpg` | [Sunabi Studios](https://images.unsplash.com/photo-1744901941998-087a5944f8dc) |
| `feature.jpg` | [photo-1698148700200-c7a600cb2572](https://images.unsplash.com/photo-1698148700200-c7a600cb2572) |
| `product-1.jpg` | [Oz Seyrek](https://images.unsplash.com/photo-1577999499505-a4b64aaa9066) |
| `product-2.jpg` | [Sneep Crew](https://images.unsplash.com/photo-1632729677953-d85336661d1f) |
| `offer.jpg` | [Ihssan Rami Azouagh](https://images.unsplash.com/photo-1676427717094-43c57012d363) |
| `fav-a.jpg` | [photo-1622403980817-3832ae4d6362](https://images.unsplash.com/photo-1622403980817-3832ae4d6362) |
| `fav-b.jpg` | [photo-1732128103679-ea486e68ea5d](https://images.unsplash.com/photo-1732128103679-ea486e68ea5d) |
| `banner.jpg` | [Preston Lane](https://images.unsplash.com/photo-1673659087652-75d3c8d48a8b) |

