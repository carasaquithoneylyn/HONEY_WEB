<?php

// ABADIES SOLUTIONS

require_once("../../include/initialize.php");
// if (!isset($_SESSION['ACCOUNT_ID'])){
  //    redirect(web_root."/index.php");
//     }

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
 $title="Course Module";
 $header=$view;
switch ($view) {
	case 'list' :
		$content    = 'list.php';
		break;

	case 'view' :
		$content    = 'view.php';
		break;

	default :
		$content    = 'list.php';
}
require_once ("../../theme/template.php");

?>

 <script type="text/javascript">
        $(document).ready(function() {
            var t = $('#tblcourse').DataTable( {
            "processing":true,
            "serverSide":true,
            "order":[],
            "ajax":{
              url:"<?php echo WEB_ROOT; ?>module/course/course_ajax.php",
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
    var COURSE_ID = $(this).attr("COURSE_ID");
    $.ajax({
      url:"<?php echo WEB_ROOT; ?>module/course/course_ajax.php",
      method:"POST",
      data:{COURSE_ID:COURSE_ID},
      dataType:"json",
      success:function(data)
      {
       $('#editEntry').modal('show');
       $('#COURSE_ID').val(data.COURSE_ID);
       $('#COURSE_CODE1').val(data.COURSE_CODE);
       $('#COURSE_NAME1').val(data.COURSE_NAME);
       $('#COURSE_DESC1').val(data.COURSE_DESC);
       $('#STATUS1').val(data.STATUS);
        $('.modal-title').text("Modify Course");
      }
    })
  });
</script>

<script type="text/javascript">
  $(document).on('click', '.deleteEntry', function(){
    var COURSE_ID = $(this).attr("COURSE_ID");
    Swal.fire({
      title: 'Delete Course?',
      text: "This action cannot be undone. Are you sure you want to delete this course?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.value) {
        window.location.href = "<?php echo WEB_ROOT; ?>module/course/controller.php?action=delete&id=" + COURSE_ID;
      }
    });
  });
</script>
