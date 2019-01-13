<h1>Internal functions</h1>

## Variables related
`toBool($var)` <i>Boolean</i> - conversion to Boolean

`toNumber($var)` <i>Number</i> - conversion to Number

`toString($var)` <i>String</i> - conversion to String

`toDate($var)` <i>Date</i> - conversion to Date

`if($condition, $value1, $value2)` <i>Any</i> - if `$condition` is true returns `$value1` else `$value2`.

`choice($condition1, $value1, $condition2, $value2, ..., $conditionN, $valueN)` <i>Any</i> - if the first met true `$conditionK` returns `$valueK`, if all conditions false returns `Null`.

<b>max</b>($var1, $var2, ... $varN) <i>any</i> - returns maximal of values, if they are comparable.

<b>min</b>($var1, $var2, ... $varN) <i>any</i> - returns minimal of values, if they are comparable.

<b>similar</b>($var, $sample) <i>Boolean</i> - return <i>true</i>, if arguments are the same scalar type. If <i>$var</i> is an Array, all the string keys in <i>$sample</i>, have to exists in <i>$var</i> with the same type.

<b>identical</b>($var, $sample) <i>Boolean</i> - more strict <i>similar()</i>, with additional backward condition that all string keys in <i>$var</i> have to exists in <i>$sample</i>. In other words, all the elements of <i>$var</i> have pair in <i>$sample</i> with the same type.




## Numbers related
</b>round</b>($number, $precision = 0) <i>Number</i> - обычное округление до нужной точности.



## Strings related

<b>length</b>($string) <i>Number</i> - return string length in UTF-8 encoding.

<b>regex</b>($string, $regex) <i>Boolean</i> - returns true if $string matches to  <a href="http://php.net/manual/en/pcre.pattern.php">$regex</a>.

<b>regexMatch</b>($string, $regex) <i>Array</i> - вернет массив совпадений с группами регулярки <i>$regex</i>.

**toLower**($string) <i>String</i> - Makes a string lowercase in UTF-8 encoding

**toUpper**($string) <i>String</i> - Makes a string lowercase in UTF-8 encoding

**replaceString**($searchFor, $replaceWith, $inString) <i>String</i> - Replace $searchFor with $replaceWith in $inString

**findString**($searchFor, $inString, $offset) <i>Number</i> or _Boolean_ - Find position $searchFor in $inString from $offset position, or false if not found.

## Arrays related
<b>array</b>($var1, $var2, ... $varN) <i>Array</i> - returns array with given arguments

<b>length</b>($array) <i>Number</i> - returns array length

<b>append</b>($array, $item) <i>Array</i> - append `$item` to `$array`

<b>prepend</b>($array, $item) <i>Array</i> - prepend `$item` to `$array`

<b>keys</b>($array) <i>Array</i> - return array keys

<b>slice</b>($array, $offset, $length) <i>Array</i> - return _$length_ elements of array started from $offset  (<a href="http://php.net/manual/en/function.array-slice.php">details</a>).

`explode($string, $delimiter)` <i>Array</i> - returns an array of strings, each of which is a substring of `$string` formed by splitting it on boundaries formed by the string `$delimiter`.  


## Dates related
`dateFormat($date, $format)` <i>String</i> - format Date (<a href="http://php.net/manual/en/datetime.formats.php">details</a>).

<b>dateModify</b>($date, $relativeFormatString) <i>Date</i> - returns midified $date with given (<a href="http://php.net/manual/en/datetime.formats.relative.php">relative format</a>).

## Random
<b>random\Date</b>($min, $max, $withTime) <i>Date</i> - случайная дата от $min до $max включительно, если $withTime = true, то будет и случайное время, иначе полночь.

<b>random\choiceValue</b>($value1, $weight1, $value2, $weight2,...  $valueN, $weightN) - returns $valueM with probability   $weightM / sum($weight1, ..., $weightN)

<b>random\choice</b>($arrayValues, $arrayWeights = array(1, 1, ..., 1)) - like <i>random\choiceValue()<i>, but arguments are passed as Arrays 

## fzaninotto/Faker
<b>faker\randomDigit</b>() <i>Number</i> - returns random number from 0 to 9

<b>faker\randomDigitPositive</b>() - <i>Number</i> - returns random number from 1 to 9

<b>faker\randomNumber</b>($digitsCount = NULL, $strict = false) <i>Number</i> - returns random number with $digitsCount digits or less, if $strict == false

<b>faker\randomFloat</b>($digitsCount = NULL, $min = 0, $max = NULL) <i>Number</i> - returns random float number with $digitsCount digits include integer and fractional parts, between $min and $max

<b>faker\numberBetween</b>($min = 0, $max = NULL) <i>Number</i> - returns random integer number between $min and $max

<b>faker\randomLetter</b>() <i>String</i> - returns random letter

<b>faker\randomElements</b>($array, $count = 1) <i>String</i> - returns randomly ordered subsequence of a provided array

<b>faker\shuffle</b>($value) <i>String or Array</i> - returns shuffled letters of given string or elements of given array

<b>faker\numerify</b>("Hello ###") <i>String</i> - replace symbol "#" with random digits, i.e. 'Hello 609'

<b>faker\lexify</b>("Hello ???") <i>String</i> - replace symbol "?" with random letters, i.e. 'Hello wgt'

<b>faker\bothify</b>("Hello ##??") <i>String</i> - replace symbols "#" with random digits and "?" with random letters, i.e. 'Hello 42jz'

<b>faker\asciify</b>("Hello ***") <i>String</i> - replace symbols "*" with random ASCII chars, i.e. 'Hello R6+'

<b>faker\regexify</b>("[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}") <i>String</i> - return random string fit to given regex

<b>faker\word</b>() <i>String</i> - random one word

<b>faker\words</b>($wordsCount = 3, $asText = false) <i>String or Array</i> - if $asText true returns String with $wordsCount words, else Array size $count of random words

<b>faker\sentence</b>($wordsCount = 6, $randomizeCount = true) <i>String</i> - returns string with $randomizeCount words or up to $randomizeCount words if $randomizeCount == true

<b>faker\sentences($count = 3, $asText = false) <i>String or Array</i>              // array('Optio quos qui illo error.', 'Laborum vero a officia id corporis.', 'Saepe provident esse hic eligendi.')

<b>faker\paragraph($nbSentences = 3, $randomizeCount = true) <i>String or Array</i> // 'Ut ab voluptas sed a nam. Sint autem inventore aut officia aut aut blanditiis. Ducimus eos odit amet et est ut eum.'

<b>Faker\Paragraphs($nb = 3, $asText = false) <i>String or Array</i>             // array('Quidem ut sunt et quidem est accusamus aut. Fuga est placeat rerum ut. Enim ex eveniet facere sunt.', 'Aut nam et eum architecto fugit repellendus illo. Qui ex esse veritatis.', 'Possimus omnis aut incidunt sunt. Asperiores incidunt iure sequi cum culpa rem. Rerum exercitationem est rem.')

text($maxNbChars = 200)                          // 'Fuga totam reiciendis qui architecto fugiat nemo. Consequatur recusandae qui cupiditate eos quod.'


<b>faker\name</b>() <i>String</i> - random name, i.e. "Lucy Cechtelar"

<b>Faker\Address</b>() <i>String</i> - random address, i.e. "426 Jordy Lodge Cartwrightshire, SC 88120-6700"
