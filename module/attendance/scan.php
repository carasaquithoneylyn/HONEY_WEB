<section class="content">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-md-8">

        <div class="card card-primary card-outline text-center">
          <div class="card-body py-4">

            <h3>RFID Attendance Kiosk</h3>
            <p class="text-muted">Current time: <strong id="kioskClock">-</strong></p>

            <!-- Action selector: staff picks which slot this next tap is for.
                 The server still auto-corrects this if the clock disagrees
                 (e.g. tapping "AM Time In" at 7 PM records PM Time In instead). -->
            <div class="btn-group btn-group-lg mb-3" role="group" id="actionButtons">
              <button type="button" class="btn btn-outline-primary action-btn" data-action="AM_TIME_IN">AM Time In</button>
              <button type="button" class="btn btn-outline-primary action-btn" data-action="AM_TIME_OUT">AM Time Out</button>
              <button type="button" class="btn btn-outline-primary action-btn" data-action="PM_TIME_IN">PM Time In</button>
              <button type="button" class="btn btn-outline-primary action-btn" data-action="PM_TIME_OUT">PM Time Out</button>
            </div>
            <p class="text-muted"><small>Selected action: <strong id="selectedActionLabel">AM Time In</strong> - now tap the card.</small></p>

            <div class="callout callout-info text-left py-2 mt-3 mb-4">
              <small>
                <strong>Schedule:</strong> AM Time In 8:00 AM (grace until 8:15) &middot;
                AM Time Out 12:00 PM &middot; PM Time In 1:00 PM &middot; PM Time Out 5:00 PM.
              </small>
            </div>

            <!-- Scanned-person dashboard: photo + only the fields that matter
                 here, styled like the Student profile card. Replaced on
                 every scan. -->
            <div id="scanDashboard" class="d-none">
              <div class="card card-primary card-outline mx-auto mb-3" style="max-width:340px;">
                <div class="card-body text-center">
                  <img id="dashPhoto" src="" class="img-circle" style="width:90px; height:90px; object-fit:cover;" alt="Staff photo">
                  <h4 class="mt-2 mb-0" id="dashName">-</h4>
                  <p class="text-muted mb-3">Username: <span id="dashUsername">-</span></p>
                  <ul class="list-group list-group-unbordered text-left">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <b>Role</b>
                      <span class="text-muted" id="dashRole">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <b>Status</b>
                      <span class="text-muted" id="dashStatus">-</span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <div id="scanFeedback" class="alert alert-secondary" style="min-height: 3rem;">
              Waiting for a card...
            </div>

            <!-- Always-focused, effectively invisible to the person standing
                 at the kiosk - the RFID reader types into this like a keyboard. -->
            <input type="text" id="rfidInput" autocomplete="off"
                   style="position:absolute; opacity:0; height:1px; width:1px; pointer-events:none;">

            <a href="<?php echo WEB_ROOT; ?>module/attendance/index.php" class="btn btn-default btn-sm mt-3">
              <i class="fa fa-list"></i> View Attendance Report
            </a>

          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- Loud, unmistakable feedback on every tap -->
<audio id="soundGranted" src="<?php echo WEB_ROOT; ?>module/attendance/sounds/access_granted.mp3" preload="auto"></audio>
<audio id="soundDenied" src="<?php echo WEB_ROOT; ?>module/attendance/sounds/access_denied.mp3" preload="auto"></audio>