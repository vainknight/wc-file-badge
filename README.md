Detects the downloadable file type of a WooCommerce product (PDF, DOC, XLS...) and displays a small customizable badge.

== Description ==

Automatically detects the file extension of the first downloadable file attached to a WooCommerce virtual/downloadable product, and displays a small color-coded badge (e.g. "PDF", "DOC", "XLS") wherever you place it — a product page, an archive/loop, or a theme builder template.

Available in two ways:

1. Shortcode: `[badge_extension]`
2. Elementor Widget: "File Type Badge" (works both on single product pages and inside product loops/archives, e.g. Elementor Loop Grid)

From Settings → File Type Badge you can customize without touching code:

* Label text and background color for PDF, Word (DOC/DOCX), and Excel (XLS/XLSX/CSV) files
* Background color for any other file type (label falls back to the file extension automatically, e.g. ZIP, MP3)
* Badge width, height, border radius
* Typography: font size, font family, font weight, text color

Each badge also exposes `data-ftype` (pdf/xls/doc/default) and `data-fprice` (free/paid) attributes, so it can be used as a hook for custom filtering scripts elsewhere on your site.

Developed by [Fran Velazco](https://www.linkedin.com/in/fran-velazco/).
Built with Claude (Anthropic) as a development assistant.

== Installation ==

1. Upload the `file-type-badge` folder to `/wp-content/plugins/`.
2. Activate it through the 'Plugins' menu in WordPress (requires active WooCommerce).
3. Go to Settings → File Type Badge to customize labels, colors, dimensions, and typography.
4. Place the shortcode `[badge_extension]` on a product page/loop, or drag the "File Type Badge" widget in the Elementor editor.

== Changelog ==

= 1.0.0 =
* Initial release: shortcode, settings page, and Elementor widget (works on single product pages and inside loops/archives).
