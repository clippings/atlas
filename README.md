# Harp Query

[![Build Status](https://travis-ci.org/harp-orm/query.svg?branch=master)](https://travis-ci.org/harp-orm/query)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/harp-orm/query/badges/quality-score.png?s=429880c25663a4c0c4768fbb4158abe048726e82)](https://scrutinizer-ci.com/g/harp-orm/query/)
[![Code Coverage](https://scrutinizer-ci.com/g/harp-orm/query/badges/coverage.png?s=e32088c682e67d1c7eec28b58f9c6a34a2123ed7)](https://scrutinizer-ci.com/g/harp-orm/query/)
[![Latest Stable Version](https://poser.pugx.org/harp-orm/query/v/stable.svg)](https://packagist.org/packages/harp-orm/query)

A query builder library, extending PDO to allow writing queries in object oriented style.
Intelligently manages passing parameters to PDO's execute.

## Quick usage example

```php
use Harp\Query\DB;

$db = new DB('mysql:dbname=test-db;host=127.0.0.1', 'root');

$query = $db->select()
    ->from('users')
    ->where('seller', true)
    ->join('profiles', ['profiles.user_id' => 'users.id'])
    ->limit(10);

foreach ($query->execute() as $row) {
    var_dump($row);
}

echo "Executed:\n";
echo $query->humanize();
```

The query ``execute()`` method will generate a PDOStatement object that can be iterated over. All the variables are passed as position parameters ("seller = ?") so are properly escaped by the PDO driver.

## Why?

PHP has quite a lot of excellent query builder classes already. For example [Paris/Idiom](http://j4mie.github.io/idiormandparis/), [Kohana Query Builder](http://kohanaframework.org/3.3/guide/database/query/builder) etc. Why have another one? Here is my elevator pitch:

- Integrate with PDO - since it already has quite a lot of support for different DB drivers, harp-orm/query can use all that wealth of functionality out of the box. The base DB class extends PDO class, and the select result is actually PDOStatement object. And all of this [already has great docs](http://us3.php.net/manual/en/book.pdo.php)
- Use PSR coding standards and Symfony naming conventions for more familiar and readable codebase.
- Use PSR logging to integrate with any compatible logger.
- Support more rarely used SQL constructs, e.g. INSERT IGNORE, UNION, UPDATE JOIN etc.
- Precise methods to express intent more clearly e.g. methods like where, whereIn, whereLike allow you to write exactly what you intend, and not expect guesswork from the library. This can provide more error-free codebase.
- Fully covered with DockBlocks so static code analysis on packages built on top of it can be more accurate
- Full test coverage

## Connecting to the database

Connecting to the database is a done with the "DB" object. It lazy loads a PDO connection object, and has the same arguments.

```php
use Harp\Query\DB;

$db = new DB(
    'mysql:dbname=test-db;host=127.0.0.1',
    'root',
    'mypass',
    [PDO::ATTR\_DEFAULT\_FETCH\_MODE => PDO::FETCH\_BOTH]
);
$db->getPdo();
```

You can set some additional option for the DB object

```
$logger = new NullLogger(); // Some PSR logger
$db->setLogger($logger);

// Standard sql '"' double quotes to escape table / column names
$db->setEscaping(DB::ESCAPING_STANDARD);

// Mysql '`' backticks to escape table / column names
// This is the default option
$db->setEscaping(DB::ESCAPING_MYSQL);

// No escaping for table / column names
$db->setEscaping(DB::ESCAPING_NONE);
```

## Retrieving data (Select)

Tretrieve data from the database, use [Select class](/src/Select.php).

An example select:

```php
use Harp\Query\DB;

$db = new DB('mysql:dbname=test-db;host=127.0.0.1', 'root');

$select = $db->select()
    ->from('users')
    ->column('users.*')
    ->where('username', 'Tom')
    ->whereIn('type', ['big', 'small'])
    ->limit(10)
    ->order('created_at', 'DESC');

$result = $select->execute();

foreach ($result as $row) {
    var_dump($row);
}
```

## Inserting data (Insert)

Tretrieve insert new data to the database, use [Insert class](/src/Insert.php).

An example insert:

```php
use Harp\Query\DB;

$db = new DB('mysql:dbname=test-db;host=127.0.0.1', 'root');

$insert = $db->insert()
    ->into('users')
    ->set([
        'name' => 'Tom',
        'family_name' => 'Soyer'
    ]);

$insert->execute();

echo $insert->getLastInsertId();
```

## Deleting data (Delete)

Tretrieve delete data from the database, use [Delete class](/src/Delete.php).

An example delete:

```php
use Harp\Query\DB;

$db = new DB('mysql:dbname=test-db;host=127.0.0.1', 'root');

$delete = $db->delete()
    ->from('users')
    ->where('score', 10)
    ->limit(10);

$delete->execute();
```

## Updating data (Update)

Tretrieve update data in the database, use [Update class](/src/Update.php).

An example update:

```php
use Harp\Query\DB;

$db = new DB('mysql:dbname=test-db;host=127.0.0.1', 'root');

$update = $db->update()
    ->table('users')
    ->set(['name' => 'New Name'])
    ->where('id', 10);

$update->execute();
```

## Detailed docs

- [Select](/docs/Select.md)
- [Insert](/docs/Insert.md)
- [Delete](/docs/Delete.md)
- [Update](/docs/Update.md)
- [Union](/docs/Union.md)

## License

Copyright (c) 2014, Clippings Ltd. Developed by Ivan Kerin as part of [clippings.com](http://clippings.com)

Under BSD-3-Clause license, read LICENSE file.
