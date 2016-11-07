Exec {
  path => ["/usr/bin", "/bin", "/usr/sbin", "/sbin", "/usr/local/bin", "/usr/local/sbin"]
}

class bootstrap {
  apt::source { 'packages.dotdeb.org-php':
    location          => 'http://packages.dotdeb.org',
    release           => 'jessie',
    repos             => 'all',
    required_packages => 'debian-keyring debian-archive-keyring',
    key               => '89DF5277',
    key_server        => 'keys.gnupg.net',
    pin               => '999',
    include_src       => true
  }
}

stage { "init": before  => Stage["main"] }
stage { "post": require => Stage["main"] }

class {'bootstrap':
  stage => init
}

class { 'apt':
  stage => init
}


# psysh
exec { 'composer install psysh':
  command => 'su vagrant -c "composer g require psy/psysh:@stable"',
  onlyif  => "test ! -e /home/vagrant/.composer/vendor/bin/",
  require => Class['composer']
}

file { "/home/vagrant/.psysh":
  ensure => directory,
  owner => "vagrant",
  group => "vagrant",
}

exec { 'psysh php manual':
  command => 'wget -O /home/vagrant/.psysh/php_manual.sqlite http://psysh.org/manual/en/php_manual.sqlite',
  onlyif  => "test ! -e /home/vagrant/.psysh/php_manual.sqlite",
  require => [
    Exec['composer install psysh'],
    File['/home/vagrant/.psysh']
  ]
}


#gems
$gems = [
  'compass',
  'sass',
  'scss-lint'
]

exec { 'remove-rvm':
  command => 'gem uninstall rvm',
  unless  => 'test `gem list --local | grep -q rvm; echo $?` -ne 0'
}

package {["ruby", "ruby-dev"]:
  ensure => latest,
  require => Exec['remove-rvm']
}

define install_gem {
  package { $name:
    provider => 'gem',
    ensure   => latest,
    require  => [
      Exec['remove-rvm'],
      Package['sqlite3'],
      Package['ruby-dev']
    ]
  }
}

install_gem {$gems: }

$packages = [
  "vim",
  "htop",
  "nano",
  "screen",
  "rsync",
  "augeas-tools",
  "mcrypt",
  "libaugeas-ruby",
  "tar",
  "sshpass",
  "g++"
]

# install packages
package { $packages:
  ensure  => present,
  require => Exec["apt_update"]
}

######### PHP
# package install list
$php_required = [
  "php7.0",
  "php7.0-cli",
  "php7.0-mysql",
  "php7.0-dev",
  "php7.0-gd",
  "php7.0-mcrypt",
  "libapache2-mod-php7.0",
  "php7.0-curl",
  "php7.0-intl",
  "php7.0-xdebug",
  "php7.0-xml",
  "php7.0-zip"
]

package { $php_required:
  ensure  => present,
  require => Exec["apt_update"]
}

#file { '/etc/php7.0/apache2/conf.d/xdebug.ini':
#  ensure  => file,
#  source  => "puppet:///librarian/php/xdebug.ini",
#  owner   => "root",
#  group   => "root",
#  mode    => "644",
#  require => [
#    Package['php7.0-xdebug'],
#    Package['apache2']
#  ],
#  notify  => Service["apache2"],
#}

#augeas{ 'php_timezone_cli':
#  context => '/files/etc/php7.0/cli/php.ini/PHP',
#  changes => 'set date.timezone Europe/Vilnius',
#  require => [
#    Package['php7.0-cli'],
#    Package['augeas-tools']
#  ]
#}

#file { '/etc/php7.0/apache2/php.ini':
#  ensure => present,
#  source => 'puppet:///librarian/php/php.apache.ini',
#  require => [
#    Package['php7.0'],
#    Package['apache2'],
#  ],
#  notify  => Service["apache2"]
#}

class { 'composer':
  auto_update  => true,
  require => [
    Package['php7.0-cli']
  ]
}

file { '/usr/local/bin/debug':
  ensure => present,
  mode => "755",
  content => "#!/bin/sh\nenv PHP_IDE_CONFIG=\"serverName=nfqakademija.dev\" XDEBUG_CONFIG=\"idekey=PHPSTORM\" SYMFONY_DEBUG=\"1\" $@"
}

######### NODEJS
class {"nodejs":
  version => 'latest',
  make_install => false,
  target_dir => '/bin'
} ->

file {["/usr/local/bin/node", "/usr/local/bin/nodejs"]:
  ensure  => link,
  target  => "/usr/local/node/node-default/bin/node",
} ->

file {"/usr/local/bin/npm":
  ensure  => link,
  target  => "/usr/local/node/node-default/bin/npm",
}

package { "gulp":
  ensure   => present,
  provider => "npm",
  require  => File['/usr/local/bin/npm']
}->

file {"/usr/local/bin/gulp":
  ensure  => link,
  target  => "/usr/local/node/node-default/bin/gulp",
}

package { "bower":
  ensure   => present,
  provider => "npm",
  require  => File['/usr/local/bin/npm']
}->

file {"/usr/local/bin/bower":
  ensure  => link,
  target  => "/usr/local/node/node-default/bin/bower",
}

########## MYSQL
# install mysql server
package { "mysql-server":
  ensure => present,
  require => Exec["apt_update"]
}



augeas { 'my.cnf':
    require => [
        Package['mysql-server'],
        Package['libaugeas-ruby'],
    ],
    notify => Service['mysql'],
    context => '/files/etc/mysql/my.cnf',
    changes => [
        "set target[.='mysqld']/bind-address 0.0.0.0",
    ],
}->

#start mysql service
service { "mysql":
  ensure => running,
  require => Package["mysql-server"],
}->

exec { "wait-for-mysql":
    command => "sleep 10",
    unless => "mysqladmin -h127.0.0.1 -uroot -proot status",
}->

# set mysql password
exec { "set-mysql-password":
  unless => "mysqladmin -h127.0.0.1 -uroot -proot status",
  command => "mysqladmin -h127.0.0.1 -uroot password root",
  require => Service["mysql"],
}->

# add mysql remote user
exec { "create-${name}-db":
  unless => "/usr/bin/mysql -uproject -pproject",
  command => "/usr/bin/mysql -uroot -proot -e \"grant all on *.* to 'project'@'%' identified by 'project';\"",
      require => Service["mysql"],
}


########### Mailcatcher
file { '/etc/init.d/mailcatcher':
  ensure  => present,
  source  => '/vagrant/.puppet/files/mailcatcher/mailcatcher.conf',
  mode    => '744'
}

file { '/var/log/mailcatcher':
  ensure  => directory,
}

package { 'mailcatcher':
  provider => 'gem',
  ensure   => '0.5.12',
  require  => [
    Exec['remove-rvm'],
    Package['sqlite3'],
    Package['libsqlite3-dev'],
    Package['pkg-config'],
    Package['g++'],
    Package['ruby-dev']
  ]
}

package { ['sqlite3', 'libsqlite3-dev', 'pkg-config']:
  ensure => present
}

service { 'mailcatcher':
  enable  => true,
  ensure  => running,
  require => [
    Package["mailcatcher"],
    File["/var/log/mailcatcher"],
    File["/etc/init.d/mailcatcher"],
  ]
}

######## BLING
# shell config
file { '/etc/motd':
  ensure  => file,
  source  => "/vagrant/.puppet/files/motd"
}

class { 'ohmyzsh': }
ohmyzsh::install { 'vagrant': }

file { '/home/vagrant/.oh-my-zsh/custom/plugins/gulp':
  ensure  => directory,
  require => Ohmyzsh::Install['vagrant'],
  owner   => "vagrant",
  group   => "vagrant"
}

file { '/home/vagrant/.zshrc':
  ensure  => file,
  source  => '/vagrant/.puppet/files/zshrc',
  require => Ohmyzsh::Install['vagrant'],
  owner   => "vagrant",
  group   => "vagrant"
}

file { '/home/vagrant/.oh-my-zsh/custom/plugins/gulp/gulp.plugin.zsh':
  ensure  => file,
  source  => "/vagrant/.puppet/files/gulp.zsh",
  require => [
    Ohmyzsh::Install['vagrant'],
    File['/home/vagrant/.oh-my-zsh/custom/plugins/gulp'],
  ],
  owner  => "vagrant",
  group  => "vagrant"
}

############## APACHE
# install apache
package { "apache2":
  ensure  => present,
  require => Exec["apt_update"]
}

# ensures that mode_rewrite is loaded and modifies the default configuration file
file { "/etc/apache2/mods-enabled/rewrite.load":
  ensure  => link,
  target  => "/etc/apache2/mods-available/rewrite.load",
  require => Package["apache2"]
}

file { "/etc/apache2/mods-enabled/ssl.load":
  ensure  => link,
  target  => "/etc/apache2/mods-available/ssl.load",
  require => Package["apache2"]
}

# create directory
file {"/etc/apache2/sites-enabled":
  ensure  => directory,
  recurse => true,
  purge   => true,
  force   => true,
  before  => File["/etc/apache2/sites-enabled/nfqakademija.conf"],
  require => Package["apache2"],
}

file {"/etc/apache2/sites-available":
  ensure  => directory,
  recurse => true,
  purge   => true,
  force   => true,
  require => Package["apache2"],
}

# create apache config from main vagrant manifests
file { "/etc/apache2/sites-available/nfqakademija.conf":
  ensure  => present,
  source  => "/vagrant/.puppet/files/apache/nfqakademija.vhost",
  require => File["/etc/apache2/sites-available"],
}

# symlink apache site to the site-enabled directory
file { "/etc/apache2/sites-enabled/nfqakademija.conf":
  ensure  => link,
  target  => "/etc/apache2/sites-available/nfqakademija.conf",
  require => File["/etc/apache2/sites-available/nfqakademija.conf"],
  notify  => Service["apache2"],
}

# starts the apache2 service once the packages installed, and monitors changes to its configuration files and reloads if nesessary
service { "apache2":
  ensure    => running,
  require   => Package["apache2"],
  subscribe => [
    File["/etc/apache2/mods-enabled/rewrite.load"],
    File["/etc/apache2/sites-available/nfqakademija.conf"]
  ],
}

exec {"vagrant to dialout":
  unless => "grep -q 'dialout\\S*vagrant' /etc/group",
  command => "usermod -aG dialout vagrant"
}

exec {"www-data to dialout":
  unless => "grep -q 'dialout\\S*www-data' /etc/group",
  command => "usermod -aG dialout www-data"
}

file { "/home/vagrant/.composer":
  ensure => directory,
  owner => "vagrant",
  group => "vagrant",
}

file { '/var/project':
  ensure  => directory,
  owner   => "www-data",
  group   => "www-data",
  require => Package["apache2"],
}

exec { 'facl on files':
    command => '/usr/bin/setfacl -R -m u:www-data:rwX -m u:vagrant:rwX /var/project',
    require => File['/var/project']
}

exec { 'facl on dirs':
    command => '/usr/bin/setfacl -dR -m u:www-data:rwX -m u:vagrant:rwX /var/project',
    require => File['/var/project']
}
