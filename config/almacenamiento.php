<?php

return [

    // Minutos de validez de las URLs firmadas para ver/descargar evidencias privadas
    'url_temporal_minutos' => env('B2_URL_TEMPORAL_MINUTOS', 60),

    // Carpeta base dentro del bucket para las evidencias de Equipos
    'carpeta_evidencias' => 'evidencias',

];