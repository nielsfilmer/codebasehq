<?php namespace Bkwld\CodebaseHQ\Api\Objects;

use Bkwld\CodebaseHQ\Request;

class Ticket extends ApiObject
{
    protected $attribute_map = [
        'id' => ['ticket-id'],
        'type' => ['ticket-type'],
    ];


    public function addNote(TicketNote $note)
    {
        $id = $this->xml->{'ticket-id'};
        return $this->request->call('tickets/'.$id.'/notes', 'POST', $note);
    }
} 