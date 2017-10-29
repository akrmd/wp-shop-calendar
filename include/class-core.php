<?php

class wp_shop_calendar
{

    private $options;
    public function __construct()
    {
        $this->options = get_option('shop_calendar_option_name');
    }

    /**
     * 定休日以外の休み
     * @return [type] [description]
     */
    public function private_holiday()
    {
        //get_optionでclosedayを取得して改行で配列化
        $private_holiday = explode("\n", $this->options['closeday']);
        $private_holiday = array_filter($private_holiday);
        if (!isset($private_holiday)) {
            return;
        }

        foreach ($private_holiday as $key => $value) {
            $date[$key] = list($day, $name) = explode(',', $value);
        }
        return $date;
    }
    /**
     * 閉店日リスト
     * @param  string $now  [description]
     * @param  string $type [description]
     * @return [type]       [description]
     */
    public function private_holiday_list($format)
    {
        $private_holiday = self::private_holiday();
        if (!$private_holiday) {
            return;
        }

        foreach ($private_holiday as $value) {
            if (strtotime($value[0]) > strtotime("last day of -1 month") &&
                strtotime($value[0]) < strtotime("last day of +2 month")) {
                $date[] = '<dt>' . date_i18n($format, strtotime($value[0])) . '</dt><dd>' . $value[1] . '</dd>';
            }
        }
        $return = '<dl class="private_holiday_list">' . implode('', $date) . '</dl>';
        return $return;
    }
    /**
     * 日付を7等分、private_holidayのにcloseを追加
     * @param  [type] $timestamp [description]
     * @return [type]            [description]
     */
    public function get_calendar_date($timestamp)
    {
        //今月の初日曜日
        $current_firstday_week = date_i18n('w', strtotime('first day of', $timestamp));
        //今月の最終日
        $current_lastday = date_i18n('j', strtotime('last day of', $timestamp));
        //先月分最終日
        $prev_lastday = date_i18n('j', strtotime('last day of -1 month', $timestamp));
        //先月分埋める
        $date = [];
        if ($current_firstday_week != "0") {
            for ($i = 0; $i < $current_firstday_week; $i++) {
                $d      = $prev_lastday - $current_firstday_week + $i + 1;
                $date[] = [$d, 'gray'];
            }
        }
        //今月分埋める
        $private_holiday = self::private_holiday();
        for ($i = 0; $i < $current_lastday; $i++) {
            $d      = $i + 1;
            $a      = date_i18n('Y-m', $timestamp) . '-' . $d;
            $class  = (is_array($private_holiday) && array_search($a, array_column($private_holiday, 0)) !== false) ? 'close' : '';
            $date[] = [$d, $class];
        }
        //来月分埋める
        $short = 7 - (count($date) % 7); //今までの$date数を7で割ったあまりを不足分とする
        for ($i = 0; $i < $short; $i++) {
            $d      = $i + 1;
            $date[] = [$d, 'gray'];
        }
        // 曜日分7分割する
        $date = array_chunk($date, 7);

        return $date;
    }

    public function table($timestamp)
    {
        $regular_holiday = $this->options['regular_holiday'];
        $date            = self::get_calendar_date($timestamp);
        //ヘッダーの追加
        $week_head = explode(',', $this->options['week_head']);
        $th        = '';
        foreach ($week_head as $head) {
            $th[] = '<th>' . $head . '</th>';
        }
        $tr = ['<tr>' . implode('', $th) . '</tr>' . "\n"];
        //日付の追加
        foreach ($date as $week) {
            $td = '';
            foreach ($week as $key => $day) {
                $classies = [$day[1]];
                if (!empty($regular_holiday) && in_array($key, $regular_holiday)) {
                    $classies[] = 'close';
                }
                if (date_i18n('m-j') == date_i18n('m', $timestamp) . '-' . $day[0]) {
                    $classies[] = 'current';
                }

                $classies = array_filter($classies);
                $classies = array_unique($classies);
                $class    = (isset($classies)) ? ' class="' . implode(' ', $classies) . '"' : '';
                $td[]     = '<td' . $class . '>' . $day[0] . '</td>';
            }
            $tr[] = '<tr>' . implode('', $td) . '</tr>' . "\n";
        }
        //テーブルキャプションの追加
        $caption = '<caption>' . date_i18n($this->options['caption_format'], $timestamp) . '</caption>' . "\n";
        //テーブル作成
        $table = '<table class="wp-shop-calendar">' . "\n" . $caption . implode('', $tr) . '</table>';
        return $table;
    }
    public function calendar($number = 1)
    {
        $return = '';
        for ($i = 0; $i <= ($number - 1); $i++) {
            $return .= self::table(strtotime('+' . $i . ' month'));
        }
        return $return;
    }
    public function before()
    {
        $content = $this->options['before_table'];
        $content = apply_filters('the_content', $content);
        $content = html_entity_decode($content);
        $content = '<div class="wp-shop-calendar-before">' . $content . '</div>';
        return $content;
    }
    public function after()
    {
        $content = $this->options['after_table'];
        $content = apply_filters('the_content', $content);
        $content = html_entity_decode($content);
        $content = '<div class="wp-shop-calendar-after">' . $content . '</div>';
        return $content;
    }

    /**
     * 今日は？
     * @return [type] [description]
     */
    public function today($format)
    {
        $private_holiday = self::private_holiday();
        $regular_holiday = $this->options['regular_holiday'];
        $date            = date_i18n($format);
        if ($private_holiday) {
            $key = array_search(date_i18n('Y-m-d'), array_column($private_holiday, 0));
            if ($key !== false) {
                $title  = $private_holiday[$key][1];
                $return = __("本日(${date})は${title}のためお休み");
                $cls    = 'close';
            }
        }
        if (in_array(date_i18n('w'), $regular_holiday)) {
            $return = __("本日(${date})は定休日");
            $cls    = 'close';
        }
        if (!$return) {
            $return = __("本日(${date})は通常営業");
            $cls    = '';
        }
        return '<p class="today ' . $cls . '">' . $return . '</p>';
    }
}
