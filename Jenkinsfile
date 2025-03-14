pipeline {
    agent any 

    stages {
        stage('Checkout') {
            steps {
                checkout scm
                sh 'ls -la' // Debug: Cek file setelah checkout
            }
        }

        stage('Install Dependencies') {
            agent {
                docker {
                    image 'php:8.2-cli' // Menggunakan image yang sudah ada composer
                    args '--user root' // Pastikan menggunakan root user
                }
            }
            steps {
                sh 'which php' // Debug: Cek apakah php ada
                sh 'which composer' // Debug: Cek apakah composer ada
                sh 'apt-get update && apt-get install -y unzip curl'
                sh 'composer install --no-interaction --prefer-dist --no-progress'
            }
        }

        stage('Testing') {
            agent {
                docker {
                    image 'php:8.2-cli' // Ganti ubuntu dengan php:8.2-cli karena sudah ada sh
                    args '--user root'
                }
            }
            steps {
                sh 'which sh' // Debug: Cek apakah sh tersedia
                sh 'echo "Running Tests..."'
                sh 'vendor/bin/phpunit --version' // Debug: Cek apakah PHPUnit ada
                sh 'vendor/bin/phpunit' // Jalankan unit test
            }
        }

        stage('Deploy to Production') {
            agent {
                docker {
                    image 'agung3wi/alpine-rsync:1.1'
                    args '--user root'
                }
            }
            steps {
                sshagent (credentials: ['ssh-prod']) {
                    sh 'mkdir -p ~/.ssh'
                    sh 'ssh-keyscan -H "$PROD_HOST" > ~/.ssh/known_hosts'
                    sh 'ls -la' // Debug: Cek file sebelum deploy
                    sh 'rsync -rav --delete ./laravel/ ubuntu@$PROD_HOST:/home/ubuntu/prod.kelasdevops.xyz/'
                }
            }
        }
    }
}
