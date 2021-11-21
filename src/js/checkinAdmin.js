const SolCheckInAdmin = (() => {
  const UI = {
    applicantsForm: document.getElementById('show-applicants-form'),
    eventsForm: document.getElementById('show-events-form'),
    tableWrap: document.querySelector('.ci-table-wrap'),
  };

  const fetchEvents = async (date, intervalId) => {
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

  const fetchApplicants = async () => {
    const res = await fetch('../check-in-api/applicants', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });

    const data = await res.json();

    return data;
  };

  const createEventTable = (events) => {
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
      </thead>
    `;

    const tbody = document.createElement('tbody');
    table.appendChild(tbody);

    events.forEach((event, index) => {
      const {
        fio,
        dob,
        phone,
        unit_name: unit,
        start_time: startTime,
        end_time: endTime,
        district_name: district,
        event_id: eventId,
      } = event;
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${index + 1}</td>
        <td>${fio}</td>
        <td>${new Date(dob).toLocaleDateString('ru-RU')}</td>
        <td>${phone}</td>
        <td>${unit}</td>
        <td>${startTime.substring(0, 5)} - ${endTime.substring(0, 5)}</td>
        <td>${district}</td>
        <td><button class="ci-table__btn js-delete-event" data-id=${eventId}>&#128473;</button></td>
      `;
      tbody.appendChild(row);
    });

    return table;
  };

  const createApplicantsTable = (applicants) => {
    const table = document.createElement('table');
    table.className = 'ci-table';
    table.id = 'ci-table';

    table.innerHTML = `
      <thead>
        <th>№</th>
        <th>ФИО</th>
        <th>Телефон</th>
      </thead>
    `;

    const tbody = document.createElement('tbody');
    table.appendChild(tbody);

    applicants.forEach(({ fio, phone }, index) => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${index + 1}</td>
        <td>${fio}</td>
        <td>${phone}</td>
      `;
      tbody.appendChild(row);
    });

    return table;
  };

  const populateTable = (tableHTML) => {
    UI.tableWrap.innerHTML = '';
    UI.tableWrap.appendChild(tableHTML);

    scrollToTable();

    const { top } = UI.tableWrap.getBoundingClientRect();
    window.scrollTo({ top, behavior: 'smooth' });
  };

  const showEvents = async (e) => {
    e.preventDefault();

    const date = UI.eventsForm.querySelector('.js-search-date-input').value;
    const intervalId = UI.eventsForm.querySelector('.js-filter-interval-input').value || '0';

    const events = await fetchEvents(date, intervalId);
    const tableHTML = createEventTable(events);

    populateTable(tableHTML);
  };

  const showApplicants = async (e) => {
    e.preventDefault();

    const applicants = await fetchApplicants();
    const tableHTML = createApplicantsTable(applicants);

    populateTable(tableHTML);
  };

  const deleteEvent = async (e) => {
    const target = e.target;
    if (!target.classList.contains('js-delete-event')) return;

    const check = prompt('Для подтверждения введите "удалить"');
    if (check !== 'удалить') return;

    const body = new FormData();
    body.append('id', target.dataset.id);

    const res = await fetch('../check-in-api/delete-event/', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body,
    });

    const { status, message } = await res.json();

    if (status === 'success') {
      target.closest('tr').remove();
    } else {
      console.log(message);
    }
  };

  return {
    init: () => {
      UI.applicantsForm.addEventListener('submit', showApplicants);
      UI.eventsForm.addEventListener('submit', showEvents);
      UI.tableWrap.addEventListener('click', deleteEvent);
    },
  };
})();

SolCheckInAdmin.init();
