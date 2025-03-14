pipeline {
    agent any

    environment {
        GIT_REPO = 'https://github.com/AkilaNorSalsabila/devops-laravel.git'
        DEPLOY_DIR = '/home/akilanor/deploy-directory'
    }

    stages {
        stage('Checkout') {
            steps {
                script {
                    checkout([
                        $class: 'GitSCM',
                        branches: [[name: 'main']],  // FIXED: Menghapus '*/'
                        userRemoteConfigs: [[
                            url: "${GIT_REPO}",
                            credentialsId: 'github-token'  // Pastikan ID ini sudah ada di Jenkins
                        ]]
                    ])
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
                sh '''
                    apt-get update && apt-get install -y unzip git curl
                    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
                    composer install --no-interaction --prefer-dist --no-progress
                '''
            }
        }

        stage('Run Tests') {
            steps {
                sh 'echo "Menjalankan unit testing..."'
            }
        }

        stage('Deploy to Production') {
            steps {
                withCredentials([string(credentialsId: 'github-token', variable: 'GITHUB_TOKEN')]) {
                    sh '''
                    echo "Deploying application..."
                    mkdir -p $DEPLOY_DIR  # FIXED: Menggunakan direktori tujuan yang benar
                    rsync -rav --delete . ${DEPLOY_DIR}
                    '''
                }
            }
        }
    }
}
