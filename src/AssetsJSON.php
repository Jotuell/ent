<?php
namespace Ent;

class AssetsJSON {
    protected $themeUrl;
    protected $assets;

    public function __construct($json, $themeUrl) {
        $this->assets   = json_decode(file_get_contents($json), true);
        $this->themeUrl = $themeUrl;
    }

    public function get($file) {
        return $this->themeUrl . $this->assets[$file];
    }
}
