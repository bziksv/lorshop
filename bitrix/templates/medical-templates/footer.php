<? if(!CSite::InDir('/index.php')): ?>
</div>
<?endif;?>
<div class="footer">
	<div class="container">
		<div class="footer__wrapper footer__row">
			<div class="footer__col">
				<div class="footer__subscribe">
 <i class="icon-email_big"></i>
					Подпишитесь! Новинки, скидки, предложения!
				</div>
			</div>
			<div class="footer__col">
				 <?$APPLICATION->IncludeComponent(
	"bitrix:sender.subscribe",
	"sender.subscribe",
	Array(
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"COMPONENT_TEMPLATE" => "sender.subscribe",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"CONFIRMATION" => "N",
		"HIDE_MAILINGS" => "N",
		"SET_TITLE" => "N",
		"SHOW_HIDDEN" => "N",
		"USER_CONSENT" => "Y",
		"USER_CONSENT_ID" => "1",
		"USER_CONSENT_IS_CHECKED" => "N",
		"USER_CONSENT_IS_LOADED" => "N",
		"USE_PERSONALIZATION" => "Y"
	)
);?>
			</div>
		</div>
	</div>
	<hr>
	<div class="container">
		<div class="footer__row">
			<div class="footer__col">
				 <?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"bottom.menu",
	Array(
		"ALLOW_MULTI_SELECT" => "N",
		"CHILD_MENU_TYPE" => "left",
		"COMPONENT_TEMPLATE" => "catalog_horizontal",
		"DELAY" => "N",
		"MAX_LEVEL" => "2",
		"MENU_CACHE_GET_VARS" => "",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_TYPE" => "N",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_THEME" => "site",
		"ROOT_MENU_TYPE" => "bottom",
		"USE_EXT" => "N"
	)
);?>
			</div>
			<div class="footer__col flex-3">
				<div class="footer__phone icon-phone">
 <img src="/bitrix/templates/medical-templates/img/footer_phone.jpg" alt="Заказать звонок">

				</div>
 <a href="javascript:void(0);" class="footer__callback footer__link callback-btn">Заказать звонок</a>
				<div class="footer__email icon-email">
 <a href="mailto:<?=tplvar('email');?>" class="footer__link roi_visit"><?=tplvar('email', true);?></a>
					(Для заказов)
				</div>
			</div>
			<div class="footer__col flex-2">
				<div class="footer__copy icon-copyright">
					 <?=date("Y")?> Все права защищены.
				</div>
				<br>
				<a href="https://prime-ltd.su/" target="_blank" class="footer__prime" rel="noopener noreferrer">
  					<img src="https://prime-ltd.su/logo/white.svg" class="footer__prime-logo" title="Продвижение сайтов" alt="Продвижение сайтов">
				</a>
			</div>
		</div>

		<div><!--noindex-->Мы используем файлы <a target="_blank" style="color: #fff;" href="/upload/legal/legal-cookie.png">cookies</a> для повышения удобства использования сайта, настройки рекламы и анализа трафика. Продолжая посещать наш сайт, вы даёте <a target="_blank" style="color: #fff;" href="/upload/legal/legal-consent.png">согласие на обработку персональных данных</a>, принимаете <a target="_blank" style="color: #fff;" href="/upload/legal/legal-personal-data.png">политику обработки персональных данных</a> и соглашаетесь с <a target="_blank" style="color: #fff;" href="/upload/legal/legal-recommendation.png">правилами применения рекомендательных технологий</a>. Чтобы отключить cookies, измените соответствующие настройки в браузере.<!--/noindex--></div>

	</div>
</div>
<?$APPLICATION->IncludeComponent(
	"nbrains:main.feedback", 
	"popup-callback", 
	array(
		"COMPONENT_TEMPLATE" => "popup-callback",
		"COMPOSITE_FRAME_MODE" => "N",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"EMAIL_TO" => "info@lorshop.ru",
		"EVENT_MESSAGE_ID" => array(
			0 => "53",
		),
		"IBLOCK_ID" => "37",
		"IBLOCK_TYPE" => "feedback",
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"PROPERTY_CODE" => array(
			0 => "NAME",
			1 => "URL",
			2 => "PHONE",
			3 => "MAIL",
			4 => "QUERY",
		),
		"USE_CAPTCHA" => "N"
	),
	false
);?>
<script src="<?=SITE_TEMPLATE_PATH?>/js/main.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/alertify.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.maskinput.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.bpopup.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/lightgallery.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery-ui.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/functions.js?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/js/functions.js')?>"></script>

<!-- Yandex.Metrika counter от Prime -->
<script type="text/javascript" >
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter50216737 = new Ya.Metrika2({
                    id:50216737,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/tag.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks2");
</script>
<!-- /Yandex.Metrika counter -->

<!-- Сквозная от Prime -->
<script>
(function(w, d, s, h, id) {
    w.roistatProjectId = id; w.roistatHost = h;
    var p = d.location.protocol == "https:" ? "https://" : "http://";
    var u = /^.*roistat_visit=[^;]+(.*)?$/.test(d.cookie) ? "/dist/module.js" : "/api/site/1.0/"+id+"/init";
    var js = d.createElement(s); js.charset="UTF-8"; js.async = 1; js.src = p+h+u; var js2 = d.getElementsByTagName(s)[0]; js2.parentNode.insertBefore(js, js2);
})(window, document, 'script', 'cloud.roistat.com', 'bfd869dbfcce2f18ec73436f82c3a437');
</script>

</body>
</html>