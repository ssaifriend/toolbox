# toolbox

develop tools

## deploy script
write config file (see config-sample)

command list
* composer path (package_name)
* clone path commit_id
* commit type (path) (commit_id) - type list: all, add, modify, delete
* rollback start_rev (end_rev)

### Usage
* ./deploy composer admin
* ./deploy composer ./
* ./deploy composer admin ridibooks/store
* ./deploy clone admin d1deb2e
* ./deploy commit all
* ./deploy commit add admin
* ./deploy commit add admin d1deb2e
* ./deploy commit modify admin d1deb2e
* ./deploy rollback 2012
* ./deploy rollback 2012 2015