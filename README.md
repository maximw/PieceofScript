# PieceofScript

Simple language for automated testing scenarios of HTTP JSON API. 

Example scenario for imagined social network API
```
// Generating models of Users and Post
var $author = User()
var $reader = User()
var $banned = User() 
var $post = Post()

Register $author // Call API endpoint of user registration	
Register $reader 
Register $banned 

Add $banned to blacklist of $author //Call API endpoint, $banned user cannot see posts of $author

Create post $post by $author // Call API endpoint for creating post
must $response.code == 201 // Check post is created
assert $response.body.post.content == $post.content // ... and check post content is not changed 
var $postId = $response.body.post.id // Save post Id 

Read post $postId by $author // Call API endpoint of reading post by given user
assert $response.code == 200
assert $response.body.post.content == $post.content

Read post $postId by $reader // now other user reads post
assert $response.code == 200
assert $response.body.post.content == $post.content

Read post $postId by $banned 
assert $response.code == 404 // banned user sould get Not Found error instead of post
```

## Documentation

<ol>
 <li><a href="usage.md">Installation</a></li>
 <li><a href="usage.md#usage">Usage</a></li>
 <li><a href="usage.md#config">Configuration</a></li>
 <li><a href="project.md">Testing project structure</a></li>
 <li><a href="scenario.md">Scenarios</a></li>
 <li><a href="endpoints.md">API endpoints</a></li> 
 <li><a href="generators.md">Generators - data models</a></li> 
 <li><a href="testcases.md">Test cases</a></li> 
 <li>Internal functions
    <ul>
    <li><a href="functions_common.md">Common</a></li>
    <li><a href="functions_faker.md">Faker</a></li>
    <li><a href="functions_jwt.md">JWT</a></li>
    <li><a href="functions_localstorage.md">Local storage</a></li>
    </ul>
 </li>   
 <li><a href="variables.md">Variables and Contexts</a></li> 
 <li>Types
    <ul>
    <li><a href="type_array.md">Array</a></li>
    <li><a href="type_boolean.md">Boolean</a></li>
    <li><a href="type_date.md">Date</a></li>
    <li><a href="type_null.md">Null</a></li>
    <li><a href="type_number.md">Number</a></li>
    <li><a href="type_string.md">String</a></li>
    </ul>
 </li>   
 <li><a href="variables.md">Variables and Contexts</a></li> 