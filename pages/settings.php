<?php


?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
	<h1 class="h2">Settings</h1>
	<div class="btn-toolbar mb-2 mb-md-0">
		<div class="btn-group me-2">
			<button type="button" class="btn btn-sm btn-outline-secondary"><i class="bi bi-plus-circle" aria-hidden="true"></i> New</button>
		</div>
	</div>
</div>
<div class="accordion" id="accordionExample">
  <?php
  foreach ($settings->getAll() as $setting) {
	  // Determine the show states based on the 'settingUID' parameter
	  $isActive = isset($_GET['settingUID']) && $_GET['settingUID'] == $setting['id'];
	  $headingShow = $isActive ? "accordion-button show" : "accordion-button collapsed";
	  $settingShow = $isActive ? "accordion-collapse show" : "accordion-collapse collapse";
  
	  // Generate item name and the start of the output string
	  $itemName = "collapse-" . $setting['id'];
	  $output = "<div class=\"accordion-item\">
				  <h2 class=\"accordion-header\" id=\"{$setting['id']}\">
					  <button class=\"{$headingShow}\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#{$itemName}\" aria-expanded=\"true\" aria-controls=\"{$itemName}\">
						  <strong>{$setting['name']}</strong>: {$setting['description']} <span class=\"badge bg-secondary\">{$setting['type']}</span>
					  </button>
				  </h2>
				  <div id=\"{$itemName}\" class=\"{$settingShow}\" aria-labelledby=\"{$setting['id']}\" data-bs-parent=\"#accordionExample\">
					  <div class=\"accordion-body\">
						  <form method=\"post\" id=\"form-{$setting['id']}\" action=\"{$_SERVER['REQUEST_URI']}\">";
  
	  // Handle different setting types
	  switch ($setting['type']) {
		  case 'numeric':
			  $output .= "<div class=\"input-group\">
							  <input type=\"number\" class=\"form-control\" id=\"value\" name=\"value\" value=\"{$setting['value']}\">
							  <button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>
						  </div>";
			  break;
  
		  case 'boolean':
			  $checked = ($setting['value'] == "true") ? "checked" : "";
			  $output .= "<div class=\"form-check\">
							  <input type=\"hidden\" id=\"value\" name=\"value\" value=\"false\">
							  <input type=\"checkbox\" class=\"form-check-input\" id=\"value\" name=\"value\" value=\"true\" {$checked}>
							  <button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>
						  </div>";
			  break;
  
		  case 'html':
			  $output .= "<textarea rows=\"10\" class=\"form-control\" id=\"value\" name=\"value\">" . htmlspecialchars($setting['value'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</textarea>
						  <button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>";
			  break;
  
		  case 'hidden':
			  $output .= "Setting cannot be changed here";
			  break;
  
		  default:
			  $output .= "<div class=\"input-group\">
							  <input type=\"text\" class=\"form-control\" id=\"value\" name=\"value\" value=\"{$setting['value']}\">
							  <button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>
						  </div>";
			  break;
	  }
  
	  // Add the hidden UID field and close the form
	  $output .= "<input type=\"hidden\" id=\"uid\" name=\"uid\" value=\"{$setting['id']}\">
				  </form>
			  </div>
		  </div>
	  </div>";
  
	  // Output the result
	  echo $output;
  }
  ?>
</div>