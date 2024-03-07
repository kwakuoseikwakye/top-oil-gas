(()=>{$("#today-with-table").DataTable({dom:"Bfrtip",order:[],ordering:!0,ajax:{url:"".concat(APP_URL,"/api/withdrawals/today_payments/").concat(CODE),type:"GET"},processing:!0,responsive:!0,autoWidth:!1,columns:[{data:"staff"},{data:"date"},{data:"amount"}],buttons:[{extend:"print",title:"Request Lists",attr:{class:"btn btn-sm btn-secondary rounded-right"},exportOptions:{columns:[0,1,2]}},{extend:"copy",title:"Request List",attr:{class:"btn btn-sm btn-secondary rounded-right"},exportOptions:{columns:[0,1,2]}},{extend:"excel",title:"Request Lists",attr:{class:"btn btn-sm btn-secondary rounded-right"},exportOptions:{columns:[0,1,2]}},{extend:"pdf",title:"Request List",attr:{class:"btn btn-sm btn-secondary rounded-right"},exportOptions:{columns:[0,1,2]}},{text:"Refresh",attr:{class:"ml-2 btn-secondary btn btn-sm rounded"},action:function(t,n,e,a){n.ajax.reload(!1,null)}}]}),$("#today-req-table").DataTable({dom:"Bfrtip",order:[],ordering:!0,ajax:{url:"".concat(APP_URL,"/api/withdrawals/today_requests/").concat(CODE),type:"GET"},processing:!0,responsive:!0,autoWidth:!1,columns:[{data:"staff"},{data:"requestDate"},{data:"amount"},{data:"status"}],buttons:[{extend:"print",title:"Request Lists",attr:{class:"btn btn-sm btn-secondary rounded-right"},exportOptions:{columns:[0,1,2]}},{extend:"copy",title:"Request List",attr:{class:"btn btn-sm btn-secondary rounded-right"},exportOptions:{columns:[0,1,2]}},{extend:"excel",title:"Request Lists",attr:{class:"btn btn-sm btn-secondary rounded-right"},exportOptions:{columns:[0,1,2]}},{extend:"pdf",title:"Request List",attr:{class:"btn btn-sm btn-secondary rounded-right"},exportOptions:{columns:[0,1,2]}},{text:"Refresh",attr:{class:"ml-2 btn-secondary btn btn-sm rounded"},action:function(t,n,e,a){n.ajax.reload(!1,null)}}]});var t=$("#request-table").DataTable({dom:"Bfrtip",order:[],ordering:!0,ajax:{url:"".concat(APP_URL,"/api/withdrawals/staff_requests/").concat(CODE),type:"GET"},processing:!0,responsive:!0,autoWidth:!1,columns:[{data:"staff"},{data:"requestDate"},{data:"amount"},{data:"status"}],buttons:[{extend:"print",title:"Request Lists",attr:{class:"btn btn-sm btn-secondary rounded-right"},exportOptions:{columns:[0,1,2]}},{extend:"copy",title:"Request List",attr:{class:"btn btn-sm btn-secondary rounded-right"},exportOptions:{columns:[0,1,2]}},{extend:"excel",title:"Request Lists",attr:{class:"btn btn-sm btn-secondary rounded-right"},exportOptions:{columns:[0,1,2]}},{extend:"pdf",title:"Request List",attr:{class:"btn btn-sm btn-secondary rounded-right"},exportOptions:{columns:[0,1,2]}},{text:"Refresh",attr:{class:"ml-2 btn-secondary btn btn-sm rounded"},action:function(t,n,e,a){n.ajax.reload(!1,null)}}]}),n=$("#payment-table").DataTable({dom:"Bfrtip",order:[],ordering:!0,ajax:{url:"".concat(APP_URL,"/api/withdrawals/payments/").concat(CODE),type:"GET"},processing:!0,responsive:!0,autoWidth:!1,columns:[{data:"staff"},{data:"date"},{data:"amount"}],buttons:[{extend:"print",title:"Request Lists",attr:{class:"btn btn-sm btn-secondary rounded-right"},exportOptions:{columns:[0,1,2]}},{extend:"copy",title:"Request List",attr:{class:"btn btn-sm btn-secondary rounded-right"},exportOptions:{columns:[0,1,2]}},{extend:"excel",title:"Request Lists",attr:{class:"btn btn-sm btn-secondary rounded-right"},exportOptions:{columns:[0,1,2]}},{extend:"pdf",title:"Request List",attr:{class:"btn btn-sm btn-secondary rounded-right"},exportOptions:{columns:[0,1,2]}},{text:"Refresh",attr:{class:"ml-2 btn-secondary btn btn-sm rounded"},action:function(t,n,e,a){n.ajax.reload(!1,null)}}]}),e=document.getElementById("payment-filter");$(e).submit((function(t){t.preventDefault();var a=new FormData(e),s=a.get("staff"),o=a.get("from"),r=a.get("to");null!==s&&n.ajax.url("".concat(APP_URL,"/api/withdrawals/filter_payments/").concat(CODE,"/").concat(o,"/").concat(r,"/").concat(s)).load(),null==s&&n.ajax.url("".concat(APP_URL,"/api/withdrawals/filter_payments/").concat(CODE,"/").concat(o,"/").concat(r)).load()}));var a=document.getElementById("filter-request");$(a).submit((function(n){n.preventDefault();var e=new FormData(a),s=e.get("staff"),o=e.get("from"),r=e.get("to");null!==s&&t.ajax.url("".concat(APP_URL,"/api/withdrawals/filter_request/").concat(CODE,"/").concat(o,"/").concat(r,"/").concat(s)).load(),null==s&&t.ajax.url("".concat(APP_URL,"/api/withdrawals/filter_request/").concat(CODE,"/").concat(o,"/").concat(r)).load()}))})();