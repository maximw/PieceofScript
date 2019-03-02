<h1>Local storage functions</h1>

Local storage is key-value storage to keep some values between runs of scenarios.
It could be useful if scenarios used for semi-manual testing during development 
for reusing some objects, i.e. auth token or user.

Storage file name could be set in command line by `-l` or `--localstorage` options.
Local storage functions have different behaviour, if local storage is not set.  

Local storage files have YAML format and could easily be read or edited by developer.

----

`ls\get(string $key, $defaultValue, boolean $saveValue=true)` - if $key does not exists in local storage or local storage is not set, return $defaultValue. Else return saved value. 
If $saveValue is true and key does not exists in local storage, $defaultValue will be saved.

`ls\set(string $key, $value)` - save $value with $key, and returns $value.

`ls\key(string $regexp=null)` - returns all keys. If $regexp is given, returns matched keys only.
