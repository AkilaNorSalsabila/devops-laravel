pipeline {
    agent any

    stages {
        stage('Clone Repository') {
            steps {
                checkout scm
            }
        }

        stage('Install Dependencies') {
            agent {
                docker {
                    image 'php:8.2'
                    args '-u root'
                }
            }
            steps {
                sh '''
                    apt-get update && apt-get install -y unzip
                    composer install || { echo "Composer install failed"; exit 1; }
                '''
            }
        }

        stage('Testing') {
            agent {
                docker {
                    image 'debian:latest'
                    args '-u root'
                }
            }
            steps {
                sh 'echo "Ini adalah test"'
            }
        }

        stage('Deploy to Production') {
            agent {
                docker {
                    image 'agung3wi/alpine'
                    args '-u root'
                }
            }
            steps {
                sshagent (credentials: ['ssh-prod']) {
                    sh '''
                        mkdir -p ~/.ssh
                        ssh-keyscan -H "$PROD_HOST" >> ~/.ssh/known_hosts
                        rsync -rav --delete ./laravel/ ubuntu@$PROD_HOST:/home/ubuntu/prod.kelasdevops.xyz/
                    '''
                }
            }
        }
    }
}
