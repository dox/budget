	<?php
	$doc = new DOMDocument();
	$doc->load('assets/icons/icons.svg');
	$symbols = $doc->getElementsByTagName('symbol');
	
	foreach ($symbols as $symbol) {
		$id = $symbol->getAttribute('id');
		$viewBox = $symbol->getAttribute('viewBox');
	
		// Create standalone <svg> string
		$inner = '';
		foreach ($symbol->childNodes as $child) {
			$inner .= $doc->saveXML($child);
		}
	
		$svg = "<svg class=\"me-2\" viewBox=\"$viewBox\" fill=\"currentColor\" width=\"2em\" xmlns=\"http://www.w3.org/2000/svg\">$inner</svg>";

		echo "<button type=\"button\" class=\"p-2 btn btn-dark text-start\">" . $svg . $id . "</button>";
	}
	?>
