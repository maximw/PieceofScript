# Boolean

To specify a Boolean literal, use case-insensitive constants `true` or `false`.

***
#### Conversion to other types and unary operations, using as a key to access array element

Type | Description
--- | ---
 String | Error 
 Number |  Error
 Boolean | As is
 Array | Error 
 Null | Error
 Date | Error
! | Opposite value
As array key | Error

***
#### Operations with other types
<code><Boolean>**op1** **\<operation\>** \<type\>**op2**</code>

operation\type | String | Number | Boolean | Array | Null | Date
--- | --- | --- | --- | --- | --- | --- 
\+|Error |Error |Error |Error |Error |Error
\-|Error |Error |Error |Error |Error |Error 
\*|Error |Error |Error |Error |Error |Error
/ |Error |Error |Error |Error |Error |Error 
^ |Error |Error |Error |Error |Error |Error 
== |Error |Error |- |Error |False |Error 
!= |Error |Error |- |Error |True |Error 
\> |Error |Error |- |Error |Error |Error 
\< |Error |Error |- |Error |Error |Error 
\>= |Error |Error |- |Error |Error |Error
\<= |Error |Error |- |Error |Error |Error
\|\| |Convert op2 to Boolean |Convert op2 to Boolean |Convert op2 to Boolean |Convert op2 to Boolean |Convert op2 to Boolean |Convert op2 to Boolean
&& |Convert op2 to Boolean |Convert op2 to Boolean |Convert op2 to Boolean |Convert op2 to Boolean |Convert op2 to Boolean |Convert op2 to Boolean

