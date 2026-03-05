<?php

return [
    'page_title' => 'Перевірка позицій у Google',
    'heading' => 'Перевірка позиції сайту у Google (органічна видача)',
    'lead' => 'Введіть пошукове слово, назву сайту, локацію та мову. Додаток зробить запит до SERP API та покаже позицію домену в органічній видачі.',

    'form' => [
        'keyword' => [
            'label' => 'Пошукове слово',
            'placeholder' => 'напр.: домофон',
        ],
        'site' => [
            'label' => 'Назва сайту',
            'placeholder' => 'напр.: example.com або https://example.com',
            'hint' => 'Можна вказати домен або повний URL. Порівняння робиться по домену (піддомени підтримуються).',
        ],
        'location' => [
            'label' => 'Локація',
            'placeholder' => 'напр.: Україна',
        ],
        'language' => [
            'label' => 'Мова',
            'placeholder' => 'напр.: Українська',
        ],
        'defaults' => [
            'location' => 'Україна',
            'language' => 'Українська',
        ],
        'submit' => 'Пошук',
        'loading' => 'Йде пошук...',
    ],

    'result' => [
        'status' => [
            'found' => 'Знайдено',
            'not_found' => 'Не знайдено',
            'error' => 'Помилка',
        ],
        'labels' => [
            'organic_position' => 'Позиція в органічній видачі:',
            'domain' => 'Домен:',
            'absolute_position' => 'Абсолютна позиція:',
            'url' => 'URL:',
            'title' => 'Заголовок:',
            'api' => 'API:',
        ],
        'not_found_suffix' => 'не знайдено у топ-:depth органічних результатів.',
        'generic_error' => 'Щось пішло не так.',
    ],
];
