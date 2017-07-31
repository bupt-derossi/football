<?php
/*
 * brief: 抓取澳客网配置文件
 */
class OkoooConfig {
    public static $WEEK_MAP = [
        0 => '日',
        1 => '一',
        2 => '二',
        3 => '三',
        4 => '四',
        5 => '五',
        6 => '六',
    ];

    public static $FIELD_MAP = [
        2 => [
            'bf' => [
                'vict_odds' => 5,
                'bet_vict' => 6,
                'profit_vict' => 7,
            ],
            'jc' => [
                'vict_odds' => 8,
                'bet_vict' => 9,
                'profit_vict' => 10,
            ]
        ],
        3 => [
            'bf' => [
                'draw_odds' => 5,
                'bet_draw' => 6,
                'profit_draw' => 7,
            ],
            'jc' => [
                'draw_odds' => 8,
                'bet_draw' => 9,
                'profit_draw' => 10,
            ]
        ],
        4 => [
            'bf' => [
                'fail_odds' => 5,
                'bet_fail' => 6,
                'profit_fail' => 7,
            ],
            'jc' => [
                'fail_odds' => 8,
                'bet_fail' => 9,
                'profit_fail' => 10,
            ]
        ],
    ];
}