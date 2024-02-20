<?php
/**
 * Plugin Name: Forró Reservation System
 * Description: Plugin para reserva de Mesas.
 * Version: 3.0
 * Author: Caio Oliveira (Oliveira-PC)
 */

// Ensure WordPress is loaded before our code.
if (!defined('ABSPATH')) {
    exit;
}

// Define the plugin activation hook.
register_activation_hook(__FILE__, 'forro_reservation_system_activate');

function forro_reservation_system_activate()
{
    function criar_tabela() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_mesas';
    
        $sql = "CREATE TABLE $table_name (
            mesa_id INT(11) NOT NULL AUTO_INCREMENT,
            nome_cliente VARCHAR(255) NOT NULL,
            telefone_cliente VARCHAR(20) NOT NULL,
            responsavel VARCHAR(255) NOT NULL,
            PRIMARY KEY (mesa_id)
        ) $charset_collate;";
    
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    register_activation_hook(__FILE__, 'criar_tabela');
}

// Registrar o shortcode
add_shortcode('forro_reservation_system_tables', 'forro_reservation_system_display_tables');

// Função para exibir os botões de reserva de mesa
function forro_reservation_system_display_tables()
{
    ob_start();
?>
    <!-- Conteúdo de table-buttons.php -->
    <style>
        #forro-reservation-system {
            background-color: #000000;
            color: black !important;
            padding: 25px;
            position: relative;
            display: grid;
            grid-template-columns: repeat(6, minmax(50px, 1fr)); /* Define a largura das colunas */
            grid-gap: 8px;
        }
        
        body {
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%;
    }

    /* Adicione estas linhas */
    .table-button {
    color: black !important;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: bold;
    width: 50px;
    height: 50px;
    font-size: 20px;
    border-radius: 5px;
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
            echo '<span style="color: black; font-weight: bold; pointer-events: none;">' . $i . '</span>';
            echo '</div>';
        }

        // Recuperar dados do cookie, se existirem
        $table_id = isset($_COOKIE['table_id']) ? $_COOKIE['table_id'] : '';
        $name = isset($_COOKIE['name']) ? $_COOKIE['name'] : '';
        $phone = isset($_COOKIE['phone']) ? $_COOKIE['phone'] : '';
        $responsavel = isset($_COOKIE['responsavel']) ? $_COOKIE['responsavel'] : '';

        // Exibir o formulário com os dados preenchidos, se existirem
        echo '<input type="hidden" name="table_id" value="' . $table_id . '">';
        echo '<input type="hidden" name="name" value="' . $name . '">';
        echo '<input type="hidden" name="phone" value="' . $phone . '">';
        echo '<input type="hidden" name="responsavel" value="' . $responsavel . '">';
        ?>
    </div>
<?php
    return ob_get_clean();
}

// Adicionar uma ação para processar a submissão do formulário de reserva
add_action('admin_post_forro_reservation_system_reserve_table', 'forro_reservation_system_reserve_table');
add_action('admin_post_nopriv_forro_reservation_system_reserve_table', 'forro_reservation_system_reserve_table'); // Para usuários não logados

function forro_reservation_system_reserve_table()
{
    if (isset($_POST['table_id'], $_POST['name'], $_POST['phone'], $_POST['responsavel'])) {
        // Armazenar dados do formulário em cookies por 1 hora
        setcookie('table_id', $_POST['table_id'], time() + 3600);
        setcookie('name', $_POST['name'], time() + 3600);
        setcookie('phone', $_POST['phone'], time() + 3600);
        setcookie('responsavel', $_POST['responsavel'], time() + 3600);

        // Insira os dados da reserva no banco de dados
        // Código para inserção no banco de dados ...

        // Redirecionar de volta para a página atual após a submissão do formulário
        wp_redirect($_SERVER['HTTP_REFERER']);
        exit;
    }
}

// Adicionar script e localizar a URL do arquivo admin-ajax.php
function forro_reservation_system_enqueue_scripts()
{
    // Registrar e enfileirar o script de reserva
    wp_enqueue_script('reservation-script', plugin_dir_url(__FILE__) . 'reservation-script.js', array('jquery'), '1.0', true);
    // Localizar a URL do arquivo admin-ajax.php e passá-la para o script
    wp_localize_script('reservation-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

    // Depuração: exibir mensagem no console do navegador para verificar se o script está sendo carregado
    echo "<script>console.log('Script de reserva carregado.');</script>";
}
add_action('wp_enqueue_scripts', 'forro_reservation_system_enqueue_scripts');

