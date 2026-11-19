import { buildBulkFormData } from '../common/DOMElements';

/**
 * Split an array into sequential chunks of the given size.
 *
 * @param {string[]} ids  - The flat array of IDs to split.
 * @param {number}   size - Maximum items per chunk (must be > 0).
 * @returns {string[][]} - Array of chunks; a single-chunk result satisfies the small-selection case.
 * @since 3.0.0
 */
export const chunk = (ids, size) => {
	if (!size || size <= 0) return ids.length ? [ids] : [];
	const chunks = [];
	for (let i = 0; i < ids.length; i += size) {
		chunks.push(ids.slice(i, i + size));
	}
	return chunks;
};

/**
 * Run a fully sequential queue of per-file AJAX requests.
 *
 * Exactly one fetch is in flight at a time (chunk outer / file inner nested for…of + await).
 * Per-file failures are recorded and the loop continues — a single bad file must not strand the rest.
 * A single resulting chunk transparently satisfies the small-selection (< chunkSize) case.
 *
 * @param {Object}   options
 * @param {string[]} options.ids          - Attachment IDs to process.
 * @param {string}   options.ajaxAction   - The AJAX action value sent as FormData 'action' field.
 * @param {string}   options.nonce        - The WP nonce (_wpnonce).
 * @param {number}   options.chunkSize    - Files per chunk (pacing/progress boundary).
 * @param {Function} options.onProgress   - Called after each file: ({ done, total }).
 * @param {Function} options.onFileDone   - Called per file: (id, ok, json) — ok is a boolean, json is the parsed response or null on network error.
 * @returns {Promise<{succeeded: number, failed: string[]}>}
 * @since 3.0.0
 */
export const runQueue = async ({ ids, ajaxAction, nonce, chunkSize, onProgress, onFileDone }) => {
	const chunks = chunk(ids, chunkSize);
	let done = 0;
	let succeeded = 0;
	const failed = [];

	for (const group of chunks) {
		for (const id of group) {
			const fd = buildBulkFormData(ajaxAction, id, nonce);
			try {
				const res = await fetch(ajaxurl, { method: 'POST', body: fd });
				const json = await res.json();
				if (json.success) {
					succeeded++;
				} else {
					failed.push(id);
				}
				onFileDone(id, json.success, json);
			} catch (e) {
				console.error('[ilovepdf bulk] file', id, 'failed:', e);
				failed.push(id);
				onFileDone(id, false, null);
			}
			done++;
			onProgress({ done, total: ids.length });
		}
		// Chunk k+1 starts only after chunk k is fully settled (AC2).
	}

	return { succeeded, failed };
};
