jQuery(document).ready(function ($) {
  // ✅ Handler for selecting a new date to create an event
  function select(start, end) {
    $("#event_date").val(start.format("YYYY-MM-DD"));
    $("#event_title").val("");
    $("#event_description").val("");
    var formatted = start.format("dddd, MMMM D");
    $("#selectedDayText").text(formatted);
    // $("#eventModal").show();
  }



  $("#eventForm").on("submit", function (e) {
    e.preventDefault();
    var title = $("#event_title").val();
    var description = $("#event_description").val();
    var event_date = $("#event_date").val();
    if (title) {
      $.post(
        ajaxurl,
        {
          action: "calendar_events_add",
          title: title,
          description: description,
          event_date: event_date,
        },
        function (response) {
          if (response.success) {
            alert("Event added!");
            
          } else {
            alert("Failed to add event.");
          }
        }
      );
    }
  });
});
