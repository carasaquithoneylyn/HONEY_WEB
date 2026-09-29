<?php
// ABADIES SOLUTIONS

require_once(__DIR__."/config.php");

$table = isset($_GET['t']) ? $_GET['t'] : '';
$cfg   = generic_table_config($table);
if (!$cfg) { redirect(WEB_ROOT."module/error/index.php?view=list"); exit; }

$rec     = new GenericRecord($table);
$columns = $rec->columns();

function generic_nice_label($field) {
	global $GENERIC_LABEL_OVERRIDES;
	foreach ($GENERIC_LABEL_OVERRIDES as $pattern => $niceLabel) {
		if (stripos($field, $pattern) !== false) { return $niceLabel; }
	}
	return ucwords(strtolower(str_replace('_', ' ', $field)));
}

/* fixed dropdown choices for a column (YEAR_LEVEL, SEMESTER, REMARKS, ...) */
function generic_choice_options($field) {
	global $GENERIC_FIELD_CHOICES;
	$key = strtoupper($field);
	return isset($GENERIC_FIELD_CHOICES[$key]) ? $GENERIC_FIELD_CHOICES[$key] : null;
}

/* pull [id => label] options for a foreign-key-looking column */
function generic_fk_options($field) {
	global $GENERIC_FK_MAP, $mydb;
	if (!isset($GENERIC_FK_MAP[$field])) { return null; }
	$fk = $GENERIC_FK_MAP[$field];
	if (!$mydb->tableExists($fk['table'])) { return null; }
	$mydb->setQuery("SELECT `".$fk['pk']."` as fk_id, ".$fk['label']." as fk_label FROM `".$fk['table']."` ORDER BY fk_id DESC");
	$rows = $mydb->loadResultList();
	$out = array();
	foreach ($rows as $r) { $out[$r->fk_id] = $r->fk_label; }
	return $out;
}

function generic_input_type($col) {
	$t = strtolower($col->Type);
	if (stripos($col->Field, 'PASSWORD') !== false) { return 'password'; }
	if (strpos($t, 'date') === 0 || strpos($t, 'timestamp') === 0) { return 'date'; }
	if (strpos($t, 'int') !== false || strpos($t, 'decimal') !== false || strpos($t, 'float') !== false || strpos($t, 'double') !== false) { return 'number'; }
	if (strpos($t, 'text') !== false) { return 'textarea'; }
	return 'text';
}

/* Renders one form-group for the Add or Edit modal. $suffix is appended to
   the DOM id (edit_ for the edit modal, empty for add) so both modals can
   live on the same page without id clashes; the `name` stays the raw
   column name in both so the controller can read $_POST[name] directly. */
function generic_render_field($col, $rec, $idPrefix = '') {
	$field = $col->Field;
	if ($field == $rec->pk && $rec->isAutoIncrement($field)) { return; }

	$domId = $idPrefix.$field;
	echo '<div class="col-sm-12"><div class="form-group">';
	echo '<label for="'.$domId.'" class="col-form-label col-form-label-sm">'.htmlspecialchars(generic_nice_label($field)).'</label>';

	if (strtoupper($field) === 'STATUS') {
		global $GENERIC_STATUS_CHOICES, $table;
		$statusOpts = isset($GENERIC_STATUS_CHOICES[$table])
			? $GENERIC_STATUS_CHOICES[$table]
			: array('Active', 'Inactive');
		echo '<select class="form-control form-control-sm" name="'.$field.'" id="'.$domId.'">';
		foreach ($statusOpts as $opt) {
			echo '<option value="'.htmlspecialchars($opt).'">'.htmlspecialchars($opt).'</option>';
		}
		echo '</select>';
		echo '</div></div>';
		return;
	}

	$choices = generic_choice_options($field);
	if ($choices !== null) {
		echo '<select class="form-control form-control-sm" name="'.$field.'" id="'.$domId.'">';
		foreach ($choices as $choice) {
			echo '<option value="'.htmlspecialchars($choice).'">'.htmlspecialchars($choice).'</option>';
		}
		echo '</select>';
		echo '</div></div>';
		return;
	}

	$fkOptions = generic_fk_options($field);
	if ($fkOptions !== null) {
		echo '<select class="form-control form-control-sm" name="'.$field.'" id="'.$domId.'">';
		echo '<option value="">-- Select '.htmlspecialchars(generic_nice_label($field)).' --</option>';
		foreach ($fkOptions as $optId => $optLabel) {
			echo '<option value="'.htmlspecialchars($optId).'">'.htmlspecialchars($optLabel).'</option>';
		}
		echo '</select>';
		echo '</div></div>';
		return;
	}

	$type = generic_input_type($col);
	if ($type === 'textarea') {
		echo '<textarea class="form-control form-control-sm" name="'.$field.'" id="'.$domId.'" placeholder="'.htmlspecialchars(generic_nice_label($field)).'"></textarea>';
	} else {
		echo '<input type="'.$type.'" class="form-control form-control-sm" name="'.$field.'" id="'.$domId.'" placeholder="'.htmlspecialchars(generic_nice_label($field)).'">';
	}
	echo '</div></div>';
}
?>
<section class="content">

  <!-- ABADIES SOLUTIONS -->

  <div class="container-fluid">
    <?php check_message(); ?>
    <div class="row">
      <div class="col-12">

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">List of <?php echo htmlspecialchars($cfg['title']); ?></h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table id="tblgeneric" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th width="5%">#</th>
                <?php foreach ($columns as $col): ?>
                  <th><?php echo htmlspecialchars(generic_nice_label($col->Field)); ?></th>
                <?php endforeach; ?>
                <th>Action</th>
              </tr>
              </thead>
              <tbody></tbody>
              <tfoot></tfoot>
            </table>
            <div class="btn-group">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#AddNewEntry">Add New</button>
            </div>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</section>

<!-----START of Add Form---->
<div class="modal fade" id="AddNewEntry">
  <div class="modal-dialog">
    <form action="controller.php?action=add&amp;t=<?php echo urlencode($table); ?>" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Add New <?php echo htmlspecialchars($cfg['title']); ?></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <?php foreach ($columns as $col) { generic_render_field($col, $rec, ''); } ?>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" name="save" type="submit">Save changes</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </form>
  </div>
  <!-- /.modal-dialog -->
</div>
<!-----End of Add Form---->

<!-----Start of Edit Form---->
<div class="modal fade" id="editEntry">
  <div class="modal-dialog">
    <form action="controller.php?action=edit&amp;t=<?php echo urlencode($table); ?>" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Modify <?php echo htmlspecialchars($cfg['title']); ?></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="record_pk" id="record_pk" value="">
            <?php foreach ($columns as $col) { generic_render_field($col, $rec, 'edit_'); } ?>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" name="edit" type="submit">Save changes</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </form>
  </div>
  <!-- /.modal-dialog -->
</div>
<!-----End of Edit Form---->
