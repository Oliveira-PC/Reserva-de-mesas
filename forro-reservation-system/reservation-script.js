document.addEventListener('DOMContentLoaded', function() {
    console.log('Script carregado.');

    // Definir variável para armazenar o ID do intervalo de atualização da página
    let refreshIntervalId;

    const tableButtons = document.querySelectorAll('.table-button');

    tableButtons.forEach((button) => {
        button.addEventListener('click', function(event) {
            event.stopPropagation();
            console.log('Clique no botão de mesa:', this.dataset.tableId);

            // Parar o intervalo de atualização da página
            clearInterval(refreshIntervalId);

            const tableId = this.dataset.tableId;
            const form = document.getElementById(`reservation-form-${tableId}`);

            if (form && form.style.display === 'block') {
                console.log('Formulário já visível. Escondendo...');
                form.style.display = 'none';
            } else {
                const isReserved = this.style.backgroundColor === 'rgb(255, 0, 0)'; // Vermelho indica reservado
                console.log('Mesa reservada:', isReserved);

                if (isReserved) {
                    fetch(`/wp-content/themes/generatepress/reservations/get_reservation_details.php?table_id=${tableId}`)
                        .then(response => {
                            if (response.ok) {
                                return response.json();
                            } else {
                                throw new Error('Erro ao obter detalhes da reserva');
                            }
                        })
                        .then(data => {
                            if (data && data.nome && data.telefone && data.responsavel) {
                                const reservationInfo = document.createElement('div');
                                reservationInfo.className = 'reservation-info';
                                reservationInfo.innerHTML = `
                                    <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; padding: 20px; z-index: 100; color: black;">
                                        <p><b>Detalhes da Reserva:</b></p>
                                        <p><b>Mesa ID:</b> ${tableId}</p>
                                        <p><b>Nome:</b> ${data.nome}</p>
                                        <p><b>Telefone:</b> ${data.telefone}</p>
                                        <p><b>Responsável:</b> ${data.responsavel}</p>
                                        <button class="close-reservation-info" style="margin-top: 10px; padding: 5px 10px; background-color: #ff0000; color: white; border: none; cursor: pointer;">Fechar</button>
                                    </div>
                                `;

                                document.body.appendChild(reservationInfo);
                            } else {
                                throw new Error('Dados da reserva incompletos ou incorretos');
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao obter detalhes da reserva:', error.message);
                            alert('Erro ao obter detalhes da reserva. Por favor, tente novamente.');
                        });
                } else {
                    const formHtml = `
                        <form id="reservation-form-${tableId}" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; padding: 20px; z-index: 100;">
                            <label for="name-${tableId}" style="display: block; color: black; margin-bottom: 20px;"><b>Reserva para a Mesa ${tableId} </b></label>
                            <label for="name-${tableId}" style="display: block; color: black; margin-bottom: 10px;">Nome:</label>
                            <input type="text" id="name-${tableId}" name="name" style="width: calc(100% - 20px); padding: 5px; margin-bottom: 10px;" required>
                            <label for="phone-${tableId}" style="display: block; color: black; margin-bottom: 10px;">Telefone:</label>
                            <input type="tel" id="phone-${tableId}" name="phone" style="width: calc(100% - 20px); padding: 5px; margin-bottom: 10px;" pattern="[0-9]{11}" required>
                            <label for="responsavel-${tableId}" style="display: block; color: black; margin-bottom: 10px;">Responsável:</label>
                            <input type="text" id="responsavel-${tableId}" name="responsavel" style="width: calc(100% - 20px); padding: 5px; margin-bottom: 10px;" required>
                            <button type="button" class="reserve-button" data-table-id="${tableId}" style="width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; cursor: pointer;">Reservar</button>
                            <button type="button" class="close-button" style="position: absolute; top: 5px; right: 5px; padding: 5px; background-color: #ff0000; color: white; border: none; cursor: pointer;">X</button>
                        </form>
                    `;

                    document.querySelectorAll('[id^="reservation-form-"]').forEach((otherForm) => {
                        if (otherForm !== form) {
                            console.log('Escondendo outros formulários...');
                            otherForm.style.display = 'none';
                        }
                    });

                    console.log('Exibindo formulário de reserva...');
                    document.body.appendChild(createForm(formHtml));
                }
            }
        });
    });

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('close-button')) {
            const form = event.target.closest('form');
            if (form) {
                console.log('Clique no botão de fechar. Escondendo formulário...');
                form.style.display = 'none';
            }
        } else if (event.target.classList.contains('close-reservation-info')) {
            const reservationInfo = event.target.closest('.reservation-info');
            if (reservationInfo) {
                console.log('Clique no botão de fechar. Removendo informações da reserva...');
                reservationInfo.remove();
            }
        }
    });

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('reserve-button')) {
            const tableId = event.target.dataset.tableId;
            console.log('Clique no botão de reservar para a mesa:', tableId);

            const form = document.getElementById(`reservation-form-${tableId}`);
            const name = form.querySelector(`#name-${tableId}`).value;
            const phone = form.querySelector(`#phone-${tableId}`).value;
            const responsavel = form.querySelector(`#responsavel-${tableId}`).value;

            const formData = new FormData();
            formData.append('table_id', tableId);
            formData.append('name', name);
            formData.append('phone', phone);
            formData.append('responsavel', responsavel);

            fetch('/wp-content/themes/generatepress/reservations/process_reservation.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log(response);
                if (response.ok) {
                    console.log('Reserva feita com sucesso!');
                    location.reload();
                } else {
                    console.error('Erro ao fazer reserva:', response.statusText);
                    alert('Erro ao fazer reserva. Por favor, tente novamente.');
                }
            })
            .catch(error => {
                console.error('Erro ao fazer reserva:', error.message);
                alert('Erro ao fazer reserva. Por favor, tente novamente.');
            });
        }
    });

    // Definir intervalo de atualização da página a cada 2 segundos
    refreshIntervalId = setInterval(function() {
        location.reload();
    }, 5000);

    function createForm(htmlString) {
        const div = document.createElement('div');
        div.innerHTML = htmlString.trim();
        return div.firstChild;
    }
});
