jQuery(document).ready(function($) {
    // Adiciona um evento de clique ao botão de reserva
    $('#reserve-button').on('click', function(e) {
        e.preventDefault(); // Previne o comportamento padrão do link

        // Executa a reserva ou qualquer outra ação necessária

        // Após a reserva, recarrega a página
        window.location.href = autoRefreshParams.current_url;
    });
});
