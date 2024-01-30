<?php

declare(strict_types=1);

/**
 *
 */
class Multifields3
{
    /**
     * @var Multifields3|null
     */
    protected static ?Multifields3 $instance = null;

    public function __construct()
    {
    }

    /**
     * @return static
     */
    public static function getInstance(): Multifields3
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @return string
     */
    public function getStartScripts(): string
    {
        $path = str_replace(MODX_BASE_PATH, '', str_replace(DIRECTORY_SEPARATOR, '/', __DIR__));
        $out =
            '<script>window["Vue"] || document.write("<script src=https://unpkg.com/vue@3/dist/vue.runtime.global.prod.js><\/script>");</script>';
        $out .= '<script>window["mf3Config"] = window["Vue"].reactive({});</script>';

        if (file_exists($hot = MODX_BASE_PATH . $path . '/hot')) {
            $v = '?v=' . time();
            $hot = trim(file_get_contents($hot));
            $out .= '<script type="module" src="' . $hot . '/@vite/client"></script>';
            $out .= '<script type="module" src="' . $hot . '/src/js/mf.js' . $v . '"></script>';
        } else {
            $script = $path . '/dist/mf/mf.js';
            $v = '?v=' . filemtime(MODX_BASE_PATH . $script);
            $out .= '<link rel="modulepreload" href="' . MODX_SITE_URL . $script . $v . '">';
            $out .= '<script type="module" src="' . MODX_SITE_URL . $script . $v . '"></script>';
        }

        return $out;
    }

    /**
     * @param array $tv
     *
     * @return string
     */
    public function getConfig(array $tv): string
    {
        if (!empty($tv['elements'])) {
            $config = $tv['elements'];
        } elseif (file_exists($path = __DIR__ . '/config/' . $tv['name'] . '.json')) {
            $config = file_get_contents($path);
        } else {
            $config = null;
        }

        $config = $config ? json_decode($config, true) : null;

        if (!empty($config['templates'])) {
            function f($items): array
            {
                return array_map(
                    function ($key, $item) {
                        $item['key'] ??= $key;

                        if (!empty($item['items'])) {
                            $item['items'] = f($item['items']);
                        }

                        return $item;
                    },
                    array_keys($items),
                    $items
                );
            }

            $config['templates'] = f($config['templates']);
        }

        return json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param $input
     *
     * @return array|mixed|string
     */
    protected function getElements($input)
    {
        if (!$input) {
            return [];
        }

        if (!function_exists('ProcessTVCommand')) {
            require_once EVO_CORE_PATH . 'functions/tv.php';
        }

        if (!is_string($input)) {
            return $input;
        }

        $values = [];
        $elements = ParseInputOptions(ProcessTVCommand($input, '', '', 'tvform'));

        if (!empty($elements)) {
            foreach ($elements as $element) {
                [$key, $val, $help] = is_array($element) ? $element : explode('==', $element);

                if (strlen($val) == 0) {
                    $val = $key;
                }

                if (strlen($key) == 0) {
                    $key = $val;
                }

                $values[] = [
                    'key' => $key,
                    'value' => $val,
                    'help' => $help ?? null,
                ];
            }
        }

        return $values;
    }

    /**
     * @return void
     */
    public function managerInit()
    {
        if (!empty($_REQUEST['a']) && $_REQUEST['a'] === 'mf3') {
            $result = [];
            $action = $_REQUEST['action'] ?? null;
            $body = json_decode(file_get_contents('php://input'), true)
                ?: ($_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST : $_GET);

            switch ($action) {
                case 'elements':
                    $result = $this->getElements($body['elements'] ?? '');
                    break;
            }

            echo json_encode($result, JSON_UNESCAPED_UNICODE);

            exit;
        }
    }

    /**
     * @return string
     */
    public function jsonEditorScripts(): string
    {
        $path = MODX_SITE_URL . str_replace(MODX_BASE_PATH, '', str_replace(DIRECTORY_SEPARATOR, '/', __DIR__));

        return '
        <link href="' . $path . '/dist/jsoneditor/jsoneditor.min.css" rel="stylesheet" type="text/css">
        <script src="' . $path . '/dist/jsoneditor/jsoneditor-minimalist.min.js"></script>
        <script src="' . $path . '/dist/jsoneditor/jsoneditor.init.js"></script>';
    }
}
