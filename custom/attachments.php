<?php
include_once dirname(__FILE__) . '/includes/functions.php';

$thread_id = $ticket->getId();
renderAttachmentsSection($thread_id);

function renderAttachmentsSection($thread_id)
{
    $attachments = getAttachmentsByThreadId($thread_id);
    include_once 'templates/attachments.tmpl.php';
}


function getAttachmentsByThreadId($thread_id)
{
    $thread_id = intval($thread_id);
    if ($thread_id) {
        $query = sprintf('SELECT t.number, f.name, f.key, f.signature, a.id, f.id as `fid`, f.size, e.created, a.object_id
FROM sem_ticket t
LEFT JOIN sem_thread th ON (t.ticket_id = th.object_id)
LEFT JOIN sem_thread_entry e ON (e.thread_id = th.id)
LEFT JOIN sem_attachment a ON (e.id = a.object_id)
LEFT JOIN sem_file f ON (a.file_id = f.id)
WHERE t.ticket_id = %1$d AND f.key <> \'NULL\'
ORDER by a.object_id desc, f.name asc', $thread_id);
        $result = executeQuery($query);
        return getDataFromResultSet($result);
    }
}
