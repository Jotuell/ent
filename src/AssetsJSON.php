<?php
namespace Ent;

class AssetsJSON {
    protected $themeUrl;
    protected $assets;

    public function __construct($themeUrl) {
        $this->themeUrl = $themeUrl;
    }

    public function get($filePath) {
        return $this->themeUrl . $filePath;
    }
}
