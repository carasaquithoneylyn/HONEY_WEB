=======================================================================
ABADIES SOLUTIONS
Student and Alumni Records Management System
Version 3.0.5
=======================================================================

Folder name : abadies-class
Database    : abadies_db
Stack       : PHP 8 + PDO / MariaDB, AdminLTE 3, Bootstrap 4,
              DataTables, SweetAlert2


-----------------------------------------------------------------------
1. INSTALL
-----------------------------------------------------------------------

STEP 1 - Copy the folder
    Extract the zip and place the "abadies-class" folder inside htdocs:

        C:\xampp\htdocs\abadies-class\

    You should end up with C:\xampp\htdocs\abadies-class\index.php

STEP 2 - Start XAMPP
    Open the XAMPP Control Panel and start Apache and MySQL.

STEP 3 - Import the database
    Open http://localhost/phpmyadmin
    Click the "Import" tab (do NOT create the database first, the file
    creates it for you).
    Choose file:  abadies-class\database\abadies_db.sql
    Click Go.

    You should see abadies_db appear on the left with 11 tables.

STEP 4 - Set up the vhost
    Follow  setup\httpd-vhosts.conf
    Then    setup\hosts.txt
    Restart Apache.

STEP 5 - Open the site

        http://abadies-class.local/

    Or, if you skip steps 4 and 5 entirely, this also works with no
    setup at all:

        http://localhost/abadies-class/

    The app detects which one you are using and adjusts its links
    automatically. Nothing in the code needs changing.


-----------------------------------------------------------------------
2. LOGIN
-----------------------------------------------------------------------

    Username: abadies
    Password: abadies

    The original admin account still works: admin / admin

    The other legacy accounts (jason, dfd, dfdf) carry over with their
    original password hashes. Those passwords were never in the dump,
    so if nobody remembers them, reset from Manage User Accounts.

    Change these before showing the system to anyone. Manage User
    Accounts is under Account Settings in the sidebar.


-----------------------------------------------------------------------
3. ENROLLMENT FLOW (TWO STAGES)
-----------------------------------------------------------------------

    Student > Reg          creates the record as RESERVED
       |                     - Academic Year, Semester, Course
       |                     - Year Level, Curriculum Yr, Category
       |                     - Date Reserved
       |                     - NO section, NO enrollment date yet
       v
    Enrollment > Sectioning  completes it as ENROLLED
       |                     - assigns the Section
       |                     - stamps Date Enrolled
       v
    Enrollment Details       attach the subjects
       v
    Grades                   encode the grades

The Enrollment screen has filter chips (All / Reserved / Enrolled) with
live counts, so you can see at a glance what is still waiting to be
sectioned.

Enrollment has its own module now (module/enrollment) instead of the
generic table builder, because the generic builder cannot produce a
Sectioning action. tblenrollment was removed from $GENERIC_TABLES so
there are not two competing ways to edit the same table.

Column notes:
    SECTION_ID and DATE_ENROLLED are NULLABLE. A reserved student has
    neither yet. This is the whole point of the two-stage flow.

    ENCODED_BY is a real foreign key to tblusers, recording who
    processed the record.

    UNIQUE KEY on (S_ID, SY_ID, SEMESTER) blocks the same student being
    reserved twice for the same term, at the database level.

    There is no DEPT_ID. This database has no department table, so the
    column would sit empty. Course already identifies the program.


-----------------------------------------------------------------------
4. THE DATABASE IS FULLY CONNECTED
-----------------------------------------------------------------------

All 11 tables are related. There are no orphan tables, so phpMyAdmin's
Designer view shows one connected diagram.

    tblusertype
        └── tblusers                (TYPEID)
              ├── tblstudent        (AddedBy)
              └── alumni_details    (AddedBy)

    tblcourses
        ├── tblstudent              (COURSE_ID)
        ├── tblsections             (COURSE_ID)
        ├── tblsubjects             (COURSE_ID)
        └── tblenrollment           (COURSE_ID)

    tblschoolyear
        ├── tblsections             (SY_ID)
        ├── tblenrollment           (SY_ID)
        └── tblgrades               (SY_ID)

    tblstudent
        ├── alumni_details          (S_ID)
        ├── tblenrollment           (S_ID)
        └── tblgrades               (S_ID)

    tblenrollment
        ├── tblenrollment_details   (ENROLLMENT_ID)
        └── tblgrades               (ENROLLMENT_ID)

    tblsubjects
        ├── tblenrollment_details   (SUBJECT_ID)
        └── tblgrades               (SUBJECT_ID)

    tblsections
        └── tblenrollment           (SECTION_ID)

18 foreign keys in total.

To see the diagram: phpMyAdmin > click abadies_db > Designer tab.


-----------------------------------------------------------------------
5. WHAT CHANGED FROM THE ORIGINAL BUILD
-----------------------------------------------------------------------

STUDENT MODULE - edit modal now prefills correctly
    * ajax.php now returns LNAME, SEX and BDAY, which it never did
      before. That is why Last Name, Gender and Date were blank.
    * The edit modal's gender and date fields were reusing the SAME
      HTML ids as the Add modal. Renamed to SEX1 and BDAY1.
    * The UID box is hidden now instead of being a visible text field
      floating at the top of the form.
    * controller.php actually saves gender and date on edit. It was
      silently dropping both.
    * The empty gender option had no value, so leaving it alone posted
      the literal text "Select Gender". That is how a student ended up
      with SEX = "Select Gen" in the old database.

DATABASE
    * New name abadies_db, so it no longer collides with alumni_db.
    * All legacy tables linked (see section 3).
    * tblstudent.IDNO changed from int to varchar(20). An int cannot
      hold a 12-digit LRN, it overflows at 2147483647.
    * tblstudent charset changed from latin1 to utf8mb4 to match every
      other table.
    * tblsubjects was completely empty, which left the Subject module,
      Enrollment Details and Grades with nothing to reference. Seeded
      with 10 subjects across the three courses.
    * Sample enrollment and grade rows added so the modules are not
      blank on first run.
    * Blank tblusertype STATUS values normalized to Active / Inactive.
    * Both school years were marked Active. Only 2025-2026 is now.

GENERIC MODULE
    * Empty date fields no longer write 0000-00-00. Blank values now
      become NULL, or fall back to the column DEFAULT.
    * The STATUS dropdown is no longer hardcoded to Active/Inactive
      everywhere. Enrollment correctly shows Enrolled/Dropped/Completed.
    * AddedBy and TYPEID columns render as proper dropdowns.

PHP 8 COMPATIBILITY
    * strftime() replaced with date() in 7 places. strftime was
      deprecated in PHP 8.1 and removed in 8.4.
    * module/error/index.php opened with <? instead of <?php, so the
      404 page printed its own source code when short_open_tag is off.

LOGIN
    * Only Administrator, Doctor and Staff were let through. Any other
      user type was authenticated and then left staring at the login
      page. Any active account now reaches the dashboard.
    * A failed login redirected to index.php, which redirected back to
      login.php. It now stays put and shows a clear message.

SCHOOL LOGO
    * csr-scc.png is now the St. Joseph Catholic School of Sagay, Inc.
      seal. The filename was kept deliberately so the three places that
      reference it (theme/template.php, login.php, module/about/list.php)
      did not need touching. Replace that one file any time you want a
      different school and everything follows.
    * The white area outside the seal circle was made transparent, so it
      sits cleanly on the dark sidebar instead of showing a white box.

SIZE
    * Removed the untouched AdminLTE demo (pages/, build/,
      index11.html) and 34 plugin libraries that nothing references.
    * csr-scc.png was a 3.1 MB 2048px image being displayed at 20%
      width. Resized to 300px, now 143 KB.
    * Total went from roughly 70 MB to 25 MB.


-----------------------------------------------------------------------
6. STILL OUTSTANDING
-----------------------------------------------------------------------

These are known and were NOT changed, because fixing them properly
means reworking how the whole app talks to the database. Listed so you
know they exist.

    * SQL injection. User::AuthenticateUser and the older *_ajax.php
      files build queries by string concatenation with no binding. The
      login form is exploitable. Fixing this means moving the Database
      class to prepared statements with bound parameters.
    * Passwords are unsalted SHA1. Should be password_hash() with
      password_verify().
    * escape_value() calls mysql_real_escape_string, which does not
      exist in PHP 8, so it silently falls back to addslashes().
    * The User, Student and Details classes declare no properties, so
      every save creates dynamic properties. Deprecated in PHP 8.2.
      Course and Subject were written correctly and are fine.
    * The Student module label says "Date Started" but the column is
      BDAY and the list header says BDAY. Decide which one it is and
      make the label match.


-----------------------------------------------------------------------
ABADIES SOLUTIONS
-----------------------------------------------------------------------
