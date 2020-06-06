<!-- Top bar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <div class="users">
        <i class="fa fa-users"></i>
        <?= $liveUser ?>
    </div>
    <!-- Side bar Toggle (Top bar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    <!-- Top bar Navigation bar -->
    <ul class="navbar-nav ml-auto">
        <!--<div class="topbar-divider d-none d-sm-block"></div>-->
        <!-- Navigation Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="/profile" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <!--<span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $Auth->user('username') ?></span>-->
                <!--<i class="fa fa-user fa-lg"></i>-->
                <?= $this->Html->image('login-img.png',['height'=>'70']) ?>
            </a>
            <!-- Drop down - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" style="z-index: 99999999;" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="/profile">
                    <?= $Auth->user('name') ?>
                    <br>
                    <?= $Auth->user('email') ?>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
<!-- End of Top bar -->