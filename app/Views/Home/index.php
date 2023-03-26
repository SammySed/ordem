<?php echo $this->extend('Layout/principal'); ?>


<?php echo $this->section('titulo') ?> <?php echo $titulo; ?> <?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>

<!-- Aqui coloco os estilos da view-->

<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>

<!-- Aqui coloco o conteudo da view-->

<h1>Estendendo o layout principal através da view index de Home</h1>

<?php echo $this->endSection() ?>



<?php echo $this->section('scripts') ?>

<!-- Aqui coloco o scripts da view-->

<?php echo $this->endSection() ?>