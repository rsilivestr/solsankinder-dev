const SolVoucher = (() => {
  const UI = {
    form: document.querySelector('.ci-form'),
    message: document.querySelector('.ci-form__message'),
    familyNameInput: document.getElementById('ci-family-name'),
    givenNameInput: document.getElementById('ci-given-name'),
    patrNameInput: document.getElementById('ci-patr-name'),
    telInput: document.getElementById('ci-tel'),
    pdaInput: document.getElementById('ci-pda'),
    submitButton: document.getElementById('ci-submit'),
  };

  const FORM_DATA = new FormData();

  const validateInput = (input, regExp) => {
    const inputValue = input.value.trim();
    const errorBox = input.nextElementSibling;

    let errorMessage = '';

    if ('' === inputValue) {
      errorMessage = 'Поле не должно быть пустым';
    } else if (!regExp.test(inputValue)) {
      errorMessage = 'Поле заполнено не корректно';
    }

    if (errorMessage) {
      input.classList.add('invalid');
      errorBox.textContent = errorMessage;
    } else {
      input.classList.remove('invalid');
      errorBox.textContent = '';
    }
    return !!errorMessage;
  };

  const validatePDAInput = () => {
    const { checked } = UI.pdaInput;
    const errorBox = UI.pdaInput.nextElementSibling;

    if (checked) {
      errorBox.textContent = '';
    } else {
      errorBox.textContent = 'Согласие необходимо для отправки формы';
    }

    return !checked;
  };

  const setFormData = () => {
    FORM_DATA.set(
      'fio',
      `${UI.familyNameInput.value.trim()} ${UI.givenNameInput.value.trim()} ${UI.patrNameInput.value.trim()}`
    );
    FORM_DATA.set('phone', UI.telInput.value.trim());
  };

  const validateFormData = () => {
    const nameRE = /^[а-яА-ЯёЁ]{2,}(\-[а-яА-ЯёЁ]{2,})?$/;
    const telRE =
      /^((\+?7|8)?(-|\.|\s|\s?\()?(\d{3})(-|\.|\s|\)\s?)?)?\d{3}[-\.\s]?\d{2}[-\.\s]?\d{2}$/;

    const fnErr = validateInput(UI.familyNameInput, nameRE);
    const gnErr = validateInput(UI.givenNameInput, nameRE);
    const pnErr = validateInput(UI.patrNameInput, nameRE);
    const telErr = validateInput(UI.telInput, telRE);
    const pdaErr = validatePDAInput();

    const isInvalid = fnErr || gnErr || pnErr || telErr || pdaErr;

    if (isInvalid) return false;

    setFormData();
    return true;
  };

  const updateMessage = (status, message) => {
    UI.message.className = `ci-form__message ci-form__message--type_${status}`;
    UI.message.textContent = message;
  };

  const onSubmit = async (e) => {
    e.preventDefault();

    const isDataValid = validateFormData();

    if (!isDataValid) return;

    try {
      const res = await fetch('../check-in-api/get-voucher', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: FORM_DATA,
      });

      const { status, message } = await res.json();

      updateMessage(status, message);
    } catch (err) {
      console.log(err);
    }
  };

  const clearInputs = () => {
    UI.familyNameInput.value = 'ли';
    UI.givenNameInput.value = 'ян';
    UI.patrNameInput.value = 'ильич';
    UI.telInput.value = '89219999999';
    UI.pdaInput.checked = false;
  };

  const addListeners = () => {
    UI.form.addEventListener('submit', onSubmit);
  };

  return {
    init: () => {
      clearInputs();
      addListeners();
    },
  };
})();

SolVoucher.init();
