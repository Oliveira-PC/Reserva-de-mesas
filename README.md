# Reserva-de-mesas
plugin wordpress para reserva de mesas

Para o Funcionamento correto do plugin é necessario colocar a pasta "reservations" dentro da pasta do seu tema em "wp-content".

O plugin foi configurado para trabalhar com o "generatepress", então se for utilizar com outro altere linhas 28 e 121 do "reservation-script.js"

É necessario tambem preencher no codigo da pasta "reservations" os dados de acesso ao seu banco de dados:
	$servername = "localhost"; // substitua pelo seu host
	$username = "nome_usuario"; // substitua pelo seu nome de usuário do banco de dados
	$password = "senha"; // substitua pela sua senha do banco de dados
	$dbname = "nome_banco_dados"; // substitua pelo nome do seu banco de dados

Vale lembrar que o banco de dados está configurado no padrão wordpress com prefix (wp_)
