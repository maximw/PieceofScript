<h1>Internal functions</h1>

## Variables related
`toBool($var)` <i>boolean</i> - conversion to Boolean

`toNumber($var)` <i>number</i> - conversion to Number

`toString($var)` <i>string</i> - conversion to String

`toDate($var)` <i>date</i> - conversion to Date

`if($condition, $value1, $value2)` <i>any</i> - if `$condition` is true returns `$value1` else `$value2`.

`choice($condition1, $value1, $condition2, $value2, ..., $conditionN, $valueN)` <i>any</i> - returns $valueK if the first met $conditionK is true.

`max</b>($var1, $var2, ... $varN)` <i>any</i> - returns maximal of values, if they are comparable.

`min</b>($var1, $var2, ... $varN)` <i>any</i> - returns minimal of values, if they are comparable.

`similar($var, $sample)` <i>boolean</i> - returns <i>true</i>, if arguments are the same scalar type. If <i>$var</i> is an Array, all the string keys in <i>$sample</i>, have to exists in <i>$var</i> with the same type.

`identical($var, $sample)` <i>boolean</i> - more strict <i>similar()</i>, with additional backward condition that all string keys in <i>$var</i> have to exists in <i>$sample</i>. In other words, all the elements of <i>$var</i> have pair in <i>$sample</i> with the same type.



 
## Numbers related
`round</b>(number $number, number $precision = 0)` <i>Number</i> - returns the rounded value of $number to specified $precision.




## Strings related

`size(string $string)` <i>number</i> - returns string length in UTF-8 encoding.

`regex(string $string, string $regex)` <i>boolean</i> - returns true if $string matches to  <a href="http://php.net/manual/en/pcre.pattern.php">$regex</a>.

<b>regexMatch</b>($string, $regex) <i>Array</i> - вернет массив совпадений с группами регулярки <i>$regex</i>.

**toLower**($string) <i>String</i> - Makes a string lowercase in UTF-8 encoding

**toUpper**($string) <i>String</i> - Makes a string lowercase in UTF-8 encoding

**replaceString**($searchFor, $replaceWith, $inString) <i>String</i> - Replace $searchFor with $replaceWith in $inString

**findString**($searchFor, $inString, $offset) <i>Number</i> or _Boolean_ - Find position $searchFor in $inString from $offset position, or false if not found.

`urlEncode(string $string)` <i>string</i> - returns URL-encoded string (<a href="http://php.net/manual/en/function.urlencode.php">details</a>).

`urlDecode(string $string)` <i>string</i> - returns decoded URL-encoded string (<a href="http://php.net/manual/en/function.urldecode.php">details</a>).


## Arrays related
`append($array, $item)` <i>array</i> - append $item to $array

`array($var1, $var2, ... $varN)` <i>array</i> - returns array with given arguments

`size(array $array)` <i>number</i> - returns count of array's elements 

`prepend(array $array, $item)` <i>array</i> - prepend $item to $array

`keys(array $array)` <i>array</i> - return array keys

`slice(array $array, number $offset, number $size)` <i>array</i> - return $size elements of array started from $offset  (<a href="http://php.net/manual/en/function.array-slice.php">details</a>).

`explode(string $string, string $delimiter)` <i>array</i> - returns an array of strings, each of which is a substring of `$string` formed by splitting it on boundaries formed by the string `$delimiter`.  

`implode(array $array, string $delimiter)` <i>string</i> - returns string of concatenated array elements one by one with $delimiter.  


## Dates related
`dateFormat(date $date, string $format)` <i>String</i> - format Date (<a href="http://php.net/manual/en/datetime.formats.php">details</a>).

<b>dateModify</b>($date, $relativeFormatString) <i>Date</i> - returns midified $date with given (<a href="http://php.net/manual/en/datetime.formats.relative.php">relative format</a>).



























