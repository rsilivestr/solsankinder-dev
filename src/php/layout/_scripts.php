<!-- электронное обращение к администрации СПб -->
<?php if ($page->lettersGovRu) {
  echo '<script>var receptionWidget=["Ly9sZXR0ZXJzLmdvdi5zcGIucnU=","left","blue","JUQwJUExJUQwJTlGJUQwJUIxJTIwJUQwJTkzJUQwJTkxJUQwJUEzJUQwJTk3JTIwJUMyJUFCJUQwJTk0JUQwJUI1JUQxJTgyJUQxJTgxJUQwJUJBJUQwJUI4JUQwJUI5JTIwJUQxJTgxJUQwJUIwJUQwJUJEJUQwJUIwJUQxJTgyJUQwJUJFJUQxJTgwJUQwJUI4JUQwJUI5JTIwJUMyJUFCJUQwJUExJUQwJUJFJUQwJUJCJUQwJUJEJUQwJUI1JUQxJTg3JUQwJUJEJUQwJUJFJUQwJUI1JUMyJUJC","140"];document.write(decodeURI("%3Cscript%20src=//letters.gov.spb.ru/static/widget/widget.js%3E%3C/script%3E"))</script>';
} ?>

<!-- Insert stylesheets -->
<script>
  var insertStyle = function (href) {
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.type = 'text/css';
    link.href = href;

    var firstHeadLink = document.querySelector('link');
    firstHeadLink.parentElement.insertBefore(link, firstHeadLink);
  }

  insertStyle('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,200;0,400;0,700;1,200;1,400;1,700&display=swap');
  insertStyle('/site/templates/styles/fontello.min.css');
</script>
