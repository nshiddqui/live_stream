<!-- Side bar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Side bar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= $this->Url->build(['controller' => 'Dashboard', 'action' => 'index']) ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <?= $this->Html->image('logo.png', ['height' => '50']) ?>
        </div>
        <div class="sidebar-brand-text mx-3"><?= $name ?></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Navigation Item - Dashboard -->
    <li class="nav-item">

        <?=
        $this->Html->link('<i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>', [
            'controller' => 'Dashboard',
            'action' => 'index'
                ], [
            'class' => 'nav-link',
            'escape' => false]
        )
        ?>
    </li>
    <!-- Navigation Item - Friend List -->
    <li class="nav-item">

        <?=
        $this->Html->link('<i class="fas fa-fw fa-users"></i>
            <span>Friend List</span></a>', [
            'controller' => 'UserFriends',
            'action' => 'index'
                ], [
            'class' => 'nav-link',
            'escape' => false]
        )
        ?>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">


    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->