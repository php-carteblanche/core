CarteBlanche - CHANGELOG
=========================

version 0.0.3-dev (17/03/2013)
------------------------------

* The bootstrap file is now generated by the
[CarteBlanche Composer installer](https://github.com/php-carteblanche/installer)
from the bundles list in `composer.json`.

* Installation of bundles and tools is now done by the
[CarteBlanche Composer installer](https://github.com/php-carteblanche/installer)

* All bundles and Tools are now external packages ; bundles can be developed as usual (internally)
but default Tools and proposed Bundles are now external to allow a very light-weight installation
if you just need one of them (or none).


version 0.0.2-dev (14/03/2013)
------------------------------

* Patterns are now external for commodity and re-usability.
See [Patterns](https://github.com/atelierspierrot/patterns).

* Correction of an issue in the binary description of the `composer.json`.


version 0.0.1-dev (13/03/2013)
------------------------------

* First version of the code, with Composer and Bower installers.