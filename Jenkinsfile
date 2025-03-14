pipeline {
    agent any 

    stages {
        stage('Checkout') {
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
                sh 'apt-get update'
                sh 'apt-get install -y unzip'
                sh 'composer install'
            }
        }

        stage('Testing') {
            agent {
                docker {
                    image 'ubuntu'
                    args '-u root'
                }
            }
            steps {
                sh 'echo "Running Tests..."'
                // Tambahkan command untuk menjalankan PHPUnit atau testing lain
            }
        }

        stage('Deploy to Production') {
            agent {
                docker {
                    image 'agung3wi/alpine-rsync:1.1'
                    args '-u root'
                }
            }
            steps {
                sshagent (credentials: ['ssh-prod']) {
                    sh 'mkdir -p ~/.ssh'
                    sh 'ssh-keyscan -H "$PROD_HOST" > ~/.ssh/known_hosts'
                    sh "rsync -rav --delete ./laravel/ ubuntu@$PROD_HOST:/home/ubuntu/prod.kelasdevops.xyz/"
                }
            }
        }
    }
}
