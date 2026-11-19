import { getRowCompressedSize, getRowOriginalSize } from './DOMElements';
import {
	setFilesProcessed,
	setAverageReduction,
	setSpaceSaved,
	setResume as setCompressResume,
	setSize
} from '../compress/statistics';
import { setFilesProtected, setResume as setWatermarkResume } from '../watermark/statistics';

/**
 * Check whether the overview summary tiles exist on the current page.
 * Returns false on the core Media Library page, which has no overview section.
 *
 * @returns {boolean}
 */
const hasOverview = () => !!document.querySelector('.ilovepdf-media__overview-inner');

// ---------------------------------------------------------------------------
// Per-row helpers — operate on a given <tr> element and an optional stats object
// ---------------------------------------------------------------------------

/**
 * Activate the success badge, deactivate the "not compressed" sibling badge,
 * write the compression percentage into the badge span, disable the compress
 * action button, and refresh the row's size cells — exactly as the single-item
 * compress flow does.
 *
 * @param {HTMLElement}  rowEl - The <tr> row element.
 * @param {Object|null}  stats - Inner stats object from the AJAX response
 *                               (json.data.data for bulk, response.data.data for single).
 *                               Pass null when the response carries no stats
 *                               (e.g. already-processed early-return).
 */
export const applyCompressRowResult = (rowEl, stats) => {
	rowEl.querySelector('.ipdf-item-status-not-compressed')?.classList.remove('ipdf-item-status-active');
	rowEl.querySelector('.ipdf-item-status-compressed')?.classList.add('ipdf-item-status-active');

	if (stats?.percentage) {
		const span = rowEl.querySelector('.ipdf-item-status-compressed span');
		if (span) {
			span.textContent = stats.percentage;
		}
	}

	rowEl.querySelector('#ipdf-action-compress')?.classList.add('ipdf-btn--media-action-trigger');

	if (stats) {
		setSize(stats.compressed_size, getRowCompressedSize(rowEl));
		setSize(stats.original_size, getRowOriginalSize(rowEl));
	}
};

/**
 * Activate the success badge, deactivate the "not watermarked" sibling badge,
 * and disable the watermark action button — exactly as the single-item watermark
 * flow does. When the file is also compressed the compress badge percentage and
 * size cells are cross-updated using data.data.compress_statistics, replicating
 * the single-item watermark handler byte-for-byte.
 *
 * @param {HTMLElement}  rowEl - The <tr> row element.
 * @param {Object|null}  stats - Inner stats object from the AJAX response.
 *                               Cross-update fields come from stats.compress_statistics
 *                               (a nested object), matching watermark/index.js line 119.
 */
export const applyWatermarkRowResult = (rowEl, stats) => {
	rowEl.querySelector('.ipdf-item-status-not-watermarked')?.classList.remove('ipdf-item-status-active');
	rowEl.querySelector('.ipdf-item-status-watermark-applied')?.classList.add('ipdf-item-status-active');
	rowEl.querySelector('#ipdf-action-watermark')?.classList.add('ipdf-btn--media-action-trigger');

	if (stats?.file_is_compressed) {
		const cs = stats.compress_statistics;
		if (cs) {
			const statusCompressed = rowEl.querySelector('.ipdf-item-status-compressed');
			if (statusCompressed) {
				statusCompressed.querySelector('span').textContent = cs.percentage;
			}
			setSize(cs.compressed_size, getRowCompressedSize(rowEl));
			setSize(cs.original_size, getRowOriginalSize(rowEl));
		}
	}
};

/**
 * Activate the fail badge on the row.
 *
 * @param {HTMLElement} rowEl - The <tr> row element.
 */
export const markRowFailed = (rowEl) => {
	rowEl.querySelector('.ipdf-item-status-fail')?.classList.add('ipdf-item-status-active');
};

// ---------------------------------------------------------------------------
// Global summary helpers — operate on the stats object only; no-op when the
// overview section is absent (e.g. core Media Library page).
// ---------------------------------------------------------------------------

/**
 * Refresh the compression global summary tiles using the stats fields that
 * the single-item compress handler passes to the same setters.
 *
 * @param {Object} stats - Inner stats object: { files_processed, average_reduction,
 *                         space_saved, total_resume }.
 */
export const refreshCompressSummary = (stats) => {
	if (!hasOverview()) return;
	setFilesProcessed(stats.files_processed);
	setAverageReduction(stats.average_reduction);
	setSpaceSaved(stats.space_saved);
	setCompressResume(stats.total_resume);
};

/**
 * Refresh the watermark global summary tiles (and, when the file is also
 * compressed, the compress tiles too) — matching exactly the field paths
 * the single-item watermark handler uses. Compress fields come from the
 * nested stats.compress_statistics object (confirmed in watermark/index.js).
 *
 * @param {Object} stats - Inner stats object: { files_protected, resume,
 *                         file_is_compressed, compress_statistics? }.
 */
export const refreshWatermarkSummary = (stats) => {
	if (!hasOverview()) return;
	setFilesProtected(stats.files_protected);
	setWatermarkResume(stats.resume);

	if (stats.file_is_compressed) {
		const cs = stats.compress_statistics;
		if (cs) {
			setFilesProcessed(cs.files_processed);
			setAverageReduction(cs.average_reduction);
			setSpaceSaved(cs.space_saved);
			setCompressResume(cs.total_resume);
		}
	}
};
