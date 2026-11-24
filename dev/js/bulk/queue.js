import { buildBulkFormData } from '../common/DOMElements';

/** Milliseconds between consecutive status polls. */
const POLL_INTERVAL = 1000;

/**
 * Maximum number of polls before giving up (~5 min backstop, deliberately above the
 * 300 s server stale window so the server produces a terminal status first in practice).
 */
const MAX_POLLS = 300;

/**
 * Map each trigger AJAX action to its read-only status/poll action.
 * The poll endpoint only reads status meta — it never re-runs the pipeline.
 */
const STATUS_ACTIONS = {
	ilovepdf_action_compress:  'ilovepdf_status_compress',
	ilovepdf_action_watermark: 'ilovepdf_status_watermark',
};

/**
 * Resolve after `ms` milliseconds.
 *
 * @param {number} ms
 * @returns {Promise<void>}
 */
const wait = ( ms ) => new Promise( ( resolve ) => setTimeout( resolve, ms ) );

/**
 * Poll the per-tool status endpoint until the server reports a terminal state.
 *
 * The server returns HTTP 202 while the job is still in-process. Because 202 is
 * res.ok === true, we must branch on res.status (not res.ok) — it does not throw.
 * Any network throw, non-2xx, or non-JSON body is treated as "still uncertain" and
 * the loop continues — the connection may still be flaky while PHP is running.
 *
 * @param {string} statusAction - AJAX action for the read-only status endpoint.
 * @param {string} id           - Attachment post ID.
 * @param {string} nonce        - WP nonce (same one the trigger used).
 * @returns {Promise<{ok: boolean, json: Object|null}>}
 */
const pollUntilSettled = async ( statusAction, id, nonce ) => {
	for ( let polls = 0; polls < MAX_POLLS; polls++ ) {
		await wait( POLL_INTERVAL );
		const fd = buildBulkFormData( statusAction, id, nonce );
		try {
			const res = await fetch( ajaxurl, { method: 'POST', body: fd } );
			// 202 means the server job is still running — keep polling.
			// (202 is res.ok === true, so check res.status explicitly.)
			if ( res.status === 202 ) {
				continue;
			}
			const json = await res.json();
			return { ok: !! json.success, json };
		} catch {
			// Network error, non-2xx, or non-JSON body — connection still unreliable, keep polling.
			continue;
		}
	}
	// Backstop: MAX_POLLS exhausted without a terminal response.
	return { ok: false, json: null };
};

/**
 * Process a single file: POST the trigger and return a terminal result.
 *
 * A genuine application response (success or error envelope) is accepted immediately.
 * If fetch throws OR the body is not valid JSON (proxy cut the connection), the file is
 * NOT marked failed — we enter pollUntilSettled to find the real outcome server-side.
 *
 * @param {string}      ajaxAction   - Trigger AJAX action.
 * @param {string|null} statusAction - Read-only status AJAX action (derived from ajaxAction).
 * @param {string}      id           - Attachment post ID.
 * @param {string}      nonce        - WP nonce.
 * @returns {Promise<{ok: boolean, json: Object|null}>}
 */
const processFile = async ( ajaxAction, statusAction, id, nonce ) => {
	const fd = buildBulkFormData( ajaxAction, id, nonce );
	try {
		const res = await fetch( ajaxurl, { method: 'POST', body: fd } );
		const json = await res.json(); // throws if body is not JSON (proxy-cut case)
		return { ok: !! json.success, json }; // genuine terminal envelope (success or error)
	} catch {
		// fetch threw (network error) OR res.json() threw (non-JSON proxy-cut body).
		// Do NOT mark failed yet — recheck the real server state via the status endpoint.
		if ( statusAction ) {
			return await pollUntilSettled( statusAction, id, nonce );
		}
		// No status action mapped for this tool — fall back to failed.
		return { ok: false, json: null };
	}
};

/**
 * Run a fully sequential queue of per-file AJAX requests.
 *
 * Exactly one fetch is in flight at a time. Per-file failures are recorded and
 * the loop continues — a single bad file must not strand the rest.
 *
 * @param {Object}   options
 * @param {string[]} options.ids          - Attachment IDs to process.
 * @param {string}   options.ajaxAction   - The AJAX action value sent as FormData 'action' field.
 * @param {string}   options.nonce        - The WP nonce (_wpnonce).
 * @param {Function} options.onProgress   - Called after each file: ({ done, total }).
 * @param {Function} options.onFileDone   - Called per file: (id, ok, json) — ok is a boolean, json is the parsed response or null on network error.
 * @returns {Promise<{succeeded: number, failed: string[]}>}
 * @since 3.0.0
 */
export const runQueue = async ( { ids, ajaxAction, nonce, onProgress, onFileDone } ) => {
	const statusAction = STATUS_ACTIONS[ ajaxAction ] ?? null;
	let done = 0;
	let succeeded = 0;
	const failed = [];

	for ( const id of ids ) {
		const result = await processFile( ajaxAction, statusAction, id, nonce );
		if ( result.ok ) {
			succeeded++;
		} else {
			failed.push( id );
		}
		onFileDone( id, result.ok, result.json );
		done++;
		onProgress( { done, total: ids.length } );
	}

	return { succeeded, failed };
};
