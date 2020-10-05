# ADVProgressbar

ADVProgressbar is a refined and flexible way of displaying progress for a time-consuming task.

## Installation

Include the ADVProgressbar.php file using "include" or "require" operators.

```php
include '../includes/ADVProgressbar.php'
//OR
require '../includes/ADVProgressbar.php'
```
## Examples
ADVProgressbar has customizable names, colors, datatypes, error handling, and more.

![Preview](https://i.imgur.com/TUxwVyK.gif)

## Usage

Initializing the ADVProgressbar object
```php
require_once 'ADVProgressbar.php'

//Lets create a style object first.
//Style object has 4 parameters {$name, $color, $datatype, $length}.
$progressbarstyle = new ADVProgressbarStyle("Downloading", "white", "Kb", 16);

//Now lets create the progressbar object.
//Progressbar object has 2 parameters {$styleobject, $initialmax}
$progressbar = new ADVProgressbar($progressbarstyle, 31282);
```
Using the ADVProgressbar
```php
//Loop until the progressbar is complete
for ($i = 0; $i < $progressbar->GetInitialMax(); $i++) {
    $progressbar->stepBy(10);
    usleep(1000);
}
```
Result:

![Preview](https://i.imgur.com/AODnrv3.gif)

## Methods:
```php
//Increases the progressbar value by 1.
$progressbar->step();

//Increases the progressbar value by x.
$progressbar->stepBy(x);

//Changes the progressbar value to x.
$progressbar->stepTo(x);

//Gets the progressbar value.
$progressbar->getValue();

//Gets the max initial value.
$progressbar->getInitialMax();

//Forces a redraw on the progressbar.
$progressbar->update();

//Enables the pause mode for the progressbar, pause can be removed by using any of the step methods or forcing a redraw.
$progressbar->pauseProgressbar();

//Terminates the progressbar and resets the object.
$progressbar->terminateProgressbar();

//Resets the progressbar object.
$progressbar->resetProgressbar();
```


## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[GNU](https://www.gnu.org/licenses/gpl-3.0.en.html)