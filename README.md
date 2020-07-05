# CAPTCHA+

Through the use of permissions, you can require CAPTCHA for:
* Posting new topics or replies to existing topics
* Sending PMs

## Installation

Copy the extension to phpBB/ext/davidiq/captchaplus

Go to "ACP" > "Customise" > "Extensions" and enable the "CAPTCHA+" extension.

## Tests and Continuous Integration

We use Travis-CI as a continuous integration server and phpunit for our unit testing. See more information on the [phpBB Developer Docs](https://area51.phpbb.com/docs/dev/master/testing/index.html).
To run the tests locally, you need to install phpBB from its Git repository. Afterwards run the following command from the phpBB Git repository's root:

Windows:

    phpBB\vendor\bin\phpunit.bat -c phpBB\ext\davidiq\captchaplus\phpunit.xml.dist

others:

    phpBB/vendor/bin/phpunit -c phpBB/ext/davidiq/captchaplus/phpunit.xml.dist

## License

[GNU General Public License v2](license.txt)
