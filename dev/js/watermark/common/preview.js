/**
 * Gets preview elements from the watermark container.
 *
 * @param {string} [type=text] - Type of preview element to get ('text' or 'image').
 * @param {boolean} [isMosaic=false] - Whether to get mosaic preview elements.
 * @returns {(Element|{texts: NodeList, images: NodeList})} Single element or object with text/image NodeLists for mosaic.
 */
export const getPreview = (type = 'text', isMosaic = false) => {
	if (isMosaic) {
		return {
			texts: document.querySelectorAll('#ipdf-element-watermark p.ipdf-element-clone'),
			images: document.querySelectorAll('#ipdf-element-watermark img.ipdf-element-clone')
		};
	}

	if (type === 'text') {
		return document.querySelector('#ipdf-element-watermark p:not(.ipdf-element-clone)');
	}

	if (type === 'image') {
		return document.querySelector('#ipdf-element-watermark img:not(.ipdf-element-clone)');
	}
};
