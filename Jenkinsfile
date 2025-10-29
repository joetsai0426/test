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

    // 只有在 dev 分支、且前面階段成功時才會執行；將 dev 合併進 main 並推回 GitHub
    stage('Merge dev -> main') {
      when {
        expression { 
          return (env.BRANCH_NAME == 'dev') || (env.GIT_BRANCH ?: '').endsWith('/dev')
        }
      }
      steps {
        withCredentials([usernamePassword(credentialsId: 'github-token', usernameVariable: 'GIT_USER', passwordVariable: 'GIT_TOKEN')]) {
          bat '''
            echo == 準備執行合併 dev -> main ==
            git --version

            echo == 設定使用者 ==
            git config user.name "%GIT_USER%"
            git config user.email "joe_tsai@168money.com.tw"


            echo == 更新遠端引用 ==
            git fetch origin

            echo == 切換到 main ==
            git checkout main || git checkout -b main origin/main

            echo == 合併 origin/dev 到 main ==
            git merge origin/dev --no-edit

            echo == 推送 main ==
            git push https://%GIT_USER%:%GIT_TOKEN%@github.com/joetsai0426/test.git main
          '''
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