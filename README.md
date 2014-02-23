# php-utils

A collection of my PHP code snippets.

## Classes

- Autoloader – A configurable class autoloader.
- ErrorHandler
- IterableCountable – A countable iterator with additional magic functions.
- KeyValueTable – This is a simple key-value storage that creates it own database table to save the data to a MySQL database.
- HashTable – An iterator, using a SHA1 hash as key. This iterator is able to use non-scalar values as key.
- Lexicon Singleton class for handling strings in different languages via a lexicon-database

## Functions

- getArrayValueReference() – Return a reference to an array value specified by its key path.
- getHtmlList() – This function generates a recurive HTML list from an array or an iterable object. By default, it returns an unordered list (UL). But you can change the templates through the $options argument.
- enumToArray() – Retrieve ENUM-values from a MySQL-column
- getAlias() – Format a document title to be URL-safe.
- array_kshift() – Shift an element off the beginning of &$arr return that as single-entry array, using the original key for the value.
- arrayToCsv() – Return two-dimensional array as CSV string.
- readCsv() – Read a CSV file, given an open resource handle.
- writeCsv() – Write data to a CSV file.
- sortArray() – Sort 2D-associative array by one $field. (ASC)
- sortArrayMultiple() – Sort 2D-associative array by one or many $fields. (ASC and DESC) Extension of getArray(), to allow sorting to multiple columns.
- mergeArrayRecursive() – Merges multiple associative arrays into one.

