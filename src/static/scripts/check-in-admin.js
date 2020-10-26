const SolCheckInAdmin = (() => {
  const UIformShowEvents = document.getElementById('show-events-form');
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
      <th>⨯</th>
    </thead>`;

    events.forEach((event, index) => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${index + 1}</td>
        <td>${event.fio}</td>
        <td>${(new Date(event.dob)).toLocaleDateString('ru-RU')}</td>
        <td>${event.phone}</td>
        <td>${event.start_time.substring(0, 5)} - ${event.end_time.substring(0, 5)}</td>
        <td>
          <!--
          <button class="ci-table__btn js-edit-event" data-id=${event.id}>🖉</button>
          -->
          <button class="ci-table__btn js-delete-event" data-id=${event.id}>⨯</button>
        </td>
      `;
      table.appendChild(row);
    });

    return table;
  }

  const showEvents = async (e) => {
    e.preventDefault();

    const date = UIformShowEvents.querySelector('input').value;
    const eventData = await fetchEventData(date);
    const tableHTML = createTable(eventData);
    const tableWrap = document.querySelector('.ci-table-wrap');

    tableWrap.innerHTML = '';
    tableWrap.appendChild(tableHTML);
  }

  return {
    init: () => {
      UIformShowEvents.addEventListener('submit', showEvents);
      // UIformCloseEvent.addEventListener('submit', closeDate);
    }
  }
})();

SolCheckInAdmin.init();
