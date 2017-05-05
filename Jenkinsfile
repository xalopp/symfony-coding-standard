pipeline {
    agent { docker 'composer/composer' }
    stages {
        stage('prepare') {
            steps {
                sh 'php --version'
                echo 'Install composer'
            }
        }
        stage('build') {
            steps {
                sh 'php --version'
            }
        }
        stage('Test') {
            steps {
                echo 'Testing..'
            }
        }
    }
}

//  - composer install --dev
//  - mkdir -p vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/Symfony
//  - cp -R Sniffs/ vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/Symfony/Sniffs/
//  - cp -R Tests/ vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/Symfony/Tests/
//  - cp ruleset.xml vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/Symfony
//
//script:
//  - (cd vendor/squizlabs/php_codesniffer ; phpunit --filter Symfony_)
//  - ./vendor/squizlabs/php_codesniffer/scripts/phpcs Sniffs --standard=PHPCS --report=summary -np

