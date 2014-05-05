CarteBlanche - Controllers
==========================

Related PHP classes:

-   `\CarteBlanche\Abstracts\AbstractController`
-   `\CarteBlanche\Interfaces\ControllerInterface`

Controllers of the distribution:

-   `\CarteBlanche\Controller\DefaultController`
-   `\CarteBlanche\Controller\AjaxController`
-   `\CarteBlanche\Controller\ErrorController`
-   `\CarteBlanche\Controller\CommandLineController`
-   they must all extend the `\CarteBlanche\Abstracts\AbstractControllerCarteBlancheDefault` class

Other useful classes:

-   `\CarteBlanche\Abstracts\AbstractControllerConfigurable`

Classic HTML errors:

-   `\CarteBlanche\Exception\NotFoundException` (404 not found error)
-   `\CarteBlanche\Exception\AccessForbiddenException` (403 forbidden error)
-   `\CarteBlanche\Exception\InternalServerErrorException` (500 internal server error)


## What is a controller?

A "controller" is a PHP class that will be in charge to handle a request, with arguments, 
data, files or other components eventually, to treat these informations, to execute one or 
more actions and, finally, to render an output content. The "controller" will, as its name 
seems to mean, control the requested action of the application.

## How does it work in CarteBlanche?

In CarteBlanche, controllers must extend the `\CarteBlanche\Abstracts\AbstractController` class or
its descendants or at least implement the `\CarteBlanche\Interfaces\ControllerInterface` interface.
Each action of a controller is defined as a method named `[action_name]Action()` that can
handles some parameters, required or not, which will be fetched from treated request (by default the
`\CarteBlanche\App\Request` instance). Finally, the action must return a "thing" that permits
to send a final response (by default with the `\CarteBlanche\App\Response` class).

### Fetching arguments

When you define a controller method, you can sign it with parameters that will be searched
in the current request or environment and finally be passed calling the action's method.
To do so, your parameters MUST be named as they will appear in the request. As CarteBlanche
will send an error if a parameter has no default value (seems therefore required) and is not
found in the request, the best practice is to define a default value most of the time.

For instance, saying we are executing the following `testAction` method:

    function testAction( $arg1 , $arg2 = "default" ) {...}

the parameters `$arg1` and `$arg2` will be searched in the request. If `$arg2` is not found,
nothing will be passed calling the method so the default value will be taken. If `$arg1` is
not found, an error will be thrown as no default value is defined, so it is considered as
mandatory to execute the action.

    .../?arg1=A&arg2=B      =>  testAction ( 'A' , 'B' )
    .../?arg1=A             =>  testAction ( 'A' , 'default' )
    .../?arg1=A&arg2=0      =>  testAction ( 'A' , 0 or NULL or FALSE )
    .../?arg2=B             =>  Error because no `arg1` is defined

### Returning the output

At the end of an action, the controller's method must return an output to inform the client
about processed action. This output can be a full HTML string, a plain text string, a file
to download, a redirection to a new URL etc.

In CarteBlanche, controller's methods MUST return one of the followings:

-   **nothing** if the PHP process stops before method's return (with a `die()` for example)
-   **a string** that will be considered as the final response main content and will be loaded
    in the global `$container->get('response')` object or in the global template
-   **an array** of values, constructed like `array ( view_file , parameters )`, that will
    be considered as the name of a view to include followed by an array of parameters to load
    to this view ; in this case, the final content will be the string of the rendering of the
    view, passing it the parameters, and will be finally loaded in the `$container->get('response')`
    main content
-   **a Response object** which will replace the global response prepared by the Kernel.



----
**(c) 2013-2014 [Les Ateliers Pierrot](http://www.ateliers-pierrot.fr/)** - Paris, France - Some rights reserved.

This documentation is licensed under the [Creative Commons - Attribution - Share Alike - Unported - version 3.0](http://creativecommons.org/licenses/by-sa/3.0/) license.
