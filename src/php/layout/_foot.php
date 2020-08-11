  </main>

  <footer class="footer">
    <nav class="footer-nav">
      <a class="footer-nav__link" href="/contacts/">Контакты</a>
      <a class="footer-nav__link" href="/search/">Поиск</a>
      <a class="footer-nav__link" href="/site-map/">Карта сайта</a>
      <a class="footer-nav__link" href="/contact-form/">Напишите нам</a>
    </nav>
    <div class="footer-officials">
      <a
        class="footer-officials__banner"
        id="ban_gu"
        href="https://gosuslugi.ru/"
      ></a
      ><a
        class="footer-officials__banner"
        id="ban_miac"
        href="https://spbmiac.ru/"
      ></a
      ><a
        class="footer-officials__banner"
        id="ban_poll"
        href="http://anketa.rosminzdrav.ru/staticmojustank/9211#reviews"
      ></a>
    </div>
    <div class="container footer-copy">
      <p class="footer-copy__para">© 2019<?php if(date('Y')>2019){echo' - '.date('Y');}?>, СПб ГБУЗ «Детский санаторий «Солнечное»</p>
    </div>
  </footer>
  <?php
    // электронное обращение к администрации СПб
    if($page->lettersGovRu) {
      echo '<script>var receptionWidget=["Ly9sZXR0ZXJzLmdvdi5zcGIucnU=","left","blue","JUQwJUExJUQwJTlGJUQwJUIxJTIwJUQwJTkzJUQwJTkxJUQwJUEzJUQwJTk3JTIwJUMyJUFCJUQwJTk0JUQwJUI1JUQxJTgyJUQxJTgxJUQwJUJBJUQwJUI4JUQwJUI5JTIwJUQxJTgxJUQwJUIwJUQwJUJEJUQwJUIwJUQxJTgyJUQwJUJFJUQxJTgwJUQwJUI4JUQwJUI5JTIwJUMyJUFCJUQwJUExJUQwJUJFJUQwJUJCJUQwJUJEJUQwJUI1JUQxJTg3JUQwJUJEJUQwJUJFJUQwJUI1JUMyJUJC","140"];document.write(decodeURI("%3Cscript%20src=//letters.gov.spb.ru/static/widget/widget.js%3E%3C/script%3E"))</script>';
    }
  ?>
</body>
</html>