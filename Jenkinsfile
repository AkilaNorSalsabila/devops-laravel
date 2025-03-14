pipeline {
    agent any

    environment {
        GIT_REPO = 'https://github.com/AkilaNorSalsabila/devops-laravel'
        DEPLOY_DIR = '/home/akilanor/deploy-directory'
        COMPOSER_HOME = '/root/.composer'
    }

    stages {
        stage('Cleanup Workspace') {
            steps {
                script {
                    deleteDir()
                }
            }
        }

        stage('Checkout') {
            steps {
                script {
                    checkout([
                        $class: 'GitSCM',
                        branches: [[name: '*/main']],
                        userRemoteConfigs: [[
                            url: "${GIT_REPO}",
                            credentialsId: 'github-token'
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
                    echo "Updating system and installing required dependencies..."
                    apt-get update && apt-get install -y unzip git curl zip \
                        libzip-dev libonig-dev libpng-dev libjpeg-dev \
                        libfreetype6-dev libmcrypt-dev libxml2-dev php-dom \
                        && docker-php-ext-configure zip \
                        && docker-php-ext-install zip gd mbstring pdo pdo_mysql intl xml dom
                    
                    echo "Checking Composer installation..."
                    if ! [ -x "$(command -v composer)" ]; then
                        curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
                    fi
                    composer self-update

                    echo "Installing Composer dependencies..."
                    composer install --no-dev --optimize-autoloader || composer install --ignore-platform-req=ext-dom --no-dev --optimize-autoloader
                '''
            }
        }

        stage('Run Tests') {
            steps {
                script {
                    try {
                        sh 'php artisan test || ./vendor/bin/phpunit'
                    } catch (Exception e) {
                        echo "Tests failed, skipping deployment."
                        currentBuild.result = 'FAILURE'
                    }
                }
            }
        }

        stage('Deploy to Production') {
            when {
                expression { currentBuild.result == null || currentBuild.result == 'SUCCESS' }
            }
            steps {
                withCredentials([string(credentialsId: 'github-token', variable: 'GIT_CREDENTIALS')]) {
                    sh '''
                        echo "Deploying application..."
                        mkdir -p ${DEPLOY_DIR}
                        rsync -avz --delete --exclude '.env' --exclude 'storage/' --exclude 'vendor/' --exclude '.git' . ${DEPLOY_DIR}
                        cd ${DEPLOY_DIR}
                        composer install --no-dev --optimize-autoloader
                        php artisan migrate --force
                        php artisan config:clear
                        php artisan cache:clear
                        php artisan config:cache
                        php artisan route:cache
                        php artisan view:cache
                        php artisan storage:link || true
                        php artisan queue:restart
                        chown -R www-data:www-data ${DEPLOY_DIR}
                        chmod -R 775 ${DEPLOY_DIR}/storage ${DEPLOY_DIR}/bootstrap/cache
                    '''
                }
            }
        }
    }
}
