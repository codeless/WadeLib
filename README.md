WadeLib is a tiny one-file PHP library to be used for deploying web applications.


# Description

WadeLib is the library used by WadePHP, the Web Application Deployer written in PHP. To be able to use the library without WadePHP too, WadeLib is distributed as standalone package.

The idea behind this package is to integrate it into a PHP deployment script. Example of such an deployment script which downloads and extracts the Joomla! CMS:

	<?php
	// Contents of WadeLib.php:
	// ...

	// Deployment instructions like:
	WadeLib::downloadFile('http://www.codeless.at/joomla-2.5.7.zip');
	WadeLib::extractZipfile('joomla-2.5.7.zip');
	WadeLib::createFile('configuration.php');
	WadeLib::changeFilemode('configuration.php', 0646);
	WadeLib::extractZipfile('myself.zip'); # Special instruction!

	// Start of myself.zip after __halt_compiler():
	__halt_compiler();Integrate some zipped data


# Installation

Through http://www.packagist.org.


# History

- Version 0.5.0 released on 2012-09-21


# Credits and Bugreports

WadeLib was written by Codeless (http://www.codeless.at/). All bugreports can be directed to more@codeless.at. Even better, bugreports are posted on the github-repository of this package: https://www.github.com/codeless/wadelib.


# License

WadeLib is available under the MIT license:

Copyright (c) 2012 Manuel Hiptmair <more@codeless.at>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
