<?php

namespace FseoOuter\common;

/**
 * Вспомогательный класс
 * Class SupportingFunction
 * @package FseoOuter\common
 */
class SupportingFunction
{
    /**
     * добавить соц. кнопки после тега more в категория
     * @param $content
     * @return mixed|null|string|string[]
     */
    public static function socButtonMoreCat($content)
    {
        $social_post = (int)get_option('fseo_outer_social', true);
        $social_cat = (int)get_option('fseo_cat_social', true);
        $soc_btns = '';
        // список сервисов
        $soc_btns_service = 'vkontakte,facebook,odnoklassniki,moimir,twitter,viber,whatsapp,skype,telegram';
        if (get_option('social_btns') !== ''){
            $soc_btns_service = get_option('social_btns');
        }
        // показывать или нет счетчики
        $soc_btns_counters = 'data-counter=""';
        if (get_option('social_btns_counter') !== '1'){
            $soc_btns_counters = '';
        }
        if (($social_post === 1 && !is_category()) || ($social_cat === 1 && is_category())) {
            $soc_btns = '<div class="ya_share">'
                . '<div class="ya-share2" data-services="' . $soc_btns_service . '" ' . $soc_btns_counters . '></div>'
                . '</div>'
                . '<div class="ya_share_after"></div>';
        }
        $content = preg_replace('#<span.*?id="more-(.*?)".*?></span>#', '<div class="mih"></div>' . '<span id="more-\1"></span></p>' . $soc_btns, $content);
        if (is_category()) {
            $content = str_replace('<span id="more"></span>', '<div class="mih"></div>' . '<span id="more-\1"></span></p>' . $soc_btns, $content);
        }
        return $content;
    }

    /**
     * Отключае сжатие wp для картинок
     * @param $arg
     * @return int
     */
    public static function imgQuality($arg)
    {
        return (int)100;
    }

    /**
     * Парсинг поста - картинки, блоки, видео
     */
    public static function parseArticleText()
    {
        $articleID = get_the_ID();
        $post = get_post($articleID);
        $text = $post === null ? '' : $post->post_content;
        preg_match_all('/<img[^>]+>/i', $text, $imgs);
        preg_match_all('/\[\/embed\]/i', $text, $frames);
        preg_match_all('|<div class=\"warning\">(.*?)</div>|is', $text, $divs_warnings);
        preg_match_all('|<div class=\"advice\">(.*?)</div>|is', $text, $divs_advice);
        preg_match_all('|<div class=\"stop\">(.*?)</div>|is', $text, $divs_stop);
        preg_match_all('|<div class=\"zakon\">(.*?)</div>|is', $text, $divs_zakon);
        preg_match_all('|href=\"([^\"]+)|i', $text, $links);
        $outLinkCounter = 0;
        $docsCounter = 0;
        foreach ($links[0] as $link) {
            if (mb_strpos($link, get_site_url()) === false) {
                $outLinkCounter++;
            }
            if (
                mb_strpos($link, '.doc') ||
                mb_strpos($link, '.docx') ||
                mb_strpos($link, '.txt') ||
                mb_strpos($link, '.pdf') ||
                mb_strpos($link, '.ods')
            ) {
                $docsCounter++;
            }
        }
        $thmb = get_the_post_thumbnail($articleID) ? 1 : 0;
        $value = [
            'blocks' => count($divs_warnings[0]) + count($divs_stop[0]) + count($divs_advice[0]),
            'zakon' => count($divs_zakon[0]),
            'images' => $thmb + count($imgs[0]),
            'videos' => count($frames[0]),
            'out_links' => $outLinkCounter,
            'docs' => $docsCounter
        ];
        update_post_meta($articleID, 'post_parsing', json_encode($value));
    }

    /**
     * nofollow в диалоге вставки ссылки в админке
     */
    public static function tnlAddNofollow()
    {
        wp_deregister_script('wplink', plugins_url('/js/nofollow.min.js', __FILE__), ['jquery'], FSEO_OUTER_VER, true);
    }
}
