#Testing project structure

Working directory is directory containing start file, set in command line.
```
pos.phar run ./RickAndMorty/20MinutesTest.pos
```
Working directory is `./RickAndMorty` here. 

Project has 3 types of files 
- definitions of API endpoints are in YAML format and could be set in files `endpoints.yaml` 
or `./endpoints/*.yaml`
- definitions of testing data generators are in YAML format too and could be set in files `generators.yaml` 
or `./generators/*.yaml`
- testing scenarios,
