<?php
$config = parse_ini_file('config/config.ini');

$hostname = "{{$config['server_host']}:{$config['server_port']}/imap/ssl}INBOX";
$username = "${config['server_user']}";
$password = "${config['server_password']}";

//Attempt to connect using the imap_open function.
$imapResource = imap_open($hostname, $username, $password);

//If the imap_open function returns a boolean FALSE value,
//then we failed to connect.
if ($imapResource === false) {
    //If it failed, throw an exception that contains
    //the last imap error.
    throw new Exception(imap_last_error());
}

//If we get to this point, it means that we have successfully
//connected to our mailbox via IMAP.

//Lets get all emails that were received since a given date.
// $search = 'SINCE "' . date("j F Y", strtotime("-7 days")) . '"';
$search = 'UNSEEN';
$emails = imap_search($imapResource, $search);

//If the $emails variable is not a boolean FALSE value or
//an empty array.
if (!empty($emails)) {
    //Loop through the emails.
    foreach ($emails as $email) {
        //Fetch an overview of the email.
        $overview = imap_fetch_overview($imapResource, $email);
        $overview = $overview[0];
        //Print out the subject of the email.
        echo '<b>' . htmlentities($overview->subject) . '</b><br>';
        //Print out the sender's email address / from email address.
        echo 'From: ' . $overview->from . '<br>';
        //Print seen flag
        echo 'Seen: ' . ($overview->seen ? 'Yes' : 'No') . '<br>';
        // if ($overview->seen === 0) {
        //     $status = imap_setflag_full($imapResource, $email, "\\Seen");
        //     echo 'Changed to seen <br>';
        // }
        //Print deleted flag
        echo 'Deleted: ' . ($overview->deleted ? 'Yes' : 'No') . '<br>';
        //Get the body of the email.
        //Note that we use FT_PEEK with the imap_fetchbody function because we want to read
        //the email without flagging it as read
        echo 'Body:<br>';
        $message = escape(imap_fetchbody($imapResource, $email, '1', FT_PEEK)); // TODO: Confirm escape() is required
        $message = imap_qprint($message);
        $message = nl2br($message); // Required to display CRLF on the browser.
        echo $message . '<br><br>';
    }
}

imap_close($imapResource);

/**
 * Helper function to escape plaintext before display
 * 
 * This should be used to escape html entities, etc.
 * 
 * @param string $input
 * @param bool $double_encode
 * @return string
 */
function escape($input, $double_encode = FALSE)
{
    // Ensure we have valid correctly encoded string..
    // http://stackoverflow.com/questions/1412239/why-call-mb-convert-encoding-to-sanitize-text
    $input = mb_convert_encoding($input, 'UTF-8', 'UTF-8');
    // why are we using html entities? this -> http://stackoverflow.com/a/110576/992171
    return htmlentities($input, ENT_QUOTES, 'UTF-8', $double_encode);
}
