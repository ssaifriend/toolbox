# deploy script

git을 주 개발 스크립트로 사용하고, svn을 배포용으로 사용할 때 사용 할 수 있는 script 입니다.
스크립트는 bash script 기반으로 작성되어 있습니다.

## 기본 설정

실행 전 config-sample 파일을 config 파일로 복사 후 config 내 경로를 설정해주세요.

## 스크립트별 사용 방법
공통 사항
* 아래 내용에서 arg로 있는 하위경로는 config의 git 폴더 내 하위폴더명 입니다. 최상위인 경우 ./ 를 사용하시면 됩니다.
* 하위경로가 submodule이나 다른 git repo인 경우 해당 git repo의 git커밋ID를 써야 합니다.


* ./composer 하위경로 (특정 패키지)
  * svn의 composer를 업데이트 후 git의 동일 경로에 composer.lock을 복사한 뒤, composer install 합니다.
  * require-dev는 설치되지 않습니다.
  * 특정 패키지를 입력 한 경우 해당 패키지만 업데이트 합니다.
* ./clone 하위경로 git커밋ID
  * git의 commit을 svn에 적용합니다. (merge 커밋도 지원)
* ./commit 커밋종류 (하위경로) (git커밋ID)
  * svn을 커밋합니다.
  * 커밋종류 목록: all, add, modify, delete
  * 하위경로와 커밋ID를 입력하면 해당 커밋의 커밋 메세지를 그대로 사용합니다.
  * 하위경로와 커밋ID가 없으면 커밋 메세지를 직접 입력 할 수 있습니다. (한 줄 입력만 지원)
* ./clone_commit (하위경로) (git커밋ID)
  * clone + commit all 명령입니다.
* ./rollback 시작_rev (종료_rev)
  * svn의 특정 커밋을 롤백 + 커밋합니다.
  * 종료 rev가 있는 경우 해당 범위 전체를 롤백합니다. 롤백하려는 rev를 그대로 입력해주세요.

## Usage

* ./composer
  * svn 최상위 폴더의 composer 전체를 업데이트 후 composer.lock을 git으로 복사하고 composer install 합니다.
* ./composer ./
  * 위와 동일합니다.
* ./composer admin
  * svn 내 admin 폴더의 composer 전체를 업데이트 후 composer.lock을 git으로 복사하고 composer install 합니다.
* ./composer admin ridibooks/store
  * svn 내 admin 폴더의 ridibooks/store 패키지만 업데이트 후 composer.lock을 git으로 복사하고 composer install 합니다.
* ./clone admin d1deb2e
  * git 내 admin 폴더의 d1deb2e 커밋의 수정 내용을 svn 내 admin 폴더에 그대로 적용합니다.
* ./commit all
  * svn의 모든 수정내역(추가/수정/삭제)을 커밋합니다.
  * 커밋 메세지는 수동으로 입력 합니다.
* ./commit add
  * svn의 추가된 파일을 커밋 합니다.
  * 커밋 메세지는 수동으로 입력 합니다.
* ./commit add admin d1deb2e
  * svn의 추가된 파일을 커밋 합니다.
  * 커밋 메세지는 git 내 admin 폴더의 d1deb2e 커밋을 사용합니다.
* ./commit modify admin d1deb2e
  * svn의 수정된 파일을 커밋 합니다.
  * 커밋 메세지는 git 내 admin 폴더의 d1deb2e 커밋을 사용합니다.
* ./clone_commit admin d1deb2e
  * git 내 admin 폴더의 d1deb2e 커밋을 svn 내 admin 폴더에 적용 후, 모든 수정 내역을 커밋합니다.
  * 커밋 메세지는 git 내 admin 폴더의 d1deb2e 커밋 메세지를 사용합니다.
* ./rollback 2012
  * svn의 r2012 커밋을 롤백 + 커밋 합니다.
* ./rollback 2012 2015
  * svn의 r2012 ~ r2015 커밋을 롤백 + 커밋 합니다.
