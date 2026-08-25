<? if(!$arCityes = include $_SERVER['DOCUMENT_ROOT'].'/.cityes.php') return false; ?>

<li class="nav__item">
    <a href="#" class="nav__link" id="location_btn">
        <?=($_COOKIE['city']) ?: 'Выбор города'?>
        <i class="icon-arrow_down" style="transform: none;"></i>
    </a>
</li>

