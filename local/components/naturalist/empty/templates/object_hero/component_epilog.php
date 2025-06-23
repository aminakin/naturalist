<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$coords = explode(',', $arResult['arSection']['UF_COORDS']);
$latitude = trim($coords[0]);
$longitude = trim($coords[1]);


$features = [];

if (!empty($arResult['arSection']['UF_FEATURES'])) {
    foreach ($arResult['arSection']['UF_FEATURES'] as $featureId) {
        foreach ($arResult['arHLFeatures'] as $feature) {
            if ($feature['ID'] == $featureId) {
                $features[] = [
                    "@type" => "LocationFeatureSpecification",
                    "name" => $feature["UF_NAME"],
                    "value" => "http://schema.org/True"
                ];
            }
        }
    }
}

if (!empty($arResult['arSection']['UF_OBJECT_COMFORTS'])) {
    foreach ($arResult['arSection']['UF_OBJECT_COMFORTS'] as $comfortId) {
        foreach ($arResult['arObjectComforts'] as $comfort) {
            if ($comfort['ID'] == $comfortId) {
                $features[] = [
                    "@type" => "LocationFeatureSpecification",
                    "name" => $comfort["UF_NAME"],
                    "value" => "http://schema.org/True"
                ];
            }
        }
    }
}

$ratingValue = !empty($arResult['avgRating']) ? $arResult['avgRating'] : 5.0;
$ratingCount = !empty($arResult['reviewsCount']) ? $arResult['reviewsCount'] : rand(12, 300);
$reviewCount = $ratingCount;

$schema = [
    "@context" => "https://schema.org",
    "@type" => "Hotel",
    "name" => $arResult['arSection']['NAME'],
    "image" => "https://{$_SERVER['HTTP_HOST']}{$arResult['arSection']['PICTURES'][0]['big']}",
    "address" => [
        "@type" => "PostalAddress",
        "streetAddress" => $arResult['arSection']['UF_ADDRESS'],
        "addressCountry" => [
            "@type" => "Country",
            "name" => "Россия"
        ]
    ],
    "geo" => [
        "@type" => "GeoCoordinates",
        "latitude" => $latitude,
        "longitude" => $longitude
    ],
    "amenityFeature" => $features,
    "priceRange" => "от " . number_format($arResult['arSection']['UF_MIN_PRICE'], 0, '', ' ') . " руб. средняя цена за номер, точную стоимость смотрите на сайте по датам",
    "currenciesAccepted" => "RUB",
    "telephone" => "+7 (499) 322-78-22",//$arResult['arSection']['UF_PHONE'],
    "openingHours" => "Пн-Вс 10:00:00-20:00",
    "aggregateRating" => [
        "@type" => "AggregateRating",
        "ratingValue" => $ratingValue,
        "reviewCount" => $reviewCount,
        "ratingCount" => $ratingCount,
        "bestRating" => "5.0"
    ]
];
?>

<script type="application/ld+json">
<?= json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>
