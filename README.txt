=== PDF Compressor & Watermark - iLovePDF ===
Plugin Name: Image Compressor & Optimizer - iLovePDF
Version: 2.1.4
Author: iLovePDF
Author URI: https://www.ilovepdf.com/
Contributors: iLovePDF
Tags: compress, watermark, optimize, performance, pdf optimizer
Requires at least: 5.3
Tested up to: 6.6.2
Stable tag: 2.1.4
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Optimize and protect your PDFs with automatic compression and watermarking. Save space and secure your documents effortlessly.

== Description ==

Compress your PDF files and Stamp Images or text into PDF files. This is the Official iLovePDF plugin for Wordpress. You can optimize all your PDF and stamp them automatically as you do in ilovepdf.com.

= How it works =

The plugin has two modes of work, automatic or manual.
Compress PDF: Every time a PDF file is uploaded to your Media Library, is compressed by our iLovePDF API and saved in your WordPress site already optimized, saving you disk space. This feature can be disabled.

It can also compress all PDF already in your Media Library at once or compress PDF files one by one. The compression ratio depends on the PDF but on average you can save up to 50% of disk space occupied by PDF files without loosing quality.

Watermark PDF: Every time a PDF file is uploaded to your Media Library, is stamped by our iLovePDF API and saved in your WordPress. This feature can be disabled.
It can also stamp images or text in PDF already in your Media Library at once or stamp PDF files one by one.

= Bulk actions =

All tools can be done for an individual files or applied to all your existing files.

= Automatic process =

All tools can be applied automatically when a PDF file is upload, so you don't need to apply any manual anymore.

== Installation ==

From your Admin panel:
1. Visit Plugins > Add New.
2. Search for **ilovePDF** and press the **Install Now** button.
3. Activate the plugin from your Plugins page.

Manual:
1. Upload `ilovepdf` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create a free account in the plugin settings page, or use your existing one
4. Configure the plugin as you like

== Configure your iLoveAPI Developer Account ==

Install this plugin and go to `Settings -> iLovePDF` to create your iLoveAPI Developer account (if you already registered previously on [iLoveAPI Developer](https://iloveapi.com) you can login directly) and choose your Project, but by default it will be selected **Default Project**. A project is what defines which API keys will be used. If you need it, you can manage your projects in your iLoveAPI developer account. With your iLoveAPI Free account you get 2500 free credits to process per month. If you need more you can purchase them

By going to `Settings -> iLovePDF -> Compress PDF` you can set the compression level you prefer to optimize your PDF files. We strongly recommend you to use ‘Recommended Compression’ which offers the perfect balance between compression and quality. In this section you can disable the auto compression of new uploaded PDF files in your Media Library.

By going to `Settings -> iLovePDF -> Watermark PDF` you can set many options on what to stamp and how into your PDF files. In this section you can disable the auto stamping of new uploaded PDF files in your Media Library.

To Compress or Stamp PDF files that are already in your Media Library go to `Media -> iLovePDF -> Compress PDF or Media -> iLovePDF -> Watermark`

== Frequently Asked Questions ==

= Do I need a iLoveAPI account? =

Yes, you need a `developer` account. It can be created easily from your Wordpress, or you can use your existing one.

= Is this service free? =

With a free account you can process up to 2500 credits each month. If you need more, you can upgrade your account.

= What happens when the limit is reatched? =

We will send you an email before limit is reached. When limit is reached, no more PDF files will be processed. But remember each month, 2500 credits are free, so in next month you will be able to process more files again!

== Screenshots ==

1. Create an account or login.
2. Configure Compress PDF.
3. Configure Watermark PDF.

== Changelog ==

= 2.1.4 =
Improved
* Update Libraries.
* Improved texts.
* Compatibility with WordPress 6.6.2

= 2.1.3 =
Improved
* Update Libraries.
* Improved readme texts.
* Improved texts when a credential problem occurs.

= 2.1.2 =
Improved
* Update Libraries.
* Now IlovePDF will use credits to process the files.

Fixed
* A problem was solved with the option to create backup, where in some cases the option was not stored in the database.

= 2.1.1 =
Improved
* Update Libraries.

Fixed
* Fixed an issue when activating the plugin for the first time. Default settings now load correctly.
* Library was included internally. This will improve file loading and possible CDN blocking in some countries.

= 2.1.0 =
Added
* New Backup option added in General Settings. Now the backup works for all tools.
* Regenerate thumbnails when wattermark is applied.
* Added link to configuration page from plugins page.
* Added link to tools from page settings.

Improved
* Update Libraries.
* Update Library Ilovepdf-php to 1.2.4
* Removed unused file.
* On the individual page of a file, ilovepdf buttons are now displayed whenever the file is a PDF.

Fixed
* In some cases, when a user logged in, they could get a PHP error or warning.

= 2.0.5 =
Improved
* Update Libraries.
* Assets.

= 2.0.4 =
Fixed
* Compatibility issue with the Woocommerce plugin.

= 2.0.3 =
Fixed
* Specify minimum version of PHP for dependencies.

= 2.0.2 =
Added
* Improved class loading.
* Update library ilovepdf to v1.2.2

= 2.0.1 =
Changed
* Remove Function Upload duplicate. Caused an error loading pdf files.

Added
* Support WP 6.4.1

Fixed
* Fixed a problem with the compress and watermark buttons, they were duplicated.
* Cannot read property 'model' of undefined.

= 2.0.0 =
Changed
* Minimum Support WP Version to 5.3
* Minimum Support PHP to 7.4
* remove public folder, not used.

Fixed
* fix bulk actions to compress and watermark functions

= 1.2.4 =
Added
* Formatting files according to php/wordpress standards
* WP Requires at least 4.7
* added prefix to functions and const
* code documentation
* version number to enqueue styles

Changed
* update readme
* Change function dirname() to __DIR__ const
* loose comparisons
* conditional structure
* variables name to snake_case
* Capabilities should be used instead of roles
* remove comments
* json_encode changed to wp_json_encode
* strip_tags changed to wp_strip_all_tags
* Limit posts_per_page
* output literal string
* function rename() to WP_Filesystem


Fixed
* name assignment changed
* Rename Class Name Ilove_Pdf_i18n to Ilove_Pdf_I18n
* nonce verification
* rename files without hyphens
* escaped data outputs
* Sanitize data
* text domain mismatch
* changed name of parameter $object to $file_object
* add version to js fiel enqueue
* data types in parameters
* variables not defined
* check if watermark image is empty
* set value default if compress size is null|empty

= 1.2.3 =
* match version and tag.
* update readme

= 1.2.2 =
* update version

= 1.0 =
* Initial version.