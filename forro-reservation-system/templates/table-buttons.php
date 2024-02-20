<style>
    #forro-reservation-system {
        background-color: #000000;
        padding: 25px;
        position: relative;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(20%, 1fr)); /* Define a largura das colunas */
        grid-gap: 5px;
    }

    /* Adicione estas linhas */
    body {
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%;
    }

    /* Adicione estas linhas */
    .table-button {
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: bold;
}


    /* Adicione estas linhas */
    #reservation-form {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 20px;
        z-index: 100;
    }

    /* Adicione estas linhas */
    .close-button {
        position: absolute;
        top: 5px;
        right: 5px;
        cursor: pointer;
    }
</style>

<div id="forro-reservation-system">
    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservas_mesas'; // Substitua 'reservas_mesas' pelo nome correto da sua tabela

    // Gerar 129 botões quadrados
    for ($i = 1; $i <= 120; $i++) {
        // Verificar se a mesa está reservada consultando o banco de dados
        $is_reserved = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE mesa_id = %d", $i));

        // Definir a classe CSS e a cor de fundo com base no status de reserva
        $button_class = 'table-button';
        $button_bg_color = ($is_reserved) ? '#ff0000' : '#00ff00';

        // Saída do botão
        echo '<div class="' . $button_class . '" style="background-color: ' . $button_bg_color . ';" data-table-id="' . $i . '">';
        echo '<span style="color: white; font-weight: bold; pointer-events: none;">' . $i . '</span>';
        echo '</div>';
    }
    ?>
</div>

<!-- Adicione este formulário -->
<form id="reservation-form">
    <span class="close-button">×</span>
    <label for="name">Nome:</label><br>
    <input type="text" id="name" name="name" required><br>
    <label for="phone">Telefone:</label><br>
    <input type="tel" id="phone" name="phone" pattern="[0-9]{11}" required><br>
    <label for="responsavel">Responsável:</label><br>
    <input type="text" id="responsavel" name="responsavel" required><br>
    <button type="submit">Reservar</button>
</form>

<script>
    // Adicionar manipulador de eventos para o botão de reserva
    document.addEventListener('DOMContentLoaded', function() {
        const tableButtons = document.querySelectorAll('.table-button');

        tableButtons.forEach((button) => {
            button.addEventListener('click', function(event) {
                event.stopPropagation();
                const tableId = this.dataset.tableId;
                const form = document.getElementById('reservation-form');
                
                if (form && form.style.display === 'block') {
                    // Se o formulário já estiver visível, escondê-lo
                    form.style.display = 'none';
                } else {
                    // Ocultar outros formulários, se existirem
                    document.querySelectorAll('[id^="reservation-form"]').forEach((otherForm) => {
                        if (otherForm !== form) {
                            otherForm.style.display = 'none';
                        }
                    });

                    // Exibir o formulário atual
                    form.style.display = 'block';
                }
            });
        });

        // Adicionar evento de clique no documento para esconder formulários ao clicar em qualquer lugar fora deles
        document.addEventListener('click', function(event) {
            const forms = document.querySelectorAll('[id^="reservation-form"]');
            forms.forEach((form) => {
                if (form.style.display === 'block' && !event.target.closest('form')) {
                    form.style.display = 'none';
                }
            });
        });

        // Adicione este manipulador de eventos para o botão de fechar
        document.querySelector('.close-button').addEventListener('click', function() {
            document.getElementById('reservation-form').style.display = 'none';
        });
    });
</script>
