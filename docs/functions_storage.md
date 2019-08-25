<h1>Storage functions</h1>

Key-value storage allows to keep some values between runs of scenarios.
It could be useful if scenarios used for semi-manual testing during development 
for reusing some objects, i.e. auth token or user.

Storage file name could be set in command line by `-s` or `--storage` options.

Storage files have YAML format and could easily be read or edited by developer.

----

`storage\get(string $key, $defaultValue, boolean $saveValue=true)` - if $key does not exists in storage or storage file is not set, return $defaultValue. Else return saved value. 
If $saveValue is true and key does not exists in storage, $defaultValue will be saved.

`storage\set(string $key, $value)` - save $value with $key, and returns $value.

`storage\key(string $regexp=null)` - returns all keys. If $regexp is given, returns matched keys only.
