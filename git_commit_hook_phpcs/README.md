# PHP Codesniffer Pre-Commit Hook for GIT

Author: Soenke Ruempler <soenke@ruempler.eu>
Website: http://github.com/s0enke/git-hooks

### 안내
위의 phpcs의 pre-commit hook 수정 버전입니다.
config의 설정을 점검하고 사용하시면 됩니다.

phpcs와 eslint를 지원합니다.

### config 항목별 설명
* TMP_STAGING - 임시 폴더
* PHPCS_BIN - phpcs 경로
* PHPCS_CODING_STANDARD - phpcs ruleset 경로 
* PHPCS_FILE_PATTERN - phpcs 검사할 파일명의 pattern 설정
* PHPCS_IGNORE_WARNINGS - warning 무시 여부
* PHPCS_ENCODING - 파일 인코딩
* PHPCS_USE - 사용 여부 (legacy가 있는 경우 임시로 끌 수 있습니다.)
* ESLINT_BIN - eslint 경로
* ESLINT_CONFIG_PATH - eslint ruleset 경로
* ESLINT_FILE_PATTERN - eslint를 검사할 파일명의 pattern 설정
* ESLINT_USE - 사용 여부 (legacy가 있는 경우 임시로 끌 수 있습니다.)