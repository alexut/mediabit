<?php

namespace Mediabit\Build;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

class Loader {
	public function loadParts() {
		$folders = [
			'config',
			'handlers',
			'templates',
			'shortcodes',
			'tools',
		];
		foreach ($folders as $foldername) {
			$folder = __DIR__ . "/" . $foldername;
			$files1 = glob($folder . '/*.php'); // return array files
			$files2 = glob($folder . '/**/*.php'); // return array files
			$files = array_merge($files1, $files2);
			// var_dump($files);
			foreach ($files as $filename) {
				if (basename($filename)[0] !== '_') {
					require_once $filename;
				}
			}
		}
	}
	
	public function init() {
		$this->loadParts();
	}
}

$loader = new Loader();

$loader->init();