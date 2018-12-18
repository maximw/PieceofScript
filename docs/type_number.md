#### Conversion to other types, unary operations, using as a key to access array element

Type | Description
--- | ---
 String | As decimal representation of a number 
 Number | As is
 Boolean | False if 0, else True 
 Array | Error 
 Null | Error
 Date | As timestamp with microseconds if case of real Number
 ! | True if 0, else False 
As array key | As rounded to integer

#### Operations with other types
<code>\<Number\>**op1** **\<operation\>** \<type\>**op2**</code>

operation\type | String | Number | Boolean | Array | Null | Date
--- | --- | --- | --- | --- | --- | --- 
\+|Error |- |Error |Error |Error |Error
\-|Error |- |Error |Error |Error |Error 
\*|Error |- |Error |Error |Error |Error
/ |Error |- |Error |Error |Error |Error 
^ |Error |- |Error |Error |Error |Error 
== |Error |- |Error |Error |Error |Error
!= |Error |- |Error |Error |Error |Error
\> |Error |- |Error |Error |Error |Error
\< |Error |- |Error |Error |Error |Error
\>= |Error |- |Error |Error |Error |Error
\<= |Error |- |Error |Error |Error |Error
\|\| |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean
&& |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean

Conversion one of operands to other type means that operation will be evaluating after conversion. 
