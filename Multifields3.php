<?php

class Multifields3
{
    public function __construct()
    {
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
            $script = $path . '/dist/mf.js';
            $v = '?v=' . filemtime(MODX_BASE_PATH . $script);
            $out .= '<link rel="modulepreload" href="' . MODX_SITE_URL . $script . $v . '">';
            $out .= '<script type="module" src="' . MODX_SITE_URL . $script . $v . '"></script>';
        }

        return $out;
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
}
