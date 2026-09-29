<?php

//ABADIES SOLUTIONS

require_once("../../include/initialize.php");
// if (!isset($_SESSION['ACCOUNT_ID'])){
  //    redirect(web_root."/index.php");
//     }

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
 $title="User Module"; 
 $header=$view; 
switch ($view) {
	case 'list' :
		$content    = 'list.php';		
		break;

	case 'add' :
		$content    = 'add.php';		
		break;

	case 'edit' :
		$content    = 'edit.php';		
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
            var t = $('#tbluser').DataTable( {
            "processing":true,
            "serverSide":true,
            "order":[],
            "ajax":{
              url:"<?php echo WEB_ROOT; ?>module/user/user_ajax.php",
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
            $(function () {
                $('#reservationdate').datetimepicker({
                    format: 'L'
                });
            });
        </script>
        <script type="text/javascript">
          $(document).ready( function() {
      $(document).on('change', '.btn-file :file', function() {
    var input = $(this),
      label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [label]);
    });

    $('.btn-file :file').on('fileselect', function(event, label) {
        
        var input = $(this).parents('.input-group').find(':text'),
            log = label;
        
        if( input.length ) {
            input.val(log);
        } else {
            if( log ) alert(log);
        }
      
    });
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('#img-upload').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imgInp").change(function(){
        readURL(this);
    });   
  });
        </script>
<script type="text/javascript">
  $(document).on('click', '.editEntry', function(){
    var uid = $(this).attr("UID");
    $.ajax({
      url:"<?php echo WEB_ROOT; ?>module/user/user_ajax.php",
      method:"POST",
      data:{UID:uid},
      dataType:"json",
      success:function(data)
      {
       $('#editEntry').modal('show');
       $('#UID').val(data.UID);
       $('#FULLNAME').val(data.DISPLAYNAME);

       $("#TYPE option[value='" + data.TYPE +"']").attr("selected","selected");
       if (data.STATUSACTIVE == 1) {
        $('#customSwitch3').prop("checked", true);
       }else{
        $('#customSwitch3').prop("checked", false);
       }
       $('#USERNAME').val(data.USERNAME);
       $('.modal-title').text("Modify User Account");
      

       /* $('#user_id').val(user_id);
        $('#action').val("Edit");
        $('#operation').val("Edit");*/
      }
    })
  });
</script>

<script type="text/javascript">
  $(document).on('click', '.changepass', function(){
    var uid = $(this).attr("UID");
    $.ajax({
      url:"<?php echo WEB_ROOT; ?>module/user/user_ajax.php",
      method:"POST",
      data:{UID:uid},
      dataType:"json",
      success:function(data)
      {
       $('#changepass').modal('show');
       $('#UIDpas').val(data.UID);
       $('#dNAME').val(data.DISPLAYNAME);

       $("#TYPE option[value='" + data.TYPE +"']").attr("selected","selected");
       if (data.STATUSACTIVE == 1) {
        $('#customSwitch3').prop("checked", true);
       }else{
        $('#customSwitch3').prop("checked", false);
       }
       $('#UNAME').val(data.USERNAME);
       $('.modal-title').text("Change User Password");
      

       /* $('#user_id').val(user_id);
        $('#action').val("Edit");
        $('#operation').val("Edit");*/
      }
    })
  });
</script>

<script type="text/javascript">
  $(document).on('click', '.deleteEntry', function(){
    var uid = $(this).attr("UID");
    Swal.fire({
      title: 'Delete User Account?',
      text: "This action cannot be undone. Are you sure you want to delete this user account?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.value) {
        window.location.href = "<?php echo WEB_ROOT; ?>module/user/controller.php?action=delete&id=" + uid;
      }
    });
  });
</script>