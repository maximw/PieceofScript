# Null

Null value means variable has no any special value.


#### Conversion to other types, unary operations, using as a key to access array element

Type | Description
--- | ---
 String | Error 
 Number | Error
 Boolean | Always False
 Array | Error 
 Null | As is
 Date | Error
 ! | Always True
As array key | Error

#### Operations with other types
<code>\<Null\>**op1** **\<operation\>** \<type\>**op2**</code>

operation\type | String | Number | Boolean | Array | Null | Date
--- | --- | --- | --- | --- | --- | --- 
\+| Error |Error |Error |Error |Error |Error
\-|Error |Error |Error |Error |Error |Error 
\*|Error |Error |Error |Error |Error |Error
/ |Error |Error |Error |Error |Error |Error 
^ |Error |Error |Error |Error |Error |Error 
== |False |False |False |False |True |False
!= |True |True |True |True |False |True
\> |False |False |False |False |False |False
\< |True |True |True |True |False |True
\>= |False |False |False |False |True |False
\<= |True |True |True |True |True |True
\|\| |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean
&& |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean |Convert both to Boolean

Conversion one of operands to other type means that operation will be evaluating after conversion. 
