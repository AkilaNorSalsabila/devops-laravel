pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Build') {
            steps {
                script {
                    docker.image('composer:2.6').inside('-u root') {
                        sh '''
                            cp .env.example .env || true
                            rm -f composer.lock
                            composer install --no-interaction --prefer-dist
                        '''
                    }
                }
            }
        }

        stage('Set APP_KEY') {
            steps {
                script {
                    docker.image('php:8.2-cli').inside('-u root') {
                        sh '''
                            php artisan key:generate
                        '''
                    }
                }
            }
        }

        stage('Test') {
            steps {
                script {
                    docker.image('php:8.2-cli').inside('-u root') {
                        sh 'php artisan test'
                    }
                }
            }
        }

        stage('Deploy') {
            steps {
                script {
                    docker.image('ubuntu').inside('-u root') {
                        sh 'echo "Deploying Laravel Application"'
                    }
                }
            }
        }
    }
}
