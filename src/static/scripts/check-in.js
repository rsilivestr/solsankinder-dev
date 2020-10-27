const SolCheckIn = (() => {
  let DISTRICTS;
  // UI BEHAVIOUR
  const closeDropdowns = (target) => {
    // Select all dropdowns
    const dropdowns = document.getElementsByClassName('ci-dropdown');
    // Close all except clicked
    Array.from(dropdowns).forEach((label) => {
      if (label !== target.parentElement) {
        label.classList.remove('ci-dropdown--open');
        label
          .querySelector('.ci-dropdown__list')
          .classList.add('ci-dropdown__list--hidden');
      }
    });
  };

  const toggleDropdown = (target) => {
    closeDropdowns(target);

    if (
      target.classList.contains('ci-dropdown__input') &&
      !target.classList.contains('ci-form__input--disabled')
    ) {
      target.parentElement.classList.toggle('ci-dropdown--open');
      target.parentElement
        .querySelector('.ci-dropdown__list')
        .classList.toggle('ci-dropdown__list--hidden');
    }
  };

  const fillDropdownInput = (target) => {
    // If target is a list item
    if (target.classList.contains('ci-dropdown__list-item')) {
      const dropdown = target.closest('.ci-dropdown');
      const input = dropdown.querySelector('.ci-dropdown__input');
      // Set value and data-id
      input.value = target.textContent;
      input.dataset.id = target.dataset.id;
      // Find previously selected item
      const previousSelection = dropdown.querySelector(
        '.ci-dropdown__list-item--selected'
      );

      if (previousSelection) {
        // Remove previous selection
        previousSelection.classList.remove('ci-dropdown__list-item--selected');
      }
      // Add selection to current element
      target.classList.add('ci-dropdown__list-item--selected');
    }
  };

  // Fill dropdown ul with items
  const appendItems = (listSelector, items, dataAttributes = null) => {
    items.forEach((item, index) => {
      const li = document.createElement('li');

      li.className = 'ci-dropdown__list-item';
      li.dataset.id = item[0];
      li.textContent = item[1];

      if (dataAttributes) {
        li.dataset[dataAttributes.name] = dataAttributes.values[index];
      }
      document.querySelector(listSelector).appendChild(li);
    });
  };

  // Fetch data
  const getUnits = async () => {
    const res = await fetch('../check-in-api/units/?active=1', {
      headers: { "X-Requested-With": "XMLHttpRequest" },
    });
    return await res.json();
  };

  const getDistricts = async () => {
    const res = await fetch('../check-in-api/districts/', {
      headers: { "X-Requested-With": "XMLHttpRequest" },
    });
    const data = await res.json();
    // Save as global
    DISTRICTS = data;
    return data;
  };

  const getIntervalsById = async (dateId) => {
    const formData = new FormData();

    formData.append('date_id', dateId);

    const res = await fetch('../check-in-api/intervals/', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: formData,
    });
    const data = await res.json();
    return data;
  };

  const getClinics = async (districtId) => {
    const formData = new FormData();
    formData.append('district_id', districtId);

    const res = await fetch('../check-in-api/clinics/', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: formData,
    });
    const data = await res.json();
    return data;
  };

  const getDateByUnit = async (target) => {
    const unitId = target.dataset.id;

    const formData = new FormData();
    formData.append('unit_id', unitId);

    const res = await fetch('../check-in-api/dates/', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: formData,
    });
    const data = await res.json();
    return data;
  };

  const fillClinics = async (districtId) => {
    // Clear list
    document.querySelector('.js-clinic-drop').innerHTML = '';
    // Fill list
    getClinics(districtId).then((clinics) =>
      appendItems('.js-clinic-drop', clinics)
    );
    // Get form elements
    const clinicInput = document.getElementById('ci-clinic');
    const clinicLabel = clinicInput.parentElement;
    // Clear input field
    clinicInput.value = '';
    // Togle classes
    clinicInput.classList.remove('ci-form__input--disabled');
    clinicLabel.classList.remove('ci-form__label--disabled');
  };

  const clearClinics = () => {
    document.querySelector('.js-clinic-drop').innerHTML = '';

    const clinicInput = document.getElementById('ci-clinic');
    const clinicLabel = clinicInput.parentElement;

    clinicInput.value = '';
    clinicInput.classList.add('ci-form__input--disabled');
    clinicLabel.classList.add('ci-form__label--disabled');
  };

  const fillDate = (data) => {
    // convert date to RU locale
    const dateRU = new Date(data[1]).toLocaleDateString('RU');
    const dateId = data[0];
    // Get input element
    const dateInput = document.getElementById('ci-date');
    // set input value, data-id
    dateInput.value = dateRU;
    dateInput.dataset.id = dateId;
    // Enable input
    dateInput.classList.remove('ci-form__input--disabled');
    // fill intervals
    getIntervalsById(dateId).then((intervals) => fillIntervals(intervals));
  };

  const fillIntervals = (intervals) => {
    // Change interval input styles
    const intervalInput = document.getElementById('ci-interval');
    intervalInput.classList.remove('ci-form__input--disabled');
    // Change interval label styles
    const intervalLabel = intervalInput.parentElement;
    intervalLabel.classList.remove('ci-form__label--disabled');
    // remove previous dropdown items
    document.querySelector('.js-interval-drop').innerHTML = '';
    // add new items
    appendItems('.js-interval-drop', intervals);
    // clear input and make it active
    intervalInput.value = '';
    intervalInput.classList.remove('ci-dropdown__input--disabled');
  };

  const validateInput = (input, re) => {
    // const input = label.querySelector('.ci-form__input');
    const inputValue = input.value.trim();
    const error = input.nextElementSibling;

    // Correct input, no error
    let errorMessage = '';

    if ('' === inputValue) {
      // Input field is empty
      errorMessage = 'Поле не должно быть пустым';
    } else if (!re.test(inputValue)) {
      // Wrong input, error
      errorMessage = 'Поле заполнено не корректно';
    }

    if (errorMessage) {
      // Show message
      input.classList.add('invalid');
      error.textContent = errorMessage;
      // error.classList.remove('hidden');
    } else {
      // Hide message
      input.classList.remove('invalid');
      error.textContent = '';
      // error.classList.add('hidden');
    }
    return !!errorMessage;
  };

  const FORM_DATA = new FormData();

  const formNext = () => {
    const formPage1 = document.getElementById('form-page-1');
    const formPage2 = document.getElementById('form-page-2');

    // Validate fields
    const nameRE = /^[а-яА-ЯёЁ]{2,}(\-[а-яА-ЯёЁ]{2,})?$/;
    const familyNameInput = document.getElementById('ci-family-name');
    const fnErr = validateInput(familyNameInput, nameRE);

    const givenNameInput = document.getElementById('ci-given-name');
    const gnErr = validateInput(givenNameInput, nameRE);

    const patrNameInput = document.getElementById('ci-patr-name');
    const pnErr = validateInput(patrNameInput, nameRE);

    const dobInput = document.getElementById('ci-dob');
    const dobErr = validateInput(dobInput, /^\d{4}\-\d{2}\-\d{2}$/);

    const telInput = document.getElementById('ci-tel');
    const telRE = /^((\+?7|8)?(-|\.|\s|\s?\()?(\d{3})(-|\.|\s|\)\s?)?)?\d{3}[-\.\s]?\d{2}[-\.\s]?\d{2}$/;
    const telErr = validateInput(telInput, telRE);

    if (!fnErr && !gnErr && !pnErr && !dobErr && !telErr) {
      // Save form data
      FORM_DATA.append(
        'patient_fio',
        `${familyNameInput.value} ${givenNameInput.value} ${patrNameInput.value}`
      );
      FORM_DATA.append('patient_dob', dobInput.value);
      FORM_DATA.append('patient_phone', telInput.value);
      // Go to the next page
      formPage1.classList.remove('ci-form__page--active');
      formPage2.classList.add('ci-form__page--active');
    }
  };

  const validateDropInput = (input) => {
    const error = input.parentElement.querySelector('.ci-form__error');

    if ('' === input.value) {
      error.textContent = 'Поле не должно быть пустым';
    } else {
      error.textContent = '';
    }
    return !!error.textContent;
  };

  const validateDistrictInput = () => {
    const input = document.getElementById('ci-district');
    const error = input.parentElement.querySelector('.ci-form__error');

    if ('' === input.value) {
      input.classList.add('invalid');
      error.textContent = 'Поле не должно быть пустым';
    } else if (
      !DISTRICTS.find((el) => el[1].toLowerCase() === input.value.toLowerCase())
    ) {
      input.classList.add('invalid');
      error.textContent = 'Введите верный район';
    } else {
      input.classList.remove('invalid');
      error.textContent = '';
    }
  };

  const createMessage = (status, message) => {
    const span = document.createElement('span');

    span.className = `ci-form__message ci-form__message--type_${status}`;
    span.textContent = message;

    return span;
  };

  const createPDFLink = (url) =>{
    // Remove existing link
    const existingLink = document.querySelector('.ci-form__pdf-link');
    if (existingLink) existingLink.remove();

    const link = document.createElement('a');
    link.className = 'ci-form__pdf-link file-pdf';
    link.href = url;
    link.textContent = 'Скачать пропуск';

    return link;
  };

  const formSubmit = async () => {
    const unitInput = document.getElementById('ci-unit');
    const unitErr = validateDropInput(unitInput);

    const dateInput = document.getElementById('ci-date');

    const intervalInput = document.getElementById('ci-interval');
    const intErr = validateDropInput(intervalInput);

    const districtInput = document.getElementById('ci-district');
    const districtErr = validateDropInput(districtInput);

    const clinicInput = document.getElementById('ci-clinic');
    const clinicErr = validateDropInput(clinicInput);

    if (!unitErr && !intErr && !districtErr && !clinicErr) {
      FORM_DATA.append('unit_id', unitInput.dataset.id);
      FORM_DATA.append('date_id', dateInput.dataset.id);
      FORM_DATA.append('interval_id', intervalInput.dataset.id);
      FORM_DATA.append('district_id', districtInput.dataset.id);
      FORM_DATA.append('clinic_id', clinicInput.dataset.id);

      const res = await fetch('../check-in-api/register/', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: FORM_DATA,
      });

      const data = await res.json();
      const { status, message, ticketURL } = data;
      const form = document.querySelector('.ci-form');
      // Show message
      const UImessage = createMessage(status, message);
      form.appendChild(UImessage);
      // Append / update PDF link
      if (ticketURL) {
        const UIlink = createPDFLink(ticketURL);
        form.appendChild(UIlink);
      }
    }
  };

  const handleDistrictInput = async () => {
    const input = document.getElementById('ci-district');
    // Search for input
    const chosen = DISTRICTS.find(
      (el) => el[1].toLowerCase() === input.value.toLowerCase()
    );
    // Set district id
    if (!chosen) {
      clearClinics();
    } else {
      const id = chosen[0];
      // Save id for submit
      input.dataset.id = id;
      // Fill clinics
      fillClinics(id);
    }
  };

  // LISTENERS
  const addListeners = () => {
    document.body.addEventListener('mousedown', (e) =>
      toggleDropdown(e.target)
    );

    // All dropdowns, fill input on select
    document
      .querySelector('.ci-form')
      .addEventListener('mousedown', (e) => fillDropdownInput(e.target));

    // Unit dropdown, fill date on select (and get hours)
    document
      .querySelector('.js-unit-drop')
      .addEventListener('mousedown', (e) =>
        getDateByUnit(e.target).then((data) => fillDate(data))
      );

    // Districts datalist, fill clinics on select
    document
      .getElementById('ci-district')
      .addEventListener('input', handleDistrictInput);

    document
      .getElementById('ci-district')
      .addEventListener('change', validateDistrictInput);

    // Form, prevent submission
    document
      .querySelector('.ci-form')
      .addEventListener('submit', (e) => e.preventDefault());

    // Go next button
    document
      .querySelector('#form-next-btn')
      .addEventListener('click', formNext);

    // Submit button
    document
      .querySelector('#form-submit-btn')
      .addEventListener('click', formSubmit);
  };

  // Module exports
  return {
    init: () => {
      getDistricts();

      addListeners();

      getUnits().then((units) => appendItems('.js-unit-drop', units));
    },
  };
})();

SolCheckIn.init();
