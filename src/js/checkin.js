import '../scss/checkin.scss';

const SolCheckIn = (() => {
  const UI = {
    form: document.querySelector('.ci-form'),
    clinicDrop: document.querySelector('.js-clinic-drop'),
    message: document.querySelector('.ci-form__message'),
    nav: document.querySelector('.ci-form__nav'),
    navBtn1: document.getElementById('nav-page-1'),
    navBtn2: document.getElementById('nav-page-2'),
    formPage1: document.getElementById('form-page-1'),
    formPage2: document.getElementById('form-page-2'),
    familyNameInput: document.getElementById('ci-family-name'),
    givenNameInput: document.getElementById('ci-given-name'),
    patrNameInput: document.getElementById('ci-patr-name'),
    dobInput: document.getElementById('ci-dob'),
    telInput: document.getElementById('ci-tel'),
    unitInput: document.getElementById('ci-unit'),
    dateInput: document.getElementById('ci-date'),
    intervalInput: document.getElementById('ci-interval'),
    districtInput: document.getElementById('ci-district'),
    clinicInput: document.getElementById('ci-clinic'),
  };

  const FORM_DATA = new FormData();

  let DISTRICTS;

  const closeDropdowns = (target) => {
    const dropdowns = document.getElementsByClassName('ci-dropdown');

    Array.from(dropdowns).forEach((label) => {
      if (label !== target.parentElement) {
        label.classList.remove('ci-dropdown--open');
        label.querySelector('.ci-dropdown__list').classList.add('ci-dropdown__list--hidden');
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
    if (target.classList.contains('ci-dropdown__list-item')) {
      const dropdown = target.closest('.ci-dropdown');
      const input = dropdown.querySelector('.ci-dropdown__input');

      input.value = target.textContent;
      input.dataset.id = target.dataset.id;

      const previousSelection = dropdown.querySelector('.ci-dropdown__list-item--selected');

      if (previousSelection) {
        previousSelection.classList.remove('ci-dropdown__list-item--selected');
      }

      target.classList.add('ci-dropdown__list-item--selected');

      dropdown.querySelector('.ci-form__error').textContent = '';
    }
  };

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

  const getUnits = async () => {
    const res = await fetch('../check-in-api/units/?active=1', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });
    return await res.json();
  };

  const getDistricts = async () => {
    const res = await fetch('../check-in-api/districts/', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });
    const data = await res.json();

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
    UI.clinicDrop.innerHTML = '';

    getClinics(districtId).then((clinics) => appendItems('.js-clinic-drop', clinics));

    const clinicInput = document.getElementById('ci-clinic');
    const clinicLabel = clinicInput.parentElement;

    clinicInput.value = '';

    clinicInput.classList.remove('ci-form__input--disabled');
    clinicLabel.classList.remove('ci-form__label--disabled');
  };

  const clearClinics = () => {
    UI.clinicDrop.innerHTML = '';

    const clinicInput = document.getElementById('ci-clinic');
    const clinicLabel = clinicInput.parentElement;

    clinicInput.value = '';
    clinicInput.classList.add('ci-form__input--disabled');
    clinicLabel.classList.add('ci-form__label--disabled');
  };

  const fillDate = (data) => {
    const dateRU = new Date(data[1]).toLocaleDateString('RU');
    const dateId = data[0];

    const dateInput = document.getElementById('ci-date');
    dateInput.value = dateRU;
    dateInput.dataset.id = dateId;
    dateInput.classList.remove('ci-form__input--disabled');

    getIntervalsById(dateId).then((intervals) => fillIntervals(intervals));
  };

  const fillIntervals = (intervals) => {
    const intervalInput = document.getElementById('ci-interval');
    intervalInput.classList.remove('ci-form__input--disabled');

    const intervalLabel = intervalInput.parentElement;
    intervalLabel.classList.remove('ci-form__label--disabled');

    document.querySelector('.js-interval-drop').innerHTML = '';

    appendItems('.js-interval-drop', intervals);

    intervalInput.value = '';
    intervalInput.classList.remove('ci-dropdown__input--disabled');
  };

  const validateInput = (input, re) => {
    const inputValue = input.value.trim();
    const error = input.nextElementSibling;

    let errorMessage = '';

    if ('' === inputValue) {
      errorMessage = 'Поле не должно быть пустым';
    } else if (!re.test(inputValue)) {
      errorMessage = 'Поле заполнено не корректно';
    }

    if (errorMessage) {
      input.classList.add('invalid');
      error.textContent = errorMessage;
    } else {
      input.classList.remove('invalid');
      error.textContent = '';
    }
    return !!errorMessage;
  };

  const getFullYears = (dateString) => {
    if (!dateString) return -1;

    const date = new Date(dateString);
    const today = new Date();

    const dayDiff = today.getDate() - date.getDate();
    const monthDiff = today.getMonth() - date.getMonth();
    const yearDiff = today.getFullYear() - date.getFullYear();

    const fullMonths = dayDiff < 0 ? monthDiff - 1 : monthDiff;
    const fullYears = fullMonths < 0 ? yearDiff - 1 : yearDiff;

    return fullYears;
  };

  const validateAge = (ageMin, ageMax) => {
    const age = getFullYears(UI.dobInput.value);
    const errorSpan = UI.dobInput.parentElement.querySelector('.ci-form__error');

    if (age < ageMin || age > ageMax) {
      errorSpan.textContent = 'Проверьте правильность заполения поля';
      return true;
    }

    errorSpan.textContent = '';
    return false;
  };

  const navForward = () => {
    UI.formPage1.classList.remove('ci-form__page--active');
    UI.formPage2.classList.add('ci-form__page--active');
    UI.nav.querySelectorAll('button').forEach((btn) => btn.classList.toggle('ci-nav-btn--current'));
  };

  const navBackward = () => {
    UI.formPage1.classList.add('ci-form__page--active');
    UI.formPage2.classList.remove('ci-form__page--active');
    UI.nav.querySelectorAll('button').forEach((btn) => btn.classList.toggle('ci-nav-btn--current'));
  };

  const showNavError = (pageNumber) => {
    const navBtn = pageNumber === 1 ? UI.navBtn1 : UI.navBtn2;
    navBtn.classList.add('ci-nav-btn--has-error');
    navBtn.classList.remove('ci-nav-btn--is-ok');
    navBtn.firstElementChild.textContent = '!';
  };

  const clearNavError = (pageNumber) => {
    const navBtn = pageNumber === 1 ? UI.navBtn1 : UI.navBtn2;
    navBtn.classList.remove('ci-nav-btn--has-error');
    navBtn.classList.add('ci-nav-btn--is-ok');
    navBtn.firstElementChild.innerHTML = '&#10004;'; // ✔
  };

  const validatePatientData = () => {
    const nameRE = /^[а-яА-ЯёЁ]{2,}(\-[а-яА-ЯёЁ]{2,})?$/;
    const telRE = /^((\+?7|8)?(-|\.|\s|\s?\()?(\d{3})(-|\.|\s|\)\s?)?)?\d{3}[-\.\s]?\d{2}[-\.\s]?\d{2}$/;

    const fnErr = validateInput(UI.familyNameInput, nameRE);
    const gnErr = validateInput(UI.givenNameInput, nameRE);
    const pnErr = validateInput(UI.patrNameInput, nameRE);
    const dobErr = validateAge(1, 17);
    const telErr = validateInput(UI.telInput, telRE);

    if (!fnErr && !gnErr && !pnErr && !dobErr && !telErr) {
      clearNavError(1);

      return true;
    }

    showNavError(1);

    return false;
  };

  const setPatientData = () => {
    FORM_DATA.set(
      'patient_fio',
      `${UI.familyNameInput.value.trim()} ${UI.givenNameInput.value.trim()} ${UI.patrNameInput.value.trim()}`
    );
    FORM_DATA.set('patient_dob', UI.dobInput.value);
    FORM_DATA.set('patient_phone', UI.telInput.value.trim());

    console.log(UI.familyNameInput.value);
  };

  const formNext = () => {
    const ok = validatePatientData();

    if (ok) {
      setPatientData();
      navForward();
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
    } else if (!DISTRICTS.find((el) => el[1].toLowerCase() === input.value.toLowerCase())) {
      input.classList.add('invalid');
      error.textContent = 'Введите верный район';
    } else {
      input.classList.remove('invalid');
      error.textContent = '';
    }
  };

  const createPDFLink = (url) => {
    const existingLink = document.querySelector('.ci-form__pdf-link');
    if (existingLink) existingLink.remove();

    const link = document.createElement('a');
    link.className = 'ci-form__pdf-link file-pdf';
    link.href = url;
    link.textContent = 'Скачать пропуск';

    return link;
  };

  const validateEventData = () => {
    const unitErr = validateDropInput(UI.unitInput);
    const intErr = validateDropInput(UI.intervalInput);
    const districtErr = validateDropInput(UI.districtInput);
    const clinicErr = validateDropInput(UI.clinicInput);

    if (!unitErr && !intErr && !districtErr && !clinicErr) {
      clearNavError(2);

      return true;
    }

    showNavError(2);

    return false;
  };

  const setEventData = () => {
    FORM_DATA.set('unit_id', UI.unitInput.dataset.id);
    FORM_DATA.set('date_id', UI.dateInput.dataset.id);
    FORM_DATA.set('interval_id', UI.intervalInput.dataset.id);
    FORM_DATA.set('district_id', UI.districtInput.dataset.id);
    FORM_DATA.set('clinic_id', UI.clinicInput.dataset.id);
  };

  const updateMessage = (status, message) => {
    UI.message.className = `ci-form__message ci-form__message--type_${status}`;
    UI.message.textContent = message;
  };

  const formSubmit = async () => {
    const eventOk = validateEventData();
    const patientOk = validatePatientData();

    if (eventOk) {
      setEventData();

      if (patientOk) {
        const res = await fetch('../check-in-api/register/', {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: FORM_DATA,
        });

        const data = await res.json();
        const { status, message, ticketURL } = data;

        updateMessage(status, message);

        if (ticketURL) {
          const UIlink = createPDFLink(ticketURL);
          UI.form.appendChild(UIlink);
        }
      }
    }
  };

  const handleDistrictInput = async () => {
    const input = document.getElementById('ci-district');
    const chosen = DISTRICTS.find((el) => el[1].toLowerCase() === input.value.toLowerCase());

    if (!chosen) {
      clearClinics();
    } else {
      const id = chosen[0];
      input.dataset.id = id;
      fillClinics(id);
    }
  };

  const handleNav = (e) => {
    const targetBtn = e.target.closest('.ci-nav-btn');
    const currentBtn = UI.nav.querySelector('.ci-nav-btn--current');

    if (targetBtn !== null && targetBtn !== currentBtn) {
      if (targetBtn.id === 'nav-page-1') {
        navBackward();
      } else {
        formNext();
      }
    }
  };

  const clearEventInputs = () => {
    UI.unitInput.value = '';
    UI.dateInput.value = '';
    UI.intervalInput.value = '';
    UI.districtInput.value = '';
    UI.clinicInput.value = '';
  };

  const addListeners = () => {
    document.body.addEventListener('mousedown', (e) => toggleDropdown(e.target));

    UI.form.addEventListener('mousedown', (e) => fillDropdownInput(e.target));

    document
      .querySelector('.js-unit-drop')
      .addEventListener('mousedown', (e) => getDateByUnit(e.target).then((data) => fillDate(data)));

    document.getElementById('ci-district').addEventListener('input', handleDistrictInput);
    document.getElementById('ci-district').addEventListener('change', validateDistrictInput);
    document.querySelector('.ci-form').addEventListener('submit', (e) => e.preventDefault());
    document.querySelector('#form-next-btn').addEventListener('click', formNext);
    document.querySelector('#form-submit-btn').addEventListener('click', formSubmit);

    UI.nav.addEventListener('click', handleNav);
  };

  return {
    init: () => {
      clearEventInputs();

      getDistricts();

      addListeners();

      getUnits().then((units) => appendItems('.js-unit-drop', units));
    },
  };
})();

SolCheckIn.init();
