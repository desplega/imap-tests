<?php
require_once __DIR__ . '/vendor/autoload.php';

use Webklex\PHPIMAP\ClientManager;
// use Webklex\PHPIMAP\Client;

$cm = new ClientManager('config/imap.php');

/** @var \Webklex\PHPIMAP\Client $client */
$client = $cm->account('gmail');

//Connect to the IMAP Server
$client->connect();

//Get all Mailboxes
/** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */
$folder = $client->getFolderByName('INBOX');

//Get all Messages of the current Mailbox $folder
/** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
$messages = $folder->messages()->unseen()->get();

/** @var \Webklex\PHPIMAP\Message $message */
foreach ($messages as $message) {
    echo $message->getSubject() . '<br>';

    if ($message->hasAttachments()) {
        echo 'Attachments: ' . $message->getAttachments()->count() . '<br>';
        $attachments = $message->getAttachments();
        foreach ($attachments as $attachment) {
            echo 'Extension: ' . $attachment->getExtension() . '<br>'; // Requires: composer require symfony/mime
            echo 'Mime Type: ' . $attachment->getMimeType() . '<br>';
            $status = $attachment->save('store/');
            echo 'Stored status: ' . $status;
        }
    } else {
        echo 'No attachments' . '<br>';
    }

    if ($message->hasHTMLBody()) {
        echo 'HTML body ---------------------------------<br>';
        //echo nl2br(strip_tags($message->getHTMLBody()));
        $html = new \Html2Text\Html2Text($message->getHTMLBody());
        echo nl2br($html->getText());
        echo nl2br($message->getTextBody());
    } elseif ($message->hasTextBody()) {
        echo 'Text body ---------------------------------<br>';
        echo $message->getTextBody();
        echo nl2br($message->getTextBody());
    }
    echo '======================================<br>';

    // Mark message as seen
    $message->setFlag('Seen');
    unset($attachments);
}
