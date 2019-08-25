# Date

Dates is wrapped PHP [DateTime](http://php.net/manual/en/class.datetime.php).

Date constants is strings in single quotes in one of <a href="http://php.net/manual/en/datetime.formats.php">available formats</a>.

I.e. _'now', '2008-08-07 18:11:31', 'last day of next month'_

Date have some options on configuration:

default_timezone - default <a href="http://php.net/manual/en/timezones.php">timezone name</a>

date_format_string - default format of Date when it is converted to String

date_format_key - - default format of Date when it is converted to String in context of using as array key


#### Conversion to other types, unary operations, using as a key to access array element

Type | Description
--- | ---
 String | According to date_format_string in config
 Number | As timestamp
 Boolean | Always True 
 Array | Error 
 Null | Error
 Date | -
 ! | Always False 
As array key | According to date_format_key in config

#### Operations with other types
<code>\<Date\>op1 \<operation\> \<type\>op2</code>

operation\type | String | Number | Boolean | Array | Null | Date
--- | --- | --- | --- | --- | --- | --- 
\+|Error |Error |Error |Error |Error |Error
\-|Error |Error |Error |Error |Error |Error
\*|Error |Error |Error |Error |Error |Error
/ |Error |Error |Error |Error |Error |Error 
^ |Error |Error |Error |Error |Error |Error 
== |Error |Error |Error |Error |False |-
!= |Error |Error |Error |Error |True |-
\> |Error |Error |Error |Error |Error |-
\< |Error |Error |Error |Error |Error |-
\>= |Error |Error |Error |Error |Error |-
\<= |Error |Error |Error |Error |Error |-
\|\| |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean
&& |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean

Conversion one of operands to other type means that operation will be evaluating after conversion. 
