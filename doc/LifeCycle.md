CarteBlanche - Global life cycle
================================


CarteBlanche is an "MVC" like framework that handles a request and respond a response trying
to execute the configured actions during the application life-cycle.


## Request as input, Response as output

The very global schema of the life cycle of CarteBlanche is basically:

    Request => [CarteBlanche] => Response

The `Request` is most of the time a TCP/IP request (but not always, like in command line usage).
The `Response` is most of the time an HTTP formated content (but not always, like for RSS).


## The CarteBlanche work

When you use CarteBlanche, you will set up a first interface, called by the browser, which
will create a new `\CarteBlanche\App\Kernel` instance giving it specific options if needed. Then
you will give it a `Request` to handle and ask it to distribute the work (well, by default, 
the kernel will handle the current request).

This distribution is handled by the **front controller**, which defaults to `\CarteBlanche\App\FrontController`,
whom will try to always have a request to handle and so always give a response to client. According to the route and
arguments found in the handled request, the distribution will call a controller's method passing
it the current request arguments. This method will most of the time construct a content to render with the
response. Finally, the response will be fetched to the client by the front controller.

Anything but the `Kernel` itself and the `Container` is considered as a service and
is handled and accessible via the `Container`. All services are configurable and can be
overridden using any object implementing the required interface. These interfaces are defined
in the `Kernel` itself and you can visualize them browsing to `/dev.php?c=dev&a=cb_config` on
an installed CarteBlanche.



----
**(c) 2013-2015 [Les Ateliers Pierrot](http://www.ateliers-pierrot.fr/)** - Paris, France - Some rights reserved.

This documentation is licensed under the [Creative Commons - Attribution - Share Alike - Unported - version 3.0](http://creativecommons.org/licenses/by-sa/3.0/) license.
