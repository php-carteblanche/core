CarteBlanche - The container
============================


## What is the CarteBlanche's container?

The `\CarteBlanche\App\Container` object is the central gateway to get any existing object of the application.
It manages all kernel dependencies and all application services in one single and simple
object with getter, setter and loader methods.

The container is the most important object of CarteBlanche with its Kernel. It is created at
the kernel booting process and is therefore always accessible.


## How does it work?

The container basically works as a simple registry of objects referenced by a unique index.

### Getting a container object

The basic usage of the container is the way to get an object using the `get()` method:

    $container->get( object index )

It is as simple as this! Every object used or instanciated during the kernel booting or after
is referenced with an index and is accessible getting this index.

### Setting a container object

To register a new instance in the container, just use the `set()` method:

    $container->set( object index , object [, force_overload = false] )

The `object` parameter MUST be a full created object.

### Load a new service

The container also works as a dependency injector. It is designed to create an instance of
a service "on the fly" and return this instance following some specific rules.

In CarteBlanche, a service loader is just a class that MUST implement the
`\CarteBlanche\App\Interfaces\DependencyLoaderInterface`. This interface only forces its implementors to
define a `load( config , container )` method that will receive a configuration array extracted
form the application global configuration.

    $container->load( object index [, loader class] [, force_overload = false] )

The default loader class name is constructed like `\Loader\ObjectIndexLoader`.


----
**Copyleft (c) 2013 [Les Ateliers Pierrot](http://www.ateliers-pierrot.fr/)** - Paris, France - Some rights reserved.

This documentation is licensed under the [Creative Commons - Attribution - Share Alike - Unported - version 3.0](http://creativecommons.org/licenses/by-sa/3.0/) license.
