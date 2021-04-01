// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').DataTable({
      lengthMenu: [0, 5, 10, 20, 50, 100, 200, 500],
  });
});
