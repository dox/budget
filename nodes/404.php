<h2 class="text-center"><svg width="200" height="200" class="text-center">
  <use xlink:href="img/icons.svg#patch-question"/>
</svg></h2>
<h2 class="text-center"> 404</h2>
<h3 class="text-center">This page hasn't been created yet!</h3>
<br />

<?php
$logMessage = "404: Page not found '" . $_SERVER[REQUEST_URI] . "'";
$log->insert("page", $logMessage);
?>
