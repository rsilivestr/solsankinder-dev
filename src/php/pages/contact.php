<?php

include dirname(__FILE__) . '/../../vendor/vlucas/valitron/src/Valitron/Validator.php';

$dice = $modules->get('FormDiceCaptcha');

$content = "<section class='section section--width_m'><h1>$title</h1>";

$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];
$captcha = $_POST['captcha'];

$notSpam = true;
$captchaOk = true;

if ($name && !$dice->validate($captcha)) {
  $captchaOk = false;
}

$filteredWords = [
  'рассыл',
  'продвижени',
  'конкурен',
  'прогон',
  ' трастов',
  'cloud',
  'broker',
  'financ',
  'invest',
  'business',
  'every',
  'advertis',
  'Авито',
  'visit',
  'website',
  'contact',
  'hack',
  'contact',
  'bitcoin',
];

foreach ($filteredWords as $word) {
  if (strripos($message, $word) !== false) {
    $notSpam = false;
    break;
  }
}

$v = new \Valitron\Validator([
  'name' => $sanitizer->text($name),
  'email' => $sanitizer->email($email),
  'message' => $sanitizer->text($message),
]);

$v->rule('required', ['name', 'message']);
$v->rule('lengthMin', 'name', 3);
$v->rule('lengthMin', 'message', 15);
$v->rule('email', 'email');

$formHtml =
  "
  <form
    method='post'
    action='./'
    class='contact-form'
    enctype='multipart/form-data'
  >
    <label class='contact-form__label'>Имя *
      <input
        class='contact-form__input'
        name='name'
        required
        type='text'
      >
    </label>
    <label class='contact-form__label'>Почта для обратной связи *
      <input
        class='contact-form__input'
        name='email'
        required
        type='email'
      >
    </label>
    <label class='contact-form__label'>Текст сообщения *
      <textarea
        class='contact-form__input'
        name='message'
        required
        rows='8'
      ></textarea>
    </label>
    <label class='contact-form__label'>Введите сумму кубиков *
      <span class='contact-form-captcha'>
        <img class='contact-form-captcha__image' src='" .
  $dice->captcha() .
  "'>
        <input
          class='contact-form__input contact-form-captcha__input'
          name='captcha'
          required
          type='text'
        >
      </span>
    </label>
    <input
      type='submit'
      value='Отправить'
      class='btn contact-form__input contact-form__input--type_submit'
    >
    <span class='contact-form__notice'>* — отмеченные поля обязательны для заполнения.</span>
  </form>";

if ($name && $notSpam) {
  if ($v->validate() && $captchaOk) {
    $subject = "Контактная форма | $name";

    $headers = "From: $name < $email >\n";
    $headers .= 'X-Mailer: PHP/' . phpversion();
    $headers .= "Reply-To: $email\n";
    $headers .= "Content-Type: text/html; charset=utf-8\n";

    $mailBody = "<p>$message</p><p>Отправитель: $name $email</p>";

    mail('info@solsankinder.ru', $subject, $mailBody, $headers);

    header('Location: ?submit=success');
    die();
  } else {
    header('Location: ?submit=error');
    die();
  }
} else {
  if ($_GET['submit'] === 'success') {
    $content .= '<p class="contact-form__message contact-form__message--type_success">
      Ваше сообщение успешно отправлено. Спасибо за обращение!
    </p>';
  }

  if ($_GET['submit'] === 'error') {
    $content .= '<p class="contact-form__message contact-form__message--type_error">
      Капча введена неверно
    </p>';
  }

  $content .= $formHtml;
}

$content .= '</section>';
