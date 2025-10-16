<div class="row row-cols-4 row-cols-md-6 ">
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
		
		$output  = "<div class=\"card\">";
		$output .= "<div class=\"card-body\">";
		$output .= $svg;
		$output .= $id;
		$output .= "</div>";
		$output .= "</div>";
		
		echo $output;
	}
	?>
</div>