<?php # 

// Probe for a language include with constants. Still include defines later on, if some constants were missing

if (IN_serendipity !== true) {
    die ("Don't hack!");
}

$probelang = dirname(__FILE__) . '/' . $serendipity['charset'] . 'lang_' . $serendipity['lang'] . '.inc.php';
if (file_exists($probelang)) {
    include $probelang;
}

include_once dirname(__FILE__) . '/lang_en.inc.php';

class serendipity_plugin_multilingual extends serendipity_event
{
    var $title = PLUGIN_SIDEBAR_MULTILINGUAL_TITLE;

    function introspect(&$propbag)
    {
        global $serendipity;

        $propbag->add('name',          PLUGIN_SIDEBAR_MULTILINGUAL_TITLE);
        $propbag->add('description',   PLUGIN_SIDEBAR_MULTILINGUAL_DESC);
        $propbag->add('stackable',     false);
        $propbag->add('author',        'Garvin Hicking, Wesley Hwang-Chung');
        $propbag->add('requirements',  array(
            'serendipity' => '0.8',
            'smarty'      => '2.6.7',
            'php'         => '4.1.0'
        ));


        $conf = array('title', 'show_submit', 'size');
        foreach($serendipity['languages'] AS $lkey => $lval) {
            $conf[] = $lkey;
        }
        $propbag->add('configuration', $conf);
        $propbag->add('version',       '1.11');
        $propbag->add('groups',        array('FRONTEND_VIEWS'));
        $this->dependencies = array('serendipity_event_multilingual' => 'remove');
    }

    function introspect_config_item($name, &$propbag) {
        global $serendipity;
        
        foreach($serendipity['languages'] AS $lkey => $lval) {
            if ($name == $lkey) {
				$propbag->add('type',        'boolean');
				$propbag->add('name',        $lval);
				$propbag->add('default',     'true');
				return true;
            }
        }
        
        switch($name) {
            case 'title':
                $propbag->add('type',        'string');
                $propbag->add('name',        TITLE);
                $propbag->add('description', TITLE);
                $propbag->add('default',     '');
                break;

            case 'show_submit':
                $propbag->add('type',        'boolean');
                $propbag->add('name',        PLUGIN_SIDEBAR_MULTILINGUAL_SUBMIT);
                $propbag->add('description', PLUGIN_SIDEBAR_MULTILINGUAL_SUBMIT_DESC);
                $propbag->add('default',     'false');
                break;

			case 'size':
                $propbag->add('type',        'string');
                $propbag->add('name',        PLUGIN_SIDEBAR_MULTILINGUAL_SIZE);
                $propbag->add('default',     '9');
                break;

            default:
                    return false;
        }
        return true;
    }

    function generate_content(&$title) {
        global $serendipity;

        $title = $this->get_config('title', $this->title);
        $url = serendipity_currentURL(true);

        echo '<form id="language_chooser" action="' . $url . '" method="post"><div>';
        echo '<select style="font-size: ' . $this->get_config('size', '9') . 'px" name="user_language" onchange="document.getElementById(\'language_chooser\').submit();">';
        foreach ($serendipity['languages'] as $lang_key => $language) {
        	if ($this->get_config($lang_key, 'false') == 'true') {
            	echo '<option value="' . $lang_key . '" ' . ($serendipity['lang'] == $lang_key ? 'selected="selected"' : '') . '>' . htmlspecialchars($language) . '</option>';
            }
        }
        echo '</select>';

        if ($this->get_config('show_submit', 'false') == 'true') {
            echo '<input type="submit" name="submit" value="' . GO . '" size="4" />';
        }
        echo '</div></form>';
    }
}

/* vim: set sts=4 ts=4 expandtab : */
?>