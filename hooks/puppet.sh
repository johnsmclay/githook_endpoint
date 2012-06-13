ssh jamhead@172.16.11.192 -o StrictHostKeyChecking=no "cd /etc/puppet ; sudo git reset --hard ; sudo git checkout master ; sudo git pull origin master"
RETVAL=$?
if [ $RETVAL -ne 0 ]; then echo "failed to deploy to puppetmaster"; else echo "master was deployed if changes exist"; fi

ssh jamhead@172.16.11.193 -o StrictHostKeyChecking=no "cd /etc/puppet ; sudo git reset --hard ; sudo git pull origin stages ; sudo git checkout stages"
RETVAL=$?
if [ $RETVAL -ne 0 ]; then echo "failed to deploy to puppetmaster2"; else echo "stages was deployed if changes exist"; fi
