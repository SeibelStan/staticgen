<meta charset="utf-8">
<title>Генератор статики</title>

<?php
    if(!curl_init($test)) {
        die('Не установлен cURL');
    }
    curl_close($test);

    define('SUBDIR', 'staticgen/'); // '', если проект находится в корне сервера

    function get_html($link) { // Используется cURL, должен быть установлен
        $link = $_SERVER['HTTP_HOST'] . '/' . SUBDIR . $link;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);

        return $data;
    }

    $page_groups = scandir('src');

    echo '<h1>Собраны</h1><ul>';

    foreach($page_groups as $page_group) {
        if(!preg_match('/\./', $page_group)) {

            $pages = scandir('src/' . $page_group);

            foreach($pages as $page) {
                if(!in_array($page, ['.', '..'])) {
                    $src = trim(get_html('src/' . $page_group . '/' . $page));
                    $tpl = trim(get_html('tpl/' . $page_group . '.php'));
                    $res = preg_replace('/\{\{content\}\}/i', $src, $tpl);

                    $fres = preg_replace('/(.+)\..+/', '$1', $page) . '.html';
                    fopen($fres, "w");
                    chmod($fres, 0777);
                    file_put_contents($fres, $res);

                    echo '<li><a href="' . $fres . '">' . $fres . '</a>';
                }
            }

        }
    }

    echo '</ul>';
?>
