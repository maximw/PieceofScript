# Arrays

Arrays are very similar to <a href="http://php.net/manual/en/language.types.array.php">PHP Arrays</a>
and internally based on them.

In case of non-negative integer or `/[a-z][a-z0-9\_]*/i`-string keys, dot-notation can be used to access array elements:
`$array.element.10`

Also the array[key] syntax and mixed are possible.

`$array.0.["element name"][$i + 1].one_more_key`


### Conversion to other types, unary operations, using as a key to access array element

Type | Description
--- | ---
 String | Error
 Number |  Error
 Boolean | If empty array False, else True 
 Array | As is 
 Null | Error
 Date | Error
 ! | If empty array True, else False
As array key | Error


### Operations with other types
`<Array>op1 <operation> <type>op2`

operation\type | String | Number | Boolean | Array | Null | Date
--- | --- | --- | --- | --- | --- | --- 
\+|Error |Error |Error |[Array merge](http://php.net/manual/en/function.array-merge.php) |Error |Error
\-|Error |Error |Error |Error |Error |Error 
\*|Error |Error |Error |Error |Error |Error
/ |Error |Error |Error |Error |Error |Error 
^ |Error |Error |Error |Error |Error |Error 
== |Error |Error |Error |Error |Error |Error
!= |Error |Error |Error |Error |Error |Error
\> |Error |Error |Error |Error |Error |Error
\< |Error |Error |Error |Error |Error |Error
\>= |Error |Error |Error |Error |Error |Error
\<= |Error |Error |Error |Error |Error |Error
\|\| |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean
&& |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean

Conversion one of operands to other type means that operation will be evaluating after conversion. 
