<div class="user-block">

    <div class="form-row mb-4">

        <div class="col-md-12">

            <?php if ($ordem->id === null) : ?>

                <div class="contributions">

                    Ordem aberta por: <?php usuario_logado()->nome; ?>

                </div>ordens

            <?php else : ?>

                <div class="contributions">

                    Ordem aberta por: <?php echo esc($ordem->usuario_abertura); ?>

                </div>

            <?php endif; ?>

        </div>

    </div>

    <?php if ($ordem->id === null) : ?>

        <div class="form-group">

            <label class="form-control-label">Escolha o cliente</label>

            <select name="cliente_id" class="selectize">

                <option value="">Digite o nome do cliente ou CNPJ</option>

            </select>

        </div>

        <?php else: ?>

            <div class="form-group">
                <label class="form-control-label">Cliente</label>
                <a tabindex="0" style="text-decoration: none;" role="button" data-toggle="popover" data-trigger="focus" 
                title="Importante" data-content="Não é permitido editar o cliente da ordem de serviço">&nbsp;&nbsp;<i class="fa fa-question-circle fa-lg text-info"></i></a>
            <input type="text" class="form-control" disabled readonly value="<?php echo esc($ordem->nome); ?>">           
        </div>

        <?php endif; ?>


    <div class="form-group">
        <label class="form-control-label">Observações da ordem de serviço</label>
        <textarea class="form-control" name="observacoes" placeholder="Descreva as observações"><?php echo esc($ordem->observacoes); ?></textarea>
    </div>

</div>