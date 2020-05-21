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
        <!-- Custom styles for this template-->
        <?= $this->Html->css('stream.min') ?>

    </head>

    <body class="bg-gradient-primary">

        <div class="container">

            <!-- Outer Row -->
            <div class="row justify-content-center">

                <div class="col-xl-10 col-lg-12 col-md-9">

                    <div class="card o-hidden border-0 shadow-lg my-5">
                        <div class="card-body p-0">
                            <!-- Nested Row within Card Body -->
                            <?= $this->fetch('content') ?>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <!-- Bootstrap core Java Script-->
        <?= $this->Html->component('jquery/jquery.min', 'script') ?>
        <!-- Add Moment for this template-->
        <?= $this->Html->script('moment.min') ?>
        <?= $this->Html->component('bootstrap/js/bootstrap.bundle.min', 'script') ?>
        <!-- Core Component Java Script-->
        <?= $this->Html->component('jquery-easing/jquery.easing.min', 'script') ?>

        <!-- Custom scripts for all pages-->
        <?= $this->Html->script('stream.min') ?>

    </body>

</html>

