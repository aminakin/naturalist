<?php
foreach ($arResult as $key => $value) {
    ${$key} = $value;
}

// Сформируем массив для JSON-LD
$crumbs = [
    ['Главная', '/'],
    ['Каталог', '/catalog/'],
    [$arSection['NAME'], null]
];

// JSON-LD для Google
$itemList = [];
foreach ($crumbs as $index => [$title, $link]) {
    $item = [
        '@type' => 'ListItem',
        'position' => $index + 1,
        'name' => $title
    ];
    if ($link) {
        $item['item'] = 'https://' . $_SERVER['HTTP_HOST'] . $link;
    }
    $itemList[] = $item;
}

$breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => $itemList
];

echo '<script type="application/ld+json">' .
    json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) .
    '</script>';
?>

<div class="crumbs">
<ul class="list crumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList"><?php
foreach ($crumbs as $index => [$title, $link]): ?><li class="list__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><?php
if ($link): ?><a class="list__link" href="<?= htmlspecialchars($link) ?>" itemprop="item"><span itemprop="name"><?= htmlspecialchars($title) ?></span></a><?php
else: ?><span class="list__link" itemprop="name"><?= htmlspecialchars($title) ?></span><?php
endif; ?><meta itemprop="position" content="<?= $index + 1 ?>" /></li><?php
endforeach; ?></ul>
</div>