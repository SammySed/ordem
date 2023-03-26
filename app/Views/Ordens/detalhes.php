<?php echo $this->extend('Layout/principal'); ?>


<?php echo $this->section('titulo') ?> <?php echo $titulo; ?> <?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>

<!-- Aqui coloco os estilos da view-->

<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>


<div class="row">

    <div class="col-lg-12">

        <div class="user-block block">

            <div class="user-block text-center mt-5">

                <div class="user-title mb-4 ">

                    <h5 class="card-title mt-2"><?php echo esc($ordem->nome); ?></h5>
                    <span>Ordem: <?php echo esc($ordem->codigo); ?></span>

                </div>

                <p class="contributions mt-0"><?php echo $ordem->exibeSituacao(); ?></p>
                <p class="contributions mt-0">Aberta por: <?php echo esc($ordem->usuario_abertura); ?></p>

                <?php if ($ordem->situacao === 'encerrada') : ?>
                    <p class="contributions mt-0">Encerrada por: <?php echo esc($ordem->usuario_encerramento); ?></p>

                <?php endif ?>




                <p class="card-text">Criado <?php echo $ordem->criado_em->humanize(); ?></p>
                <p class="card-text">Atualizado <?php echo $ordem->atualizado_em->humanize(); ?></p>

                <hr class="border-secondary">

                <?php if ($ordem->ordens === null) : ?>

                    <div class="contributions py-3">

                        <p>Nenhum serviço foi adicionado à ordem</p>

                        <?php if ($ordem->situacao === 'aberta') : ?>

                            <a class="btn btn-outline-info btn-sm" href="<?php echo site_url("ordensordens/ordens/$ordem->codigo") ?>">Adicionar ordens</a>

                        <?php endif; ?>

                    </div>

                <?php else : ?>


                <?php endif; ?>

            </div>


            <!-- Example single danger button -->
            <div class="btn-group">
                <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">

                    <?php if ($ordem->situacao === 'aberta') : ?>

                        <a class="dropdown-item" href="<?php echo site_url("ordens/editar/$ordem->codigo"); ?>">Editar ordem</a>

                        <a class="dropdown-item" href="<?php echo site_url("ordens/encerrar/$ordem->codigo"); ?>">Encerrar a ordem</a>

                        <a class="dropdown-item" href="<?php echo site_url("ordensitens/itens/$ordem->codigo"); ?>">Gerenciar itens da ordem</a>

                    <?php endif; ?>

                    <a class="dropdown-item" href="<?php echo site_url("ordens/email/$ordem->codigo"); ?>">Enviar por e-mail</a>

                    <a class="dropdown-item" href="<?php echo site_url("ordens/gerarpdf/$ordem->codigo"); ?>">Gerar PDF</a>


                    <div class="dropdown-divider"></div>

                    <?php if ($ordem->deletado_em === null) : ?>

                        <a class="dropdown-item" href="<?php echo site_url("ordens/excluir/$ordem->codigo") ?>">Excluir ordem</a>

                    <?php else : ?>

                        <a class="dropdown-item" href="<?php echo site_url("ordens/desfazerexclusao/$ordem->codigo") ?>">Restaurar ordem</a>

                    <?php endif; ?>

                </div>

            </div>

            <a href="<?php echo site_url("ordens"); ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>

        </div> <!-- ./block -->



    </div>

</div>

<?php echo $this->endSection() ?>



<?php echo $this->section('scripts') ?>

<!-- Aqui coloco o scripts da view-->

<?php echo $this->endSection() ?>