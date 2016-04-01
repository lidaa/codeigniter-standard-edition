<?php

namespace Cise;

use Composer\Script\Event;

/**
 * Command
 *
 * @author Lidaa <aa_dil@hotmail.fr>
 */
class Command
{
    /**
     * getIO
     * 
     * @param Event $event
     * @return \Cise\Input_Output
     */
    protected static function getIO($event)
    {
        if ($event instanceof Event) {
            $io = $event->getIO();
        } else {
            $io = new Input_Output();
        }
        
        return $io;
    }
}
