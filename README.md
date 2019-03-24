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
 <li><a href="docs/usage.md">Installation</a></li>
 <li><a href="docs/usage.md#usage">Usage</a></li>
 <li><a href="docs/usage.md#config">Configuration</a></li>
 <li><a href="docs/project.md">Testing project structure</a></li>
 <li><a href="docs/scenario.md">Scenarios</a></li>
 <li><a href="docs/endpoints.md">API endpoints</a></li> 
 <li><a href="docs/generators.md">Generators - data models</a></li> 
 <li><a href="docs/testcases.md">Test cases</a></li> 
 <li>Internal functions
    <ul>
    <li><a href="docs/functions_common.md">Common</a></li>
    <li><a href="docs/functions_faker.md">Faker</a></li>
    <li><a href="docs/functions_jwt.md">JWT</a></li>
    <li><a href="docs/functions_localstorage.md">Local storage</a></li>
    </ul>
 </li>   
 <li><a href="docs/variables.md">Variables and Contexts</a></li> 
 <li>Types
    <ul>
    <li><a href="docs/type_array.md">Array</a></li>
    <li><a href="docs/type_boolean.md">Boolean</a></li>
    <li><a href="docs/type_date.md">Date</a></li>
    <li><a href="docs/type_null.md">Null</a></li>
    <li><a href="docs/type_number.md">Number</a></li>
    <li><a href="docs/type_string.md">String</a></li>
    </ul>
 </li>   

