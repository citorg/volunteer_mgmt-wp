jQuery(document).ready(function ($) {
  var ajaxurl = calendarEventsAjax.ajaxurl;

  $("#calendar").fullCalendar({
    header: {
      left: "prev,next today",
      center: "title",
      right: "month,agendaWeek,agendaDay",
    },
    editable: false,
    eventLimit: true,
    selectable: true,
    selectHelper: true,
    events: ajaxurl + "?action=calendar_events_fetch",

    // ✅ Event click handler for showing the modal
    eventClick: function (event) {
      $("#viewEventTitle").text(event.title);
      $("#viewEventDate").text(moment(event.start).format("dddd, MMMM D"));
      $("#viewEventDescription").text(
        event.description || "No description available."
      );
      $("#viewEventModal").show();
    }
  });

  // $("#closeModal").on("click", function () {
  //   $("#eventModal").hide();
  //   $("#calendar").fullCalendar("unselect");
  // });

  // ✅ view modal close handler
  // $("#closeViewModal").on("click", function () {
  //   $("#viewEventModal").hide();
  // });

  // $("#eventForm").on("submit", function (e) {
  //   e.preventDefault();
  //   var title = $("#event_title").val();
  //   var description = $("#event_description").val();
  //   var event_date = $("#event_date").val();
  //   if (title) {
  //     $.post(
  //       ajaxurl,
  //       {
  //         action: "calendar_events_add",
  //         title: title,
  //         description: description,
  //         event_date: event_date,
  //       },
  //       function (response) {
  //         if (response.success) {
  //           $("#calendar").fullCalendar("refetchEvents");
  //           $("#eventModal").hide();
  //         } else {
  //           alert("Failed to add event.");
  //         }
  //       }
  //     );
  //   }
  // });
});
