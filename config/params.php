<?php

/*
 * 返回自定义参数
 */
return [

    /*
     * 报警通知方式
     * 钉钉 dingtalk
     */
    'warning_type' => 'dingtalk',

    /*
     * 报警参数
     */
    'dingtalk' => [
        //DJD专属测试群
        'default' => [
            'keywords' => '报错提示',
            'token' => '4a9e6f5318247d96d5700a69d59530b817286d6770a43adddc4750b06c6527ab'
        ],
        //签到打卡群
        'clock_in_and_out' => [
            'keywords' => '友情提醒',
            'token' => 'b077c498119870016eb695f9d91247c12f98ef97c5ace41f476a9ca7c0d5e2b9'
        ],
        //运营签到打卡群
        'clock_in_and_out_for_yun_ying' => [
            'keywords' => '友情提醒',
            'token' => '887df2c12f68249f4ba33ae5cb0ce7dc7e54d695e8aef646d1f5c4f8057fadb4'
        ],
        //小组群
        'schedule_reminder' => [
            'keywords' => '友情提醒',
            'token' => 'a747e4e7325e99da94cd2de9f8819c4d2f7c9ff4ebd854c4f7e3e95d068ec30f'
        ],
        //技术部恰饭群
        'eating_census' => [
            'keywords' => '友情提醒',
            'token' => '6143f281aa651bf31da2787aa0c075ea2b1ab3e93488bd0d2091572de9c27c50'
        ],
    ],
];