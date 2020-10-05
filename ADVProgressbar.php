<?php

/**
 * *****************************
 * Advanced CLI PHP Progressbar
 * @package ADVProgressbar
 * @version 1.1
 * *****************************
 * @license GNU General Public License v3.0
 * @copyright 2020
 * @author Miika Oja-Kaukola
 * *****************************
 */

class ADVProgressbar
{
    
    private $style;
    private $initialmax;
    private $starttime;

    private $value;

    /**
     * Constructor for the Advanced progressbar object
     * @param object $ProgressbarStyle Style object
     * @param float $initialmax Intial max number
     */
    function __construct(object $ProgressbarStyle, float $initialmax)
    {

        if ($ProgressbarStyle != NULL) {
            $this->style = $ProgressbarStyle;
        } else {
            throw new Exception("Variable [" . gettype($ProgressbarStyle)  . " ProgressbarStyle]  was not passed to the constructor!");
        }

        if ($initialmax >= 0 and !empty($initialmax)) {
            $this->initialmax = $initialmax;
        } else {
            throw new Exception("Variable [" . gettype($initialmax)  . " initialmax]  was not passed to the constructor or it is negative number!");
        }

        $this->starttime = time();
        $this->value = 0;
    }

    /**
     * Calculates the current progress, returns as float example (0.**)
     * @return float
     */
    private function calculateProgress(): float
    {
        if ($this->initialmax > 0) {
            return $this->value / $this->initialmax;
        }
        throw new Exception("Numeric error, cannot divide a zero!");
    }

    /**
     * Builds the progressbar as a string
     * @return string
     */
    private function constructProgressbar(): string
    {
        //Get predefined variables
        list($length, $progress) = array($this->style->length, $this->calculateProgress());

        //Calculate variables
        list($wholewidth, $remainderwidth) = array(floor($progress * $length), floatval("0." . explode(".", number_format($progress * $length, 2))[1]));

        //Get the desired char depending on the current progress.
        $char = array(" ", "▌")[floor($remainderwidth * 2)];

        //Clears the last space after the progressbar is completed.
        if (($length - $wholewidth - 1) < 0) {
            $char = "";
        }
        return $this->style->name . " " . number_format($progress * 100, 0) . " % │" . str_repeat("█", $wholewidth) . $char . str_repeat(" ", ($length - $wholewidth)) . "│";
    }

    /**
     * Constructs the iteration part of the progressbar string
     * @return string
     */
    private function constructIterationString(): string
    {
        list($value, $initialmax, $datatype) = array($this->value, $this->initialmax, $this->style->datatype);
        if (empty($datatype)) {
            return ($value . "/" . $initialmax);
        } else {
            return ($value . "/" . $initialmax . " " . $datatype);
        }
    }

    /**
     * Constructs the runtime of the progressbar string
     * @return string
     */
    private function constructTimeString(): string
    {
        $time_elapsed = time() - $this->starttime;
        list($hours, $mins, $secs) = array($time_elapsed / 3600, $time_elapsed / 60 % 60, $time_elapsed % 60);
        return " (" . sprintf('%02d:%02d:%02d', $hours, $mins, $secs) . ")";
    }

    /**
     * **************************************************************
     * All the user accessible functions are below this comment block
     * **************************************************************
     * @author Miika Oja-Kaukola
     * 
     * @todo Create function that allows user to pause the progressbar
     * @todo Create function that allows user to terminate the progressbar
     * @todo Create function that allows user to reset the progressbar
     */

    /**
     * Increases the progressbar by one step. Triggers the update function automatically if not disabled.
     * @param bool Defines if the update function should be trigger on each step
     * @return void
     */
    public function step(bool $autoupdate = true): void
    {
        if ($this->value < $this->initialmax) {
            $this->value++;
        } else {
            //Show only warning because this is not critical 
            trigger_error("Value cannot be increased over initial max at line " . __LINE__, E_USER_WARNING);
        }
        if ($autoupdate) {
            $this->update();
        }
    }

    /**
     * Increases the progressbar by the defined step. Triggers the update function automatically if not disabled.
     * @param float $step Step size
     * @param bool Defines if the update function should be trigger on each step
     * @return void
     */
    public function stepBy(float $step, bool $autoupdate = true): void
    {
        if ($step > 0) {
            if ($step <= abs($this->initialmax - $this->value)) {
                $this->value += $step;
            } else {
                throw new Exception("Step cannot be greater than the initial max!");
            }
        } else {
            throw new Exception("Step must be positive number and it can't be a zero!");
        }
        if ($autoupdate) {
            $this->update();
        }
    }

    /**
     * Sets the progressbar to the defined step. Triggers the update function automatically if not disabled.
     * @param $target Target step
     * @param bool Defines if the update function should be trigger on each step
     * @return void
     */
    public function stepTo(float $target, bool $autoupdate = true): void
    {
        if ($target >= 0) {
            if ($target <= $this->initialmax) {
                $this->value = $target;
            } else {
                throw new Exception("Invalid target value given, target cannot be greater than initial max!");
            }
        } else {
            throw new Exception("Target cannot be below zero!");
        }
        if ($autoupdate) {
            $this->update();
        }
    }

    /**
     * Returns the current progressbar value
     * @return float
     */
    public function getValue(): float
    {
        return floatval($this->value);
    }

    /**
     * Redraw the progressbar to the CLI
     * @return void
     */
    public function update(): void
    {
        echo ("\r" . $this->style->color . $this->constructProgressbar() . "\e[1m " . $this->constructIterationString() . $this->constructTimeString() . "\e[0m");
    }
}

class ADVProgressbarStyle
{
    public $name;
    public $color;
    public $datatype;
    public $length;

    /**
     * @param string $name Name of the tracked progress
     * @param string $color Color of the progressbar
     * @param string $datatype Defines what datatype you are iterating. Example MB or Kg
     * @param int $length Length of the progressbar
     */
    function __construct(string $name, string $color, string $datatype = "", int $length = 16)
    {
        if (!empty(trim($name))) {
            $this->name = $name;
        } else {
            throw new Exception("Progressbar name cannot be empty!");
        }

        $colors = array("1;37" => "white", "0;31" => "red", "1;33" => "yellow", "0;32" => "green", "0;34" => "blue", "0;35" => "magenta");

        if (in_array(strtolower($color), $colors)) {
            $this->color = "\e[" . array_search($color, $colors) . ";40m";
        } else {
            throw new Exception("Invalid color specified for style object at line " . __LINE__ . ". Valid colors are " . implode(", ", $colors));
        }

        if ($length > 0) {
            $this->length = $length;
        } else {
            throw new Exception("Progressbar length must be greater than zero!");
        }

        $this->datatype = $datatype;
    }
}
