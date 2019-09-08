# config valid only for current version of Capistrano
lock "3.8.0"

set :application, "capistrano_practice"
set :repo_url, "git@github.com:sukhmeetwebapp/m2webapp.git"

# Default branch is :master
# ask :branch, `git rev-parse --abbrev-ref HEAD`.chomp

# Default deploy_to directory is /var/www/my_app_name
set :deploy_to, "/home/m2webappmate/public_html"

# Default value for :format is :airbrussh.
# set :format, :airbrussh

# You can configure the Airbrussh format using :format_options.
# These are the defaults.
# set :format_options, command_output: true, log_file: "log/capistrano.log", color: :auto, truncate: :auto

# Default value for :pty is false
# set :pty, true

# Default value for :linked_files is []
# append :linked_files, "config/database.yml", "config/secrets.yml"

# Default value for linked_dirs is []
# append :linked_dirs, "log", "tmp/pids", "tmp/cache", "tmp/sockets", "public/system"

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for keep_releases is 5
# set :keep_releases, 5
set :magento_deploy_languages, ['en_US', 'en_CA']
set :magento_deploy_composer, false

set :linked_files, [
  'app/etc/env.php',
  'app/etc/config.local.php',
  'var/.setup_cronjob_status',
  'var/.update_cronjob_status'
]

set :linked_dirs, [
  'pub/media',
  'pub/sitemaps',
  'var/backups', 
  'var/composer_home', 
  'var/importexport', 
  'var/import_history', 
  'var/log',
  'var/session', 
  'var/tmp'
]

set :magento_auth_public_key, '8198890c18c32166d15be9b19c1844b4'
set :magento_auth_private_key, '2f48c3639b36e4254c3ecdcc2212c806'