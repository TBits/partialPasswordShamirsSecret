Partial Passwords with Shamirs' Secret Sharing in PHP
=====================================================

The idea is from http://www.smartarchitects.co.uk/news/9/15/Partial-Passwords---How.html, to use Shamir's Secret Sharing to test a partial password.

This is just an implementation, without proof or guarantuee for security. If you find a problem with it, please report it or submit a pull request!

The idea is to provide a password, and define how many letters the user has to enter to verify that he knows the password. The password is not actually stored, but some numbers are calculated based on it, and a hashed value of the shared secret is stored. If the user authenticates with the requested letters, we calculate the shared secret, hash it and compare it to our stored secret.

Example
=======

See the file example/test.php

License
=======

This code is licensed under the MIT License - see the LICENSE file for details
