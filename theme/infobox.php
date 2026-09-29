<section>

   <!-- ABADIES SOLUTIONS  -->
   
<div class="container-fluid">
        <h5 class="mb-2">Info Box</h5>
        <div class="row">
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
              <?php
              $Campus = new Campus();
              $status = $Campus->count_campus();
              ?>
              <span class="info-box-icon bg-success"><i class="far fa-envelope"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Campuses</span>
                <span class="info-box-number"><?php echo $status->STATUS; ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
              <?php
              $Admission = new Admission();
              $status = $Admission->count_discharge();
              ?>
              <span class="info-box-icon" style="background-color:#A9A9A9;"><i class="far fa-flag"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Discharge</span>
                <span class="info-box-number"><?php echo $status->STATUS; ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
              <?php
              $Admission = new Admission();
              $status = $Admission->count_for_MGH();
              ?>
              <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">For May Go Home</span>
                <span class="info-box-number"><?php echo $status->STATUS; ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
              <?php
              $Admission = new Admission();
              $status = $Admission->count_cleared();
              ?>
              <span class="info-box-icon"><i class="far fa-star"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Cleared</span>
                <span class="info-box-number"><?php echo $status->STATUS; ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
        </div>
        <div class="row">
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
              <?php
              $Admission = new Admission();
              $status = $Admission->count_MGH();
              ?>
              <span class="info-box-icon" style="background-color:yellow;"><i class="far fa-envelope"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">May Go Home</span>
                <span class="info-box-number"><?php echo $status->STATUS; ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
               <?php
              $Admission = new Admission();
              $status = $Admission->count_untagged_as_MGH();
              ?>
              <span class="info-box-icon" style="background-color: yellowgreen;"><i class="far fa-flag"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Untagged As May Go Home</span>
                <span class="info-box-number"><?php echo $status->STATUS; ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
               <?php
              $Admission = new Admission();
              $status = $Admission->count_cancelled();
              ?>
              <span class="info-box-icon bg-danger"><i class="far fa-copy"></i></span>

              <div class="info-box-content">0003722220
                <span class="info-box-text">Cancelled</span>
                <span class="info-box-number"><?php echo $status->STATUS; ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
              <?php
              $Admission = new Admission();
              $status = $Admission->count_died();
              ?>
              <span class="info-box-icon bg-dark"><i class="far fa-star"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Expired</span>
                <span class="info-box-number"><?php echo $status->STATUS; ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
        </div>
      </div>
    </section>