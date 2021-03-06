<?php
/**
 * Generated by the WordPress Option Page generator
 * at http://jeremyhixon.com/wp-tools/option-page/
 */

class wp_shop_calendar_admin
{
    private $options;
    const DOMAIN = 'wp-shop-calendar';

    public function __construct()
    {
        self::stylesheets();
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
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
    public function add_plugin_page()
    {
        add_options_page(
            __('営業日カレンダー', self::DOMAIN), // page_title
            __('営業日カレンダー', self::DOMAIN), // menu_title
            'manage_options', // capability
            self::DOMAIN, // menu_slug
            array($this, 'admin_page') // function
        );
    }
    public function admin_page()
    {
        $calendar      = new wp_shop_calendar();
        $this->options = get_option('shop_calendar_option_name');?>
        <div class="wrap">
            <h2><?php _e('営業日カレンダー', self::DOMAIN);?></h2>
            <p></p>
            <?php settings_errors();?>
            <form method="post" action="options.php">
                <?php settings_fields('shop_calendar_option_group');?>
                <?php do_settings_sections('shop-calendar-admin');?>
                <?php submit_button();?>
            </form>
        <?php echo $calendar->before(); ?>
        <?php echo $calendar->calendar($this->options['number']); ?>
        <?php echo $calendar->after(); ?>
        </div>
    <?php }

    public function page_init()
    {
        register_setting(
            'shop_calendar_option_group', // option_group
            'shop_calendar_option_name', // option_name
            array($this, 'sanitize') // sanitize_callback
        );

        add_settings_section(
            'shop_calendar_setting_section', // id
            __('設定', self::DOMAIN), // title
            array($this, 'section_info'), // callback
            'shop-calendar-admin' // page
        );

        add_settings_field(
            'number', // id
            __('表示数', self::DOMAIN), // title
            array($this, 'number_callback'), // callback
            'shop-calendar-admin', // page
            'shop_calendar_setting_section' // section
        );

        add_settings_field(
            'regular_holiday', // id
            __('定休日', self::DOMAIN), // title
            array($this, 'regular_holiday_callback'), // callback
            'shop-calendar-admin', // page
            'shop_calendar_setting_section' // section
        );

        add_settings_field(
            'closeday', // id
            __('定休日以外の休み', self::DOMAIN), // title
            array($this, 'closeday_callback'), // callback
            'shop-calendar-admin', // page
            'shop_calendar_setting_section' // section
        );

        add_settings_field(
            'caption_format', // id
            __('表題フォーマット', self::DOMAIN), // title
            array($this, 'caption_format_callback'), // callback
            'shop-calendar-admin', // page
            'shop_calendar_setting_section' // section
        );

        add_settings_field(
            'before_table', // id
            __('表前に表示', self::DOMAIN), // title
            array($this, 'before_table_callback'), // callback
            'shop-calendar-admin', // page
            'shop_calendar_setting_section' // section
        );

        add_settings_field(
            'after_table', // id
            __('表後に表示', self::DOMAIN), // title
            array($this, 'after_table_callback'), // callback
            'shop-calendar-admin', // page
            'shop_calendar_setting_section' // section
        );
        add_settings_field(
            'week_head', // id
            __('曜日の表示', self::DOMAIN), // title
            array($this, 'week_head_callback'), // callback
            'shop-calendar-admin', // page
            'shop_calendar_setting_section' // section
        );
        add_settings_field(
            'no_use_css', // id
            __('プラグインのCSSを使用しない', self::DOMAIN), // title
            array($this, 'no_use_css_callback'), // callback
            'shop-calendar-admin', // page
            'shop_calendar_setting_section' // section
        );
    }

    public function sanitize($input)
    {
        $sanitary_values = array();
        if (isset($input['number'])) {
            $sanitary_values['number'] = $input['number'];
        }

        if (isset($input['closeday'])) {
            $sanitary_values['closeday'] = esc_textarea($input['closeday']);
        }
        if (isset($input['caption_format'])) {
            $sanitary_values['caption_format'] = $input['caption_format'];
        }
        if (isset($input['before_table'])) {
            $sanitary_values['before_table'] = esc_textarea($input['before_table']);
        }

        if (isset($input['after_table'])) {
            $sanitary_values['after_table'] = esc_textarea($input['after_table']);
        }

        if (isset($input['regular_holiday'])) {
            $sanitary_values['regular_holiday'] = $input['regular_holiday'];
        }
        if (isset($input['week_head'])) {
            $sanitary_values['week_head'] = $input['week_head'];
        }
        if (isset($input['no_use_css'])) {
            $sanitary_values['no_use_css'] = $input['no_use_css'];
        }
        return $sanitary_values;
    }

    public function section_info()
    {
        ?>
        <p><?php _e('[private_holiday_list  format=m月d日]で定休日以外の休みを表示します。');?></p>
        <p><?php _e('[today_status  format=m月d日]で今日のステータスを表示します。');?></p>
        <?php
}

    public function number_callback()
    {
        ?> <select name="shop_calendar_option_name[number]" id="number">
            <?php $selected = (isset($this->options['number']) && $this->options['number'] === '1') ? 'selected' : '';?>
            <option <?php echo $selected; ?>>1</option>
            <?php $selected = (isset($this->options['number']) && $this->options['number'] === '2') ? 'selected' : '';?>
            <option <?php echo $selected; ?>>2</option>
        </select> <?php
}

    public function closeday_callback()
    {
        printf(
            '<textarea class="large-text" rows="5" name="shop_calendar_option_name[closeday]" id="closeday">%s</textarea>',
            isset($this->options['closeday']) ? esc_attr($this->options['closeday']) : ''
        );
        ?>
        <p><?php _e('例：日付カンマ内容＋改行で区切り<br>2017-12-25,休業日<br>2017-12-26,休業日<br>');?></p>
        <p><a href="http://www8.cao.go.jp/chosei/shukujitsu/gaiyou.html#link2" target="_blank"><?php _e('総務省の「国民の祝日」はこちら');?></a></p>
        <?php
}
    public function caption_format_callback()
    {
        printf(
            '<input class="large-text" rows="5" name="shop_calendar_option_name[caption_format]" id="caption_format" value="%s">',
            isset($this->options['caption_format']) ? esc_attr($this->options['caption_format']) : ''
        );
        ?>
        <p><?php _e('日付/時刻の書式化を使用する。例:Y年m月');?> <a href="http://php.net/manual/ja/function.date.php" target="_blank">date()</a> </p>
        <?php
}

    public function before_table_callback()
    {
        printf(
            '<textarea class="large-text" rows="5" name="shop_calendar_option_name[before_table]" id="before_table">%s</textarea>',
            isset($this->options['before_table']) ? esc_attr($this->options['before_table']) : ''
        );
        ?>
        <p><?php _e('HTML・ショートコード可');?></p>
        <?php
}

    public function after_table_callback()
    {
        printf(
            '<textarea class="large-text" rows="5" name="shop_calendar_option_name[after_table]" id="after_table">%s</textarea>',
            isset($this->options['after_table']) ? esc_attr($this->options['after_table']) : ''
        );
        ?>
        <p><?php _e('HTML・ショートコード可');?></p>
        <?php
}

    public function regular_holiday_callback()
    {
        ?> <fieldset><?php $checked = (isset($this->options['regular_holiday']) && in_array(0, $this->options['regular_holiday'])) ? 'checked' : '';?>
        <label for="regular_holiday-0"><input type="checkbox" name="shop_calendar_option_name[regular_holiday][]" id="regular_holiday-0" value="0" <?php echo $checked; ?>> 日</label>
        <?php $checked = (isset($this->options['regular_holiday']) && in_array(1, $this->options['regular_holiday'])) ? 'checked' : '';?>
        <label for="regular_holiday-1"><input type="checkbox" name="shop_calendar_option_name[regular_holiday][]" id="regular_holiday-1" value="1" <?php echo $checked; ?>> 月</label>
        <?php $checked = (isset($this->options['regular_holiday']) && in_array(2, $this->options['regular_holiday'])) ? 'checked' : '';?>
        <label for="regular_holiday-2"><input type="checkbox" name="shop_calendar_option_name[regular_holiday][]" id="regular_holiday-2" value="2" <?php echo $checked; ?>> 火</label>
        <?php $checked = (isset($this->options['regular_holiday']) && in_array(3, $this->options['regular_holiday'])) ? 'checked' : '';?>
        <label for="regular_holiday-3"><input type="checkbox" name="shop_calendar_option_name[regular_holiday][]" id="regular_holiday-3" value="3" <?php echo $checked; ?>> 水</label>
        <?php $checked = (isset($this->options['regular_holiday']) && in_array(4, $this->options['regular_holiday'])) ? 'checked' : '';?>
        <label for="regular_holiday-4"><input type="checkbox" name="shop_calendar_option_name[regular_holiday][]" id="regular_holiday-4" value="4" <?php echo $checked; ?>> 木</label>
        <?php $checked = (isset($this->options['regular_holiday']) && in_array(5, $this->options['regular_holiday'])) ? 'checked' : '';?>
        <label for="regular_holiday-5"><input type="checkbox" name="shop_calendar_option_name[regular_holiday][]" id="regular_holiday-5" value="5" <?php echo $checked; ?>> 金</label>
        <?php $checked = (isset($this->options['regular_holiday']) && in_array(6, $this->options['regular_holiday'])) ? 'checked' : '';?>
        <label for="regular_holiday-6"><input type="checkbox" name="shop_calendar_option_name[regular_holiday][]" id="regular_holiday-6" value="6" <?php echo $checked; ?>> 土</label></fieldset> <?php
}

    public function week_head_callback()
    {
        printf(
            '<input class="large-text" rows="5" name="shop_calendar_option_name[week_head]" id="week_head" value="%s">',
            isset($this->options['week_head']) ? esc_attr($this->options['week_head']) : ''
        );
        ?>
        <p><?php _e('日曜開始。カンマ区切り。例：日,月,火,水,木,金,土');?></p>
        <?php
}
    public function no_use_css_callback()
    {
        $checked = (isset($this->options['no_use_css']) && in_array(0, $this->options['no_use_css'])) ? 'checked' : '';?>
        <label for="no_use_css"><input type="checkbox" name="shop_calendar_option_name[no_use_css][]" id="no_use_css" value="0" <?php echo $checked; ?>><?php _e('使用しない');?></label><?php
}
}
if (is_admin()) {
    $shop_calendar = new wp_shop_calendar_admin();
}

/*

 * Retrieve this value with:
 * $options = get_option( 'shop_calendar_option_name' ); // Array of All Options
 * $number = $options['number']; // number
 * $closeday = $options['closeday']; // closeday
 * $before_table = $options['before_table']; // before_table
 * $after_table = $options['after_table']; // after_table
 * $regular_holiday_4 = $options['regular_holiday_4']; // regular_holiday
 * $regular_holiday = $options['regular_holiday']; // regular_holiday
 */
