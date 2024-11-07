"use strict";
import {
      handlePrompt,
} from "../prompt";

var customerTable = $('#customer-table').DataTable({
      dom: 'Bfrtip',
      ajax: {
            url: `${APP_URL}/api/customers`,
            type: "GET"

      },
      ordering: false,
      order: [],
      processing: true,
      columns: [{
            data: "name"
      },
      {
            data: "phone"
      },
      {
            data: "address"
      },
      {
            data: "action"
      },
      ],
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

const form = 'add-customer-form';
const appUrl = `${APP_URL}/api/customers`; 
const modal = 'add-customer-modal';
const promptMessage = 'Are you sure you want to add this customers';
$(document).ready(function () {
      handlePrompt(
            form,
            modal,
            appUrl,
            promptMessage,
            (data) => {
                  console.log('Submission successful:', data);
            },
            customerTable
      );
});