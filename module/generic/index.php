<?php
// ABADIES SOLUTIONS

require_once("../../include/initialize.php");
require_once("config.php");

$table = isset($_GET['t']) ? $_GET['t'] : '';
$cfg   = generic_table_config($table);

if (!$cfg) {
	// unknown / not-whitelisted table -> reuse the app's existing 404 page
	redirect(WEB_ROOT."module/error/index.php?view=list");
	exit;
}

$title   = $cfg['title'];
$header  = isset($_GET['view']) ? $_GET['view'] : '';
$content = 'list.php';

require_once("../../theme/template.php");
?>

<script type="text/javascript">
    $(document).ready(function() {
        var t = $('#tblgeneric').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                url: "<?php echo WEB_ROOT; ?>module/generic/generic_ajax.php?t=<?php echo urlencode($table); ?>",
                type: "POST"
            },
            "scrollX": true,
            "scrollY": "400px",
            "scrollCollapse": true
        });

        t.on('order.dt search.dt', function() {
            t.column(0, {search: 'applied', order: 'applied'}).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();
    });
</script>

<script type="text/javascript">
    $(document).on('click', '.editEntry', function() {
        var recId = $(this).attr('data-id');
        $.ajax({
            url: "<?php echo WEB_ROOT; ?>module/generic/generic_ajax.php?t=<?php echo urlencode($table); ?>",
            method: "POST",
            data: {record_id: recId},
            dataType: "json",
            success: function(data) {
                $.each(data, function(key, value) {
                    $('#edit_' + key).val(value);
                });
                $('#record_pk').val(recId);
                $('#editEntry').modal('show');
            }
        });
    });
</script>

<script type="text/javascript">
    $(document).on('click', '.deleteEntry', function() {
        var recId = $(this).attr('data-id');
        Swal.fire({
            title: 'Delete this record?',
            text: "This action cannot be undone. Are you sure you want to delete it?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.value) {
                window.location.href = "<?php echo WEB_ROOT; ?>module/generic/controller.php?action=delete&t=<?php echo urlencode($table); ?>&id=" + recId;
            }
        });
    });
</script>
