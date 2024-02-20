<?php
// Verificar se o formulário foi enviado
echo "Iniciando processamento..."; // Mensagem de depuração
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['table_id'], $_POST['name'], $_POST['phone'], $_POST['responsavel'])) {
    // Conectar ao banco de dados (substitua os valores pelas suas configurações)
    $servername = "localhost";
    $username = "nome_usuario";
    $password = "senha";
    $dbname = "nome_banco_dados";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar a conexão
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    } else {
        echo "Conexão bem-sucedida. "; // Mensagem de depuração
    }

    // Verificar se a tabela existe
    $table_name = "wp_reservas_mesas"; // Nome da tabela corrigido
    $sql_check_table = "SHOW TABLES LIKE '$table_name'";
    $result = $conn->query($sql_check_table);
    if ($result->num_rows == 0) {
        die("Erro: A tabela $table_name não foi encontrada no banco de dados.");
    } else {
        echo "Tabela $table_name encontrada. "; // Mensagem de depuração
    }

    // Preparar e executar a consulta SQL para inserir os dados da reserva
$stmt = $conn->prepare("INSERT INTO wp_reservas_mesas (mesa_id, nome_cliente, telefone_cliente, responsavel) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $_POST['table_id'], $_POST['name'], $_POST['phone'], $_POST['responsavel']);


    if ($stmt->execute()) {
        // Reserva bem-sucedida
        echo json_encode(array("status" => "success", "message" => "Reserva feita com sucesso."));
    } else {
        // Erro ao fazer a reserva
        echo json_encode(array("status" => "error", "message" => "Erro ao fazer a reserva."));
    }

    // Fechar a conexão com o banco de dados
    $stmt->close();
    $conn->close();
} else {
    // Se o formulário não foi enviado corretamente, retornar um erro
    echo json_encode(array("status" => "error", "message" => "Dados do formulário inválidos"));
}
?>
