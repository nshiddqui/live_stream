<!-- Side bar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion <?= isset($toggled) && $toggled ? 'toggled' : '' ?>" id="accordionSidebar">

    <!-- Side bar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= $this->Url->build(['controller' => 'Dashboard', 'action' => 'index']) ?>">
        <div class="sidebar-brand-icon">
            <?= $this->Html->image('logo.png', ['height' => '100']) ?>
        </div>
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

    <li class="nav-item">

        <?=
        $this->Html->link('<i class="fas fa-fw fa-user-alt"></i>
            <span>Profile</span></a>', [
            'controller' => 'Profile',
            'action' => 'index'
                ], [
            'class' => 'nav-link',
            'escape' => false]
        )
        ?>
    </li>


    <li class="nav-item">

        <?=
        $this->Html->link('<i class="fas fa-fw fa-handshake"></i>
            <span>Schedule New Meeting</span></a>', [
            'controller' => 'Streams',
            'action' => 'add'
                ], [
            'class' => 'nav-link',
            'escape' => false]
        )
        ?>
    </li>

    <!-- Navigation Item - Upcoming Meetings -->
    <li class="nav-item">

        <?=
        $this->Html->link('<i class="fas fa-fw fa-handshake"></i>
            <span>Upcoming Meetings</span></a>', [
            'controller' => 'Streams',
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
        $this->Html->link('<i class="fas fa-fw fa-handshake"></i>
            <span>Previous Meetings</span></a>', [
            'controller' => 'Streams',
            'action' => 'previous'
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