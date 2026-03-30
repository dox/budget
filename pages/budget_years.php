<?php
$current     = BudgetYear::current();
$lastYear    = BudgetYear::yearsAgo(1);
$nextYear    = BudgetYear::yearsFromNow(1);

$explicit    = new BudgetYear(2021);


echo "<p>Current Year</p>";
printArray($current);


echo "<p>Previous Year</p>";
printArray($lastYear);

echo "<p>Next Year</p>";
printArray($nextYear);
