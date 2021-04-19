<?php namespace ProcessWire;

$formAction = $pages->get('template=search')->url;
$formValue = $sanitizer->entities($input->whitelist('q'));

$content = "<section class='section section--width_m'>
	<h1>$title</h1>
	<form class='search-form' action='$formAction' method='get'>
		<input
			type='text'
			name='query'
			class='search-form__input'
			value='$formValue'
		/>
		<button type='submit' class='search-form__submit'>Найти</button>
	</form>
</section>";

// search.php template file
// See README.txt for more information.

// look for a GET variable named 'q' and sanitize it
$q = $sanitizer->text($input->get->query);

// did $q have anything in it?
if ($q) {
  // Send our sanitized query 'q' variable to the whitelist where it will be
  // picked up and echoed in the search box by _main.php file. Now we could just use
  // another variable initialized in _init.php for this, but it's a best practice
  // to use this whitelist since it can be read by other modules. That becomes
  // valuable when it comes to things like pagination.
  $input->whitelist('q', $q);

  // Sanitize for placement within a selector string. This is important for any
  // values that you plan to bundle in a selector string like we are doing here.
  $q = $sanitizer->selectorValue($q);

  // Search the title and body fields for our query text.
  // Limit the results to 50 pages.
  $selector = "title|body%=$q, limit=50";

  // If user has access to admin pages, lets exclude them from the search results.
  // Note that 2 is the ID of the admin page, so this excludes all results that have
  // that page as one of the parents/ancestors. This isn't necessary if the user
  // doesn't have access to view admin pages. So it's not technically necessary to
  // have this here, but we thought it might be a good way to introduce has_parent.
  if ($user->isLoggedin()) {
    $selector .= ', has_parent!=2';
  }

  // Find pages that match the selector
  $matches = $pages->find($selector);

  // did we find any matches?
  if ($matches->count) {
    $count = $matches->count;
    // yes we did
    $pageCase = '';
    $foundCase = 'о';
    if ($count % 10 >= 5 || $count % 10 == 0 || ($count >= 10 && $count <= 20)) {
      $pageCase = '';
    } elseif ($count % 10 >= 2 && $count % 10 <= 4) {
      $pageCase = 'ы';
    } elseif ($count % 10 == 1) {
      $pageCase = 'а';
      $foundCase = 'а';
    }
    $searchResults = "<section class='section section--width_m'><h2>Нашл${foundCase}сь $count страниц$pageCase по вашему запросу:</h2>";
    // we'll use our renderNav function (in _func.php) to render the navigation
    $searchResults .= renderNav($matches);
    $content .= $searchResults . '</section>';
  } else {
    // we didn't find any
    $content .= "<section class='section section--width_m'>
			<h2>Поиск по вашему запросу не дал результатов</h2>
		</section>";
  }
}
