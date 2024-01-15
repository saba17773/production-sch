<?php

function renderView($path, $data = null)
{
	$templates = new \League\Plates\Engine("./views");
	if (isset($data)) {
		echo $templates->render($path, $data);
	} else {
		echo $templates->render($path);
	}
}

function response($result, $message)
{
	return [
		"result" => $result,
		"message" => $message
	];
}
