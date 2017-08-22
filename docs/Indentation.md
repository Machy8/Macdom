# Indentation

Indenting in Macdom is used to define whether the tag is nested in another or not.

## Defining levels
**Two options**
1. Spaces (default value is 4 spaces)
2. Tabulators (default indentation method)

### Tabulators
*Example:*
```` Slim
div (0 tabulators)
  div (1 tabulators)
    div (2 tabulators)
````

### Spaces
If you want to to use spaces indentation use `$macdom->setSpacesIndentation(4)`.

*Example:*
```` Slim
div (0 spaces)
  div (4 spaces)
    div (8 spaces)
````
