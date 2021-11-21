const SolCheckInAdmin = (() => {
  const UI = {
    formShowEvents: document.getElementById('show-events-form'),
    tableWrap: document.querySelector('.ci-table-wrap'),
  };

  const fetchEventData = async (date, intervalId) => {
    const formData = new FormData();
    formData.append('ci_date', date);
    formData.append('interval_id', intervalId);

    const res = await fetch('../check-in-api/events/', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: formData,
    });

    const data = await res.json();

    return data;
  };

  const createTable = (events) => {
    const table = document.createElement('table');
    table.className = 'ci-table';
    table.id = 'ci-table';

    table.innerHTML = `
    <thead>
      <th>№</th>
      <th>ФИО</th>
      <th>Дата рождения</th>
      <th>Телефон</th>
      <th>Отделение</th>
      <th>Интервал</th>
      <th>Район</th>
      <th>Удалить</th>
    </thead>`;

    const tbody = document.createElement('tbody');
    table.appendChild(tbody);

    events.forEach((event, index) => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${index + 1}</td>
        <td>${event.fio}</td>
        <td>${new Date(event.dob).toLocaleDateString('ru-RU')}</td>
        <td>${event.phone}</td>
        <td>${event.unit_name}</td>
        <td>${event.start_time.substring(0, 5)} - ${event.end_time.substring(0, 5)}</td>
        <td>${event.district_name}</td>
        <td><button class="ci-table__btn js-delete-event" data-id=${
          event.event_id
        }>&#128473;</button></td>
      `;
      tbody.appendChild(row);
    });

    return table;
  };

  const scrollToTable = () => {
    const { top } = UI.tableWrap.getBoundingClientRect();
    window.scrollTo({ top, behavior: 'smooth' });
  };

  const showEvents = async (e) => {
    e.preventDefault();

    const date = UI.formShowEvents.querySelector('.js-search-date-input').value;
    const intervalId = UI.formShowEvents.querySelector('.js-filter-interval-input').value || '0';

    const eventData = await fetchEventData(date, intervalId);

    const tableHTML = createTable(eventData);

    UI.tableWrap.innerHTML = '';
    UI.tableWrap.appendChild(tableHTML);

    scrollToTable();
  };

  const deleteEvent = async (e) => {
    const target = e.target;
    if (!target.classList.contains('js-delete-event')) return;

    const check = prompt('Для подтверждения введите "удалить"');
    if ('удалить' !== check) return;

    const body = new FormData();
    body.append('id', target.dataset.id);

    const res = await fetch('../check-in-api/delete-event/', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body,
    });

    const data = await res.json();

    if ('success' === data.status) {
      target.closest('tr').remove();
    } else {
      console.log(data.message);
    }
  };

  return {
    init: () => {
      UI.formShowEvents.addEventListener('submit', showEvents);
      UI.tableWrap.addEventListener('click', deleteEvent);
    },
  };
})();

SolCheckInAdmin.init();
