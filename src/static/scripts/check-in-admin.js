const SolCheckInAdmin = (() => {
  const UI = {
    formShowEvents: document.getElementById('show-events-form'),
    tableWrap: document.querySelector('.ci-table-wrap')
  }
  // const UIformCloseEvent = document.getElementById('close-event-form');

  const fetchEventData = async (date) => {
    const formData = new FormData();
    formData.append('ci_date', date);

    const res = await fetch('../check-in-api/events/', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: formData,
    });

    const data = await res.json();

    return data;
  }

  const createTable = (events) => {
    const table = document.createElement('table');
    table.className = 'ci-table';
    table.innerHTML = `<thead>
      <th>№</th>
      <th>ФИО</th>
      <th>Дата рождения</th>
      <th>Телефон</th>
      <th>Интервал</th>
      <th>Удалить</th>
    </thead>`;

    events.forEach((event, index) => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${index + 1}</td>
        <td>${event.fio}</td>
        <td>${(new Date(event.dob)).toLocaleDateString('ru-RU')}</td>
        <td>${event.phone}</td>
        <td>${event.start_time.substring(0, 5)} - ${event.end_time.substring(0, 5)}</td>
        <td><button class="ci-table__btn js-delete-event" data-id=${event.event_id}>&#128473;</button></td>
      `;
      table.appendChild(row);
    });

    return table;
  }

  const showEvents = async (e) => {
    e.preventDefault();

    const date = UI.formShowEvents.querySelector('input').value;
    const eventData = await fetchEventData(date);
    const tableHTML = createTable(eventData);

    UI.tableWrap.innerHTML = '';
    UI.tableWrap.appendChild(tableHTML);
  }

  const deleteEvent = async (e) => {
    const target = e.target;
    if (!target.classList.contains('js-delete-event')) return;
    // Prompt for confirmation
    const check = prompt('Для подтверждения введите "удалить"');
    if ('удалить' !== check) return;
    // Append id to request body
    const body = new FormData();
    body.append('id', target.dataset.id);
    // Send request
    const res = await fetch('../check-in-api/delete-event/', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body
    });

    const data = await res.json();

    if ('success' === data.status) {
      // Update table
      target.closest('tr').remove();
    } else {
      console.log(data.message);
    }
  }

  return {
    init: () => {
      UI.formShowEvents.addEventListener('submit', showEvents);
      UI.tableWrap.addEventListener('click', deleteEvent)
      // UIformCloseEvent.addEventListener('submit', closeDate);
    }
  }
})();

SolCheckInAdmin.init();
