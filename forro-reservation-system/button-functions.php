<?php
// Função para verificar se a mesa está reservada
function check_reservation_status($table_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservas_mesas';
    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE mesa_id = %d", $table_id);
    $result = $wpdb->get_results($query);
    return !empty($result); // Retorna true se houver reservas para a mesa
}

// Função para exibir o formulário de reserva
function display_reservation_form($table_id) {
    ?>
    <!-- Formulário de reserva -->
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <input type="hidden" name="action" value="submit_reservation">
        <input type="hidden" name="table_id" value="<?php echo $table_id; ?>">
        <!-- Campos para nome e telefone -->
        <label for="name_<?php echo $table_id; ?>">Nome:</label>
        <input type="text" id="name_<?php echo $table_id; ?>" name="name" required>
        <label for="phone_<?php echo $table_id; ?>">Telefone:</label>
        <input type="tel" id="phone_<?php echo $table_id; ?>" name="phone" pattern="[0-9]{11}" required>
        <button type="submit">Reservar</button>
    </form>
    <?php
}

// Função para exibir os detalhes da reserva
function display_reservation_details($table_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservas_mesas';
    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE mesa_id = %d", $table_id);
    $result = $wpdb->get_results($query);
    if ($result) {
        foreach ($result as $reservation) {
            // Exiba os detalhes da reserva, como nome e telefone do cliente
            echo 'Reservado por: ' . $reservation->nome_cliente . ' (' . $reservation->telefone_cliente . ')';
        }
    }
}

// Função para processar a submissão do formulário de reserva
function submit_reservation() {
    if (isset($_POST['table_id'], $_POST['name'], $_POST['phone'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservas_mesas';
        $table_id = intval($_POST['table_id']);
        $name = sanitize_text_field($_POST['name']);
        $phone = sanitize_text_field($_POST['phone']);
        // Insira os dados da reserva no banco de dados
        $wpdb->insert($table_name, array(
            'mesa_id' => $table_id,
            'nome_cliente' => $name,
            'telefone_cliente' => $phone
        ));
        
        // Redireciona para a página atual
        wp_redirect(autoRefreshParams.current_url);
        exit;
    }
}

// Registre a ação para processar a submissão do formulário de reserva
add_action('admin_post_submit_reservation', 'submit_reservation');
add_action('admin_post_nopriv_submit_reservation', 'submit_reservation'); // Para usuários não logados
