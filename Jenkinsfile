pipeline {
    agent any  // Gunakan agen yang tersedia

    stages {
        stage('Checkout') {
            steps {
                script {
                    withCredentials([string(credentialsId: 'github-token', variable: 'GITHUB_TOKEN')]) {
                        sh '''
                        if [ -d ".git" ]; then
                            echo "Repo sudah ada, melakukan git pull..."
                            git reset --hard
                            git clean -fd
                            git pull origin main
                        else
                            echo "Repo belum ada, melakukan git clone..."
                            git clone --branch main https://$GITHUB_TOKEN@github.com/AkilaNorSalsabila/devops-laravel.git .
                        fi
                        '''
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
