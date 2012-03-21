# server to support chrome
# or start chrome with: --allow-file-access-from-files
require 'sinatra'
require 'rack/coffee'

set :public_folder, '.'

use Rack::Coffee, 
  root: '.',
#  bare: true,
#  nowrap: true,
  urls: ['/src','/spec']
