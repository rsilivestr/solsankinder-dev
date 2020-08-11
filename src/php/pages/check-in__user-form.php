<?php

$content .= '
<section id="check-in-form-section">
  <h2>Форма предварительной регистрации на заезд</h2>
  <p>Регистрация действительна только для пациентов, получивших санаторно-курортную карту в поликлинике</p>
</section>

<noscript>
  <section>
    <h3 style="color:red">Для корректной работы формы необходим браузер с поддержкой JavaScript</h3>
  </section>
</noscript>

<script src="'.$config->urls->siteModules.'SolCheckIn/scripts/check-in.0ab6b5ca198c948fd0fbc63c9f8e9c92.js"></script>
<script>
  (async () => {
    const chekInForm = await SolCheckIn.createForm();
    const section = document.getElementById("check-in-form-section");
    section.appendChild(chekInForm);
  })();
</script>';
