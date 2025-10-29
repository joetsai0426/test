pipeline {
  agent any
  options {
    timestamps()
    disableConcurrentBuilds()
  }

  stages {
    stage('啟動通知') {
      steps {
        echo 'job已啟動'
      }
    }

    stage('Checkout') {
      steps {
        checkout scm
        echo "已從 origin 抓取最新程式碼"
      }
    }

    stage('Composer install') {
      steps {
        bat '''
          if exist composer.json (
            echo == Composer install ==
            composer install --no-interaction --prefer-dist
          ) else (
            echo composer.json not found, skip composer install
          )
        '''
      }
    }

    stage('PHPCS 檢查') {
      when {
        expression { 
          return (env.BRANCH_NAME == 'dev') || (env.GIT_BRANCH ?: '').endsWith('/dev')
        }
      }
      steps {
        bat '''
          echo == 準備報告資料夾 ==
          if not exist build\\reports mkdir build\\reports

          set PHPCS=vendor\\bin\\phpcs.bat
          if not exist %PHPCS% set PHPCS=vendor\\bin\\phpcs
          if not exist %PHPCS% set PHPCS=phpcs

          echo 使用 PHPCS：%PHPCS%
          "%PHPCS%" ^
            --standard=PSR12 ^
            --extensions=php ^
            --ignore=vendor/*,node_modules/*,storage/*,bootstrap/cache/*,public/build/* ^
            --report-full ^
            --report-checkstyle=build/reports/phpcs-checkstyle.xml ^
            app\\Http\\Controllers\\TestSmsController.php
        '''
      }
      post {
        always {
          archiveArtifacts artifacts: 'build/reports/**/*.xml', fingerprint: true, onlyIfSuccessful: false
        }
      }
    }
  }

  post {
    always {
      echo "Pipeline 結束"
    }
  }
}
