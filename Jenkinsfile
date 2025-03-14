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
            steps {
                sh '''
                    echo "Installing required dependencies..."
                    apt-get update && apt-get install -y unzip git curl zip \
                        libzip-dev libonig-dev libpng-dev libjpeg-dev \
                        libfreetype6-dev libxml2-dev php8.2-curl php8.2-mbstring \
                        php8.2-xml php8.2-tokenizer php8.2-dom php8.2-zip php8.2-bcmath \
                        php8.2-xmlwriter rsync || echo "Failed to install dependencies"

                    echo "Checking Composer installation..."
                    if ! [ -x "$(command -v composer)" ]; then
                        curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
                    fi
                    composer self-update

                    echo "Installing Composer dependencies..."
                    composer install --optimize-autoloader --ignore-platform-req=ext-curl || exit 1
                '''
            }
        }

        stage('Run Tests') {
            steps {
                script {
                    def testsFailed = false
                    try {
                        sh '''
                            echo "Running Laravel tests..."
                            if [ -f artisan ]; then
                                php artisan test || testsFailed=true
                            fi
                            
                            echo "Running PHPUnit tests..."
                            if [ -x vendor/bin/phpunit ]; then
                                ./vendor/bin/phpunit || testsFailed=true
                            else
                                echo "PHPUnit not found, skipping tests."
                                testsFailed=true
                            fi
                        '''
                    } catch (Exception e) {
                        testsFailed = true
                    }

                    if (testsFailed) {
                        echo "Tests failed, marking build as FAILED."
                        currentBuild.result = 'FAILURE'
                        error("Stopping pipeline due to failed tests.")
                    }
                }
            }
        }

        stage('Deploy to Production') {
            when {
                expression { currentBuild.result == null || currentBuild.result == 'SUCCESS' }
            }
            steps {
                withCredentials([usernamePassword(credentialsId: 'github-token', usernameVariable: 'GIT_USER', passwordVariable: 'GIT_PASSWORD')]) {
                    sh '''
                        echo "Deploying application..."
                        mkdir -p ${DEPLOY_DIR}
                        rsync -avz --delete --exclude '.env' --exclude 'storage/' --exclude 'vendor/' --exclude '.git' . ${DEPLOY_DIR} || exit 1
                        
                        cd ${DEPLOY_DIR}
                        composer install --no-dev --optimize-autoloader || exit 1
                        
                        php artisan migrate --force || exit 1
                        php artisan config:clear
                        php artisan cache:clear
                        php artisan config:cache
                        php artisan route:cache
                        php artisan view:cache
                        php artisan storage:link || true
                        php artisan queue:restart || true
                        
                        chown -R www-data:www-data ${DEPLOY_DIR}
                        chmod -R 775 ${DEPLOY_DIR}/storage ${DEPLOY_DIR}/bootstrap/cache
                    '''
                }
            }
        }
    }
}
