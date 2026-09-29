<?php

// ABADIES SOLUTIONS

require_once("../../include/initialize.php");
// if (!isset($_SESSION['ACCOUNT_ID'])){
  //    redirect(web_root."/index.php");
//     }

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
 $title="Subject Module";
 $header=$view;
switch ($view) {
	case 'list' :
		$content    = 'list.php';
		break;

	default :
		$content    = 'list.php';
}
require_once ("../../theme/template.php");

?>

 <script type="text/javascript">
        $(document).ready(function() {
            var t = $('#tblsubject').DataTable( {
            "processing":true,
            "serverSide":true,
            "order":[],
            "ajax":{
              url:"<?php echo WEB_ROOT; ?>module/subject/subject_ajax.php",
              type:"POST"
            },
                "columnDefs": [ {
                    "searchable": true,
                    "orderable": true,
                    "targets": 1
                } ],
                //vertical scroll
                 "scrollY":        "400px",
                "scrollCollapse": true,
                //ordering start at column 2
               "order": [[ 2, 'asc' ]]
            } );

                t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();

        });
    </script>

  <script type="text/javascript">
  $(document).on('click', '.editEntry', function(){
    var SUBJECT_ID = $(this).attr("SUBJECT_ID");
    $.ajax({
      url:"<?php echo WEB_ROOT; ?>module/subject/subject_ajax.php",
      method:"POST",
      data:{SUBJECT_ID:SUBJECT_ID},
      dataType:"json",
      success:function(data)
      {
       $('#editEntry').modal('show');
       $('#SUBJECT_ID').val(data.SUBJECT_ID);
       $('#SUBJECT_CODE1').val(data.SUBJECT_CODE);
       $('#SUBJECT_NAME1').val(data.SUBJECT_NAME);
       $('#UNITS1').val(data.UNITS);
       $('#COURSE_ID1').val(data.COURSE_ID);
       $('#YEAR_LEVEL1').val(data.YEAR_LEVEL);
       $('#SEMESTER1').val(data.SEMESTER);
        $('.modal-title').text("Modify Subject");
      }
    })
  });
</script>

<script type="text/javascript">
  $(document).on('click', '.deleteEntry', function(){
    var SUBJECT_ID = $(this).attr("SUBJECT_ID");
    Swal.fire({
      title: 'Delete Subject?',
      text: "This action cannot be undone. Are you sure you want to delete this subject?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.value) {
        window.location.href = "<?php echo WEB_ROOT; ?>module/subject/controller.php?action=delete&id=" + SUBJECT_ID;
      }
    });
  });
</script>
