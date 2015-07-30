Contributing
------------

# Step 1
Git clone the php code sniffer

    git clone git@github.com:squizlabs/PHP_CodeSniffer.git

# Step 2
[Install PHPUnit](https://phpunit.de/getting-started.html)


# Step 3
Fork repository and clone your coding standard repository into "CodeSniffer/Standards/"

    git clone git@github.com:<github_username>/symfony-coding-standard.git Symfony


# Step 4

Check out a new branch to make your changes on: `git checkout -b <your_new_branch>`

# Step 5

Make sure that your tests are running green

    phpunit --filter Symfony
