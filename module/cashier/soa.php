<?php
/* =====================================================================
   ABADIES SOLUTIONS - Statement of Account (printable)

   Standalone page on purpose: it renders its own clean document so the
   print output is the statement alone, not the admin shell. It still
   requires a logged-in session.

   Sources, all from the existing system:
     tblenrollment / tblstudent / tblcourses / tblschoolyear - who + term
     tblfeeschedule (payments module) ...... tuition for course + year level
     tblfeeitems    (cashier_soa.sql) ...... itemised standard/non-standard/other
     tblpayments    (payments module) ...... amount already paid
   ===================================================================== */
require_once("../../include/initialize.php");
global $mydb;

if (!isset($_SESSION['UID'])) { redirect(WEB_ROOT."login.php"); exit; }

$eid = isset($_GET['eid']) ? intval($_GET['eid']) : 0;

$found = false;
$rec   = null;
if ($eid > 0) {
	$mydb->setQuery("SELECT e.ENROLLMENT_ID, e.COURSE_ID, e.YEAR_LEVEL, e.SEMESTER,
			e.CATEGORY, e.STATUS, e.DATE_ENROLLED, e.DATE_RESERVED,
			s.IDNO, s.LNAME, s.FNAME, s.MNAME,
			c.COURSE_CODE, c.COURSE_NAME, sy.SCHOOL_YEAR
		FROM `tblenrollment` e
		JOIN `tblstudent`    s  ON s.S_ID      = e.S_ID
		JOIN `tblcourses`    c  ON c.COURSE_ID = e.COURSE_ID
		JOIN `tblschoolyear` sy ON sy.SY_ID    = e.SY_ID
		WHERE e.ENROLLMENT_ID = '".$eid."' LIMIT 1");
	$list = $mydb->loadResultList();
	if (count($list) >= 1) { $rec = $list[0]; $found = true; }
}

/* ---------- Assessment figures ------------------------------------- */
$standard    = array();
$nonstandard = array();
$other       = array();
$payments    = array();
$tuition     = 0.0;
$tuitionFromItems = 0.0;
$totalPaid   = 0.0;

if ($found) {

	$yl = $rec->YEAR_LEVEL;

	/* Itemised fees for this year level. */
	if ($mydb->tableExists('tblfeeitems')) {
		$mydb->setQuery("SELECT CATEGORY, ITEM_NAME, AMOUNT
			FROM `tblfeeitems`
			WHERE YEAR_LEVEL = '".$mydb->escape_value($yl)."'
			ORDER BY SORT_ORDER ASC, FEEITEM_ID ASC");
		foreach ($mydb->loadResultList() as $it) {
			$amt = (float)$it->AMOUNT;
			switch ($it->CATEGORY) {
				case 'STANDARD':    $standard[]    = array($it->ITEM_NAME, $amt); break;
				case 'NONSTANDARD': $nonstandard[] = array($it->ITEM_NAME, $amt); break;
				case 'OTHER':       $other[]       = array($it->ITEM_NAME, $amt); break;
				case 'TUITION':     $tuitionFromItems += $amt; break;
			}
		}
	}

	/* Tuition is per COURSE + YEAR LEVEL, taken from the fee schedule
	   (edited in Cashier > Fee Setup > Tuition Fee per Course). Any TUITION
	   rows still in tblfeeitems are used only as a fallback. */
	$tuition = 0.0;
	if ($mydb->tableExists('tblfeeschedule')) {
		$mydb->setQuery("SELECT TUITION_FEE FROM `tblfeeschedule`
			WHERE COURSE_ID = '".intval($rec->COURSE_ID)."'
			  AND YEAR_LEVEL = '".$mydb->escape_value($yl)."' LIMIT 1");
		$fs = $mydb->loadResultList();
		if (count($fs) >= 1) { $tuition = (float)$fs[0]->TUITION_FEE; }
	}
	if ($tuition <= 0) { $tuition = $tuitionFromItems; }

	/* Every payment collected for this enrolment - listed on the SOA so it
	   reflects what was paid, and summed to reduce the balance. */
	if ($mydb->tableExists('tblpayments')) {
		$mydb->setQuery("SELECT p.DATE_PAID, p.OR_NUMBER, p.FEE_TYPE, p.AMOUNT, u.DISPLAYNAME
			FROM `tblpayments` p
			LEFT JOIN `tblusers` u ON u.UID = p.RECEIVED_BY
			WHERE p.ENROLLMENT_ID = '".$eid."'
			ORDER BY p.DATE_PAID ASC, p.PAYMENT_ID ASC");
		foreach ($mydb->loadResultList() as $p) {
			$payments[] = $p;
			$totalPaid += (float)$p->AMOUNT;
		}
	}
}

$stdTotal   = 0.0; foreach ($standard    as $r) { $stdTotal   += $r[1]; }
$nonTotal   = 0.0; foreach ($nonstandard as $r) { $nonTotal   += $r[1]; }
$otherTotal = 0.0; foreach ($other       as $r) { $otherTotal += $r[1]; }

$stdNonTotal = $stdTotal + $nonTotal;
$totalFees   = $tuition + $stdNonTotal + $otherTotal;
$balance     = $totalFees - $totalPaid;

/* Peso, plain figure (blank when a labelled item has no amount, like the
   paper form). */
function peso($n)  { return '&#8369; '.number_format((float)$n, 2); }
function money($n) { return number_format((float)$n, 2); }
function fig($n)   { return ((float)$n == 0.0) ? '' : number_format((float)$n, 2); }

$preparedBy = isset($_SESSION['DISPLAYNAME']) ? $_SESSION['DISPLAYNAME'] : 'Cashier';
$fullName   = $found ? trim($rec->LNAME.', '.$rec->FNAME.' '.$rec->MNAME) : '';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Statement of Account<?php echo $found ? ' - '.htmlspecialchars($rec->IDNO) : ''; ?></title>
  <style>
    :root { --ink:#1a1a1a; --line:#333; --muted:#666; --accent:#c31f5e; --brand:#1c2f9e; }
    * { box-sizing: border-box; }
    body {
      font-family: "Segoe UI", Arial, Helvetica, sans-serif;
      color: var(--ink); background: #e9ecef; margin: 0; padding: 24px;
    }
    .sheet {
      background: #fff; width: 780px; max-width: 100%; margin: 0 auto;
      padding: 34px 40px 28px; box-shadow: 0 2px 14px rgba(0,0,0,.15);
    }
    .head { display: flex; align-items: center; gap: 16px; border-bottom: 3px double var(--line); padding-bottom: 14px; }
    .head img { width: 66px; height: 66px; object-fit: contain; }
    .head .org { line-height: 1.25; }
    .head .org .name { font-size: 20px; font-weight: 700; letter-spacing: .5px; color: var(--brand); }
    .head .org .sub  { font-size: 12px; color: var(--muted); }
    .doc-title { text-align: center; font-size: 17px; font-weight: 700; letter-spacing: 3px; margin: 16px 0 4px; color: var(--brand); }
    .doc-sub   { text-align: center; font-size: 12px; color: var(--muted); margin-bottom: 18px; }

    .meta { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 13px; }
    .meta td { padding: 3px 6px; vertical-align: top; }
    .meta .lbl { color: var(--muted); width: 90px; }
    .meta .val { font-weight: 600; border-bottom: 1px solid #ccc; }

    table.soa { width: 100%; border-collapse: collapse; font-size: 13px; }
    table.soa th, table.soa td { border: 1px solid var(--line); padding: 5px 10px; }
    table.soa thead th { background: #f1f1f1; text-align: left; letter-spacing: .5px; }
    table.soa td.amt { text-align: right; width: 150px; font-variant-numeric: tabular-nums; }
    tr.section td  { font-weight: 700; background: #fafafa; }
    tr.subtot td   { font-weight: 700; }
    tr.grand td    { font-weight: 700; color: var(--accent); font-size: 14px; border-top: 2px solid var(--line); }
    tr.spacer td   { height: 10px; border-left: 1px solid var(--line); border-right: 1px solid var(--line); border-top: none; border-bottom: none; }
    td.item { padding-left: 22px; }

    .summary { width: 340px; margin-left: auto; margin-top: 18px; border-collapse: collapse; font-size: 13px; }
    .summary td { padding: 5px 10px; border: 1px solid var(--line); }
    .summary td.k { color: var(--muted); }
    .summary td.v { text-align: right; font-weight: 700; font-variant-numeric: tabular-nums; }
    .summary tr.bal td { color: var(--accent); font-size: 15px; }

    .signs { display: flex; justify-content: space-between; margin-top: 46px; font-size: 12px; }
    .signs .box { width: 46%; text-align: center; }
    .signs .line { border-top: 1px solid var(--line); padding-top: 4px; }
    .foot-note { margin-top: 22px; font-size: 11px; color: var(--muted); text-align: center; }

    .toolbar { width: 780px; max-width: 100%; margin: 0 auto 16px; display: flex; gap: 10px; justify-content: flex-end; }
    .btn { border: 0; border-radius: 4px; padding: 9px 16px; font-size: 13px; cursor: pointer; color: #fff; text-decoration: none; display: inline-block; }
    .btn-print { background: #007bff; }
    .btn-back  { background: #6c757d; }
    .empty { text-align:center; padding:60px 20px; color:var(--muted); }

    @media print {
      body { background: #fff; padding: 0; }
      .toolbar { display: none !important; }
      .sheet { box-shadow: none; width: auto; padding: 0 6mm; }
      @page { size: A4 portrait; margin: 12mm; }
    }
  </style>
</head>
<body>

<div class="toolbar">
  <a href="<?php echo WEB_ROOT; ?>module/cashier/index.php" class="btn btn-back">&larr; Back</a>
  <?php if ($found) { ?><a href="#" onclick="window.print();return false;" class="btn btn-print">&#128424; Print</a><?php } ?>
</div>

<div class="sheet">

  <?php if (!$found) { ?>
    <div class="empty">
      <h3>Statement of Account</h3>
      <p>That enrolment record could not be found. Go back and choose a student from the list.</p>
    </div>
  <?php } else { ?>

  <div class="head">
    <img src="<?php echo WEB_ROOT; ?>csr-scc.png" alt="Logo">
    <div class="org">
      <div class="name">ABADIES SOLUTIONS</div>
      <div class="sub">Student and Alumni Records Management System</div>
      <div class="sub">Office of the Cashier</div>
    </div>
  </div>

  <div class="doc-title">STATEMENT OF ACCOUNT</div>
  <div class="doc-sub">School Year <?php echo htmlspecialchars($rec->SCHOOL_YEAR); ?> &middot; <?php echo htmlspecialchars($rec->SEMESTER); ?></div>

  <table class="meta">
    <tr>
      <td class="lbl">Student</td>
      <td class="val"><?php echo htmlspecialchars($fullName); ?></td>
      <td class="lbl">ID No.</td>
      <td class="val"><?php echo htmlspecialchars($rec->IDNO); ?></td>
    </tr>
    <tr>
      <td class="lbl">Course</td>
      <td class="val"><?php echo htmlspecialchars($rec->COURSE_CODE.' - '.$rec->COURSE_NAME); ?></td>
      <td class="lbl">Year Level</td>
      <td class="val"><?php echo htmlspecialchars($rec->YEAR_LEVEL); ?></td>
    </tr>
    <tr>
      <td class="lbl">Category</td>
      <td class="val"><?php echo htmlspecialchars($rec->CATEGORY); ?></td>
      <td class="lbl">Status</td>
      <td class="val"><?php echo htmlspecialchars($rec->STATUS); ?></td>
    </tr>
  </table>

  <table class="soa">
    <thead>
      <tr><th>ITEMS</th><th class="amt">AMOUNT</th></tr>
    </thead>
    <tbody>
      <tr class="subtot">
        <td>TUITION FEE</td>
        <td class="amt"><?php echo fig($tuition); ?></td>
      </tr>

      <tr class="spacer"><td></td><td></td></tr>

      <tr class="section"><td>STANDARD SCHOOL FEES</td><td class="amt"></td></tr>
      <?php foreach ($standard as $r) { ?>
      <tr><td class="item"><?php echo htmlspecialchars($r[0]); ?></td><td class="amt"><?php echo fig($r[1]); ?></td></tr>
      <?php } ?>
      <tr class="subtot"><td>TOTAL STANDARD FEES</td><td class="amt"><?php echo money($stdTotal); ?></td></tr>

      <tr class="spacer"><td></td><td></td></tr>

      <tr class="section"><td>NON-STANDARD SCHOOL FEES</td><td class="amt"></td></tr>
      <?php foreach ($nonstandard as $r) { ?>
      <tr><td class="item"><?php echo htmlspecialchars($r[0]); ?></td><td class="amt"><?php echo fig($r[1]); ?></td></tr>
      <?php } ?>
      <tr class="subtot"><td>TOTAL NON-STANDARD FEES</td><td class="amt"><?php echo money($nonTotal); ?></td></tr>

      <tr class="spacer"><td></td><td></td></tr>

      <tr class="subtot"><td>TOTAL STANDARD &amp; NON-STANDARD FEES</td><td class="amt"><?php echo money($stdNonTotal); ?></td></tr>
      <?php foreach ($other as $r) { ?>
      <tr class="subtot"><td><?php echo htmlspecialchars(strtoupper($r[0])); ?></td><td class="amt"><?php echo money($r[1]); ?></td></tr>
      <?php } ?>

      <tr class="grand"><td>TOTAL FEES</td><td class="amt"><?php echo money($totalFees); ?></td></tr>
    </tbody>
  </table>

  <?php if (count($payments) > 0) { ?>
  <div style="margin-top:20px;">
    <div style="font-weight:700; letter-spacing:1px; color:var(--brand); margin-bottom:6px;">PAYMENTS RECEIVED</div>
    <table class="soa">
      <thead>
        <tr>
          <th style="width:110px;">Date</th>
          <th style="width:120px;">OR No.</th>
          <th>Fee Type</th>
          <th>Received By</th>
          <th class="amt">Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($payments as $p) { ?>
        <tr>
          <td><?php echo htmlspecialchars($p->DATE_PAID); ?></td>
          <td><?php echo htmlspecialchars($p->OR_NUMBER); ?></td>
          <td><?php echo htmlspecialchars($p->FEE_TYPE); ?></td>
          <td><?php echo htmlspecialchars($p->DISPLAYNAME === null ? '-' : $p->DISPLAYNAME); ?></td>
          <td class="amt"><?php echo money($p->AMOUNT); ?></td>
        </tr>
        <?php } ?>
        <tr class="subtot"><td colspan="4">TOTAL PAID</td><td class="amt"><?php echo money($totalPaid); ?></td></tr>
      </tbody>
    </table>
  </div>
  <?php } else { ?>
  <p style="margin-top:18px; font-size:12px; color:var(--muted); font-style:italic;">No payments recorded yet for this enrolment.</p>
  <?php } ?>

  <table class="summary">
    <tr><td class="k">Total Assessment</td><td class="v"><?php echo peso($totalFees); ?></td></tr>
    <tr><td class="k">Less: Payments</td><td class="v"><?php echo peso($totalPaid); ?></td></tr>
    <tr class="bal"><td class="k">Balance Due</td><td class="v"><?php echo peso($balance); ?></td></tr>
  </table>

  <div class="signs">
    <div class="box"><div class="line">Cashier: <?php echo htmlspecialchars($preparedBy); ?></div></div>
    <div class="box"><div class="line">Student / Parent Signature</div></div>
  </div>

  <div class="foot-note">
    Generated on <?php echo date('F d, Y g:i A'); ?> &middot; This statement is system-generated and valid without alteration.
  </div>

  <?php } ?>
</div>

</body>
</html>
