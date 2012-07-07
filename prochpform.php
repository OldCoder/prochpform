<?php
// prochpform.php - Part of a simple HTML-PHP forms system
// Author:   OldCoder from http://oldcoder.org/
// License:  Creative Commons Attribution-ShareAlike 3.0 Unported
// Revision: 120706

//--------------------------------------------------------------------
//                             overview
//--------------------------------------------------------------------

// "prochpform.html" and  "prochpform.php" are a simple HTML-PHP forms
// system. The HTML file illustrates how a form may be set up and used
// to send data to the PHP script. The PHP script illustrates how data
// submitted via a form  may be retrieved, displayed, mailed to a spe-
// cified target, and/or stored in a MySQL database.

//--------------------------------------------------------------------
//                          technical notes
//--------------------------------------------------------------------

// 1. The  mail  feature can only be used  on systems that have  (a) a
// standard mail system installed and  (b) a copy of PHP that has mail
// support enabled.

// 2. The  MySQL feature can only be used  on systems that have  (a) a
// copy of MySQL installed and  (b) a copy of PHP that  has MySQL sup-
// port enabled.

// 3. If  MySQL support is requested,  the system administrator is re-
// sponsible for creating a database.  This program  handles the crea-
// tion of the required table.

// 4. For MySQL support to work,  the  associated "$MYSQL_" parameters
// must be set properly. For more information, see the parameters sec-
// tion.

// 5. If MySQL support is requested,  and  if the set of fields speci-
// fied by the HTML form changes,  the existing  MySQL  database  will
// need to be deleted and recreated.

// 6. The HTML form may contain  any reasonable number of fields.  All
// fields may be optional or, alternatively, one or more of the fields
// may be designated  as  "required".  For  more information,  see the
// parameters section below.

// 7. If  a data value begins with  "(" it is assumed to be a "prompt"
// string  that the HTML form displayed in the associated input field.
// In this case, the value is discarded.

//--------------------------------------------------------------------
//                             disclaimer
//--------------------------------------------------------------------

// This software is provided on an AS IS basis with ABSOLUTELY NO WAR-
// RANTY.  The entire risk as to the  quality and  performance of  the
// software is with you.  Should the software prove defective, you as-
// sume the cost of all necessary servicing, repair or correction.  In
// no event will any of the developers,  or any other party, be liable
// to anyone for damages arising out of use of the software, or  inab-
// ility to use the software.

//--------------------------------------------------------------------
//                             parameters
//--------------------------------------------------------------------

// Set $AFIELDS  to a list of the names of all fields that are set  by
// the HTML form used.

// Set $RFIELDS  to a list of the names of the fields that are requir-
// ed.

// Note: Presently, order here doesn't need to match order in the HTML
// form.

$AFIELDS = array
(
    "name", "email", "food", "wish", "remarks"
);

$RFIELDS = array
(
    "name", "email", "food"
);

//--------------------------------------------------------------------

// If you'd like  to display the  form results in the HTML output, set
// $DISPLAY_FLAG to TRUE. Otherwise, set this parameter to FALSE.

$DISPLAY_FLAG = TRUE;

//--------------------------------------------------------------------

// If you'd like to mail the form results somewhere, set the following
// parameters appropriately.  Note: If $MAIL_FLAG is FALSE,  the other
// parameters are not used.

// $MAIL_FLAG = TRUE to enable mail or FALSE otherwise
// $MAIL_TO   = Email "To"   address to use
// $MAIL_FROM = Email "From" address to use
// $MAIL_SUBJ = Subject line         to use

   $MAIL_FLAG = FALSE;
   $MAIL_TO   = "root@localhost";
   $MAIL_FROM = "root@localhost";
   $MAIL_SUBJ = "Form results";

//--------------------------------------------------------------------

// If you'd like to save the form results in a MySQL database, set the
// following parameters appropriately.  Note: If $MYSQL_FLAG is FALSE,
// the other parameters are  not used.  Also see the  technical  notes
// near the start of this file.

// $MYSQL_FLAG     = TRUE to enable MySQL or FALSE otherwise
// $MYSQL_DATABASE = MySQL database: database name
// $MYSQL_TABLE    = MySQL database: table    name
// $MYSQL_USER     = MySQL database: user     name
// $MYSQL_PASS     = MySQL database: password
// $MYSQL_HOST     = MySQL database: host     name

$MYSQL_FLAG     = FALSE;
$MYSQL_DATABASE = "prochpform";
$MYSQL_TABLE    = "prochpform";
$MYSQL_USER     = "prochpform";
$MYSQL_PASS     = "prochpform";
$MYSQL_HOST     = "localhost";

//--------------------------------------------------------------------
//                        output initial HTML
//--------------------------------------------------------------------
?>
<html>
<head>
<title>Form results</title>
<style type="text/css">
body {
    font-size:  17px;
    margin:     55px;
    text-align: justify;
}
</style>
</head>
<body>
<?php
//--------------------------------------------------------------------
// Support routine.

// "trailer" outputs the HTML code needed to complete the current doc-
// ment and terminates the caller.

function trailer() {
    print <<<END
</body>
</html>
END;
    print "\n";
    exit (0);
}

//--------------------------------------------------------------------
// Verify that all fields exist.

reset   ($AFIELDS);             // Reset array for traversal
foreach ($AFIELDS as $name)     // Process all  entries
{                               // Process next entry
                                // Does field exist?
    if (!isset ($_POST [$name]))
    {                           // No - Output an error message
        print <<<END
<p>Error: One of two problems has occurred: 1. This PHP script
has been called directly; this is not allowed.
2. Or there is a problem with the HTML form used.</p>
END;
        print "\n";
        trailer();              // Finish and exit
    }
}

//--------------------------------------------------------------------
// Verify that expected fields have data.

$error = FALSE;                 // Reset an error flag
reset   ($RFIELDS);             // Reset array for traversal
foreach ($RFIELDS as $name)     // Process all  entries
{                               // Process next entry
                                // Value provided for field
    $value = trim ($_POST [$name]);
                                // See technical notes
    if (!strncmp ($value, "(", 1)) { $value = ""; }

    if (!strlen ($value))
    {                           // No data is provided for this entry
        $error = TRUE;          // Set error flag
                                // Output an error message
        print <<<END
<p>Error: The required field <em>$name</em> has not been set.</p>
END;
        print "\n";
    }
}

if ($error) { trailer(); }      // Exit if there was an error

//--------------------------------------------------------------------
// If requested, display form data in HTML output.

if ($DISPLAY_FLAG)              // Is display requested?
{                               // Yes
    print <<<END
<p>The form provided the following data:</p>
END;
    print "\n";
    reset   ($AFIELDS);         // Reset array for traversal
    foreach ($AFIELDS as $name) // Process all  entries
    {                           // Process next entry
        $value = trim ($_POST [$name]);
                                // See technical notes
        if (!strncmp ($value, "(", 1)) { $value = ""; }
        print <<<END
<p>Field $name: $value</p>
END;
        print "\n";
    }
}

//--------------------------------------------------------------------
// If requested, forward data using email.

if ($MAIL_FLAG)                 // Is email requested?
{                               // Yes
    $text = "";                 // Reset a text buffer
    reset   ($AFIELDS);         // Reset array for traversal
    foreach ($AFIELDS as $name) // Process all  entries
    {                           // Process next entry
        $value = trim ($_POST [$name]);
                                // See technical notes
        if (!strncmp ($value, "(", 1)) { $value = ""; }
        $text  = $text . <<<END
Field $name: $value
END;
        $text  = $text . "\n";
    }
                                // Mail headers
    $headers = 'From: '     . $MAIL_FROM . "\r\n" .
               'Reply-To: ' . $MAIL_FROM . "\r\n" .
               'X-Mailer: PHP/' . phpversion();

                                // Send mail
    @mail ($MAIL_TO, $MAIL_SUBJ, $text, $headers);
                                // Output a status message
    print <<<END
<p>Mail has been sent</p>
END;
    print "\n";
}

//--------------------------------------------------------------------
// If requested, add data to a MySQL database.

if ($MYSQL_FLAG)                // Is MySQL support requested?
{                               // Yes
                                // Try to set up database access
    $dbcon  = @mysql_connect ($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASS);
    $result = @mysql_select_db ($MYSQL_DATABASE);

    if (!$dbcon || !$result)    // Is database access set up?
    {                           // No  - Error
                                // Output error message
        print <<<END
<p>Error: Can't connect to specified MySQL database.
Check the parameters used in the PHP script.
</p>
END;
        print "\n";
        trailer();              // Finish and exit
    }
                                // Command that will access table
    $query = "SELECT * FROM $MYSQL_TABLE";

    if (!@mysql_query ($query)) // Can table be accessed?
    {                           // No  - Try to create it
        $query = <<<END
CREATE TABLE prochpform(
id INT NOT NULL AUTO_INCREMENT,
PRIMARY KEY(id)
END;
        reset   ($AFIELDS);     // Reset array for traversal
                                // Process all  entries
        foreach ($AFIELDS as $name)
        {                       // Process next entry
            $query = $query . <<<END
, $name varchar(512)
END;
        }

        $query = $query . ");";
                                // Able to create table?
        if (@mysql_query ($query))
        {                       // Yes - Output success message
            print <<<END
<p>Created MySQL database table $MYSQL_TABLE</p>
END;
        }
        else
        {                       // No  - Output error   message
            print <<<END
<p>Error: Unable to create MySQL database table</p>
END;
            print "\n";
            mysql_close();      // Close database
            trailer();          // Finish and exit
        }
    }
                                // Initialize query string
    $query   = <<<END
INSERT INTO $MYSQL_TABLE(
END;
                                // Initialize values part
    $qvalues = <<<END
VALUES(
END;
    reset   ($AFIELDS);         // Reset array for traversal
                                // Process all  entries
    foreach ($AFIELDS as $name)
    {                           // Process next entry
        $query   = $query   . $name  . ",";
        $value   = trim ($_POST [$name]);
                                // See technical notes
        if (!strncmp ($value, "(", 1)) { $value = ""; }
        $value   = preg_replace ("/'/", "\\'", $value);
        $qvalues = $qvalues . "'$value'" . ",";
    }

    $query   = preg_replace ('/,$/', ')', $query   );
    $qvalues = preg_replace ('/,$/', ')', $qvalues );
    $query   = $query . " " . $qvalues . ';';

                                // Able to add record?
    if (@mysql_query ($query))
    {                           // Yes - Output success message
        print <<<END
<p>Added record to MySQL database $MYSQL_DATABASE
table $MYSQL_TABLE</p>
END;
    }
    else
    {                           // No  - Output error   message
        print <<<END
<p>Error: Unable to add MySQL database record</p>
END;
        print "\n";
        mysql_close();          // Close database
        trailer();              // Finish and exit
    }

    mysql_close();              // Close database
}

//--------------------------------------------------------------------
// Wrap things up.

                                // Output a status message
print <<<END
<p>Form has been processed</p>
END;
print "\n";
trailer();                      // Finish and exit
?>
