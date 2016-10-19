Vagrant.configure(2) do |config|
 
  config.vm.define "dev" do |dev|
    dev.vm.box = "puppetlabs/debian-8.2-64-puppet"

    dev.vm.provision :puppet do |puppet|
      puppet.environment = "production"
      puppet.environment_path = ".puppet"
      puppet.manifests_path = ".puppet/manifests"
      puppet.manifest_file = "default.pp"
      puppet.hiera_config_path = '.puppet/hiera.yaml'
      puppet.module_path = ".puppet/modules"

      puppet.options = [
        "--verbose",
        "--fileserverconfig", "/vagrant/.puppet/fileserver.conf"
      ]
    end

    dev.vm.network "private_network", ip: "192.168.4.100"
    dev.vm.network "forwarded_port", guest: 22, host: 3355
    dev.vm.hostname = "nfqakademija.dev"


    dev.vm.provider :virtualbox do |vb|
      vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
      vb.customize ["modifyvm", :id, "--memory", 2048, "--cpus", 2]
      vb.name = "dev"
    end

    dev.vm.synced_folder "./", "/var/www"
  end

end
