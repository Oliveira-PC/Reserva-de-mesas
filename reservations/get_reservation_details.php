<?php
// Conectar ao banco de dados
$servername = "localhost"; // substitua pelo seu host
$username = "nome_usuario"; // substitua pelo seu nome de usuário do banco de dados
$password = "senha"; // substitua pela sua senha do banco de dados
$dbname = "nome_banco_dados"; // substitua pelo nome do seu banco de dados

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se o parâmetro table_id foi enviado
if (isset($_GET['table_id'])) {
    $table_id = $_GET['table_id'];

    // Consulta para obter os detalhes da reserva com base no ID da mesa
    $sql = "SELECT nome_cliente AS nome, telefone_cliente AS telefone, responsavel, mesa_id FROM wp_reservas_mesas WHERE mesa_id = $table_id"; // Corrigido o nome da tabela

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        // Retornar os detalhes da reserva como JSON
        $row = $result->fetch_assoc();
        header('Content-Type: application/json');
        echo json_encode($row);
    } else {
        // Se não houver reservas para a mesa, retornar uma mensagem de erro
        echo json_encode(array('error' => 'Nenhuma reserva encontrada para esta mesa.'));
    }
} else {
    // Se o parâmetro table_id não foi enviado, retornar uma mensagem de erro
    echo json_encode(array('error' => 'O parâmetro table_id é obrigatório.'));
}

// Fechar conexão
$conn->close();
?>
