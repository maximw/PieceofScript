## Type String

String is sequence of UTF-8 characters. 
String constant can by specified by enclosing it in double quotes ".
 
To specify a literal double quote, escape it with a backslash (\\). To specify a literal backslash, double it (\\\\). All other instances of backslash will be treated as a literal backslash: this means that the other escape sequences you might be used to, such as \r or \n, will be output literally as specified rather than having any special meaning.  

#### Conversion to other types, unary operations, using as a key to access array element

Type | Description
--- | ---
 String | -
 Number |  According to [PHP rules](http://php.net/manual/en/language.types.string.php#language.types.string.conversion)
 Boolean | False if empty string "", else True 
 Array | Error 
 Null | Error
 Date | Accorting to [PHP DateTime formats](http://php.net/manual/en/datetime.formats.php)
 ! | True if empty string "", else False 
As array key | As is
***
#### Operations with other types
<code>\<String\>**op1** **\<operation\>** \<type\>**op2**</code>

Operation \ with |String |Number |Boolean |Array |Null |Date
---|---|---|---|---|---|---
\+ |Concatenation |Error |Error |Error |Error |Error
\- |Error |Error |Error |Error |Error |Error
\* |Error |Error |Error |Error |Error |Error 
/ |Error |Error |Error |Error |Error |Error
^ |Error |Error |Error |Error |Error |Error
== |True if strings are equal |Error |Error |Error |Error |Error
!= |Lexicographical compare |Error |Error |Error |Error |Error
\> |Lexicographical compare |Error  |Error |Error |Error |Error
\< |Lexicographical compare |Error |Error |Error |Error |Error
\>= |Lexicographical compare |Error |Error |Error |Error |Error
\<= |Lexicographical compare |Error |Error |Error |Error |Error
\|\| |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both  to Boolean |Convert both to Boolean
&& |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean
