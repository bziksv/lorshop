<?php
if(!$arParams['SECTION']['UF_TAGS_ACTIVE'])
    return false;

global $APPLICATION;
ob_start();
?>
<style>
    .tags-list {
        margin: 0 0 24px;
    }

    .subcategories {
        display: flex;
        flex-direction: column;
        gap: 12px;
        position: relative;
    }

    .tags-scroll {
        position: relative;
        min-width: 0;
    }

    .tags-scroll:not(.is-expanded)::after {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        width: 40px;
        pointer-events: none;
        background: linear-gradient(to right, rgba(255,255,255,0), #fff 90%);
    }

    .sub-links-2 {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-wrap: nowrap;
        gap: 8px;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        scroll-snap-type: x proximity;
    }

    .sub-links-2::-webkit-scrollbar {
        display: none;
    }

    .sub-links-2.is-open {
        flex-wrap: wrap;
        overflow: visible;
        row-gap: 8px;
    }

    .sub-links-2 li {
        margin: 0;
        flex: 0 0 auto;
        scroll-snap-align: start;
        max-width: 100%;
    }

    .sub-links-2 a {
        display: inline-block;
        max-width: 100%;
        box-sizing: border-box;
        padding: 8px 14px;
        border: 1px solid #c5ced0;
        border-radius: 20px;
        color: #5b6273;
        background: #fff;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.3;
        text-decoration: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: color .2s ease, background .2s ease, border-color .2s ease;
    }

    .sub-links-2.is-open a {
        white-space: normal;
    }

    .sub-links-2 a:hover {
        color: #fff;
        border-color: #4b937b;
        background: #4b937b;
        text-decoration: none;
    }

    .subcategories .navi {
        display: flex;
        justify-content: flex-end;
    }

    .subcategories .navi .tags-toggle {
        appearance: none;
        -webkit-appearance: none;
        border: 0;
        background: transparent;
        color: #8a9399;
        padding: 0;
        font-size: 13px;
        font-weight: 400;
        line-height: 1.2;
        cursor: pointer;
        text-decoration: underline;
        text-decoration-style: dotted;
        text-underline-offset: 3px;
    }

    .subcategories .navi .tags-toggle:hover {
        color: #5b6273;
        text-decoration-style: solid;
    }

    @media (max-width: 767px) {
        .tags-list {
            margin-bottom: 18px;
        }

        .sub-links-2 a {
            padding: 7px 12px;
            font-size: 12px;
            max-width: min(280px, 78vw);
        }

        .subcategories .navi .tags-toggle {
            font-size: 12px;
        }
    }
</style>
<?php
\Bitrix\Main\Page\Asset::getInstance()->addString(
	ob_get_clean(),
	true,
	\Bitrix\Main\Page\AssetLocation::BEFORE_CSS
);
?>

<div class="tags-list">
    <div class="subcategories">
        <div class="tags-scroll">
            <ul class="tag-slider sub-links-2">
                <? foreach ($arParams['SECTION']['UF_TAGS_LIST'] as $tags):
                    $arTags = explode('@', $tags, 2);
                    $tagHref = isset($arTags[1]) ? trim($arTags[1]) : '';
                    if ($tagHref !== '' && preg_match('#^(.*?/search/\?q=)([^&]*)(.*)$#u', $tagHref, $tagM)) {
                        $tagHref = $tagM[1] . rawurlencode(rawurldecode(str_replace('+', ' ', $tagM[2]))) . $tagM[3];
                    }
                    ?>
                <li><a href="<?=htmlspecialcharsbx($tagHref)?>"><?=htmlspecialcharsbx($arTags[0])?></a></li>
                <? endforeach; ?>
            </ul>
        </div>
        <div class="navi">
            <button type="button" class="tags-toggle open">Показать все</button>
            <button type="button" class="tags-toggle close" hidden>Свернуть</button>
        </div>
    </div>
</div>

<script>
(function ($) {
    $(function () {
        var $scroll = $(".tags-list .tags-scroll");
        var $list = $(".tags-list .sub-links-2");
        var $open = $(".tags-list .open");
        var $close = $(".tags-list .close");

        $open.on("click", function () {
            $open.prop("hidden", true);
            $close.prop("hidden", false);
            $list.addClass("is-open");
            $scroll.addClass("is-expanded");
        });

        $close.on("click", function () {
            $close.prop("hidden", true);
            $open.prop("hidden", false);
            $list.removeClass("is-open");
            $scroll.removeClass("is-expanded");
            $scroll.find(".sub-links-2").scrollLeft(0);
        });
    });
})(jQuery);
</script>
