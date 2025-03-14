pipeline {
    agent any  // Use any available agent
    
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
            agent {
                docker {
                    image 'php:8.2-cli'
                    args '--user root'
                }
            }
            steps {
                sh 'apt-get update && apt-get install -y unzip git'
                sh 'rm -f composer.lock'
                sh 'composer install --no-interaction --prefer-dist --no-progress'
            }
        }

        stage('Testing') {
            agent {
                docker {
                    image 'php:8.2-cli'
                    args '--user root'
                }
            }
            steps {
                sh 'echo "Menjalankan testing di Debian..."'
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
