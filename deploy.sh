 #!/bin/bash

# Go to project folder
cd /home/sandsl23/public_html/aacar.sandslab.com || exit

# Ensure local changes do not block pull
/usr/bin/git reset --hard HEAD
/usr/bin/git clean -fd
/usr/bin/git pull origin main

echo "Deployment completed: $(date)" >> /home/sandsl23/public_html/aacar.sandslab.com/deploy.log