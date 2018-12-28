# PieceofScript

Simple language for automated testing scenarios of HTTP JSON API. 

Example scenario for imagined social network API
```
require "./globals.pos" // Contains some global variables, i.e. $domain 
include "./globals_dev.pos" // Redefine global variables, if file exists
include "./user/*.pos" // Include all .pos files in ./user
include "./post/*.pos"

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
var $postId = $response.body.post.id // Remember post Id 

Read post $postId by $author // Call API endpoint of reading post by given user
must $response.code == 200
assert $response.body.post.content == $post.content

Read post $postId by $reader // now other user reads post
must $response.code == 200
assert $response.body.post.content == $post.content

Read post $postId by $banned 
assert $response.code == 404 // $banned sould get Not Found
```

## Installation

## Documentation

<a href="https://maximw.github.io/PieceofScript/">Index</a>

<ol>
 <li><a>Installation</a>
 <li>Types
    <ol>
    <li><a href> Type Array
    <li> Type Boolean
    <li> Type Date
    <li> Type Null
    <li> Type Number
    <li> Type String
    </ol>