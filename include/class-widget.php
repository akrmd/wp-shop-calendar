<?php
class shop_calendar extends WP_Widget
{
    const DOMAIN = 'wp-shop-calendar';
    /**
     * Widgetを登録する
     */
    public function __construct()
    {
        parent::__construct(
            'shop_calendar', // Base ID
            __('営業日カレンダー'), // Name
            array('description' => __('営業日カレンダー')) // Args
        );
    }

    /**
     * 表側の Widget を出力する
     *
     * @param array $args      'register_sidebar'で設定した「before_title, after_title, before_widget, after_widget」が入る
     * @param array $instance  Widgetの設定項目
     */
    public function widget($args, $instance)
    {
        self::stylesheets();
        $title = $instance['title'];
        $id    = $args['widget_id'];
        echo $args['before_widget'];
        echo "<span class=\"gamma widget-title\">${title}</span>";

        $calendar = new wp_shop_calendar();
        $number   = get_option('shop_calendar_option_name')['number'];
        echo $calendar->before();
        echo $calendar->calendar($number);
        echo $calendar->after();

        echo $args['after_widget'];
    }
    public function stylesheets()
    {
        if (get_option('shop_calendar_option_name')['no_use_css']) {
            return;
        }

        add_action('wp_enqueue_scripts', array($this, 'stylesheets'));

        if (@file_exists(get_stylesheet_directory() . '/' . self::DOMAIN . '.css')) {
            $css_file = get_stylesheet_directory_uri() . '/' . self::DOMAIN . '.css';
        } else {
            $css_file = plugins_url(self::DOMAIN) . '/assets/css/' . self::DOMAIN . '.css';
        }
        wp_enqueue_style(self::DOMAIN, $css_file, false);
    }
    /** Widget管理画面を出力する
     *
     * @param array $instance 設定項目
     * @return string|void
     */
    public function form($instance)
    {
        $title      = $instance['title'];
        $title_name = $this->get_field_name('title');
        $title_id   = $this->get_field_id('title');
        echo "<p>
        <label for=\"${title_id}\">タイトル</label>
        <input class=\"widefat\" id=\"${title_id}\" name=\"${title_name}\" type=\"text\" value=\"${title}\">
        </p>";
    }

    /** 新しい設定データが適切なデータかどうかをチェックする。
     * 必ず$instanceを返す。さもなければ設定データは保存（更新）されない。
     *
     * @param array $new_instance  form()から入力された新しい設定データ
     * @param array $old_instance  前回の設定データ
     * @return array               保存（更新）する設定データ。falseを返すと更新しない。
     */
    public function update_i18n($new_instance, $old_instance)
    {
        if ($new_instance == $old_instance) {
            return false;
        }
        return $new_instance;
    }
}

add_action('widgets_init', function () {
    register_widget('shop_calendar'); //WidgetをWordPressに登録する
});
