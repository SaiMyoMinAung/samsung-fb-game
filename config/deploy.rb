# config valid for current version and patch releases of Capistrano
#lock "~> 3.14.0"

set :application, "samsung-fb-game"
set :repo_url, "git@github.com:Shanjuefamily/samsung-fb-game.git"

set :keep_releases, 2

# Which roles to consider as laravel roles
set :laravel_roles, :all

# The artisan flags to include on artisan commands by default
set :laravel_artisan_flags, "--env=#{fetch(:stage)}"

# Which roles to use for running migrations
set :laravel_migration_roles, :all

# The artisan flags to include on artisan commands by default when running migrations
set :laravel_migration_artisan_flags, "--force --env=#{fetch(:stage)}"

# The version of laravel being deployed
set :laravel_version, 10

# Whether to upload the dotenv file on deploy
set :laravel_upload_dotenv_file_on_deploy, true

# Ensure the dirs in :linked_dirs exist?
set :laravel_ensure_linked_dirs_exist, true

# Link the directores in laravel_linked_dirs?
set :laravel_set_linked_dirs, true


# Linked directories for a standard Laravel 5 application
set :laravel_5_linked_dirs, [
  'storage'
]

# Ensure the paths in :file_permissions_paths exist?
set :laravel_ensure_acl_paths_exist, true

# Set ACLs for the paths in laravel_acl_paths?
set :laravel_set_acl_paths, true


# Paths that should have ACLs set for a standard Laravel 5 application
set :laravel_5_acl_paths, [
  'bootstrap/cache',
  'storage',
  'storage/app',
  'storage/app/analytics',
  'storage/app/public',
  'storage/framework',
  'storage/framework/cache',
  'storage/framework/sessions',
  'storage/framework/views',
  'storage/logs',
  'public/'
]
