WadeLib is a tiny one-file PHP library to be used for deploying web applications.


Title: Description

WadeLib is the library used by WadePHP, the Web Application Deployer written in PHP. To be able to use the library without WadePHP too, WadeLib is distributed as standalone package.

The idea behind this package is to integrate it into a PHP deployment script. Example of such an deployment script which downloads and extracts the Joomla! CMS:

:	<?php
:	// Contents of WadeLib.php:
:	// ...
:
:	// Deployment instructions like:
:	WadeLib::downloadFile('http://www.codeless.at/joomla-2.5.7.zip');
:	WadeLib::extractZipfile('joomla-2.5.7.zip');
:	WadeLib::createFile('configuration.php');
:	WadeLib::changeFilemode('configuration.php', 0646);
:	WadeLib::extractZipfile('myself.zip'); # Special instruction!
:
:	// Start of myself.zip after __halt_compiler():
:	__halt_compiler();Integrate some zipped data


Title: Installation

Through <http://www.packagist.org>.


Title: History

- Version 0.5.1 released on 2012-10-11:
	- Fixed the extractAppendedData() method to run even when there's no appended data
	- Changed the license to CC-BY-3.0
- Re-established this project on 2012-10-11
- Decided to discontinue this project on 2012-10-10
- Version 0.5.0 released on 2012-09-21


Title: Credits and Bugreports

WadeLib was written by Codeless (<http://www.codeless.at/>). All bugreports can be directed to more@codeless.at. Even better, bugreports are posted on the github-repository of this package: <https://www.github.com/codeless/wadelib>.


Title: License

This work is licensed under a Creative Commons Attribution 3.0 Unported License, see <http://creativecommons.org/licenses/by/3.0/deed.en_US>.
