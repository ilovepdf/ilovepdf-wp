import { showAdminNotice } from '../components';
import { runQueue } from './queue';
import {
	applyCompressRowResult,
	applyWatermarkRowResult,
	markRowFailed,
	refreshCompressSummary,
	refreshWatermarkSummary
} from '../common/rowStatus';

/** ID of the injected progress banner element. */
const BANNER_ID = 'ipdf-bulk-progress-banner';

/**
 * Inject (or update) a progress banner before the media list table.
 * Uses `.wp-header-end` as the insertion anchor — the same landmark WP uses for admin notices.
 * Mirrors the markup of the success/error notices (iLovePDF logo + message) so it looks consistent.
 *
 * @param {string} message - The text to display inside the banner.
 * @since 3.0.0
 */
const upsertBanner = (message) => {
	let banner = document.getElementById(BANNER_ID);
	if (!banner) {
		banner = document.createElement('div');
		banner.id = BANNER_ID;
		banner.className = 'ipdf-notice ilovepdf-base__layout-flex notice notice-info ipdf-bulk-progress';

		const figure = document.createElement('figure');
		figure.className = 'ipdf-logo ilovepdf-base__layout-flex ilovepdf-base__layout-items--center';
		const logo = document.createElement('img');
		logo.src = IlovePdfData.logoUrl;
		logo.alt = 'logo ilovepdf';
		figure.appendChild(logo);

		banner.append(figure, document.createElement('p'));

		const anchor = document.querySelector('.wp-header-end');
		if (anchor) {
			anchor.insertAdjacentElement('afterend', banner);
		} else {
			// Fallback: prepend to the content area.
			const content = document.getElementById('wpcontent');
			if (content) {
				content.prepend(banner);
			}
		}
	}
	banner.querySelector('p').textContent = message;
};

/**
 * Remove the progress banner from the DOM (called after the run completes).
 *
 * @since 3.0.0
 */
const removeBanner = () => {
	document.getElementById(BANNER_ID)?.remove();
};

/**
 * Map a bulk action value to its AJAX action and nonce.
 *
 * @param {string} bulkAction - 'ilovepdf_compress' | 'ilovepdf_watermark'
 * @returns {{ ajaxAction: string, nonce: string, successClass: string, btnSelector: string }|null}
 * @since 3.0.0
 */
const resolveAction = (bulkAction) => {
	const data = IlovePdfData;
	if (bulkAction === 'ilovepdf_compress') {
		return {
			ajaxAction: 'ilovepdf_action_compress',
			nonce: data.compressNonce,
			successClass: 'ipdf-item-status-compressed',
			btnSelector: '#ipdf-action-compress'
		};
	}
	if (bulkAction === 'ilovepdf_watermark') {
		return {
			ajaxAction: 'ilovepdf_action_watermark',
			nonce: data.watermarkNonce,
			successClass: 'ipdf-item-status-watermark-applied',
			btnSelector: '#ipdf-action-watermark'
		};
	}
	return null;
};

/**
 * Update a media-library row to reflect the per-file result.
 * Reuses the same status CSS vocabulary as compress/index.js and watermark/index.js.
 *
 * @param {string}      id           - Attachment post ID.
 * @param {boolean}     ok           - Whether the file was processed successfully.
 * @param {string}      successClass - The status class to activate on success.
 * @param {string}      btnSelector  - Selector for the action button in the row.
 * @param {Object|null} json         - Parsed AJAX response, or null on network error.
 * @since 3.0.0
 */
const updateRow = (id, ok, successClass, _btnSelector, json) => {
	const row = document.getElementById('post-' + id);
	if (!row) {
		return;
	}

	if (!ok) {
		markRowFailed(row);
		return;
	}

	// The inner stats object lives at json.data.data (WP envelope → payload → data).
	// Early-return "already processed" responses carry no inner data — guard against that.
	const stats = json?.data?.data;

	if (successClass === 'ipdf-item-status-compressed') {
		applyCompressRowResult(row, stats ?? null);
		if (stats) {
			refreshCompressSummary(stats);
		}
	} else if (successClass === 'ipdf-item-status-watermark-applied') {
		applyWatermarkRowResult(row, stats ?? null);
		if (stats) {
			refreshWatermarkSummary(stats);
		}
	}
};

/**
 * Build the progress banner message string.
 * Prefers localized strings from IlovePdfData.i18n when present; falls back to English.
 *
 * @param {number} done   - Files completed so far.
 * @param {number} total  - Total files in the run.
 * @returns {string}
 * @since 3.0.0
 */
const buildProgressMessage = (done, total) => {
	const i18n = (IlovePdfData && IlovePdfData.i18n) || {};
	if (i18n.bulkProcessingFile) {
		// PHP localizes a printf-style string with two %d placeholders in order: done, total.
		return i18n.bulkProcessingFile.replace('%1$d', done).replace('%2$d', total);
	}
	return `Processing file ${done} of ${total}`;
};

/**
 * Build the completion summary message.
 *
 * Selects the action-specific localized string (compress vs watermark) so the
 * summary accurately reflects what was performed.
 *
 * @param {number}   succeeded  - Number of files processed successfully.
 * @param {string[]} failed     - IDs of files that failed.
 * @param {string}   bulkAction - 'ilovepdf_compress' | 'ilovepdf_watermark'
 * @returns {string}
 * @since 3.0.0
 */
const buildSummaryMessage = (succeeded, failed, bulkAction) => {
	const i18n = (IlovePdfData && IlovePdfData.i18n) || {};
	const isCompress = bulkAction === 'ilovepdf_compress';
	const template = isCompress ? i18n.bulkSummaryCompress : i18n.bulkSummaryWatermark;
	if (template) {
		return template
			.replace('%1$d', succeeded)
			.replace('%2$d', failed.length);
	}
	return `iLovePDF: ${succeeded} done, ${failed.length} failed.`;
};

/**
 * Initialize the bulk-queue controller.
 *
 * Guards on the Media Library list page (`#posts-filter` form), binds a submit listener,
 * and intercepts iLovePDF bulk actions to run them file-by-file via the queue runner.
 * If no items are checked, the native WP "nothing selected" path is preserved.
 *
 * @since 3.0.0
 */
export const initBulkQueue = () => {
	const form = document.getElementById('posts-filter');
	if (!form) {
		return;
	}

	form.addEventListener('submit', async function (event) {
		// Resolve whichever <select> is not set to '-1' (WP's inactive default).
		const sel1 = form.querySelector('select[name="action"]');
		const sel2 = form.querySelector('select[name="action2"]');
		const bulkAction =
			(sel1 && sel1.value !== '-1' ? sel1.value : null) ||
			(sel2 && sel2.value !== '-1' ? sel2.value : null);

		if (bulkAction !== 'ilovepdf_compress' && bulkAction !== 'ilovepdf_watermark') {
			// Not our action — let WP handle it natively.
			return;
		}

		// Checkbox field name differs by table: the core Media Library list table uses
		// 'media[]', while the plugin's own Files_List_Table (on the iLovePDF media
		// optimization submenu page) uses 'file[]'. Match both — the checkbox VALUE (post ID)
		// is all this code needs; it doesn't rely on the field name beyond selecting them.
		const checked = [...form.querySelectorAll('input[name="media[]"]:checked, input[name="file[]"]:checked')];
		if (checked.length === 0) {
			// Empty selection: do NOT preventDefault — let WP's native "no items selected" path run.
			return;
		}

		event.preventDefault();

		const ids = checked.map((cb) => cb.value);
		const resolved = resolveAction(bulkAction);
		if (!resolved) {
			return;
		}

		const { ajaxAction, nonce, successClass, btnSelector } = resolved;

		upsertBanner(buildProgressMessage(0, ids.length));

		const { succeeded, failed } = await runQueue({
			ids,
			ajaxAction,
			nonce,
			onProgress: ({ done, total }) => {
				upsertBanner(buildProgressMessage(done, total));
			},
			onFileDone: (id, ok, json) => {
				updateRow(id, ok, successClass, btnSelector, json);
			}
		});

		removeBanner();

		const summaryType = failed.length > 0 ? 'warning' : 'success';
		showAdminNotice(buildSummaryMessage(succeeded, failed, bulkAction), summaryType);
	});
};
