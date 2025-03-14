pipeline {
    agent any
    stages {
        stage('Checkout') {
            steps {
                script {
                    deleteDir() // Bersihkan workspace sebelum clone ulang
                    checkout([$class: 'GitSCM', branches: [[name: '*/main']], userRemoteConfigs: [[url: 'https://github.com/AkilaNorSalsabila/devops-laravel.git']]])
                }
            }
        }
        stage('Build') {
            steps {
                sh 'echo "Building..."'
            }
        }
    }
}
