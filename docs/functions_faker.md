# Faker functions

This set of functions based on <a href="https://github.com/fzaninotto/Faker">fzaninotto/Faker</a> 
library, and intended for generating random various data.

There are 2 related config settings:

`random_seed` - integer seed for random functions. Can be used to exactly reproduce generated data. By default or null, this value is randomly selected and could be found in report properties. 

`faker_locale` - locale of generated data. In some case could fallback to `en_US`, by default `en_US`.

## Functions list

`Faker\address()` <i>string</i> - returns random address string, i.e. "8888 Cummings Vista Apt. 101, Susanbury, NY 95473"

`Faker\arrayElement(array $array)` <i>number</i> - returns random element of $array

`Faker\asciify(string $template)` <i>string</i> - replace symbols "*" in $template with random ASCII chars, i.e. "Hello ***" to 'Hello R6+'

`Faker\bankCardExpiration()` <i>string</i> - return random bank card expiration date, i.e. "02/19"

`Faker\bankCardNumber()` <i>string</i> - return random bank card number, i.e. "4485480221084675"

`Faker\bankCardType()` <i>string</i> - return random bank card type, i.e. "MasterCard"

`Faker\boolean(number $chanceOfTrue)` <i>boolean</i> - returns random boolean value with chance of getting true 0 to 100

`Faker\city()` <i>string</i> - returns random city name, i.e. "West Judge"

`Faker\cololrCss()` <i>string</i> - returns random color code in CSS format, i.e. "rgb(0,255,122)"

`Faker\cololrHex()` <i>string</i> - returns random color code in HEX format, i.e. "#fa3cc2"

`Faker\cololrName()` <i>string</i> - returns random color name, i.e. "Gainsbor"

`Faker\cololrRgb()` <i>string</i> - returns random color code, i.e. "0,255,122"

`Faker\cololrSafeName()` <i>string</i> - returns random color name from limited set of colors, i.e. "fuchsia"

`Faker\country()` <i>string</i> - returns random country name, i.e. "Falkland Islands (Malvinas)"

`Faker\countryCode()` <i>string</i> - returns random country code, i.e. "UK"

`Faker\currencyCode()` <i>string</i> - returns random currency code, i.e. "EUR"

`Faker\dateTime($dateMin = "-30 years", $dateMax = "now")` <i>date</i> - returns random date between $dateMin and $dateMax. Null value - use default value, number value - use ad timestamp, date value - user as is, string value - use as converted string to date, include <a href="http://php.net/manual/en/datetime.formats.php">relative formats</a>.

`Faker\domain()` <i>string</i> - returns random domain name, i.e. "wolffdeckow.net"

`Faker\email()` <i>string</i> - returns random email address, i.e. "tkshlerin@collins.com"

`Faker\emoji()` <i>string</i> - returns random emoji symbol, i.e. "😁"

`Faker\file(string $dirName)` <i>string</i> - returns path of random file in directory $dirName

`Faker\fileExtension()` <i>string</i> - returns random file extension, i.e. "avi"

`Faker\firstName(string $gender = null)` <i>string</i> - returns random first name, in dependency of $gender "male" or "female" if given, i.e. "Maynard"

`Faker\html(number $maxDepth = 2, number $maxWidth = 3)` <i>string</i> - returns random text with HTML markup, $maxDepth - maximal tags nesting level, $maxWith - maximal number of tags on one level

`Faker\iban(string $countryCode)` <i>string</i> - returns random IBAN, i.e. "IT31A8497112740YZ575DJ28BP4"

`Faker\iban(number $maxDepth = 2, number $maxWidth = 3)` <i>string</i> - returns random IBAN, i.e. "IT31A8497112740YZ575DJ28BP4"

`Faker\imageFile(number $width = 640, number $height = 480, string $category = null, boolean $fullPath = true, string $watermark = null)` <i>string</i> - returns path to random image file in cache directory

`Faker\imageUrl(number $width = 640, number $height = 480, string $category = null, string $watermark = null, boolean $monochrome = false)` <i>string</i> - returns random image URL 

`Faker\integer(number $min = 0, number $max = PHP_INT_MAX)` <i>number</i> - returns random integer between $min and $max inclusive

`Faker\ipv4(number $min = 0, number $max = PHP_INT_MAX)` <i>number</i> - returns random integer between $min and $max inclusive


<b>faker\randomFloat</b>($digitsCount = NULL, $min = 0, $max = NULL) <i>Number</i> - returns random float number with $digitsCount digits include integer and fractional parts, between $min and $max

<b>faker\numberBetween</b>($min = 0, $max = NULL) <i>Number</i> - returns random integer number between $min and $max

<b>faker\randomLetter</b>() <i>String</i> - returns random letter

<b>faker\randomElements</b>($array, $count = 1) <i>String</i> - returns randomly ordered subsequence of a provided array

<b>faker\shuffle</b>($value) <i>String or Array</i> - returns shuffled letters of given string or elements of given array

<b>faker\numerify</b>("Hello ###") <i>String</i> - replace symbol "#" with random digits, i.e. 'Hello 609'

<b>faker\lexify</b>("Hello ???") <i>String</i> - replace symbol "?" with random letters, i.e. 'Hello wgt'

<b>faker\bothify</b>("Hello ##??") <i>String</i> - replace symbols "#" with random digits and "?" with random letters, i.e. 'Hello 42jz'


<b>faker\regexify</b>("[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}") <i>String</i> - return random string fit to given regex

<b>faker\word</b>() <i>String</i> - random one word

<b>faker\words</b>($wordsCount = 3, $asText = false) <i>String or Array</i> - if $asText true returns String with $wordsCount words, else Array size $count of random words

<b>faker\sentence</b>($wordsCount = 6, $randomizeCount = true) <i>String</i> - returns string with $randomizeCount words or up to $randomizeCount words if $randomizeCount == true

<b>faker\sentences($count = 3, $asText = false) <i>String or Array</i>              // array('Optio quos qui illo error.', 'Laborum vero a officia id corporis.', 'Saepe provident esse hic eligendi.')

<b>faker\paragraph($nbSentences = 3, $randomizeCount = true) <i>String or Array</i> // 'Ut ab voluptas sed a nam. Sint autem inventore aut officia aut aut blanditiis. Ducimus eos odit amet et est ut eum.'

<b>Faker\Paragraphs($nb = 3, $asText = false) <i>String or Array</i>             // array('Quidem ut sunt et quidem est accusamus aut. Fuga est placeat rerum ut. Enim ex eveniet facere sunt.', 'Aut nam et eum architecto fugit repellendus illo. Qui ex esse veritatis.', 'Possimus omnis aut incidunt sunt. Asperiores incidunt iure sequi cum culpa rem. Rerum exercitationem est rem.')
text($maxNbChars = 200)                          // 'Fuga totam reiciendis qui architecto fugiat nemo. Consequatur recusandae qui cupiditate eos quod.'


<b>faker\name</b>() <i>String</i> - random name, i.e. "Lucy Cechtelar"
