<?php

use Bitrix\Highloadblock\HighloadBlockTable;

$crumbs = [];
$crumbs[] = ['Главная', '/'];

if (!empty($arParams["map"])) {
    $crumbs[] = ['Карта', '/map/'];
} else {
    $crumbs[] = ['Каталог', '/catalog/'];

    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $segments = explode('/', $path);

    if ($segments[0] === 'catalog') {
        array_shift($segments);
    }

    // Добавляем "Подборки", только если URL содержит "vpechatleniya"
    if (in_array('vpechatleniya', $segments)) {
        $crumbs[] = ['Подборки', '/impressions/'];
    }


    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $segments = explode('/', $path);

    if ($segments[0] === 'catalog') {
        array_shift($segments);
    }

    $iblockId = IMPRESSIONS_IBLOCK_ID;
    $url = '/catalog/';

    foreach ($segments as $index => $segment) {
        $url .= $segment . '/';

        $section = \CIBlockSection::GetList([], [
            'IBLOCK_ID' => $iblockId,
            'CODE' => $segment
        ], false, ['ID', 'NAME'])->Fetch();

        if ($section) {
            $crumbs[] = [$section['NAME'], $url];
            continue;
        }

        if ($index === count($segments) - 1) {
            $element = \CIBlockElement::GetList([], [
                'IBLOCK_ID' => $iblockId,
                'CODE' => $segment
            ], false, false, ['ID', 'NAME'])->Fetch();

            if ($element) {
                $crumbs[] = [$element['NAME'], $url];
            }
        }
    }

    if (!empty($_GET['housetypes']) && \Bitrix\Main\Loader::includeModule('highloadblock')) {
        $entityDataClass = HighloadBlockTable::compileEntity(SUIT_TYPES_HL_ENTITY)->getDataClass();

        $item = $entityDataClass::getList([
            'filter' => ['ID' => $_GET['housetypes']],
            'select' => ['UF_NAME'],
        ])->fetch();

        if ($item) {
            $crumbs[] = [$item['UF_NAME'], null];
        }
    }
}

// Генерация JSON-LD (Google)
$itemList = [];
foreach ($crumbs as $index => [$title, $link]) {
    if (!empty($link)) {
        $itemList[] = [
            "@type" => "ListItem",
            "position" => $index + 1,
            "name" => $title,
            "item" => "https://" . $_SERVER["HTTP_HOST"] . $link
        ];
    } else {
        $itemList[] = [
            "@type" => "ListItem",
            "position" => $index + 1,
            "name" => $title
        ];
    }
}

$breadcrumbSchema = [
    "@context" => "https://schema.org",
    "@type" => "BreadcrumbList",
    "itemListElement" => $itemList
];

echo '<script type="application/ld+json">' .
    json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) .
    '</script>';
?>

<div class="crumbs">
    <ul class="list crumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">
        <?php foreach ($crumbs as $i => [$title, $link]): ?>
            <li class="list__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <?php if ($i === count($crumbs) - 1 || !$link): ?>
                    <span class="list__link" itemprop="name"><?= htmlspecialchars($title) ?></span>
                <?php else: ?>
                    <a class="list__link" href="<?= htmlspecialchars($link) ?>" itemprop="item">
                        <span itemprop="name"><?= htmlspecialchars($title) ?></span>
                    </a>
                <?php endif; ?>
                <meta itemprop="position" content="<?= $i + 1 ?>" />
            </li>
        <?php endforeach; ?>
    </ul>
</div>