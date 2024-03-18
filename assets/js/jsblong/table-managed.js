var TableManaged = function () {

    return {

        //main function to initiate the module
        init: function () {
            
            if (!jQuery().dataTable) {
                return;
            }

            // begin first table
            jQuery('#sample_1').dataTable({
                "paging": false,
                "aLengthMenu": [
                    [5, 15, 20, 50, 100, -1],
                    [5, 15, 20, 50, 100, "All"] // change per page values here
                ],
                // set the initial value
                "iDisplayLength": 500,
                "oLanguage": {
                    "sLengthMenu": "_MENU_ records",
                },
                "aoColumnDefs": [
                    { 'bSortable': true, 'aTargets': [0] },
                    { "bSearchable": true, "aTargets": [ 1 ] },
                    { "bSearchable": true, "aTargets": [ 1 ] },
                    { "bSearchable": true, "aTargets": [ 1 ] },
                    { "bSearchable": true, "aTargets": [ 1 ] },
                    { "bSearchable": true, "aTargets": [ 1 ] },
                    { "bSearchable": true, "aTargets": [ 1 ] },
                    { "bSearchable": true, "aTargets": [ 1 ] },
                    { "bSearchable": true, "aTargets": [ 1 ] },
                    { "bSearchable": true, "aTargets": [ 1 ] }
                ],
                "bRetrieve": true,
            });

          
           /* jQuery('#sample_1').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });
            */

            jQuery('#sample_1_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); // modify table search input
            jQuery('#sample_1_wrapper .dataTables_length select').addClass("form-control input-xsmall"); // modify table per page dropdown
            

        }

    };

}();

function submitdata()
{
   var a;
   a = jQuery('#sample_1_wrapper .dataTables_filter input').val();
   alert(a);
}
