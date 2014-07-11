<?php namespace Bkwld\CodebaseHQ\Api\Objects;


/**
 * Class Commit
 * Commit in a CodebaseHQ repository
 * @package Bkwld\CodebaseHQ\Api\Objects
 */
class Commit extends ApiObject
{

    /**
     * Returns the tickets from the commit message
     * @return array
     */
    public function ticketIds()
    {
        $return = [];

        if (preg_match_all('#\[ *[\w-]+ *: *([\d\s\,]+) *\]#', $this->message, $matches)) {
            foreach($matches[1] as $ticket_ids) {
                $ticket_ids = explode(',', $ticket_ids);

                foreach($ticket_ids as $ticket) {
                    if(!in_array($ticket, $return)) $return[] = trim($ticket);
                }
            }
        }

        return $return;
    }
} 