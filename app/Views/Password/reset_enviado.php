<?php echo $this->extend('Layout/autenticacao/principal_autenticacao'); ?>


<?php echo $this->section('titulo') ?> <?php echo $titulo; ?> <?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>

<!-- Aqui coloco os estilos da view-->

<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>

<!-- Aqui coloco o conteudo da view-->

<div class="row">
    <!-- Logo & Information Panel-->
    <div class="col-lg-8 mx-auto">
        <div class="info d-flex align-items-center">
            <div class="content">
                <div class="logo">
                    <h1>Etarc Contabilidade</h1>
                </div>
                <p><?php echo $titulo; ?></p>
            </div>
        </div>
    </div>

    <!-- Form Panel    -->
    <div class="col-lg-6 bg-white d-none">
        <div class="form d-flex align-items-center">
            <div class="content">


            </div>
        </div>
    </div>
</div>

<?php echo $this->endSection() ?>




<?php echo $this->section('scripts') ?>


<?php echo $this->endSection() ?>