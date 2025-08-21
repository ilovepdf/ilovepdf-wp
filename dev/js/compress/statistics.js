/**
 * Update the UI with the number of files processed.
 * @param {number} filesProcessed - The number of files processed.
 */
export const setFilesProcessed = (filesProcessed) => {
	const elementFilesProcessed = document.querySelector(
		'.ilovepdf-media__overview-compress-files-processed p'
	);

	if (elementFilesProcessed) {
		elementFilesProcessed.textContent = filesProcessed;
	}
};

/**
 * Update the UI with the average reduction.
 * @param {number} averageReduction - The average reduction percentage.
 */
export const setAverageReduction = (averageReduction) => {
	const elementAverageReduction = document.querySelector(
		'.ilovepdf-media__overview-compress-average-reduction p'
	);

	if (elementAverageReduction) {
		elementAverageReduction.textContent = averageReduction;
	}
};

/**
 * Update the UI with the space saved.
 * @param {number} spaceSaved - The space saved in bytes.
 */
export const setSpaceSaved = (spaceSaved) => {
	const elementSpaceSaved = document.querySelector(
		'.ilovepdf-media__overview-compress-space-saved p'
	);

	if (elementSpaceSaved) {
		elementSpaceSaved.textContent = spaceSaved;
	}
};

/**
 * Update the UI with the compression resume.
 * @param {string} resume - The compression resume information.
 */
export const setResume = (resume) => {
	const elementResume = document.querySelector(
		'.ilovepdf-media__overview-compress-tool-resume p'
	);

	if (elementResume) {
		elementResume.textContent = resume;
	}
};

/**
 * Update the UI with the original size.
 * @param {string} size - The size in bytes.
 * @param {HTMLElement} element - The element to update.
 */
export const setSize = (size, element) => {
	if (element) {
		element.textContent = size;
	}
};
