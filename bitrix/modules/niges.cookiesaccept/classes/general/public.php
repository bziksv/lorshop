<?php
class CNigesCookiesAcceptPublic
{
	/**
	 * Inject cookie notice before </body> (OnEpilog prints after </html> and breaks HTML).
	 */
	public static function OnEndBufferContent(&$content)
	{
		if (!is_string($content) || $content === '') {
			return;
		}

		if (PHP_SAPI === 'cli' || (defined('ADMIN_SECTION') && ADMIN_SECTION === true)) {
			return;
		}

		if (stripos($content, 'nca-cookiesaccept-line') !== false) {
			return;
		}

		if (stripos($content, '</body>') === false) {
			return;
		}

		if (!CModule::IncludeModule(cookiesaccept_MODULE_ID)) {
			return;
		}

		if (COption::GetOptionString(cookiesaccept_MODULE_ID, 'ACTIVE', 'N', SITE_ID) !== 'Y') {
			return;
		}

		if (defined('PUBLIC_AJAX_MODE') && PUBLIC_AJAX_MODE === true) {
			return;
		}
		if (isset($_REQUEST['ajax']) && (string)$_REQUEST['ajax'] !== '') {
			return;
		}
		if (isset($_REQUEST['bxajaxid']) && (string)$_REQUEST['bxajaxid'] !== '') {
			return;
		}

		try {
			$request = \Bitrix\Main\Context::getCurrent()->getRequest();
			if ($request->isAjaxRequest()) {
				return;
			}
		} catch (\Throwable $e) {
			// ignore
		}

		global $APPLICATION;
		ob_start();
		$APPLICATION->IncludeComponent(
			'niges:cookiesaccept',
			'.default',
			array(),
			false,
			array('HIDE_ICONS' => 'Y')
		);
		$html = ob_get_clean();
		if (!is_string($html) || trim($html) === '') {
			return;
		}

		$content = preg_replace('/<\/body>/i', $html . '</body>', $content, 1);
	}

	/** @deprecated kept for old event registrations until reinstalled */
	public static function OnEpilog()
	{
		// no-op: banner must be injected via OnEndBufferContent
	}
}
