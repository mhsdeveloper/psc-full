# Setting up SSL for your nginx site

These instructions assume you've create a SSL key, submitted it to a signing authority, and received a SSL certificate and chain in return.

# Required SSL Certificate Files

Private key: this is the key you gave to your signing authority. Name this file mydomain-YEAR.key
Certificate Chain or Intermediate certificate: your SSL certificate authority should provide this. Name this file vendor-chain.crt (SHA1) or vendor-chain.pem (SHA2).
Signed certificate: your authority should also provide thisthe signed SSL certificate from your SSL certification vendor. Name this file mydomain-YEAR.crt

Put all these files in /etc/nginx or a subfolder of that directory.

# Make a copy to preserve your orginal in case anything goes wrong

    cp mydomain-YEAR.crt mydomain-YEAR.pem  

# Add the Intermediate Certificate to your SSL Certificate

This step concatenates the intermediate certificate with your signed SSL certificate. The certificates have to be in a correct order: your signed SSL certificate first, afterwards the intermediate.

    cat vendor-chain.crt >> mydomain-YEAR.pem  

# Edit install/server-configs/partial-ssl-config.txt

First, find the partial ssl config file and edit so that your domain name appears instead of the "mydomain" placeholder. Also, you need to make sure the path and file name to the two keys you created above appear in this file.

Second, look at the file /etc/nginx/sites-available/wpmu.conf and find the comment that mentions where you should paste in your edited partial-ssl-config.txt. You'll be removing a few lines from the top of wpmu.conf and replacing it with the edited partial-ssl-config.txt content.

That's it, simple restart nginx:

	sudo service nginx restart