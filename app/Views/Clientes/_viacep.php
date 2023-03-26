$('[name=cep]').on('keyup', function() {

var cep = $(this).val();

if (cep.length === 9) {

    $.ajax({

        type: 'GET',
        url: '<?php echo site_url('clientes/consultacep'); ?>',
        data: {
            cep: cep
        },
        dataType: 'json',
        beforeSend: function() {

            $("#form").LoadingOverlay("show");

            $("#cep").html('');
        },
        success: function(response) {

            $("#form").LoadingOverlay("hide", true);

            if (!response.erro) {

                if (!response.endereco) {

                    $('[name=endereco]').prop('readonly', false);

                    $('[name=endereco]').focus();

                }

                if (!response.bairro) {

                    $('[name=bairro]').prop('readonly', false);
                }

                // Preencher os inputs com os valores do response
                $('[name=endereco]').val(response.endereco);
                $('[name=bairro]').val(response.bairro);
                $('[name=cidade]').val(response.cidade);
                $('[name=estado]').val(response.estado);
            }

            if (response.erro) {

                // exitem erros de validação

                $("#cep").html(response.erro);
            }
        },
        erro: function() {

            $("#form").Loadingoverlay("hide", true);

            alert(
                'Não foi possível processar a solicitação. Por favor entre em contato com o suporte'
            );
        }

    });

}

});