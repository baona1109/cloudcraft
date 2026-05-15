# CloudCraft — Astra Child Theme

A child theme for Astra with three built-in features tailored for CloudCraft.

---

## Installation

1. Upload the `astra-child` folder to `/wp-content/themes/` via FTP or cPanel File Manager.
2. Go to **Appearance → Themes** in your WordPress dashboard.
3. Activate **Astra Child – CloudCraft**.

That's it. All three features activate automatically.

---

## Feature 1 — Hide tags on posts with no tags

**How it works:**  
The `the_tags` filter returns an empty string when a post has no tags assigned,
so the tag label/wrapper never renders on screen.

**Nothing to configure** — works automatically on all posts.

---

## Feature 2 — Fallback featured image

When a post has no featured image WordPress will now show:

1. **Category default image** (if set) — highest priority
2. **Site logo** — if no category image is defined
3. **Site icon / favicon** — last resort

### Setting a category default image

1. Go to **Posts → Categories** in the dashboard.
2. Click **Edit** on any category.
3. You'll see a new **"Default Featured Image"** field.
4. Click **Upload / Choose Image** and select an image from your Media Library.
5. Save the category.

Posts in that category without their own featured image will now show this image.

---

## Feature 3 — Top Categories widget

### Using the widget (sidebar)

1. Go to **Appearance → Widgets**.
2. Find the **"CloudCraft: Top Categories"** widget.
3. Drag it into your desired sidebar (e.g. Blog Sidebar).
4. Set a title, number of categories to show, and whether to display post counts.
5. Save.

Categories are automatically sorted by number of published posts — no manual ordering needed.

### Using the shortcode (page/post)

Paste this anywhere in the block editor (Shortcode block) or classic editor:

```
[cloudcraft_top_categories]
```

**Optional parameters:**

| Parameter    | Default | Description                          |
|--------------|---------|--------------------------------------|
| `number`     | `10`    | How many categories to show          |
| `show_count` | `1`     | Show post count (`1` = yes, `0` = no)|
| `title`      | _(none)_| Optional heading above the list      |

**Example:**
```
[cloudcraft_top_categories number="5" show_count="1" title="Browse by Topic"]
```

---

## File structure

```
astra-child/
├── style.css        ← Child theme declaration + minor CSS fixes
├── functions.php    ← All three features
└── README.md        ← This file
```

---

## Updating Astra

Because this is a child theme, updating the Astra parent theme from
**Appearance → Themes → Update** will **not** overwrite any of your changes.
Your `functions.php` and `style.css` are always safe.
