<?php namespace Bkwld\CodebaseHQ\Api\Objects;


class TicketNote extends ApiObject
{
    protected $attribute_map = [
        'time' => ['time-added'],
        'status' => ['changes','status-id'],
        'priority' => ['changes','priority-id'],
        'category' => ['changes','category-id'],
        'assignee' => ['changes','assignee-id'],
        'milestone' => ['changes','milestone-id'],
        'summary' => ['changes','summary'],
    ];

    /**
     * Creates an empty TicketNote
     * @return TicketNote
     */
    public static function make()
    {
        $xml = new \SimpleXMLElement('<ticket-note></ticket-note>');
        return new self($xml);
    }
}