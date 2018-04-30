<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'Utterfare');

// Project repository
set('repository', 'https://github.com/cmeehan1991/Utterfare');

set('use_relative_symlinks', false);

set('ssh_multiplexing', true);

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

set('git_recursive', true);

set('branch', 'dev');

set('default_stage', 'dev');

// Shared files/dirs between deploys 
//set('shared_files', []);
//set('shared_dirs', []);

// Writable dirs by web server 
set('writable_dirs', []);

// Hosts

host('dev')
	->hostname('utterfare.com')
	->stage('dev')
	->user('cmeehan')
	->port('2222')
    ->set('deploy_path', '/home1/cmeehan/public_html/ufdev');
    
host('production')
	->hostname('utterfare.com')
	->stage('production')
	->user('cmeehan')
	->port('2222')
    ->set('deploy_path', '/home1/cmeehan/public_html')
    ->set('shared_files', []);    
    
    
/*host('utterfare.com')
	->stage('dev')
	->set('deploy_path', '/var/www/public_html/ufdev');*/
// Tasks

task('test', function(){
	writeln('Hello world');
});

task('pwd', function(){
	$result = run('pwd');
	writeln("Current dir: $result");
});

task('move_to_live', function(){
	run('cp -af public_html/ufdev/current/. /home1/cmeehan/public_html/ufdev/');
});


desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

after('cleanup', 'move_to_live');
