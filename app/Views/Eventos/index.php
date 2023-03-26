<?php echo $this->extend('Layout/principal'); ?>


<?php echo $this->section('titulo') ?> <?php echo $titulo; ?> <?php echo $this->endSection() ?>


<?php echo $this->section('estilos') ?>

<link rel="stylesheet" href="<?php echo site_url('recursos/vendor/fullcalendar/fullcalendar.min.css'); ?>">
<link rel="stylesheet" href="<?php echo site_url('recursos/vendor/fullcalendar/toastr.min.css'); ?>">

<style>
    .fc-event,
    .fc-event-doc {
        background-color: #343a40 !important;
    }
</style>

<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>

<div id="calendario" class="container-fluid">

    <!-- rederizar o fullcalendar -->

</div>



<?php echo $this->endSection() ?>



<?php echo $this->section('scripts') ?>

<script src="<?php echo site_url('recursos/vendor/fullcalendar/fullcalendar.min.js'); ?>"></script>
<script src="<?php echo site_url('recursos/vendor/fullcalendar/toastr.min.js'); ?>"></script>
<script src="<?php echo site_url('recursos/vendor/fullcalendar/moment.min.js'); ?>"></script>

<script>
    $(document).ready(function() {
        var calendario = $("#calendario").fullCalendar({

            header: {
                left: 'prev, next today',
                center: 'title',
                right: 'month',

            },
            height: 580,
            editable: true,
            events: '<?php echo site_url('eventos/eventos'); ?>',
            displayEventTime: false,
            selectable: true,
            selectHelper: true,
            select: function(start, end, allDay) {

                var title = prompt('Informe o título do evento');

                if (title) {

                    var start = $.fullCalendar.formatDate(start, 'Y-MM-DD'); // format moment.js
                    var end = $.fullCalendar.formatDate(end, 'Y-MM-DD'); // format moment.js

                    $.ajax({

                        url: '<?php echo site_url('eventos/cadastrar'); ?>',
                        type: 'GET',
                        data: {
                            title: title,
                            start: start,
                            end: end,
                        },
                        success: function(response) {

                            exibeMensagem('Evento criado com sucesso!');

                            calendario.fullCalendar('renderEvent', {

                                id: response.id,
                                title: title,
                                start: start,
                                end: end,
                                allDay: allDay,

                            }, true);

                            calendario.fullCalendar('unselect');
                        }, // fin success

                    }); // fim ajax

                } // fim if title

            },

            // atualiza envento
            eventDrop: function(event, delta, revertFunc) {

                if (event.ordem_id || event.conta_id) {

                    alert('Não é possivel alterar um evento, pois o mesmo está atrelado a uma ordem de serviço');
                    revertFunc();

                } else {

                    //pode editar event

                    var start = $.fullCalendar.formatDate(event.start, 'Y-MM-DD'); // format moment.js
                    var end = $.fullCalendar.formatDate(event.end, 'Y-MM-DD'); // format moment.js

                    $.ajax({

                        url: '<?php echo site_url('eventos/atualizar/'); ?>' + event.id, // id do event a ser att
                        type: 'GET',
                        data: {
                            start: start,
                            end: end,
                        },
                        success: function(response) {

                            exibeMensagem('Evento atualizado com sucesso!');

                        }, // fin success

                    }); // fim atualizacao

                } //fim else

            }, // fim att event

            // exclusão de evento
            eventClick: function(event) {

                if (event.ordem_id || event.conta_id) {

                    alert(event.title);

                } else {

                    var exibeEvento = confirm(event.title + '\r\n\r' + 'Gostaria de excluir esse evento?');

                    if (exibeEvento) {

                        var confirmaExclusao = confirm("Tem certeza?");

                        if (confirmaExclusao) {
                            //excluir event

                            $.ajax({

                                url: '<?php echo site_url('eventos/excluir'); ?>',
                                type: 'GET',
                                data: {

                                    id: event.id,

                                },
                                success: function(response) {

                                    calendario.fullCalendar('removeEvents', event.id);
                                    exibeMensagem('Evento removido com sucesso!');



                                    calendario.fullCalendar('unselect');

                                }, // fin success

                            }); // fim ajax remover

                        } // fim if confirmaExclusao

                    } // fim exibe event

                } // fim else


            } // fim event click

        });
    });

    function exibeMensagem(mensagem) {

        toastr.success(mensagem, 'Evento');
    }
</script>


<?php echo $this->endSection() ?>