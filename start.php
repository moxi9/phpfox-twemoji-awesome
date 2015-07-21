<?php

new Core\Route('/emojis', function(Core\Controller $controller) {
	$doc = new \DOMDocument();
	libxml_use_internal_errors(true);
	$html = '';
	$emoticons = @file_get_contents('https://raw.githubusercontent.com/arvida/emoji-cheat-sheet.com/master/public/index.html');
	if ($emoticons !== false) {
		$doc->loadHTML($emoticons);
		$xml = $doc->saveXML($doc);

		$xml = @simplexml_load_string($xml);
		$skip = ['feelsgood', 'finnadie', 'goberserk', 'godmode', 'hurtrealbad', 'rage1', 'rage2', 'rage3', 'rage4', 'suspect', 'trollface', 'bowtie', 'disappointed_relieved', 'neckbeard', 'collision', 'hankey', 'shit', '+1', '-1', 'facepunch', 'metal', 'fu', 'running', 'raising_hand'];
		if ($xml instanceof SimpleXMLElement && isset($xml->body) && isset($xml->body->ul)) {
			foreach ($xml->body->ul as $ul) {
				if ($ul instanceof SimpleXMLElement) {
					if (!isset($ul->attributes()->class)) {
						continue;
					}

					$class = (string) $ul->attributes()->class;
					if ($class == 'people emojis') {
						foreach ($ul->li as $li) {
							$key = (string) $li->div->span;
							if (in_array($key, $skip)) {
								continue;
							}

							$html .= '<li><i class="twa twa-' . str_replace('_', '-', $key) . '"></i><span>:' . $key . ':</span></li>';
						}
					}
				}
			}
		}
	}

	return [
		'h1_clean' => 'Emoji Cheat Sheet',
		// 'content' => ($html ? '<ul class="emoji-list">' . $html . '</ul>' : '<div id="emoji-cheat-sheet"><i class="fa fa-spin fa-circle-o-notch"></i></div>')
		'content' => ($html ? '<ul class="emoji-list">' . $html . '</ul>' : '<div class="error_message">Unable to load Emojis</div>')
	];
});