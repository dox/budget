<h2>Reports</h2>


<div class="row">
  <div class="column">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Monthly Spend</h5>
        <!--<h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>-->
        <p class="card-text">Spend per month, displayed in a graph.</p>
        <a href="index.php?n=reports_monthly" class="card-link">Run Report</a>
        <a href="#" class="card-link"><strike>Edit</strike></a>
      </div>
    </div>
  </div>
  <div class="column">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Most Expensive Orders</h5>
        <!--<h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>-->
        <p class="card-text">All-time most expensive orders.</p>
        <a href="index.php?n=reports_mostexpensive" class="card-link">Run Report</a>
        <a href="#" class="card-link"><strike>Edit</strike></a>
      </div>
    </div>
  </div>
  <div class="column">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Suppliers</h5>
        <!--<h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>-->
        <p class="card-text">All-suppliers and how much has been spent with them.</p>
        <a href="index.php?n=reports_suppliers_all" class="card-link">Run Report</a>
        <a href="#" class="card-link"><strike>Edit</strike></a>
      </div>
    </div>
  </div>
  <div class="column">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Spend By User</h5>
        <!--<h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>-->
        <p class="card-text">All-users and how much has been spent by them.</p>
        <a href="index.php?n=reports_users_all" class="card-link">Run Report</a>
        <a href="#" class="card-link"><strike>Edit</strike></a>
      </div>
    </div>
  </div>
  <div class="column">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Test</h5>
        <!--<h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>-->
        <p class="card-text">Test</p>
        <a href="index.php?n=reports_all" class="card-link">Run Report</a>
        <a href="#" class="card-link"><strike>Edit</strike></a>
      </div>
    </div>
  </div>
</div>


<style>
/* Float four columns side by side */
.column {
  float: left;
  width: 25%;
  padding: 0 10px;
}

/* Remove extra left and right margins, due to padding in columns */
.row {margin: 0 -5px;}

/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}


/* Responsive columns - one column layout (vertical) on small screens */
@media screen and (max-width: 600px) {
  .column {
    width: 100%;
    display: block;
    margin-bottom: 20px;
  }
}
</style>
