##PieceofScript installation and run

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

### Run

    ```
    php pos.phar run ./startFile.pos --junit=result_in_junit_format.xml -vvv --config=config.yaml
    ```
    
