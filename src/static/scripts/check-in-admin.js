const SolCheckInAdmin = (() => {
  const UIformShowEvents = document.getElementById('show-events-form');
  const UIformCloseEvent = document.getElementById('close-event-form');

  const fetchEventData = async (date) => {
    return 123;
  }

  const showEvents = async (e) => {
    e.preventDefault();

    const date = UIformShowEvents.querySelector('input').value;

    const eventData = await fetchEventData(date);

    console.log(eventData);
  }

  return {
    init: () => {
      UIformShowEvents.addEventListener('submit', showEvents);
      // UIformCloseEvent.addEventListener('submit', closeDate);
    }
  }
})();

SolCheckInAdmin.init();
