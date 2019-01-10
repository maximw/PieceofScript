##PieceofScript installation and run

<a name="install"></a>
### Donloading phar-archinve

Download from repository <a href=https://github.com/maximw/PieceofScript/raw/master/bin/pos.phar>https://github.com/maximw/PieceofScript/raw/master/bin/pos.phar</a>

### Building phar-archive

1. Install <a href="https://github.com/humbug/box/blob/master/doc/installation.md#installation">Box phar manager</a>

2. Clone PieceofScript repository
    ```
    git clone https://github.com/maximw/PieceofScript.git
    ```

3. Move to  PieceofScript folder
    ```
    cd ./PieceofScript
    ```

4. Build phar
    ```
    box compile
    ```

<a name="run"></a>
### Run

Running testing scenario `startFile.pos`: 

    ```
    php pos.phar run ./startFile.pos --junit=result_in_junit_format.xml -vvv --config=config.yaml
    ```

Directory containing startFile.pos set as current working dir during testing. All relative paths count from it. 


Get list of all available commands: 

    ```
    php pos.phar list
    ```

<a name="config"></a>    
### Configuration

Configuration file could be set in command line by `--config` option or it will be read from `./config.yaml` from current directory. If config file was not set, default values will be used.

<b>endpoints_file</b> - file to read API Endpoints definitions from. Default is `./endpoints.yaml`.

<b>endpoints_dir</b> - directory to search files *.yaml to read API Endpoints definitions from. Default is `./endpoints`.

<b>generators_file</b> - file to read generators definitions from. Default is `./generators.yaml`.

<b>generators_dir</b>  - directory to search files *.yaml to read generetors definitions from. Default is `./generators`.

<b>cache_dir</b>  - directory to store temporary files. Default is system temp directory.

<b>http_connect_timeout</b> - HTTP connection timeout in seconds. Default `0` - no timeout.

<b>http_read_timeout</b> - HTTP read timeout in seconds. Default is value of default_socket_timeout PHP ini setting.

<b>http_max_redirects</b> - maximal count of HTTP redirects allowed. Default is `0` - no redirects allowed.

<b>current_timestamp</b> - current timestamp. Default is timestamp of moment of config initialization.

<b>default_date_format</b> - format to convert Dates to Strings. Default is ISO8601 `Y-m-d\TH:i:sO`.

<b>default_timezone</b> - one of <a href="https://secure.php.net/manual/en/timezones.php">available timezones</a>. Default is system timezone returned by PHP date_default_timezone_get().

<b>json_max_depth</b> - JSON parsing recursion depth. Default is `512`.

<b>random_seed</b> - integer seed for initiate random functions. Default value selected randomly.

<b>faker_locale</b> - locale for Faker functions. Default is `en_US`.