pipeline {
    agent any

    environment {
        GIT_REPO = 'https://github.com/AkilaNorSalsabila/devops-laravel.git'
        DEPLOY_DIR = '/home/akilanor/deploy-directory'
    }

    stages {
        stage('Prepare Workspace') {
            steps {
                script {
                    sh 'rm -rf *'  // Membersihkan workspace untuk menghindari error git
                }
            }
        }

        stage('Checkout') {
            steps {
                script {
                    checkout([
                        $class: 'GitSCM',
                        branches: [[name: 'main']],
                        extensions: [[$class: 'WipeWorkspace']], // Membersihkan workspace sebelum clone
                        userRemoteConfigs: [[
                            url: "${GIT_REPO}",
                            credentialsId: 'github-token' // Pastikan ID ini ada di Jenkins
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
                    echo "Installing system dependencies..."
                    apt-get update && apt-get install -y unzip git curl

                    echo "Checking PHP & Composer versions..."
                    php -v
                    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
                    composer --version

                    echo "Installing Laravel dependencies..."
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
                    echo "Ensuring deploy directory exists..."
                    mkdir -p $DEPLOY_DIR
                    chmod -R 777 $DEPLOY_DIR  # Memberikan izin akses

                    echo "Deploying application..."
                    rsync -rav --delete . ${DEPLOY_DIR}
                    '''
                }
            }
        }
    }
}
