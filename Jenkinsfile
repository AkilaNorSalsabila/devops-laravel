pipeline {
    agent any

    environment {
        COMPOSER_ALLOW_SUPERUSER = '1'
    }

    stages {
        stage('Checkout') {
            steps {
                script {
                    withCredentials([string(credentialsId: 'github-token', variable: 'GITHUB_TOKEN')]) {
                        sh 'git clone https://$GITHUB_TOKEN@github.com/AkilaNorSalsabila/devops-laravel.git .'
                    }
                }
            }
        }

        stage('Install Dependencies') {
            steps {
                sh 'sudo apt-get update && sudo apt-get install -y unzip git'
                sh 'rm -f composer.lock'
                sh 'composer install --no-interaction --prefer-dist --no-progress'
            }
        }

        stage('Testing') {
            steps {
                sh 'echo "Menjalankan testing di Jenkins agent..."'
            }
        }

        stage('Deploy to Production') {
            steps {
                withCredentials([string(credentialsId: 'github-token', variable: 'GITHUB_TOKEN')]) {
                    sh 'rsync -rav --delete ./ ./deploy-directory/'
                }
            }
        }
    }
}
