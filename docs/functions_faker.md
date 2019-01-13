<h1>Faker functions</h1>

This set of functions based on <a href="https://github.com/fzaninotto/Faker">fzaninotto/Faker</a> 
library, and intended for generating random various data.

There are 2 related config settings:

`random_seed` - integer seed for random functions. Can be used to exactly reproduce generated data. By default or null, this value is randomly selected and could be found in report properties. 

`faker_locale` - locale of generated data. In some case could fallback to `en_US`, by default `en_US`.

## fzaninotto/Faker
<b>faker\address</b>() <i>Number</i> - 8888 Cummings Vista Apt. 101, Susanbury, NY 95473

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
