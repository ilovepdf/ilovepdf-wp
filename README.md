# PDF Compressor & Watermark - iLovePDF

**PDF Compressor & Watermark - iLovePDF** is the official iLovePDF plugin for WordPress. It allows you to automatically compress your PDF files and add watermarks, saving storage space and enhancing your PDF's security. 

With seamless integration, you can optimize all PDFs in your Media Library and stamp them automatically—just like you do on [iLovePDF.com](https://www.ilovepdf.com/).

### Key Features

- **Automatic Compression:** PDFs are compressed upon upload using the iLovePDF API, saving up to 50% storage space without losing quality.
- **Watermarking:** Automatically add text or image watermarks to your PDFs upon upload to safeguard your files.
- **Manual & Bulk Actions:** Compress or add watermarks to existing PDFs individually or in bulk.
- **Easy to Use:** Configure it once, and the plugin handles the rest, automating PDF optimization for future uploads.

## How It Works

### PDF Compression

- **Automatic:** Every time a PDF is uploaded, it gets compressed via iLovePDF API, reducing file size while maintaining quality. This feature can be toggled off if needed.
- **Manual:** Compress PDFs one by one or all at once from the Media Library. On average, compression can save up to 50% of the disk space.

### Watermarking PDFs

- **Automatic:** Add watermarks to PDFs upon upload. This feature can also be disabled if not required.
- **Manual:** Watermark PDFs individually or apply watermarks in bulk to existing files. You can choose between text or image watermarks.

### Bulk Actions

Easily apply compression or watermarking actions to multiple files at once, streamlining your file management process. Bulk actions can be performed on all files in the Media Library with a single click.

### Automatic Processing

Set up the plugin to apply compression and watermarking automatically for all future PDF uploads, eliminating the need for manual intervention.

## Installation

### From the WordPress Admin Panel:
1. Navigate to **Plugins > Add New**.
2. Search for "iLovePDF" and click **Install Now**.
3. After installation, activate the plugin from your **Plugins page.**

### Manual Installation:
1. Upload the `ilovepdf` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to the plugin settings page and create a free account or log in with an existing iLoveAPI account.
4. Configure the settings as per your requirements.

### iLoveAPI Developer Account Setup:
- Go to **Register** to create an iLoveAPI Developer account (or use an existing one if you've already registered at [iLoveAPI Developer](https://iloveapi.com)).
- Select a project, which will determine which API keys to use. The "Default Project" is selected by default.
- Free accounts can process up to **2500 credits per month**. If more is needed, additional processing can be purchased.

## Development Setup

After cloning or switching branches, install dependencies before running any tooling:

```bash
composer install   # installs PHP dependencies including dev tools (PHPStan, etc.)
npm ci             # installs JS dependencies from the committed lockfile
npm run build      # compiles assets with webpack (output goes to assets/build/)
```

## Build & Release

**`assets/build/` and `vendor/` are NOT committed to the repository.** CI builds them from the committed lockfiles and ships exactly that artifact — what is tested is what ships.

### Lockfiles are the source of truth

- `composer.lock` and `package-lock.json` are **committed** and must be kept up to date.
- CI uses `npm ci` and `composer install` (deterministic, locked installs) so builds are reproducible.
- Dependabot opens weekly PRs to refresh both lockfiles — review and merge these to keep known vulnerabilities from getting frozen.

### Local development

Developers run `npm ci && npm run build` (and `composer install`) locally for development only. **Do NOT commit `assets/build/` or `vendor/`** — both directories are gitignored and are built fresh by CI for every deploy.

When updating dependencies, run:

```bash
# JS: update package-lock.json, then commit it
npm update            # or: npm install <package>@<version>
git add package-lock.json

# PHP: update composer.lock, then commit it
composer update       # or: composer require <package>:<version>
git add composer.lock
```

### Release process

The deploy workflow (`main.yml`) automatically:
1. Runs all quality gates (PHPStan, PHPCS, composer audit, npm audit, build check).
2. Builds JS/CSS assets from the committed `package-lock.json` via `npm ci && npm run build`.
3. Installs production PHP dependencies from the committed `composer.lock` via `composer install --no-dev --optimize-autoloader`.
4. Packages the resulting directory (minus `.distignore` exclusions) and deploys to WordPress.org SVN.

