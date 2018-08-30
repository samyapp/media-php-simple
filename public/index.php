<?php

$media_path = __DIR__ . '/media';

$media_url = str_replace('/index.php', '/media', $_SERVER['SCRIPT_NAME']);

$path = $_REQUEST['path'] ?? '/';

if(strpos($path, './') !== false || strpos($path,'../') !== false) {
	exit();
}

if($path && $path[0] != '/') {
	$path = '/' . $path;
}
$full_path = $media_path . $path;

if(!is_dir($full_path)) {
	exit();
}

$tree = $_REQUEST['tree'] ?? false;

function getDir($path, $recurse = true) {
	$files = new FilesystemIterator($path);
	$results = [];
	foreach($files as $file) {
		$data = [
			'name' => $file->getFilename(),
			'type' => $file->isDir() ? 'dir' : 'file',
		];
		if($file->isDir()) {
			if($recurse) {
				$data['contents'] = getDir($file->getPathname(), true);
				$data['size'] = count($data['contents']);
			}
		}
		else {
			$data['size'] = $file->getSize();
		}
		$results[] = $data;
	}
	return $results;
}

header('content-type: text/json');
echo json_encode(getDir($full_path, true));
