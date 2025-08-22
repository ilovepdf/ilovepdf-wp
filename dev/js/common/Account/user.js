/**
 * Check if user has credits, if not show the modal to buy more credits
 */
export const userHasCredits = () => {
	const userHasCredits = IlovePdfData.userHasCredits;
	const userIsLoggued = IlovePdfData.userIsLoggued;

	if (userHasCredits || !userIsLoggued) {
		return;
	}

	tb_show(
		'IPDF',
		'#TB_inline?height=245&amp;width=425&amp;inlineId=ipdf-popup-buymore&amp;modal=true',
		null
	);
};
