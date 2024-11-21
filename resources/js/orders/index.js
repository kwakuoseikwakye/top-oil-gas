import {
      handlePrompt,
} from "../prompt";

var orderTable = $('#order-table').DataTable({
      dom: 'Bfrtip',
      ajax: {
            url: `${APP_URL}/api/admin/orders`,
            type: "GET"

      },
      ordering: false,
      order: [],
      processing: true,
      columns: [{
            data: "order_id"
      },
      {
            data: "customer"
      },
      {
            data: "cylcode"
      },
      {
            data: "date"
      },
      {
            data: "weight"
      },
      {
            data: "new"
      },
      {
            data: "location"
      },
      {
            data: "status"
      },
      {
            data: "action"
      },
      ],
      buttons: [{
            extend: 'print',
            attr: {
                  class: "btn btn-sm btn-secondary rounded-right"
            },
            exportOptions: {
                  columns: [0, 1, 2, 3, 4]
            }
      },
      {
            extend: 'copy',
            attr: {
                  class: "btn btn-sm btn-secondary rounded-right"
            },
            exportOptions: {
                  columns: [0, 1, 2, 3, 4]
            }
      },
      {
            extend: 'excel',
            attr: {
                  class: "btn btn-sm btn-secondary rounded-right"
            },
            exportOptions: {
                  columns: [0, 1, 2, 3, 4]
            }
      },
      {
            extend: 'pdf',
            attr: {
                  class: "btn btn-sm btn-secondary rounded-right"
            },
            exportOptions: {
                  columns: [0, 1, 2, 3, 4]
            }
      },
      {
            text: "Refresh",
            attr: {
                  class: "ml-2 btn-primary btn btn-sm rounded"
            },
            action: function (e, dt, node, config) {
                  dt.ajax.reload(false, null);
            }
      },
      ]
});

var cylinderTable = $('#cylinder-table').DataTable({
      dom: 'Bfrtip',
      // ajax: {
      //       url: `${APP_URL}/api/cylinder`,
      //       type: "GET"

      // },
      ordering: false,
      order: [],
      processing: true,
      pageLength: 100,
      columns: [{
            data: "cylcode"
      },
      {
            data: "owner"
      },
      {
            data: "amount"
      },
      {
            data: "weight"
      },
      {
            data: "requested"
      },
      {
            data: null,
            defaultContent: `

          <button type='button' data-row-transid='$this->transid'
          rel='tooltip' class='btn btn-primary btn-sm view-btn'>
              <i class='fas fa-eye'></i>
          </button>

          <button type='button' data-row-transid='$this->transid'
          rel='tooltip' class='btn btn-success btn-sm edit-btn'>
             <i class='fas fa-edit'></i>
          </button>

          <button type='button' data-row-transid='$this->transid'
          rel='tooltip' class='btn btn-danger btn-sm delete-btn'>
             <i class='fas fa-trash'></i>
          </button>
          
          `
      },
      ],
      // "columnDefs": [{
      //         "targets": [6],
      //         "visible": false
      //     },
      // ],
      responsive: true,
      buttons: [{
            extend: 'print',
            attr: {
                  class: "btn btn-sm btn-secondary rounded-right"
            },
            exportOptions: {
                  columns: [0, 1, 2, 3, 4]
            }
      },
      {
            extend: 'copy',
            attr: {
                  class: "btn btn-sm btn-secondary rounded-right"
            },
            exportOptions: {
                  columns: [0, 1, 2, 3, 4]
            }
      },
      {
            extend: 'excel',
            attr: {
                  class: "btn btn-sm btn-secondary rounded-right"
            },
            exportOptions: {
                  columns: [0, 1, 2, 3, 4]
            }
      },
      {
            extend: 'pdf',
            attr: {
                  class: "btn btn-sm btn-secondary rounded-right"
            },
            exportOptions: {
                  columns: [0, 1, 2, 3, 4]
            }
      },
      {
            text: "Refresh",
            attr: {
                  class: "ml-2 btn-primary btn btn-sm rounded"
            },
            action: function (e, dt, node, config) {
                  dt.ajax.reload(false, null);
            }
      },
      ]
});

const form = 'assign-cylinder-form';
const appUrl = `${APP_URL}/api/admin/cylinders/assign`; 
const modal = 'assign-cylinder-modal';
const promptMessage = 'Are you sure you want to assign cylinder to customer?';
$(document).ready(function () {
      handlePrompt(
            form,
            modal,
            appUrl,
            promptMessage,
            (data) => {
                  console.log('Submission successful:', data);
            },
            orderTable
      );
});

let cylinders = window.cylinder;
let lastSelectedRegion = null;
$("#order-table").on("click", ".assign-cylinder-btn", function () {
      console.log(cylinders);
      let data = orderTable.row($(this).parents('tr')).data();

      $("#assign-cylinder-transid").val(data.transid);
      $("#assign-cylinder-custno").val(data.custno);
      $("#assign-cylinder-orderid").val(data.order_id);
      $("#assign-cylinder-weight").val(data.weight_id);

      //populate cylinder
      let filteredCylinders = [];
      cylinders.forEach(cylinder => {
            if (cylinder.weight_id === data.weight_id) {
                  filteredCylinders.push({
                        "id": cylinder.code,
                        "text": cylinder.code
                  });
            }
      });

      // $("#assign-customer-cylinder").empty();
      let fetchCylindersId = document.getElementById('assign-customer-cylinder');
      $(fetchCylindersId).empty();

      // Append data to the select element
      $(fetchCylindersId).selectpicker('destroy');
      $(fetchCylindersId).selectpicker({
            liveSearch: true,
            liveSearchNormalize: true,
            title: '--Select--',
            noneResultsText: 'No results matched {0}',
            noneSelectedText: '--Select--',
      });

      filteredCylinders.forEach(function (loc) {
            $(fetchCylindersId).append(new Option(loc.text, loc.id));
      });
      $(fetchCylindersId).selectpicker('refresh');

      $("#assign-cylinder-modal").modal("show");
});

let locations = window.customerLocations;
var orderCylinderCustomerId = document.getElementById('order-cylinder-customer');
$(orderCylinderCustomerId).on('change', function () {
      var selectedIndex = $(this).prop('selectedIndex');
      var selectedOption = $(this).children('option').eq(selectedIndex);
      var selectedValue = selectedOption.val();

      let filteredLoc = [];
      locations.forEach(location => {
            if (location.custno == selectedValue) {
                  filteredLoc.push({
                        "id": location.id,
                        "text": location.name
                  });
            }
      });

      $('#order-cylinder-location').empty();

      // Append data to the select element
      $('#order-cylinder-location').selectpicker('destroy');
      $('#order-cylinder-location').selectpicker({
            liveSearch: true,
            liveSearchNormalize: true,
            title: '--Select--',
            noneResultsText: 'No results matched {0}',
            noneSelectedText: '--Select--',
      });

      filteredLoc.forEach(function (loc) {
            $('#order-cylinder-location').append(new Option(loc.text, loc.id));
      });
      $('#order-cylinder-location').selectpicker('refresh');
});