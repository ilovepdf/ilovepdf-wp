/**
 * Update the UI with the number of files protected.
 * @param {number} filesProtected - The number of files protected.
 */
export const setFilesProtected = (filesProtected) => {
	const filesProtectedElement = document.querySelector(
		'.ilovepdf-media__overview-watermark-files-processed p'
	);

	if (filesProtectedElement) {
		filesProtectedElement.textContent = filesProtected;
	}
};

/**
 * Update the UI with the compression resume.
 * @param {string} resume - The compression resume information.
 */
export const setResume = (resume) => {
	const elementResume = document.querySelector(
		'.ilovepdf-media__overview-watermark-tool-resume p'
	);

	if (elementResume) {
		elementResume.innerHTML = resume;
	}
};
