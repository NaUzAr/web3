@echo off
echo Menghubungkan ke server dan menjalankan deploy...
ssh -t root@76.13.21.230 "cd /opt/docker-apps/swaratani && bash deploy.sh"
pause
