pipeline {
  agent any
  options {
    timestamps()
    disableConcurrentBuilds()
  }

  stages {
    stage('啟動通知') {
      steps {
        echo 'job start...'
      }
    }

    stage('Checkout') {
      steps {
        checkout scm
        echo "get last code from origin..."
      }
    }

    // stage('Composer install') {
    //   steps {
    //     bat '''
    //       if exist composer.json (
    //         echo == Composer install ==
    //         composer install --no-interaction --prefer-dist
    //       ) else (
    //         echo composer.json not found, skip composer install
    //       )
    //     '''
    //   }
    // }

    // 只針對 dev/* 分支做 PHPCS
    // stage('PHPCS check') {
    //   when {
    //     expression { env.BRANCH_NAME ==~ /^dev\\/.*$/ }
    //   }
    //   steps {
    //     script {
    //       def result = bat(
    //         script: '''
    //           echo == 準備報告資料夾 ==
    //           if not exist build\\reports mkdir build\\reports

    //           set PHPCS=vendor\\bin\\phpcs.bat
    //           if not exist %PHPCS% set PHPCS=vendor\\bin\\phpcs
    //           if not exist %PHPCS% set PHPCS=phpcs

    //           echo 使用 PHPCS：%PHPCS%
    //           "%PHPCS%" ^
    //             --standard=PSR12 ^
    //             --extensions=php ^
    //             --ignore=vendor/*,node_modules/*,storage/*,bootstrap/cache/*,public/build/* ^
    //             --report-full ^
    //             --report-checkstyle=build/reports/phpcs-checkstyle.xml ^
    //             app\\Http\\Controllers\\TestSmsController.php
    //         ''',
    //         returnStatus: true
    //       )
    //       if (result != 0) {
    //         error "PHPCS 檢查未通過，請修正錯誤後再提交。"
    //       } else {
    //         echo "PHPCS 檢查通過！"
    //       }
    //     }
    //   }
    //   post {
    //     always {
    //       archiveArtifacts artifacts: 'build/reports/**/*.xml', fingerprint: true, onlyIfSuccessful: false
    //     }
    //   }
    // }

    // 只有 dev 或 origin/dev 時自動合併到 main（前面失敗則不會執行）
    stage('Merge dev -> main') {
      when {
        expression { (env.BRANCH_NAME == 'dev') || ((env.GIT_BRANCH ?: '').endsWith('/dev')) }
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

    // main 分支通知（只要 push 到 main，就寄信）
    stage('Notify main push') {
      // when {
      //   expression { (env.BRANCH_NAME == 'main') || ((env.GIT_BRANCH ?: '').endsWith('/main')) }
      // }
      steps {
        script {
          echo "mail sending..."
          
          // 需要 Mailer 外掛，且在 Manage Jenkins → Configure System 設好 SMTP
          mail to: 'joe_tsai@168money.com.tw',
               subject: "[Jenkins] main 分支有新的 push - ${env.JOB_NAME} #${env.BUILD_NUMBER}",
               body: """\
專案：${env.JOB_NAME}
建置編號：#${env.BUILD_NUMBER}
分支：${env.BRANCH_NAME ?: (env.GIT_BRANCH ?: 'unknown')}

此信為 Jenkins 自動通知（main 分支 push 事件）。
"""
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
