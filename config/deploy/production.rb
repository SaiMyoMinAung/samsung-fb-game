server '13.251.221.219', user: 'ubuntu', roles: %w{www-data}
set :ssh_options, {
    keys: %w(~/.ssh/id_rsa),
    forward_agent: true
  }

  
# Which dotenv file to transfer to the server
set :laravel_dotenv_file, './.env.production'

set :branch, 'main'

set :deploy_to, "/var/www/samsung-fb-game"

namespace :deploy do
  after :finishing, 'laravel:migrate'
end