<?
foreach ($arParams['VARS'] as $key => $value) {
    ${$key} = $value;
}
?>
<script>
    const mapCenter = [<?=$arSection["COORDS"][0]?>, <?=$arSection["COORDS"][1]?>];
    let isMapLoaded = false

    function mapInit() {
        const miniMap = new ymaps.Map('map-preview', {
            center: mapCenter,
            zoom: 15,
            controls: []
        })

        const balloonLayout = ymaps.templateLayoutFactory.createClass(`
                <div class="mini-balloon">
                    <div class="mini-balloon__image">
                        <img src="<?=current($arSection["PICTURES"])["src"]?>" alt="<?=$arSection["NAME"]?>">
                    </div>
                    <div class="mini-balloon__content">
                        <div class="h6"><?=$arSection["NAME"]?></div>
                        <div class="score"><img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/score.svg" alt=""><span><?=$avgRating?></span></div>
                        <div class="area-info"><img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/marker.svg" alt=""><div><span><?=$arSection["UF_ADDRESS"]?></span></div></div>
                    </div>
                </div>
            `)

        const placemarkOptions = {
            iconLayout: 'default#image',
            iconImageHref: '<?= SITE_TEMPLATE_PATH ?>/assets/img/marker-circle.svg',
            iconImageSize: [16, 16],
            iconImageOffset: [-8, -8],
            balloonLayout: balloonLayout,
            balloonPanelMaxMapArea: 1,
            hideIconOnBalloonOpen: false
        }

        const miniMapPlacemark = new ymaps.Placemark(miniMap.getCenter(), {}, placemarkOptions)

        let pixelCenter = miniMap.getGlobalPixelCenter(mapCenter[0], mapCenter[1])
        pixelCenter = [
            pixelCenter[0],
            pixelCenter[1] - 15
        ]
        const geoCenter = miniMap.options.get('projection').fromGlobalPixels(pixelCenter, miniMap.getZoom())

        miniMap.behaviors.disable('scrollZoom')
        miniMap.setCenter(geoCenter)
        miniMap.geoObjects.add(miniMapPlacemark)
        miniMapPlacemark.balloon.open()

        const largeMap = new ymaps.Map('map-large', {
                center: mapCenter,
                zoom: 16,
                controls: ['zoomControl', 'trafficControl', 'typeSelector', 'geolocationControl', 'routeButtonControl']
            },
            {
                minZoom: 5
            }
        )

        const largeMapPlacemark = new ymaps.Placemark(largeMap.getCenter(), {}, placemarkOptions)

        largeMap.geoObjects.add(largeMapPlacemark)
        largeMapPlacemark.balloon.open()
    }

    function loadMap() {
        if (isMapLoaded) {
            return false
        }

        if (
            window.scrollY + window.innerHeight * 1.3 >=
            document.getElementById('map-preview').getBoundingClientRect().top - document.body.getBoundingClientRect().top
        ) {
            isMapLoaded = true

            const script = document.createElement('script')
            script.src = '//api-maps.yandex.ru/2.1/?apikey=215b99f5-a0ec-4dfe-8611-2ff1ef697a14&lang=ru_RU&onload=mapInit'
            script.defer = true
            document.body.appendChild(script)
        }
    }

    window.addEventListener('load', loadMap)
    window.addEventListener('scroll', loadMap)
</script>