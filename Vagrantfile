# -*- mode: ruby -*-
# vi: set ft=ruby :

$script = <<SCRIPT
wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit
SCRIPT

Vagrant.configure("2") do |config|

  config.vm.box = "scotch/box"
  config.ssh.forward_agent = true
  config.vm.network "private_network", ip: "192.168.33.10"
  config.vm.hostname = "scotchbox"
  config.vm.provision "shell", inline: $script
  config.vm.synced_folder ".", "/var/www/public", id: "application",  :nfs => true
  config.vm.provider "virtualbox" do |v|
     v.memory = 1024
   end
end
