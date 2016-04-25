<?php

namespace Cise;

/**
 * Description of Input_Output
 *
 * @author Lidaa <aa_dil@hotmail.fr>
 */
class Input_Output {

    const BLACK = "\033[0;30m";
    const DARK_GRAY = "\033[1;30m";
    const BLUE = "\033[0;34m";
    const LIGHT_BLUE = "\033[1;34m";
    const GREEN = "\033[0;32m";
    const LIGHT_GREEN = "\033[1;32m";
    const CYAN = "\033[0;36m";
    const LIGHT_CYAN = "\033[1;36m";
    const RED = "\033[0;31m";
    const LIGHT_RED = "\033[1;31m";
    const PURPLE = "\033[0;35m";
    const LIGHT_PURPLE = "\033[1;35m";
    const BROWN = "\033[0;33m";
    const YELLOW = "\033[1;33m";
    const LIGHT_GRAY = "\033[0;37m";
    const WHITE = "\033[1;37m";
    const BG_BLACK = "\033[40m";
    const BG_RED = "\033[41m";
    const BG_GREEN = "\033[42m";
    const BG_YELLOW = "\033[43m";
    const BG_BLUE = "\033[44m";
    const BG_MAGENTA = "\033[45m";
    const BG_CYAN = "\033[46m";
    const BG_LIGHT_GRAY = "\033[47m";

    private $stream;

    /**
     * write
     * 
     * @param string $message
     * @param bool $newline
     */
    public function write($message, $newline = true) {
        $this->stream = STDOUT;

        fwrite($this->stream, $this->colorize($message) . ($newline ? PHP_EOL : ''));
        
        if(strpos($message, '<error>') !== false && strpos($message, '</error>') !== false ) {
            exit;
        }
    }

    /**
     * ask
     * 
     * @param string $message
     * @param string $defaut_value
     * @return string
     */
    public function ask($message, $defaut_value) {
        $this->write($message, false);
        $line = trim(fgets(STDIN));
        
        if(!$line) {
            $line = $defaut_value;
        }
        
        return $line;
    }

    /**
     * colorize
     * 
     * @param string $message
     * @return string
     */
    protected function colorize($message) {
        $colorsMap = array(
            '<info>' => self::GREEN,
            '<comment>' => self::YELLOW,
            '<question>' => self::BLACK . self::BG_CYAN,
            '<error>' => self::BG_RED,
            '</info>' => "\033[0m",
            '</comment>' => "\033[0m",
            '</question>' => "\033[0m",
            '</error>' => "\033[0m",
        );

        if(!$this->hasColorSupport()) {
            return str_replace(array_keys($colorsMap), array(''), $message);            
        }
        
        return str_replace(array_keys($colorsMap), array_values($colorsMap), $message);
    }

    /**
     * hasColorSupport
     * 
     * @return bool
     */
    protected function hasColorSupport()
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            return false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI');
        }

        return function_exists('posix_isatty') && @posix_isatty($this->stream);
    }
}
