<header>
  <nav class="navbar navbar-expand-md navbar-light bg-light">
    <div class="container">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse col" id="navbarCollapse">
        <ul class="navbar-nav me-auto mb-2 mb-md-0" >
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="index.php?n=dashboard">
              <svg width="16" height="16" class="colour-red">
                <use xlink:href="img/icons.svg#house-door-fill"/>
              </svg> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.php?n=costcentres_all">
              <svg width="16" height="16" class="colour-green">
                <use xlink:href="img/icons.svg#archive-fill"/>
              </svg> Cost Centres
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.php?n=orders_all">
              <svg width="16" height="16" class="colour-blue">
                <use xlink:href="img/icons.svg#journal-text"/>
              </svg> Orders
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.php?n=reports_all">
              <svg width="16" height="16" class="colour-grey">
                <use xlink:href="img/icons.svg#journal-text"/>
              </svg> Reports
            </a>
          </li>
        </ul>

        <form class="d-flex" method="POST" action="index.php?n=search">
          <input class="form-control form-control-sm me-2" type="search" name="search" placeholder="Search" aria-label="Search">
        </form>

        <div class="btn-group">
          <a href="index.php?n=orders_create" class="btn btn-sm btn-primary">Create Order</a>
          <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="visually-hidden">Toggle Dropdown</span>
          </button>
          <ul class="dropdown-menu">
            <li>
              <a class="dropdown-item" href="index.php?n=search">
                <svg width="16" height="16">
                  <use xlink:href="img/icons.svg#search"/>
                </svg> Search
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item" href="index.php?n=costcentres_create">
                <svg width="16" height="16">
                  <use xlink:href="img/icons.svg#archive-fill"/>
                </svg> Create Cost Centre
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="./export.php" target="_blank">
                <svg width="16" height="16">
                  <use xlink:href="img/icons.svg#box-arrow-down"/>
                </svg> Create Export
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item" href="index.php?n=user_settings">
                <svg width="16" height="16">
                  <use xlink:href="img/icons.svg#person-circle"/>
                </svg> User Settings
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="index.php?n=logs_all">
                <svg width="16" height="16">
                  <use xlink:href="img/icons.svg#list-ol"/>
                </svg> Logs
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item" href="index.php?n=index&logout">
                <svg width="16" height="16">
                  <use xlink:href="img/icons.svg#door-closed-fill"/>
                </svg> Sign Out
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
</header>
<?php include('nav_message.php'); ?>
