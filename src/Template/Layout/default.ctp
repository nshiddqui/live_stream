<?php
$description = 'Stream';
?>
<!DOCTYPE html>
<html>
    <head>
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>
            <?= $description ?>:
            <?= $this->fetch('title') ?>
        </title>
        <?= $this->Html->meta('icon') ?>
        <!-- Custom fonts for this template-->
        <?= $this->Html->component('fontawesome-free/css/all.min') ?>
        <?= $this->Html->css('https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i') ?>
        <!-- Load Data Table Component -->
        <?= $this->Html->component('datatables/datatables.min', 'css', ['block' => 'css']) ?>
        <!-- Custom styles for this template-->
        <?= $this->Html->css('stream.min') ?>
        <!-- Bootstrap core Java Script-->
        <?= $this->Html->component('jquery/jquery.min', 'script') ?>
        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
        <?= $this->fetch('script') ?>
        <script>
            /* Setup Gloabal Variables */
            var baseUrl = '<?= $this->Url->build('/', true) ?>';
            var limitUser = <?= isset($limitUser) ? $limitUser : 10 ?>
        </script>
    </head>
    <body id="page-top">
        <!-- Page Wrapper -->
        <div id="wrapper">
            <?= $this->element('sidebar', ['name' => $description]) ?>
            <!-- Content Wrapper -->
            <div id="content-wrapper" class="d-flex flex-column">
                <!-- Main Content -->
                <div id="content">
                    <?= $this->element('navigation') ?>
                    <!-- Begin Page Content -->
                    <div class="container-fluid">
                        <?= $this->Flash->render() ?>
                        <?= $this->fetch('content') ?>
                    </div>
                    <!-- /.container-fluid -->
                </div>
                <!-- End of Main Content -->
                <!-- Footer -->
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-left my-auto">
                            <span>Copyright &copy; <?= $description ?>  2020</span>
                        </div>
                    </div>
                </footer>
                <!-- End of Footer -->
            </div>
            <!-- End of Content Wrapper -->
        </div>
        <!-- End of Page Wrapper -->
        <?= $this->element('footer') ?>
        <!-- Add Moment for this template-->
        <?= $this->Html->script('moment.min') ?>
        <?= $this->Html->component('bootstrap/js/bootstrap.bundle.min', 'script') ?>
        <!-- Core Component Java Script-->
        <?= $this->Html->component('jquery-easing/jquery.easing.min', 'script') ?>
        <!-- Load Data Table Component -->
        <?= $this->Html->component('datatables/datatables.min', 'script') ?>
        <!-- Custom scripts for all pages-->
        <?= $this->Html->script('stream.min') ?>
        <!-- Load Data Table Script -->
        <?= $this->DataTables->setJs() ?>
        <!-- Bottom Script -->
        <?= $this->fetch('scriptBottom') ?>
    </body>
</html>