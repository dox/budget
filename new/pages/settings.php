<div class="row">
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
	
		$svg = "<svg viewBox=\"$viewBox\">$inner</svg>";
		
		$output  = "<div class=\"col-sm-6 mb-3 mb-sm-0\">";
		$output .= "<div class=\"card\">";
		$output .= "<div class=\"card-body\">";
		$output .= $id;
		$output .= $svg;
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		
		echo $output;
	}
	?>
</div>