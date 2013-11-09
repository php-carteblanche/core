CarteBlanche - Configuration
============================


## How does it work?

The configurations in CarteBlanche is handled by the `\CarteBlanche\App\Config` class. It is based on
the parsing of "in files" configurations and can read INI files, XML files, JSON files or
direct PHP arrays files.

Except for the required configuration settings of CarteBlanche's Kernel, all configuration
file can be override quite easily by creating a copy of the original one in `config/`.

>   For instance to override the configuration file `my_config.ini`, just do:

>       | config/
        | ------ my_config.ini
        | ------ vendor\
        | -------------- my_config.ini

>   This way, your configuration settings define in `config/my_config.ini` will override the defaults
    `config/vendor/my_config.ini`.


## `\CarteBlanche\App\Config` usage

The class is quite simple to use: `$config->get(my config setting index)`.

As described in the "rules" section below, you can write an index with as many depths as
you want by writing something like `scope_1.scope_2.scope_3.index`, which will be understood
like `$config_registry [scope_1] [scope_2] [scope_3] [index]`. The dotted notation is called
the `path` of the configuration entry and is always unique.


## Configuration rules

### Write settings

Using the configuration manager of CarteBlanche, we have to keep in mind that all configuration
values will finally be a complex array with many depths but that is still an array, a quite
simple object. Knowing that, all your configuration can be easily written like:

    scope_1.my_index = "my value"
    scope_1.scope_2.my_index = "another value"

The configuration manager will follow these simple rules:

-   each index will be slugified, written in utf8, lower case with underscores replacing spaces
    or any other punctuation character
-   each scope is separated from its parent by a dot
-   each parent will be created if it doesn't exist

### Retrieve settings

Following the above examples, to retrieve your configuration values you would write:

    $config->get('scope_1.my_index') // => "my value"
    $config->get('scope_1.scope_2.my_index') // => "another value"
    $config->get('scope_1.scope_2.not_defined_index') // => null

By default, no error is thrown if the index doesn't exist in the configuration stack. You
can ask the object to send an error in this case passing it explicitly `\CarteBlanche\App\Config::NOT_FOUND_ERROR`
as second argument. Optionally, you can define a default value that will be returned if
the entry doesn't exist as 3rd argument.

    $config->get('scope_1.scope_2.not_defined_index', \CarteBlanche\App\Config::NOT_FOUND_ERROR) // => error
    $config->get('scope_1.scope_2.not_defined_index', \CarteBlanche\App\Config::NOT_FOUND_GRACEFULLY, 'abc') // => "abc"

### Call a pre-defined value or stack of values

You can use the following notation to write a reference to an existing configuration value
or set of values:

-   `%index%` as a reference to the `index` value defined elsewhere ; `index` can be a full path
-   `{closure}` as a closure that will be evaluated at runtime ; you can write a closure
    like `function(){ return true; }` or just `return true`


## Examples

Let's consider the following final configuration array:

    $config = array(
        // an array of values
        'scope_1' => array(
            // a string value
            'my_index' => "my value",
            // a boolean value
            'my_bit_value' => false,
            // an array of values
            'scope_2' => array(
                // a string value
                'my_index' => "my value",
                // a boolean value
                'my_bit_value' => true,
            ),
        ),
        // a list of items
        'scope_3' => array( "item 1" , "item 2" ),
        // an indexed list of items
        'scope_4' => array( 0=>"item 1" , 3=>"item 2" , 'last'=>"last item" ),
        // a reference to another setting
        'reference_1' => $config['scope_1']['scope_2']['my_index'],
        // a reference to a full stack of settings
        'reference_2' => $config['scope_1'],
        // an example of slugification
        'my_phrase_with_punctuation' => "a value, with punctuation !",
        // an example of closure
        'closure_index' => function(){ return true===MY_CONSTANT; },
    )

In a INI format file, we would write:

    scope_1.my_index = "my value"
    scope_1.my_bit_value = false OR off OR 0
    scope_1.scope_2.my_index = "my value"
    scope_1.scope_2.my_bit_value => true OR on OR 1
    scope_3[] = "item 1"
    scope_3[] = "item 2"
    scope_4.0 = "item 1"
    scope_4.3 = "item 2"
    scope_4.last = "last item"
    reference_1 = %scope_1.scope_2.my_index%
    reference_2 = %scope_1%
    closure_index = { return true===MY_CONSTANT; }

    // this will throw an error on parsing
    "My phrase, with punctuation" = "a value, with punctuation !"

In a JSON format file, we would write:

    {
        "scope_1.my_index": "my value",
        "scope_1.my_bit_value": "false" OR "off" OR 0,
        "scope_1.scope_2.my_index": "my value",
        "scope_1.scope_2.my_bit_value": "true" OR "on" OR 1,
        "scope_3": [ "item 1" , "item 2" ],
        "scope_4": { 0: "item 1" , 3: "item 2" , "last": "last item" },
        "reference_1": "%scope_1.scope_2.my_index%",
        "reference_2": "%scope_1%",
        "My phrase, with punctuation": "a value, with punctuation !",
        "closure_index": "{ return true===MY_CONSTANT; }"
    }

In an XML format file, we would write:

    <scope_1>
        <my_index>my value</my_index>
        <my_bit_value>false OR off OR 0</my_bit_value>
        <scope_2>
            <my_index>my value</my_index>
            <my_bit_value>true OR on OR 1</my_bit_value>
        </scope_2>
    </scope_1>
    <scope_3>
        <item>item 1</item>
        <item>item 2</item>
    </scope_3>
    <scope_4>
        <item id="0">item 1</item>
        <item id="3">item 2</item>
        <item id="last">last item</item>
    </scope_4>
    <reference_1>%scope_1.scope_2.my_index%</reference_1>
    <reference_2>%scope_1%</reference_2>
    <item id="My phrase, with punctuation">a value, with punctuation !</item>
    <closure_index>{ return true===MY_CONSTANT; }</closure_index>

In a PHP format file, we would write:

    return array(
        "scope_1.my_index" => "my value",
        "scope_1.my_bit_value" => false OR "off" OR 0,
        "scope_1.scope_2.my_index" => "my value",
        "scope_1.scope_2.my_bit_value" => true OR "on" OR 1,
        "scope_3" => array( "item 1" , "item 2" ),
        "scope_4" => array( 0=> "item 1" , 3=> "item 2" , "last"=> "last item" ),
        "reference_1" => "%scope_1.scope_2.my_index%",
        "reference_2" => "%scope_1%",
        "My phrase, with punctuation" => "a value, with punctuation !",
        "closure_index" => "{ return true===MY_CONSTANT; }"
    );

## Default CarteBlanche format: INI

We decided to use some configuration files in INI format most of the time by default. It is
easy to read and modify, allows different ways to write arrays (using section headers for instance)
and is parsed quickly by PHP. For more informations, see <http://php.net/manual/en/function.parse-ini-file.php>.


----
**Copyleft (c) 2013 [Les Ateliers Pierrot](http://www.ateliers-pierrot.fr/)** - Paris, France - Some rights reserved.

This documentation is licensed under the [Creative Commons - Attribution - Share Alike - Unported - version 3.0](http://creativecommons.org/licenses/by-sa/3.0/) license.
