pipeline {
    agent any

    environment {
        DB_HOST = "mysql-container"
        DB_PORT = "3306"
        DB_DATABASE = "laravel"
        DB_USERNAME = "root"
        DB_PASSWORD = ""
    }

    stages {
        stage('Start Database') {
            steps {
                script {
                    sh '''
                        echo "Starting MySQL using Docker..."
                        docker network create laravel-network || true
                        docker run --rm -d --name mysql-container \
                            --network=laravel-network \
                            -e MYSQL_ROOT_PASSWORD=${DB_PASSWORD} \
                            -e MYSQL_DATABASE=${DB_DATABASE} \
                            -p 3306:3306 \
                            mysql:5.7
                        sleep 10
                    '''
                }
            }
        }

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Build & Install Dependencies') {
            steps {
                script {
                    docker.image('composer:2.6').inside('--network=laravel-network -u root') {
                        sh '''
                            cp .env.example .env || true
                            rm -f composer.lock
                            composer install --no-interaction --prefer-dist
                        '''
                    }
                }
            }
        }

        stage('Set APP_KEY & Migrate Database') {
            steps {
                script {
                    docker.image('php:8.2-cli').inside('--network=laravel-network -u root') {
                        sh '''
                            php artisan key:generate
                            php artisan config:clear
                            php artisan cache:clear
                            php artisan migrate --force
                        '''
                    }
                }
            }
        }

        stage('Run Tests') {
            steps {
                script {
                    docker.image('php:8.2-cli').inside('--network=laravel-network -u root') {
                        sh 'php artisan test'
                    }
                }
            }
        }

        stage('Deploy') {
            steps {
                script {
                    docker.image('debian').inside('-u root') {  // Ubah dari Ubuntu ke Debian
                        sh 'echo "Deploying Laravel Application on Debian"'
                    }
                }
            }
        }
    }

    post {
        always {
            script {
                sh 'docker stop mysql-container || true'
                sh 'docker network rm laravel-network || true'
            }
        }
    }
}
